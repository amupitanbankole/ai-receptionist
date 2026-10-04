<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Lead;
use App\Services\LeadScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeadScoringController extends Controller
{
    public function index(): View
    {
        $leads = Lead::with('company')->orderByDesc('score')->paginate(20);

        return view('lead-scoring.index', compact('leads'));
    }

    public function run(LeadScoringService $scoring): RedirectResponse
    {
        Lead::with('company')->get()->each(fn (Lead $lead) => $scoring->scoreLead($lead));

        return redirect()->route('lead-scoring.index')->with('success', 'Lead scores recalculated.');
    }

    public function runCompany(Company $company, LeadScoringService $scoring): RedirectResponse
    {
        $score = $scoring->scoreCompany($company);
        $company->update(['lead_score' => $score]);

        $company->leads()->each(fn (Lead $lead) => $scoring->scoreLead($lead));

        return back()->with('success', 'Company and related lead scores recalculated.');
    }
}
