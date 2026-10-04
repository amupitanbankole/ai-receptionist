<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\AiReplySuggestion;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Message;
use App\Models\SalesConversation;
use App\Models\Suppression;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SalesConversationService
{
    public function ingestInbound(array $data): SalesConversation
    {
        $email = strtolower(trim($data['from_address'] ?? ''));
        [$lead, $contact, $company] = $this->resolveContext($data, $email);

        if (!$company && $lead?->company) {
            $company = $lead->company;
        }

        $conversation = SalesConversation::query()
            ->where('channel', $data['channel'] ?? 'email')
            ->when($lead, fn ($q) => $q->where('lead_id', $lead->id))
            ->when(!$lead && $email, fn ($q) => $q->whereHas('messages', fn ($mq) => $mq->whereRaw('LOWER(from_address) = ?', [$email])))
            ->whereIn('status', ['open', 'waiting'])
            ->latest('id')
            ->first();

        if (!$conversation) {
            $conversation = SalesConversation::create([
                'lead_id' => $lead?->id,
                'company_id' => $company?->id,
                'contact_id' => $contact?->id,
                'channel' => $data['channel'] ?? 'email',
                'subject' => $data['subject'] ?? null,
                'status' => 'open',
                'priority' => 'normal',
            ]);
        } else {
            $conversation->update([
                'lead_id' => $lead?->id ?? $conversation->lead_id,
                'company_id' => $company?->id ?? $conversation->company_id,
                'contact_id' => $contact?->id ?? $conversation->contact_id,
                'subject' => $data['subject'] ?? $conversation->subject,
                'status' => 'open',
            ]);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'lead_id' => $lead?->id,
            'company_id' => $company?->id,
            'contact_id' => $contact?->id,
            'campaign_recipient_id' => $data['campaign_recipient_id'] ?? null,
            'direction' => 'inbound',
            'channel' => $data['channel'] ?? 'email',
            'to_address' => $data['to_address'] ?? config('mail.from.address'),
            'from_address' => $email ?: null,
            'reply_to' => $email ?: null,
            'subject' => $data['subject'] ?? null,
            'body' => $data['body'] ?? null,
            'status' => 'received',
            'provider_message_id' => $data['provider_message_id'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);

        $conversation->update(['last_message_at' => now(), 'last_inbound_at' => now()]);

        $recipient = !empty($data['campaign_recipient_id'])
            ? \App\Models\CampaignRecipient::find($data['campaign_recipient_id'])
            : ($email ? $this->findRecipient($email) : null);

        $recipient?->update(['status' => 'replied', 'replied_at' => now()]);

        Activity::create([
            'company_id' => $company?->id,
            'contact_id' => $contact?->id,
            'lead_id' => $lead?->id,
            'type' => 'email_reply',
            'subject' => 'Inbound reply'.(!empty($data['subject']) ? ': '.$data['subject'] : ''),
            'description' => mb_substr(trim($data['body'] ?? ''), 0, 5000),
            'occurred_at' => now(),
        ]);

        if ($lead && $lead->status === 'new') {
            $lead->update(['status' => 'qualified']);
        }

        $this->generateReplySuggestion($conversation->fresh(), $message);

        return $conversation->fresh(['lead.company', 'contact', 'messages', 'aiReplySuggestions']);
    }

    public function generateReplySuggestion(SalesConversation $conversation, ?Message $inbound = null): AiReplySuggestion
    {
        $conversation->loadMissing(['lead.company.intelligence', 'company.intelligence', 'contact', 'messages']);

        $inbound ??= $conversation->messages()
            ->where('direction', 'inbound')
            ->latest('id')
            ->first();

        if (!$inbound) {
            throw new RuntimeException('No inbound message is available for AI analysis.');
        }

        if (!config('services.openai.key')) {
            throw new RuntimeException('OpenAI is not configured.');
        }

        $suggestion = AiReplySuggestion::create([
            'conversation_id' => $conversation->id,
            'inbound_message_id' => $inbound->id,
            'type' => 'reply',
            'provider' => 'openai',
            'model' => config('services.openai.model'),
            'status' => 'processing',
        ]);

        try {
            $response = Http::withToken(config('services.openai.key'))
                ->acceptJson()
                ->timeout(90)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('services.openai.model'),
                    'instructions' => 'You are an AI sales conversation assistant for a UK contractor-focused AI receptionist SaaS. Classify the inbound reply and draft a helpful human-sounding response. Never invent pricing, availability, customer results, guarantees, or business facts. If the sender asks to unsubscribe, classify as unsubscribe and do not draft a promotional reply. Return ONLY valid JSON with keys: intent, priority, next_action, summary, subject, body, rationale. intent must be one of interested, question, objection, meeting_request, not_interested, unsubscribe, out_of_office, unclear. priority must be low, normal, high, urgent.',
                    'input' => $this->buildPrompt($conversation, $inbound),
                ]);

            if ($response->failed()) {
                throw new RuntimeException('OpenAI request failed: '.$response->status().' '.$response->body());
            }

            $raw = trim((string) $response->json('output_text'));
            if ($raw === '') {
                $raw = $this->extractOutputText($response->json('output', []));
            }

            $result = json_decode(trim($raw), true);

            if (!is_array($result)) {
                throw new RuntimeException('AI returned invalid JSON for the reply analysis.');
            }

            $intent = $this->allowedIntent($result['intent'] ?? 'unclear');
            $priority = $this->allowedPriority($result['priority'] ?? 'normal');

            $suggestion->update([
                'intent' => $intent,
                'priority' => $priority,
                'subject' => $result['subject'] ?? $conversation->subject,
                'body' => $result['body'] ?? null,
                'rationale' => $result['rationale'] ?? null,
                'status' => 'draft',
                'metadata' => ['response_id' => $response->json('id')],
                'generated_at' => now(),
            ]);

            $conversation->update([
                'intent' => $intent,
                'priority' => $priority,
                'next_action' => $result['next_action'] ?? 'Review the AI suggestion.',
                'summary' => $result['summary'] ?? null,
                'status' => in_array($intent, ['not_interested', 'unsubscribe'], true) ? 'closed' : 'open',
            ]);

            if ($intent === 'unsubscribe' && $inbound->from_address) {
                Suppression::updateOrCreate(
                    ['email' => strtolower($inbound->from_address)],
                    ['reason' => 'reply_unsubscribe', 'suppressed_at' => now()]
                );
            }

            return $suggestion->fresh();
        } catch (\Throwable $e) {
            $suggestion->update([
                'status' => 'failed',
                'metadata' => ['error' => $e->getMessage()],
            ]);
            throw $e;
        }
    }

    public function markStatus(SalesConversation $conversation, string $status): void
    {
        if (!in_array($status, ['open', 'waiting', 'closed'], true)) {
            throw new RuntimeException('Invalid conversation status.');
        }

        $conversation->update(['status' => $status]);
    }

    private function resolveContext(array $data, string $email): array
    {
        $lead = !empty($data['lead_id']) ? Lead::with('company')->find($data['lead_id']) : null;
        $contact = !empty($data['contact_id']) ? Contact::with('company')->find($data['contact_id']) : null;
        $company = !empty($data['company_id']) ? Company::find($data['company_id']) : null;

        if (!$lead && $email) {
            $lead = Lead::with('company')
                ->whereHas('contact', fn ($q) => $q->whereRaw('LOWER(email) = ?', [$email]))
                ->latest('id')->first();
        }

        if (!$contact && $email) {
            $contact = Contact::with('company')
                ->whereRaw('LOWER(email) = ?', [$email])
                ->latest('id')->first();
        }

        if (!$company && $contact?->company) {
            $company = $contact->company;
        }

        if (!$company && $email) {
            $company = Company::whereRaw('LOWER(email) = ?', [$email])->latest('id')->first();
        }

        if (!$lead && $company) {
            $lead = $company->leads()->latest('id')->first();
        }

        return [$lead, $contact, $company];
    }

    private function findRecipient(string $email)
    {
        return \App\Models\CampaignRecipient::whereRaw('LOWER(email) = ?', [$email])
            ->whereIn('status', ['sent', 'waiting'])
            ->latest('id')
            ->first();
    }

    private function buildPrompt(SalesConversation $conversation, Message $inbound): string
    {
        $company = $conversation->lead?->company ?? $conversation->company;

        $history = $conversation->messages()
            ->orderBy('id')
            ->take(12)
            ->get()
            ->map(fn ($m) => strtoupper($m->direction).': '.trim($m->body ?? ''))
            ->implode("\n\n");

        return implode("\n", [
            'Company: '.($company?->name ?: 'Unknown'),
            'Industry: '.($company?->industry ?: 'Unknown'),
            'Business type: '.($company?->business_type ?: 'Unknown'),
            'City: '.($company?->city ?: 'Unknown'),
            'Contact: '.trim(($conversation->contact?->first_name ?? '').' '.($conversation->contact?->last_name ?? '')),
            'Lead title: '.($conversation->lead?->title ?: 'Unknown'),
            'Lead score: '.($conversation->lead?->score ?? 0),
            'Research summary: '.($company?->intelligence?->research_summary ?: 'None'),
            '',
            'Conversation history:',
            $history ?: 'None',
            '',
            'Latest inbound message:',
            $inbound->body ?: '',
            '',
            'Draft a concise reply that directly answers the sender. If they show buying intent, move toward a short demo or discovery call without inventing a time. If you cannot answer from the supplied facts, say you can confirm it rather than guessing.',
        ]);
    }

    private function allowedIntent(string $intent): string
    {
        $allowed = ['interested','question','objection','meeting_request','not_interested','unsubscribe','out_of_office','unclear'];
        return in_array($intent, $allowed, true) ? $intent : 'unclear';
    }

    private function allowedPriority(string $priority): string
    {
        $allowed = ['low','normal','high','urgent'];
        return in_array($priority, $allowed, true) ? $priority : 'normal';
    }

    private function extractOutputText(array $output): string
    {
        $text = '';
        foreach ($output as $item) {
            foreach (($item['content'] ?? []) as $content) {
                if (($content['type'] ?? '') === 'output_text') {
                    $text .= ($content['text'] ?? '')."\n";
                }
            }
        }
        return trim($text);
    }
}
