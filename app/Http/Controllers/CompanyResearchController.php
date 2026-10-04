<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\WebsiteResearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyResearchController extends Controller
{
    public function show(Company $company): View
    {
        $company->load('intelligence');
        return view('research.show', compact('company'));
    }

    public function run(Company $company, WebsiteResearchService $research): RedirectResponse
    {
        $research->research($company);
        return back()->with('success','Website research completed.');
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'research_status' => ['required','in:pending,completed,failed'],
            'research_summary' => ['nullable','string'],
            'services_summary' => ['nullable','string'],
            'service_areas_summary' => ['nullable','string'],
            'booking_process' => ['nullable','string'],
            'sales_opportunities' => ['nullable','string'],
            'pain_points' => ['nullable','string'],
            'strengths' => ['nullable','string'],
            'technology_notes' => ['nullable','string'],
        ]);

        $validated['researched_at'] = now();

        $company->intelligence()->updateOrCreate(
            ['company_id' => $company->id],
            $validated
        );

        return back()->with('success','Company intelligence saved.');
    }
}
