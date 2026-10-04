<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Lead;
use App\Services\LeadScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadScoringController extends Controller
{
    public function index(): View
    {
        $leads = Lead::with('company')->orderByDesc('score')->paginate(20);
        $companies = Company::orderBy('name')->get();

        return view('lead-scoring.index', compact('leads', 'companies'));
    }

    public function run(LeadScoringService $scoring): RedirectResponse
    {
        Lead::with('company')->get()->each(fn (Lead $lead) => $scoring->scoreLead($lead));

        return redirect()->route('lead-scoring.index')->with('success', 'Lead scores recalculated.');
    }

    public function updateSignals(Request $request, Company $company, LeadScoringService $scoring): RedirectResponse
    {
        $signals = [
            'emergency_service','appointment_based','phone_prominent','online_booking',
            'small_team','outside_hours_service','high_value_service','live_chat','multiple_locations',
        ];

        $values = [];
        foreach ($signals as $signal) {
            $values[$signal] = $request->boolean($signal);
        }

        $company->update($values);
        $scoring->scoreCompany($company);

        $company->leads()->each(fn (Lead $lead) => $scoring->scoreLead($lead));

        return redirect()->route('lead-scoring.index')->with('success', 'Scoring signals saved and scores recalculated.');
    }
}
