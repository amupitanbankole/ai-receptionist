<?php

namespace AppHttpControllers;

use AppModelsCompany;
use AppModelsContact;
use AppModelsLead;
use IlluminateHttpRequest;
use IlluminateViewView;

class LeadController extends Controller
{
    public function index(): View
    {
        $leads = Lead::with(['company', 'contact'])
            ->latest()
            ->paginate(20);

        return view('leads.index', compact('leads'));
    }

    public function create(): View
    {
        $companies = Company::orderBy('name')->get();
        $contacts = Contact::orderBy('first_name')->get();

        return view('leads.create', compact('companies', 'contacts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'contact_id' => ['nullable', 'exists:contacts,id'],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:new,qualified,demo,customer,lost'],
            'score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'temperature' => ['required', 'in:low,medium,high,hot'],
            'source' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $validated['score'] = $validated['score'] ?? 0;

        Lead::create($validated);

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead): View
    {
        $lead->load(['company', 'contact']);

        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead): View
    {
        $companies = Company::orderBy('name')->get();
        $contacts = Contact::orderBy('first_name')->get();

        return view('leads.edit', compact('lead', 'companies', 'contacts'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'contact_id' => ['nullable', 'exists:contacts,id'],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:new,qualified,demo,customer,lost'],
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'temperature' => ['required', 'in:low,medium,high,hot'],
            'source' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $lead->update($validated);

        return redirect()
            ->route('leads.show', $lead)
            ->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead deleted successfully.');
    }
}
