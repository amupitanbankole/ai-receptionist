<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Message;
use App\Models\ReceptionistKnowledge;
use App\Models\SalesConversation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class ReceptionistService
{
    public function __construct(private AppointmentService $appointments) {}

    public function ensureConfig(Company $company)
    {
        $config = $company->receptionistConfig()->firstOrCreate(
            ['company_id' => $company->id],
            [
                'enabled' => true,
                'display_name' => $company->name.' AI Receptionist',
                'greeting' => 'Thanks for contacting '.$company->name.'. How can I help you today?',
                'tone' => 'professional',
                'booking_enabled' => true,
                'after_hours_message' => 'We are currently outside business hours, but I can take your details and arrange a follow-up.',
                'timezone' => 'Europe/London',
                'metadata' => [],
            ]
        );

        $metadata = is_array($config->metadata) ? $config->metadata : [];
        if (empty($metadata['widget_key'])) {
            $metadata['widget_key'] = Str::random(40);
            $config->metadata = $metadata;
            $config->save();
        }

        $this->appointments->ensureDefaults($company);
        return $config->fresh();
    }

    public function regenerateWidgetKey(Company $company)
    {
        $config = $this->ensureConfig($company);
        $metadata = is_array($config->metadata) ? $config->metadata : [];
        $metadata['widget_key'] = Str::random(40);
        $config->metadata = $metadata;
        $config->save();

        return $config->fresh();
    }

    public function widgetKey($config): string
    {
        return (string) (($config->metadata ?? [])['widget_key'] ?? '');
    }

    public function allowedWidgetOrigins($config): array
    {
        $origins = (($config->metadata ?? [])['allowed_origins'] ?? []);
        if (!is_array($origins)) return [];

        return array_values(array_filter(array_map(function ($origin) {
            $origin = trim((string) $origin);
            if ($origin === '') return null;
            return rtrim($origin, '/');
        }, $origins)));
    }

    public function isWidgetOriginAllowed($config, ?string $origin): bool
    {
        $origin = $origin ? rtrim(trim($origin), '/') : '';
        $allowed = $this->allowedWidgetOrigins($config);

        if ($origin !== '' && in_array($origin, $allowed, true)) {
            return true;
        }

        return $origin === '' && in_array(config('app.env'), ['local', 'testing'], true);
    }

    public function respond(Company $company, array $input): array
    {
        $config = $this->ensureConfig($company);
        if (!$config->enabled) {
            return ['conversation' => null, 'message' => 'The AI receptionist is currently unavailable.', 'appointment' => null];
        }

        $email = strtolower(trim($input['customer_email'] ?? ''));
        $contact = $email ? Contact::where('company_id', $company->id)->whereRaw('LOWER(email) = ?', [$email])->first() : null;
        $lead = $contact ? $company->leads()->where('contact_id', $contact->id)->latest()->first() : null;

        if (!$contact && ($email || !empty($input['customer_name']))) {
            [$first, $last] = $this->splitName($input['customer_name'] ?? 'Website Visitor');
            $contact = Contact::create([
                'company_id' => $company->id,
                'first_name' => $first ?: 'Website',
                'last_name' => $last,
                'email' => $email ?: null,
                'phone' => $input['customer_phone'] ?? null,
                'status' => 'active',
            ]);
        }

        if (!$lead && $contact) {
            $lead = Lead::firstOrCreate(
                ['company_id' => $company->id, 'contact_id' => $contact->id, 'title' => 'AI Receptionist Enquiry'],
                ['status' => 'new', 'score' => 0, 'temperature' => 'low', 'source' => 'AI Receptionist']
            );
        }

        $conversation = SalesConversation::where('company_id', $company->id)
            ->where('channel', $input['channel'] ?? 'webchat')
            ->whereIn('status', ['open','waiting'])
            ->when($contact, fn ($q) => $q->where('contact_id', $contact->id))
            ->latest('id')->first();

        if (!$conversation) {
            $conversation = SalesConversation::create([
                'lead_id' => $lead?->id,
                'company_id' => $company->id,
                'contact_id' => $contact?->id,
                'channel' => $input['channel'] ?? 'webchat',
                'subject' => 'AI Receptionist Conversation',
                'status' => 'open',
                'priority' => 'normal',
            ]);
        }

        $body = trim($input['message'] ?? '');
        Message::create([
            'conversation_id' => $conversation->id,
            'lead_id' => $lead?->id,
            'company_id' => $company->id,
            'contact_id' => $contact?->id,
            'direction' => 'inbound',
            'channel' => $input['channel'] ?? 'webchat',
            'to_address' => $config->notification_email ?: ($company->email ?: 'receptionist@local'),
            'from_address' => $email ?: ($input['customer_phone'] ?? 'visitor@webchat.local'),
            'subject' => 'Customer enquiry',
            'body' => $body,
            'status' => 'received',
        ]);

        $result = $this->generate($company, $conversation, $input);
        $reply = $result['reply'];

        Message::create([
            'conversation_id' => $conversation->id,
            'lead_id' => $lead?->id,
            'company_id' => $company->id,
            'contact_id' => $contact?->id,
            'direction' => 'outbound',
            'channel' => $input['channel'] ?? 'webchat',
            'to_address' => $email ?: ($input['customer_phone'] ?? 'visitor@webchat.local'),
            'from_address' => $config->notification_email ?: ($company->email ?: 'receptionist@local'),
            'subject' => 'AI Receptionist',
            'body' => $reply,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'last_inbound_at' => now(),
            'intent' => $result['intent'],
            'priority' => $result['priority'],
            'next_action' => $result['next_action'],
            'summary' => $result['summary'],
        ]);

        Activity::create([
            'company_id' => $company->id,
            'contact_id' => $contact?->id,
            'lead_id' => $lead?->id,
            'type' => 'receptionist_interaction',
            'subject' => 'AI receptionist interaction',
            'description' => mb_substr($body, 0, 1000),
            'occurred_at' => now(),
        ]);

        if ($lead && in_array($result['intent'], ['booking_request','interested','quote_request'], true)) {
            $lead->update(['status' => 'qualified', 'temperature' => 'high']);
        }

        return ['conversation' => $conversation->fresh(), 'message' => $reply, 'appointment' => $result['appointment'] ?? null];
    }

    private function generate(Company $company, SalesConversation $conversation, array $input): array
    {
        $config = $company->receptionistConfig;
        $knowledge = ReceptionistKnowledge::where('company_id', $company->id)
            ->where('active', true)->orderByDesc('priority')->limit(30)->get();

        $prompt = $this->prompt($company, $config, $knowledge, $conversation, $input);
        $result = null;

        if (config('services.openai.key')) {
            try {
                $response = Http::withToken(config('services.openai.key'))->acceptJson()->timeout(90)
                    ->post('https://api.openai.com/v1/responses', [
                        'model' => config('services.openai.model'),
                        'instructions' => 'You are a careful AI receptionist for a UK local-service business. Answer only from supplied facts. Be concise, friendly and useful. Never invent prices, availability, guarantees or policies. Return only valid JSON with keys: intent, priority, reply, next_action, summary. intent must be one of enquiry, quote_request, booking_request, interested, complaint, escalation, unclear. priority must be low, normal, high, urgent.',
                        'input' => $prompt,
                    ]);

                if ($response->successful()) {
                    $raw = trim((string) $response->json('output_text'));
                    if (!$raw) {
                        foreach ($response->json('output', []) as $item) {
                            foreach (($item['content'] ?? []) as $c) {
                                if (($c['type'] ?? '') === 'output_text') $raw .= ($c['text'] ?? '');
                            }
                        }
                    }
                    $decoded = json_decode(trim($raw), true);
                    if (is_array($decoded) && !empty($decoded['reply'])) $result = $decoded;
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $result ??= $this->fallback($company, $config, $input);

        $appointment = null;
        if (($result['intent'] ?? '') === 'booking_request' && $config->booking_enabled) {
            $requested = $input['requested_start'] ?? null;
            if ($requested) {
                try {
                    $appointment = $this->appointments->create($company, [
                        'starts_at' => $requested,
                        'customer_name' => $input['customer_name'] ?? 'Website Visitor',
                        'customer_email' => $input['customer_email'] ?? null,
                        'customer_phone' => $input['customer_phone'] ?? null,
                        'lead_id' => $conversation->lead_id,
                        'contact_id' => $conversation->contact_id,
                        'source' => 'ai_receptionist',
                    ]);
                    $result['reply'] = 'You’re booked for '.Carbon::parse($appointment->starts_at)->format('l j F, H:i').'. We’ll use the contact details you provided for the confirmation.';
                    $result['next_action'] = 'Appointment booked and CRM updated.';
                } catch (\Throwable $e) {
                    report($e);
                    $slots = $this->appointments->slots($company, now($config->timezone), 7);
                    $result['reply'] = 'I can help with that. That exact time is not available. The next available options are: '.$this->formatSlots($slots).'.';
                    $result['next_action'] = 'Customer should choose an available slot.';
                }
            } else {
                $slots = $this->appointments->slots($company, now($config->timezone), 7);
                $result['reply'] = 'Absolutely. Here are the next available options: '.$this->formatSlots($slots).'. Which one works best for you?';
                $result['next_action'] = 'Waiting for customer to select an appointment slot.';
            }
        }

        return [
            'reply' => $result['reply'] ?? $config->greeting,
            'intent' => $result['intent'] ?? 'enquiry',
            'priority' => $result['priority'] ?? 'normal',
            'next_action' => $result['next_action'] ?? 'Continue conversation.',
            'summary' => $result['summary'] ?? null,
            'appointment' => $appointment,
        ];
    }

    private function prompt(Company $company, $config, $knowledge, SalesConversation $conversation, array $input): string
    {
        $history = $conversation->messages()->orderBy('id')->latest('id')->take(10)->get()->reverse()
            ->map(fn ($m) => strtoupper($m->direction).': '.$m->body)->implode("\n");
        $facts = $knowledge->map(fn ($k) => '['.$k->type.'] '.$k->title.': '.$k->content)->implode("\n");
        return implode("\n", [
            'Business: '.$company->name,
            'Industry: '.($company->industry ?: 'Unknown'),
            'Description: '.($company->description ?: 'Unknown'),
            'Phone: '.($company->phone ?: 'Unknown'),
            'Website: '.($company->website ?: 'Unknown'),
            'City: '.($company->city ?: 'Unknown'),
            'Services: '.json_encode($config->services ?: []),
            'Service areas: '.json_encode($config->service_areas ?: []),
            'Hours: '.json_encode($config->business_hours ?: []),
            'Instructions: '.($config->instructions ?: ''),
            'Knowledge:'.($facts ?: 'None'),
            'Conversation:'.($history ?: 'None'),
            'Customer name: '.($input['customer_name'] ?? ''),
            'Customer message: '.($input['message'] ?? ''),
        ]);
    }

    private function fallback(Company $company, $config, array $input): array
    {
        $text = strtolower($input['message'] ?? '');
        $intent = str_contains($text, 'book') || str_contains($text, 'appointment') || str_contains($text, 'schedule')
            ? 'booking_request'
            : (str_contains($text, 'quote') || str_contains($text, 'price') || str_contains($text, 'cost') ? 'quote_request' : 'enquiry');
        $reply = $config->greeting ?: 'Thanks for contacting '.$company->name.'. How can I help?';
        if ($intent === 'quote_request') $reply = 'Thanks for your enquiry. I can collect a few details and arrange for the team to confirm the right quote. What service do you need and which area is it for?';
        if ($intent === 'booking_request') $reply = 'Absolutely — I can help arrange an appointment.';
        if ($intent === 'enquiry') $reply = 'Thanks for contacting '.$company->name.'. Tell me what service you need and the area you’re in, and I’ll help with the next step.';
        return ['intent'=>$intent,'priority'=>'normal','reply'=>$reply,'next_action'=>'Continue conversation.','summary'=>'Customer contacted the AI receptionist.'];
    }

    private function formatSlots(array $slots): string
    {
        if (!$slots) return 'no slots are currently available';
        return collect($slots)->take(5)->map(fn ($s) => Carbon::parse($s['start'])->format('D j M H:i'))->implode(', ');
    }

    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);
        return [$parts[0] ?? '', $parts[1] ?? ''];
    }
}