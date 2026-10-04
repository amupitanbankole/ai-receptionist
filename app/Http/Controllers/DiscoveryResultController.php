<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\DiscoveryResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DiscoveryResultController extends Controller
{
    public function index(): View
    {
        $results = DiscoveryResult::latest()->paginate(20);
        return view('discovery.index', compact('results'));
    }

    public function create(): View
    {
        return view('discovery.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required','string','max:255'],
            'website' => ['nullable','url','max:255'],
            'phone' => ['nullable','string','max:50'],
            'email' => ['nullable','email','max:255'],
            'industry' => ['nullable','string','max:255'],
            'city' => ['nullable','string','max:100'],
            'county' => ['nullable','string','max:100'],
            'postcode' => ['nullable','string','max:20'],
            'country' => ['nullable','string','max:100'],
            'source' => ['required','string','max:100'],
            'source_url' => ['nullable','url','max:255'],
            'discovery_notes' => ['nullable','string'],
        ]);

        DiscoveryResult::create($validated);

        return redirect()->route('discovery.index')->with('success','Prospect added to discovery queue.');
    }

    public function promote(DiscoveryResult $result): RedirectResponse
    {
        if ($result->company_id) {
            return back()->with('success','This discovery result has already been converted.');
        }

        $baseSlug = Str::slug($result->company_name) ?: 'company';
        $slug = $baseSlug;
        $counter = 2;

        while (Company::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }

        $company = Company::create([
            'name' => $result->company_name,
            'slug' => $slug,
            'website' => $result->website,
            'phone' => $result->phone,
            'email' => $result->email,
            'industry' => $result->industry,
            'address_line_1' => null,
            'city' => $result->city,
            'county' => $result->county,
            'postcode' => $result->postcode,
            'country' => $result->country ?: 'United Kingdom',
            'source' => $result->source,
            'source_url' => $result->source_url,
            'description' => $result->discovery_notes,
            'status' => 'prospect',
        ]);

        $result->update(['company_id' => $company->id, 'status' => 'converted']);

        return redirect()->route('companies.show', $company)->with('success','Prospect converted into a company.');
    }

    public function discard(DiscoveryResult $result): RedirectResponse
    {
        $result->update(['status' => 'discarded']);
        return back()->with('success','Discovery result marked as discarded.');
    }
}
