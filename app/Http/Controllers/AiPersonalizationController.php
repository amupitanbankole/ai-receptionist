<?php

namespace App\Http\Controllers;

use App\Models\AiPersonalization;
use App\Models\Lead;
use App\Services\AiPersonalizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AiPersonalizationController extends Controller
{
    public function show(Lead $lead): View
    {
        $lead->load(['company.intelligence', 'contact']);
        $personalizations = $lead->aiPersonalizations()->latest()->paginate(10);

        return view('ai_personalization.show', compact('lead', 'personalizations'));
    }

    public function generate(Lead $lead, AiPersonalizationService $service): RedirectResponse
    {
        if (!config('services.openai.key')) {
            return back()->with('error', 'OpenAI is not configured. Add OPENAI_API_KEY to your .env file, then clear the Laravel config cache.');
        }

        try {
            $service->generateOutreach($lead);

            return back()->with('success', 'AI outreach draft generated successfully.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'AI generation failed. Check storage/logs/laravel.log for the provider response.');
        }
    }

    public function useAsCampaignDraft(AiPersonalization $personalization): RedirectResponse
    {
        if (!$personalization->content || !$personalization->lead) {
            return back()->with('error', 'This personalization cannot be used as a campaign draft.');
        }

        session([
            'ai_campaign_draft' => [
                'subject' => $personalization->subject,
                'body' => $personalization->content,
                'lead_id' => $personalization->lead_id,
            ],
        ]);

        return redirect()->route('campaigns.create');
    }
}
