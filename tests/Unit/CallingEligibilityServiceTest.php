<?php

namespace Tests\Unit;

use App\Models\Buyer;
use App\Models\BuyerAuthorization;
use App\Models\ConsentRecord;
use App\Models\DncScreening;
use App\Models\Prospect;
use App\Models\UsState;
use App\Services\CallingEligibilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CallingEligibilityServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_record_prospect_is_blocked_without_verified_ai_calling_consent(): void
    {
        config(['leadgen.outbound_ai_calling_enabled' => true]);
        config(['leadgen.calling_entity_legal_name' => 'Example Agency LLC']);

        $state = UsState::create([
            'code' => 'TX',
            'name' => 'Texas',
            'finder_status' => 'approved',
            'calling_status' => 'approved',
        ]);

        $buyer = Buyer::create([
            'legal_name' => 'Example Recovery LLC',
            'buyer_category' => 'recovery_company',
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $prospect = Prospect::create([
            'us_state_id' => $state->id,
            'first_name' => 'Test',
            'last_name' => 'Prospect',
            'phone_e164' => '+12125550123',
            'country_code' => 'US',
        ]);

        $decision = app(CallingEligibilityService::class)->evaluate(
            $prospect,
            '+12125550123',
            $buyer
        );

        $this->assertSame('blocked', $decision->decision);
        $this->assertContains(
            'valid_caller_and_buyer_specific_ai_telemarketing_consent_missing',
            $decision->reason_codes
        );
        $this->assertContains('buyer_state_authorization_missing_or_expired', $decision->reason_codes);
        $this->assertContains('federal_dnc_screening_missing_or_expired', $decision->reason_codes);
        $this->assertContains('state_dnc_screening_missing_or_expired', $decision->reason_codes);
    }

    public function test_outbound_calls_are_blocked_when_global_feature_flag_is_off(): void
    {
        config(['leadgen.outbound_ai_calling_enabled' => false]);

        $state = UsState::create(['code' => 'FL', 'name' => 'Florida']);
        $buyer = Buyer::create([
            'legal_name' => 'Disabled Test Buyer LLC',
            'verification_status' => 'pending',
            'is_active' => false,
        ]);
        $prospect = Prospect::create([
            'us_state_id' => $state->id,
            'phone_e164' => '+13055550123',
            'country_code' => 'US',
        ]);

        $decision = app(CallingEligibilityService::class)->evaluate($prospect, '+13055550123', $buyer);

        $this->assertSame('blocked', $decision->decision);
        $this->assertContains('outbound_ai_calling_globally_disabled', $decision->reason_codes);
    }

    public function test_latest_blocked_dnc_result_overrides_an_older_clear_result(): void
    {
        config(['leadgen.outbound_ai_calling_enabled' => true]);
        config(['leadgen.calling_entity_legal_name' => 'Example Agency LLC']);

        $state = UsState::create([
            'code' => 'CA',
            'name' => 'California',
            'finder_status' => 'approved',
            'calling_status' => 'approved',
        ]);

        $buyer = Buyer::create([
            'legal_name' => 'Example Recovery LLC',
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        BuyerAuthorization::create([
            'buyer_id' => $buyer->id,
            'us_state_id' => $state->id,
            'service_category' => 'unclaimed_property_assistance',
            'status' => 'approved',
            'verified_at' => now(),
        ]);

        $prospect = Prospect::create([
            'us_state_id' => $state->id,
            'phone_e164' => '+14155550123',
            'country_code' => 'US',
        ]);

        ConsentRecord::create([
            'prospect_id' => $prospect->id,
            'phone_e164' => '+14155550123',
            'calling_entity_legal_name' => 'Example Agency LLC',
            'named_seller_legal_name' => 'Example Recovery LLC',
            'consent_scope' => 'ai_voice_telemarketing',
            'consent_method' => 'web_form',
            'consent_text_version' => 'v1',
            'consent_text' => 'Test consent wording',
            'verification_status' => 'verified',
            'captured_at' => now(),
        ]);

        DncScreening::create([
            'phone_e164' => '+14155550123',
            'screening_type' => 'federal_dnc',
            'status' => 'clear',
            'checked_at' => now()->subDay(),
            'expires_at' => now()->addDays(30),
        ]);
        DncScreening::create([
            'phone_e164' => '+14155550123',
            'screening_type' => 'federal_dnc',
            'status' => 'blocked',
            'checked_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);
        DncScreening::create([
            'phone_e164' => '+14155550123',
            'screening_type' => 'state_dnc',
            'status' => 'not_required',
            'checked_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        $decision = app(CallingEligibilityService::class)->evaluate($prospect, '+14155550123', $buyer);

        $this->assertSame('blocked', $decision->decision);
        $this->assertContains('federal_dnc_screening_missing_or_expired', $decision->reason_codes);
    }
}
