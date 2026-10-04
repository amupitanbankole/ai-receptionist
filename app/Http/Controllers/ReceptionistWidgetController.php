<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\ReceptionistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReceptionistWidgetController extends Controller
{
    public function config(Request $request, ReceptionistService $service): JsonResponse
    {
        $company = Company::findOrFail($request->integer('company_id'));
        $config = $service->ensureConfig($company);

        $this->authorizeWidget($request, $config, $service);

        return $this->cors($request, [
            'company_id' => $company->id,
            'enabled' => (bool) $config->enabled,
            'display_name' => $config->display_name ?: $company->name.' AI Receptionist',
            'greeting' => $config->greeting ?: 'Thanks for contacting us. How can I help you today?',
            'booking_enabled' => (bool) $config->booking_enabled,
        ]);
    }

    public function chat(Request $request, ReceptionistService $service): JsonResponse
    {
        $data = $request->validate([
            'company_id' => ['required','integer','exists:companies,id'],
            'site_key' => ['required','string','size:40'],
            'customer_name' => ['required','string','max:255'],
            'customer_email' => ['nullable','email','max:255'],
            'customer_phone' => ['nullable','string','max:50'],
            'message' => ['required','string','max:10000'],
            'requested_start' => ['nullable','date'],
        ]);

        $company = Company::findOrFail($data['company_id']);
        $config = $service->ensureConfig($company);
        $this->authorizeWidget($request, $config, $service, $data['site_key']);

        $data['channel'] = 'webchat';

        try {
            $result = $service->respond($company, $data);

            return $this->cors($request, [
                'success' => true,
                'reply' => $result['message'],
                'conversation_id' => $result['conversation']?->id,
                'appointment_id' => $result['appointment']?->id,
                'booked_at' => $result['appointment']?->starts_at?->toIso8601String(),
            ], 201);
        } catch (\Throwable $e) {
            report($e);
            return $this->cors($request, [
                'success' => false,
                'message' => 'The receptionist is temporarily unavailable. Please try again in a moment.',
            ], 503);
        }
    }

    public function options(Request $request, ReceptionistService $service): JsonResponse
    {
        $companyId = $request->integer('company_id');
        $siteKey = (string) $request->header('X-Receptionist-Key');
        $company = $companyId ? Company::find($companyId) : null;

        if (!$company || !$siteKey) {
            return response()->json([], 204);
        }

        $config = $service->ensureConfig($company);
        $this->authorizeWidget($request, $config, $service, $siteKey);

        return $this->cors($request, [], 204);
    }

    private function authorizeWidget(Request $request, $config, ReceptionistService $service, ?string $siteKey = null): void
    {
        $providedKey = $siteKey ?: (string) $request->header('X-Receptionist-Key');
        $expectedKey = $service->widgetKey($config);
        $origin = $request->header('Origin');

        if (!$expectedKey || !$providedKey || !hash_equals($expectedKey, $providedKey)) {
            abort(response()->json(['message' => 'Invalid widget key.'], 403));
        }

        if (!$service->isWidgetOriginAllowed($config, $origin)) {
            abort(response()->json(['message' => 'This website is not authorized for this receptionist widget.'], 403));
        }
    }

    private function cors(Request $request, array $payload, int $status = 200): JsonResponse
    {
        $response = response()->json($payload, $status);
        $origin = $request->header('Origin');

        if ($origin) {
            $response->header('Access-Control-Allow-Origin', rtrim($origin, '/'))
                ->header('Vary', 'Origin');
        }

        return $response
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, X-Receptionist-Key');
    }
}