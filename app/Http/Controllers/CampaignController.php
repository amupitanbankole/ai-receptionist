<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignStep;
use App\Models\EmailTemplate;
use App\Models\Lead;
use App\Services\CampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(): View
    {
        $campaigns = Campaign::withCount([
            'recipients',
            'recipients as sent_count' => fn ($q) => $q->whereIn('status', ['sent','waiting','completed']),
            'recipients as replied_count' => fn ($q) => $q->where('status', 'replied'),
        ])->latest()->paginate(20);

        return view('campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        $leads = Lead::with(['company','contact'])
            ->whereHas('company')
            ->orderBy('title')
            ->get();

        $templates = EmailTemplate::where('is_active', true)->orderBy('name')->get();
        $aiDraft = session('ai_campaign_draft');

        return view('campaigns.create', compact('leads', 'templates', 'aiDraft'));
    }

    public function store(Request $request, CampaignService $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'template_id' => ['nullable','exists:email_templates,id'],
            'subject' => ['nullable','string','max:255'],
            'body' => ['nullable','string'],
            'daily_limit' => ['required','integer','min:1','max:1000'],
            'scheduled_at' => ['nullable','date'],
            'reply_to' => ['nullable','email','max:255'],
            'unsubscribe_text' => ['nullable','string','max:1000'],
            'lead_ids' => ['required','array','min:1'],
            'lead_ids.*' => ['integer','exists:leads,id'],
        ]);

        if (!$validated['template_id'] && (empty($validated['subject']) || empty($validated['body']))) {
            return back()->withInput()->withErrors(['subject' => 'Provide both a subject and email body, or select an email template.']);
        }

        if ($validated['template_id']) {
            $template = EmailTemplate::findOrFail($validated['template_id']);
            $validated['subject'] = $validated['subject'] ?: $template->subject;
            $validated['body'] = $validated['body'] ?: $template->body;
        }

        $campaign = Campaign::create([
            'name' => $validated['name'],
            'status' => 'draft',
            'template_id' => $validated['template_id'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'body' => $validated['body'] ?? null,
            'daily_limit' => $validated['daily_limit'],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'reply_to' => $validated['reply_to'] ?? null,
            'unsubscribe_text' => $validated['unsubscribe_text'] ?? 'If you no longer want to receive these emails, reply with unsubscribe.',
        ]);

        $service->ensureDefaultSteps($campaign);
        $service->syncRecipients($campaign, Lead::whereIn('id', $validated['lead_ids'])->get());
        session()->forget('ai_campaign_draft');

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign created with eligible recipients.');
    }

    public function show(Campaign $campaign): View
    {
        $campaign->load(['template','steps' => fn ($q) => $q->orderBy('step_number')]);
        $recipients = $campaign->recipients()->with(['company','contact'])->latest()->paginate(25);

        $stats = [
            'total' => $campaign->recipients()->count(),
            'queued' => $campaign->recipients()->whereIn('status', ['queued','waiting'])->count(),
            'sent' => $campaign->recipients()->whereIn('status', ['sent','waiting','completed'])->count(),
            'replied' => $campaign->recipients()->where('status','replied')->count(),
            'failed' => $campaign->recipients()->where('status','failed')->count(),
        ];

        return view('campaigns.show', compact('campaign','recipients','stats'));
    }

    public function launch(Campaign $campaign, CampaignService $service): RedirectResponse
    {
        if ($campaign->status === 'completed') {
            return back()->with('error', 'This campaign is already completed.');
        }

        $campaign->update([
            'status' => 'active',
            'started_at' => $campaign->started_at ?: now(),
        ]);

        $service->dispatchDue($campaign);

        return back()->with('success', 'Campaign launched. Due emails have been queued.');
    }

    public function pause(Campaign $campaign): RedirectResponse
    {
        $campaign->update(['status' => 'paused']);
        return back()->with('success', 'Campaign paused.');
    }

    public function addStep(Request $request, Campaign $campaign): RedirectResponse
    {
        $validated = $request->validate([
            'day_offset' => ['required','integer','min:0','max:365'],
            'subject' => ['required','string','max:255'],
            'body' => ['required','string'],
        ]);

        $next = ((int) $campaign->steps()->max('step_number')) + 1;

        CampaignStep::create([
            'campaign_id' => $campaign->id,
            'step_number' => $next,
            ...$validated,
        ]);

        return back()->with('success', 'Campaign follow-up step added.');
    }

    public function removeStep(Campaign $campaign, CampaignStep $step): RedirectResponse
    {
        abort_unless($step->campaign_id === $campaign->id, 404);

        if ($step->step_number === 1) {
            return back()->with('error', 'The initial campaign step cannot be removed.');
        }

        $step->delete();
        return back()->with('success', 'Follow-up step removed.');
    }
}
