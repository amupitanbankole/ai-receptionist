<?php

namespace App\Services;

use App\Models\AiPersonalization;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AiPersonalizationService
{
    public function generateOutreach(Lead $lead): AiPersonalization
    {
        $lead->loadMissing(['company.intelligence', 'contact', 'leadSource']);

        $company = $lead->company;
        $contact = $lead->contact;
        $intelligence = $company?->intelligence;

        if (!$company) {
            throw new RuntimeException('This lead must have a company before AI personalization can be generated.');
        }

        $prompt = $this->buildPrompt($lead);

        $record = AiPersonalization::create([
            'lead_id' => $lead->id,
            'company_id' => $company->id,
            'type' => 'outreach',
            'provider' => 'openai',
            'model' => config('services.openai.model'),
            'prompt' => $prompt,
            'status' => 'processing',
        ]);

        try {
            $response = Http::withToken(config('services.openai.key'))
                ->acceptJson()
                ->timeout(90)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('services.openai.model'),
                    'instructions' => 'You are a B2B sales copywriter for a UK AI receptionist SaaS. Write concise, credible, non-spammy outreach. Never invent facts. Use only the supplied company information. Return exactly two sections: SUBJECT: on one line, then BODY: followed by the email body.',
                    'input' => $prompt,
                ]);

            if ($response->failed()) {
                throw new RuntimeException('OpenAI request failed: '.$response->status().' '.$response->body());
            }

            $output = trim((string) $response->json('output_text'));

            if ($output === '') {
                $output = $this->extractOutputText($response->json('output', []));
            }

            if ($output === '') {
                throw new RuntimeException('OpenAI returned an empty response.');
            }

            [$subject, $body] = $this->parseOutput($output);

            $record->update([
                'model' => config('services.openai.model'),
                'subject' => $subject,
                'content' => $body,
                'status' => 'generated',
                'metadata' => [
                    'response_id' => $response->json('id'),
                ],
                'generated_at' => now(),
            ]);

            return $record->fresh();
        } catch (\Throwable $e) {
            $record->update([
                'status' => 'failed',
                'metadata' => ['error' => $e->getMessage()],
            ]);

            throw $e;
        }
    }

    private function buildPrompt(Lead $lead): string
    {
        $company = $lead->company;
        $contact = $lead->contact;
        $intelligence = $company?->intelligence;

        return implode("\n", [
            'Prospect:',
            'Company: '.($company->name ?: 'Unknown'),
            'Industry: '.($company->industry ?: 'Unknown'),
            'Business type: '.($company->business_type ?: 'Unknown'),
            'City: '.($company->city ?: 'Unknown'),
            'County: '.($company->county ?: 'Unknown'),
            'Website: '.($company->website ?: 'Unknown'),
            'Phone: '.($company->phone ?: 'Unknown'),
            'Contact: '.trim(($contact?->first_name ?? '').' '.($contact?->last_name ?? '')) ?: 'Unknown',
            'Lead title: '.($lead->title ?: 'Unknown'),
            'Lead score: '.($lead->score ?? 0),
            'Lead temperature: '.($lead->temperature ?: 'Unknown'),
            'Lead notes: '.($lead->notes ?: 'None'),
            'Website research summary: '.($intelligence?->research_summary ?: 'None'),
            'Website research pain points: '.($intelligence?->pain_points ?: 'None'),
            'Website research opportunities: '.($intelligence?->opportunities ?: 'None'),
            '',
            'Write an email that references one or two genuine observations above, explains the business outcome of an AI receptionist (answering missed calls, qualifying enquiries and booking appointments), and ends with one low-friction call to action.',
            'Do not claim the prospect is missing calls unless the supplied research explicitly says so.',
            'Do not use fake statistics, fake case studies, or excessive hype.',
        ]);
    }

    private function parseOutput(string $output): array
    {
        $subject = 'Quick question';
        $body = $output;

        if (preg_match('/SUBJECT:\s*(.+?)(?:\r?\n|$)/i', $output, $matches)) {
            $subject = trim($matches[1]);
        }

        if (preg_match('/BODY:\s*(.*)$/is', $output, $matches)) {
            $body = trim($matches[1]);
        } else {
            $body = preg_replace('/^SUBJECT:\s*.+?\r?\n/i', '', $output) ?: $output;
        }

        return [$subject, trim($body)];
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
