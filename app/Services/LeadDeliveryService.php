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
        if ((int) $appointment->buyer_id !== (int) $buyer->id) {
            throw ValidationException::withMessages(['buyer' => 'This appointment is not assigned to the selected buyer.']);
        }

        if (!in_array($appointment->status, ['confirmed', 'accepted'], true) || !$appointment->prospect_confirmed_at) {
            throw ValidationException::withMessages(['appointment' => 'The prospect has not confirmed this appointment.']);
        }

        if (!$appointment->data_sharing_consent_at || !$appointment->data_sharing_consent_version || $appointment->data_sharing_consent_buyer_legal_name !== $buyer->legal_name) {
            throw ValidationException::withMessages(['consent' => 'The prospect has not explicitly authorized sharing this appointment with this buyer.']);
        }

        if ($appointment->shared_with_buyer_at) {
            throw ValidationException::withMessages(['appointment' => 'This appointment has already been shared.']);
        }

        if (!$buyer->is_active || $buyer->verification_status !== 'verified') {
            throw ValidationException::withMessages(['buyer' => 'The buyer is not verified and active.']);
        }

        if ($appointment->prospect->do_not_contact_at) {
            throw ValidationException::withMessages(['prospect' => 'This prospect is suppressed from further contact.']);
        }

        $stateId = $appointment->prospect->us_state_id;
        $authorization = $buyer->authorizations()
            ->where('us_state_id', $stateId)
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

        return DB::transaction(function () use ($appointment, $buyer) {
            $delivery = LeadDelivery::create([
                'recovery_appointment_id' => $appointment->id,
                'buyer_id' => $buyer->id,
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);

            $appointment->forceFill(['shared_with_buyer_at' => now()])->save();

            return $delivery;
        });
    }
}
