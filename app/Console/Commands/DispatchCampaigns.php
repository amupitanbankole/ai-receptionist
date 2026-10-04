<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Services\CampaignService;
use Illuminate\Console\Command;

class DispatchCampaigns extends Command
{
    protected $signature = 'campaigns:dispatch';
    protected $description = 'Dispatch due emails for active outreach campaigns.';

    public function handle(CampaignService $service): int
    {
        $total = 0;

        Campaign::query()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now());
            })
            ->each(function (Campaign $campaign) use ($service, &$total) {
                $sent = $service->dispatchDue($campaign);
                $total += $sent;

                if ($campaign->recipients()->whereIn('status', ['queued','waiting','sending'])->doesntExist()) {
                    $campaign->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);
                }
            });

        $this->info("Queued {$total} campaign email(s).");

        return self::SUCCESS;
    }
}
