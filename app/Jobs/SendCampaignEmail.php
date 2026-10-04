<?php

namespace App\Jobs;

use App\Models\CampaignRecipient;
use App\Models\Message;
use App\Models\MessageEvent;
use App\Models\CampaignStep;
use App\Models\Suppression;
use App\Services\TemplateRendererService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendCampaignEmail implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $recipientId)
    {
    }

    public function handle(TemplateRendererService $renderer): void
    {
        $recipient = CampaignRecipient::with(['campaign.steps', 'company.intelligence', 'contact', 'lead'])
            ->find($this->recipientId);

        if (!$recipient || in_array($recipient->status, ['unsubscribed', 'replied', 'sent'], true)) {
            return;
        }

        if (Suppression::where('email', strtolower($recipient->email))->exists()) {
            $recipient->update(['status' => 'unsubscribed', 'error' => 'Email is suppressed.']);
            return;
        }

        $campaign = $recipient->campaign;

        $step = $campaign->steps->firstWhere('step_number', $recipient->current_step);

        if (!$step) {
            $recipient->update(['status' => 'completed', 'next_step_at' => null]);
            return;
        }

        $subject = $renderer->render($step->subject, $recipient);
        $body = $renderer->render($step->body, $recipient);

        $message = Message::create([
            'campaign_recipient_id' => $recipient->id,
            'direction' => 'outbound',
            'channel' => 'email',
            'to_address' => $recipient->email,
            'from_address' => config('mail.from.address'),
            'reply_to' => $campaign->reply_to,
            'subject' => $subject,
            'body' => $body,
            'status' => 'queued',
        ]);

        try {
            Mail::raw($body, function ($mail) use ($recipient, $subject, $campaign) {
                $mail->to($recipient->email)
                    ->subject($subject);

                if ($campaign->reply_to) {
                    $mail->replyTo($campaign->reply_to);
                }
            });

            $message->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            MessageEvent::create([
                'message_id' => $message->id,
                'event_type' => 'sent',
                'occurred_at' => now(),
            ]);

            $nextStep = $campaign->steps
                ->where('step_number', '>', $recipient->current_step)
                ->sortBy('step_number')
                ->first();

            if ($nextStep) {
                $recipient->update([
                    'status' => 'waiting',
                    'sent_at' => now(),
                    'current_step' => $nextStep->step_number,
                    'next_step_at' => now()->addDays(max(0, $nextStep->day_offset - $step->day_offset)),
                    'error' => null,
                ]);
            } else {
                $recipient->update([
                    'status' => 'completed',
                    'sent_at' => now(),
                    'next_step_at' => null,
                    'error' => null,
                ]);
            }
        } catch (Throwable $e) {
            $message->update([
                'status' => 'failed',
                'metadata' => ['error' => $e->getMessage()],
            ]);

            $recipient->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
