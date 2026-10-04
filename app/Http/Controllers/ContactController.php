<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $contacts = Contact::with('company')
            ->latest()
            ->paginate(20);

        return view('contacts.index', compact('contacts'));
    }

    public function create(): View
    {
        $companies = Company::orderBy('name')->get();

        return view('contacts.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'job_title' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'is_primary' => ['nullable', 'boolean'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['is_primary'] = $request->boolean('is_primary');

        Contact::create($validated);

        return redirect()
            ->route('contacts.index')
            ->with('success', 'Contact created successfully.');
    }

    public function show(Contact $contact): View
    {
        $contact->load('company');

        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact): View
    {
        $companies = Company::orderBy('name')->get();

        return view('contacts.edit', compact('contact', 'companies'));
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'job_title' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'is_primary' => ['nullable', 'boolean'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['is_primary'] = $request->boolean('is_primary');

        $contact->update($validated);

        return redirect()
            ->route('contacts.index')
            ->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()
            ->route('contacts.index')
            ->with('success', 'Contact deleted successfully.');
    }
}