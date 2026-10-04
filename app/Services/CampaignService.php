<?php

namespace App\Services;

use App\Jobs\SendCampaignEmail;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\CampaignStep;
use App\Models\Lead;
use App\Models\Suppression;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CampaignService
{
    public function syncRecipients(Campaign $campaign, Collection $leads): int
    {
        $added = 0;

        foreach ($leads as $lead) {
            $lead->loadMissing(['company', 'contact']);

            $email = $lead->contact?->email ?: $lead->company?->email;
            if (!$email) {
                continue;
            }

            $email = strtolower(trim($email));

            if (Suppression::where('email', $email)->exists()) {
                continue;
            }

            $exists = CampaignRecipient::where('campaign_id', $campaign->id)
                ->where('email', $email)
                ->exists();

            if ($exists) {
                continue;
            }

            CampaignRecipient::create([
                'campaign_id' => $campaign->id,
                'lead_id' => $lead->id,
                'contact_id' => $lead->contact_id,
                'company_id' => $lead->company_id,
                'email' => $email,
                'first_name' => $lead->contact?->first_name,
                'last_name' => $lead->contact?->last_name,
                'status' => 'queued',
                'current_step' => 1,
                'next_step_at' => now(),
            ]);

            $added++;
        }

        return $added;
    }

    public function ensureDefaultSteps(Campaign $campaign): void
    {
        if ($campaign->steps()->exists()) {
            return;
        }

        CampaignStep::create([
            'campaign_id' => $campaign->id,
            'step_number' => 1,
            'day_offset' => 0,
            'subject' => $campaign->subject ?: 'Quick question for {{company_name}}',
            'body' => $campaign->body ?: '',
        ]);
    }

    public function dispatchDue(Campaign $campaign): int
    {
        if ($campaign->status !== 'active' || ($campaign->scheduled_at && $campaign->scheduled_at->isFuture())) {
            return 0;
        }
        $limit = max(1, (int) $campaign->daily_limit);
        $sentToday = $campaign->recipients()->whereDate('sent_at', today())->count();
        $remaining = max(0, $limit - $sentToday);

        if ($remaining === 0) {
            return 0;
        }

        $count = 0;

        CampaignRecipient::query()
            ->where('campaign_id', $campaign->id)
            ->whereIn('status', ['queued', 'waiting'])
            ->where(function ($query) {
                $query->whereNull('next_step_at')->orWhere('next_step_at', '<=', now());
            })
            ->orderBy('id')
            ->limit($remaining)
            ->get()
            ->each(function (CampaignRecipient $recipient) use (&$count) {
                $recipient->update(['status' => 'sending']);
                SendCampaignEmail::dispatch($recipient->id);
                $count++;
            });

        return $count;
    }
}
