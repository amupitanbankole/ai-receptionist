<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\CallingEligibility;
use App\Models\DncScreening;
use App\Models\DncSuppression;
use App\Models\Prospect;
use Illuminate\Support\Carbon;

class CallingEligibilityService
{
    /**
     * Evaluate one prospect for one named buyer. This service never initiates a call.
     * Public-record data is never treated as consent.
     */
    public function evaluate(Prospect $prospect, string $phoneE164, Buyer $buyer): CallingEligibility
    {
        $reasons = [];
        $state = $prospect->state;

        if (!config('leadgen.outbound_ai_calling_enabled', false)) {
            $reasons[] = 'outbound_ai_calling_globally_disabled';
        }
        $now = now();

        if (!$state || $state->calling_status !== 'approved') {
            $reasons[] = 'state_calling_review_not_approved';
        }

        if (!$state || $state->finder_status !== 'approved') {
            $reasons[] = 'state_finder_model_review_not_approved';
        }

        if (!$buyer->is_active || $buyer->verification_status !== 'verified') {
            $reasons[] = 'buyer_not_verified_and_active';
        }

        if (!$prospect->phone_e164 || $prospect->phone_e164 !== $phoneE164) {
            $reasons[] = 'phone_not_verified_on_prospect';
        }

        $authorization = $state
            ? $buyer->authorizations()
                ->where('us_state_id', $state->id)
                ->where('service_category', 'unclaimed_property_assistance')
                ->where('status', 'approved')
                ->whereNotNull('verified_at')
                ->where(function ($query) use ($now) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>', $now);
                })
                ->first()
            : null;

        if (!$authorization) {
            $reasons[] = 'buyer_state_authorization_missing_or_expired';
        }

        $callingEntity = trim((string) config('leadgen.calling_entity_legal_name', ''));
        $consent = $callingEntity !== ''
            ? $prospect->consentRecords()
                ->where('phone_e164', $phoneE164)
                ->where('calling_entity_legal_name', $callingEntity)
                ->where('named_seller_legal_name', $buyer->legal_name)
                ->where('consent_scope', 'ai_voice_telemarketing')
                ->where('verification_status', 'verified')
                ->whereNotNull('captured_at')
                ->whereNull('revoked_at')
                ->latest('captured_at')
                ->first()
            : null;

        if ($callingEntity === '') {
            $reasons[] = 'calling_entity_legal_name_not_configured';
        }

        if (!$consent) {
            $reasons[] = 'valid_caller_and_buyer_specific_ai_telemarketing_consent_missing';
        }

        $suppressed = DncSuppression::query()
            ->where('phone_e164', $phoneE164)
            ->where('is_active', true)
            ->whereIn('scope', ['all_telemarketing', 'ai_voice', 'all'])
            ->exists();

        if ($suppressed || $prospect->do_not_contact_at) {
            $reasons[] = 'do_not_call_suppression_active';
        }

        // A missing, stale or unknown registry check blocks eligibility.
        foreach (['federal_dnc', 'state_dnc'] as $screeningType) {
            $screening = DncScreening::query()
                ->where('phone_e164', $phoneE164)
                ->where('screening_type', $screeningType)
                ->whereIn('status', ['clear', 'not_required'])
                ->whereNotNull('checked_at')
                ->where('expires_at', '>', $now)
                ->latest('checked_at')
                ->first();

            if (!$screening || ($screeningType === 'federal_dnc' && $screening->status !== 'clear')) {
                $reasons[] = $screeningType . '_screening_missing_or_expired';
            }
        }

        $decision = empty($reasons) ? 'eligible' : 'blocked';

        return CallingEligibility::create([
            'prospect_id' => $prospect->id,
            'phone_e164' => $phoneE164,
            'buyer_id' => $buyer->id,
            'decision' => $decision,
            'reason_codes' => $reasons,
            'evidence_snapshot' => [
                'state_calling_status' => $state?->calling_status,
                'state_finder_status' => $state?->finder_status,
                'buyer_verification_status' => $buyer->verification_status,
                'buyer_authorization_id' => $authorization?->id,
                'consent_record_id' => $consent?->id,
                'calling_entity_configured' => $callingEntity !== '',
                'suppressed' => $suppressed,
                'dnc_screenings_checked' => ['federal_dnc', 'state_dnc'],
                'evaluated_at' => $now->toIso8601String(),
            ],
            'evaluated_at' => $now,
            'expires_at' => Carbon::parse($now)->addHours(12),
        ]);
    }
}
