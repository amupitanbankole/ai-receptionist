<?php

namespace App\\Http\\Controllers;

use App\\Models\\Buyer;
use App\\Models\\CallingEligibility;
use App\\Models\\Prospect;
use App\\Models\\PropertyRecord;
use App\\Models\\RecoveryAppointment;
use Illuminate\\View\\View;

class UnclaimedFundsDashboardController extends Controller
{
    public function index(): View
    {
        $metrics = [
            'prospects' => Prospect::count(),
            'verified_records' => PropertyRecord::where('verification_status', 'verified')->count(),
            'consented_prospects' => Prospect::whereHas('consentRecords', fn ($query) => $query
                ->where('verification_status', 'verified')
                ->whereNull('revoked_at'))->count(),
            'eligible_numbers' => CallingEligibility::where('decision', 'eligible')
                ->where('expires_at', '>', now())->count(),
            'appointments' => RecoveryAppointment::whereIn('status', ['confirmed', 'accepted'])->count(),
            'active_buyers' => Buyer::where('is_active', true)->where('verification_status', 'verified')->count(),
        ];

        $recentAppointments = RecoveryAppointment::with(['prospect.state', 'buyer'])
            ->latest('starts_at')->limit(8)->get();

        return view('unclaimed-funds.dashboard', compact('metrics', 'recentAppointments'));
    }
}
