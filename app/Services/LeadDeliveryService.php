<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\LeadDelivery;
use App\Models\RecoveryAppointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LeadDeliveryService
{
    /**
     * Deliver an appointment only to the buyer the prospect explicitly approved.
     */
    public function deliver(RecoveryAppointment $appointment, Buyer $buyer): LeadDelivery
    {
        return DB::transaction(function () use ($appointment, $buyer) {
            $lockedAppointment = RecoveryAppointment::query()
                ->whereKey($appointment->id)
                ->lockForUpdate()
                ->with('prospect')
                ->firstOrFail();

            if ((int) $lockedAppointment->buyer_id !== (int) $buyer->id) {
                throw ValidationException::withMessages(['buyer' => 'This appointment is not assigned to the selected buyer.']);
            }

            if (!in_array($lockedAppointment->status, ['confirmed', 'accepted'], true) || !$lockedAppointment->prospect_confirmed_at) {
                throw ValidationException::withMessages(['appointment' => 'The prospect has not confirmed this appointment.']);
            }

            if (!$lockedAppointment->data_sharing_consent_at
                || !$lockedAppointment->data_sharing_consent_version
                || $lockedAppointment->data_sharing_consent_buyer_legal_name !== $buyer->legal_name) {
                throw ValidationException::withMessages(['consent' => 'The prospect has not explicitly authorized sharing this appointment with this buyer.']);
            }

            if ($lockedAppointment->shared_with_buyer_at) {
                throw ValidationException::withMessages(['appointment' => 'This appointment has already been shared.']);
            }

            if (!$buyer->is_active || $buyer->verification_status !== 'verified') {
                throw ValidationException::withMessages(['buyer' => 'The buyer is not verified and active.']);
            }

            if ($lockedAppointment->prospect->do_not_contact_at) {
                throw ValidationException::withMessages(['prospect' => 'This prospect is suppressed from further contact.']);
            }

            $authorization = $buyer->authorizations()
                ->where('us_state_id', $lockedAppointment->prospect->us_state_id)
                ->where('service_category', 'unclaimed_property_assistance')
                ->where('status', 'approved')
                ->whereNotNull('verified_at')
                ->where(function ($query) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->first();

            if (!$authorization) {
                throw ValidationException::withMessages(['buyer' => 'The buyer has no current approved authorization for this state and service.']);
            }

            $delivery = LeadDelivery::create([
                'recovery_appointment_id' => $lockedAppointment->id,
                'buyer_id' => $buyer->id,
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);

            $lockedAppointment->forceFill(['shared_with_buyer_at' => now()])->save();

            return $delivery;
        });
    }
}
