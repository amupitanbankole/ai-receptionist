<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        $activities = Activity::with(['company', 'lead'])->latest('occurred_at')->paginate(20);

        return view('activities.index', compact('activities'));
    }

    public function create(): View
    {
        return view('activities.create', [
            'companies' => Company::orderBy('name')->get(),
            'contacts' => Contact::orderBy('first_name')->get(),
            'leads' => Lead::orderBy('title')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'contact_id' => ['nullable', 'exists:contacts,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'type' => ['required', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'occurred_at' => ['nullable', 'date'],
        ]);

        $validated['occurred_at'] = $validated['occurred_at'] ?? now();
        Activity::create($validated);

        return redirect()->route('activities.index')->with('success', 'Activity recorded.');
    }
}
