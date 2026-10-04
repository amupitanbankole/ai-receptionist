<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailTemplateController extends Controller
{
    public function index(): View
    {
        $templates = EmailTemplate::latest()->paginate(20);
        return view('email_templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('email_templates.create', [
            'template' => new EmailTemplate(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'subject' => ['required','string','max:255'],
            'body' => ['required','string'],
            'is_active' => ['nullable','boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        EmailTemplate::create($validated);

        return redirect()->route('email-templates.index')->with('success', 'Email template created.');
    }

    public function edit(EmailTemplate $emailTemplate): View
    {
        return view('email_templates.edit', ['template' => $emailTemplate]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'subject' => ['required','string','max:255'],
            'body' => ['required','string'],
            'is_active' => ['nullable','boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $emailTemplate->update($validated);

        return redirect()->route('email-templates.index')->with('success', 'Email template updated.');
    }

    public function destroy(EmailTemplate $emailTemplate): RedirectResponse
    {
        if ($emailTemplate->campaigns()->exists()) {
            return back()->with('error', 'This template is used by a campaign and cannot be deleted.');
        }

        $emailTemplate->delete();
        return redirect()->route('email-templates.index')->with('success', 'Email template deleted.');
    }
}
