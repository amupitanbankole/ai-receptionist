<?php

namespace App\Services;

use App\Models\CampaignRecipient;

class TemplateRendererService
{
    public function render(string $template, CampaignRecipient $recipient): string
    {
        $recipient->loadMissing(['company', 'contact', 'lead', 'campaign']);

        $company = $recipient->company;
        $contact = $recipient->contact;

        $research = $company?->intelligence?->research_summary ?? '';

        $values = [
            'first_name' => $recipient->first_name ?: ($contact?->first_name ?? 'there'),
            'last_name' => $recipient->last_name ?: ($contact?->last_name ?? ''),
            'full_name' => trim(($recipient->first_name ?? '') . ' ' . ($recipient->last_name ?? '')),
            'company_name' => $company?->name ?? '',
            'industry' => $company?->industry ?? '',
            'website' => $company?->website ?? '',
            'phone' => $company?->phone ?? '',
            'city' => $company?->city ?? '',
            'county' => $company?->county ?? '',
            'research_summary' => $research,
            'lead_title' => $recipient->lead?->title ?? '',
        ];

        foreach ($values as $key => $value) {
            $template = str_replace(
                ['{{'.$key.'}}', '{{ '.$key.' }}'],
                e((string) $value),
                $template
            );
        }

        return $template;
    }
}
