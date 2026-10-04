<?php

namespace App\Http\Controllers;

use App\Models\Followup;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FollowupController extends Controller
{
    public function index(): View
    {
        $followups = Followup::with(['lead.company'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('due_at')
            ->paginate(20);

        return view('followups.index', compact('followups'));
    }

    public function create(): View
    {
        return view('followups.create', [
            'leads' => Lead::with('company')->orderBy('title')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lead_id' => ['required', 'exists:leads,id'],
            'type' => ['required', 'in:manual,email,call,whatsapp,task'],
            'subject' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'due_at' => ['required', 'date'],
        ]);

        Followup::create($validated);

        return redirect()->route('followups.index')->with('success', 'Follow-up scheduled.');
    }

    public function complete(Followup $followup): RedirectResponse
    {
        $followup->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Follow-up completed.');
    }
}
