<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyIntelligence;
use Illuminate\Support\Facades\Http;

class WebsiteResearchService
{
    public function research(Company $company): CompanyIntelligence
    {
        $intel = CompanyIntelligence::firstOrCreate(
            ['company_id' => $company->id],
            ['research_status' => 'pending']
        );

        if (!$company->website) {
            $intel->update([
                'research_status' => 'failed',
                'research_summary' => 'No website is recorded for this company.',
            ]);

            return $intel->refresh();
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'AI-Receptionist-Sales-Engine/1.0'])
                ->get($company->website);

            if (!$response->successful()) {
                $intel->update(['research_status' => 'failed', 'research_summary' => 'Website returned HTTP '.$response->status().'.']);
                return $intel->refresh();
            }

            $html = $response->body();
            $title = $this->extractTitle($html);
            $text = $this->extractText($html);
            $excerpt = mb_substr(preg_replace('/\s+/', ' ', $text), 0, 6000);

            $intel->update([
                'research_status' => 'completed',
                'website_title' => $title,
                'research_summary' => $excerpt ?: 'Website loaded, but no readable text was extracted.',
                'researched_at' => now(),
                'research_data' => [
                    'url' => $company->website,
                    'http_status' => $response->status(),
                    'content_length' => strlen($html),
                ],
            ]);

            return $intel->refresh();
        } catch (\Throwable $e) {
            $intel->update([
                'research_status' => 'failed',
                'research_summary' => 'Research failed: '.$e->getMessage(),
            ]);

            return $intel->refresh();
        }
    }

    private function extractTitle(string $html): ?string
    {
        if (!preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
            return null;
        }

        return trim(html_entity_decode(strip_tags($matches[1])));
    }

    private function extractText(string $html): string
    {
        $html = preg_replace('/<(script|style|noscript)[^>]*>.*?<\/\1>/is', ' ', $html);
        return trim(strip_tags($html));
    }
}
