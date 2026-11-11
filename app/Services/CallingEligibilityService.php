<?php

namespace App\\Services;

use App\\Models\\Buyer;
use App\\Models\\CallingEligibility;
use App\\Models\\DncSuppression;
use App\\Models\\Prospect;
use Illuminate\\Support\\Carbon;

class CallingEligibilityService
{
    /**
     * Evaluate a prospect for one named buyer. This service never initiates a call.
     * A public-record match alone is never treated as consent.
     */
    public function evaluate(Prospect $prospect, string $phoneE164, Buyer $buyer): CallingEligibility
    {
        $reasons = [];
        $state = $prospect->state;
        $now = now();

        if (!$state || $state->calling_status !== 'approved') {
            $reasons[] = 'state_calling_review_not_approved';
        }

        if (!$buyer->is_active || $buyer->verification_status !== 'verified') {
            $reasons[] = 'buyer_not_verified_and_active';
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

        $consent = $prospect->consentRecords()
            ->where('phone_e164', $phoneE164)
            ->where('named_seller_legal_name', $buyer->legal_name)
            ->where('consent_scope', 'ai_voice_telemarketing')
            ->where('verification_status', 'verified')
            ->whereNotNull('captured_at')
            ->whereNull('revoked_at')
            ->latest('captured_at')
            ->first();

        if (!$consent) {
            $reasons[] = 'valid_buyer_specific_ai_telemarketing_consent_missing';
        }

        $suppressed = DncSuppression::query()
            ->where('phone_e164', $phoneE164)
            ->where('is_active', true)
            ->whereIn('scope', ['all_telemarketing', 'ai_voice', 'all'])
            ->exists();

        if ($suppressed || $prospect->do_not_contact_at) {
            $reasons[] = 'do_not_call_suppression_active';
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
                'buyer_verification_status' => $buyer->verification_status,
                'buyer_authorization_id' => $authorization?->id,
                'consent_record_id' => $consent?->id,
                'suppressed' => $suppressed,
                'evaluated_at' => $now->toIso8601String(),
            ],
            'evaluated_at' => $now,
            'expires_at' => Carbon::parse($now)->addHours(12),
        ]);
    }
}
