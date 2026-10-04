<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Lead;

class LeadScoringService
{
    public function scoreCompany(Company $company): int
    {
        $score = 0;

        $signals = [
            'emergency_service' => 15,
            'appointment_based' => 15,
            'phone_prominent' => 10,
            'online_booking' => -10,
            'small_team' => 10,
            'outside_hours_service' => 10,
            'high_value_service' => 10,
            'live_chat' => -5,
            'multiple_locations' => 5,
        ];

        foreach ($signals as $field => $points) {
            if ($company->{$field}) {
                $score += $points;
            }
        }

        return max(0, min(100, $score));
    }

    public function temperature(int $score): string
    {
        return match (true) {
            $score >= 80 => 'hot',
            $score >= 60 => 'high',
            $score >= 40 => 'medium',
            default => 'low',
        };
    }

    public function scoreLead(Lead $lead): Lead
    {
        $score = $this->scoreCompany($lead->company);

        $lead->update([
            'score' => $score,
            'temperature' => $this->temperature($score),
        ]);

        $lead->company->update(['lead_score' => $score]);

        return $lead->refresh();
    }
}
