<?php

namespace Tests\Unit;

use App\Models\Buyer;
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
}
