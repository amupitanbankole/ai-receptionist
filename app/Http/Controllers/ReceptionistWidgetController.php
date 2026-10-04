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

        return $this->cors([
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
            'customer_name' => ['required','string','max:255'],
            'customer_email' => ['nullable','email','max:255'],
            'customer_phone' => ['nullable','string','max:50'],
            'message' => ['required','string','max:10000'],
            'requested_start' => ['nullable','date'],
        ]);

        $company = Company::findOrFail($data['company_id']);
        $data['channel'] = 'webchat';

        $result = $service->respond($company, $data);

        return $this->cors([
            'success' => true,
            'reply' => $result['message'],
            'conversation_id' => $result['conversation']?->id,
            'appointment_id' => $result['appointment']?->id,
            'booked_at' => $result['appointment']?->starts_at?->toIso8601String(),
        ], 201);
    }

    public function options(): JsonResponse
    {
        return $this->cors([], 204);
    }

    private function cors(array $payload, int $status = 200): JsonResponse
    {
        return response()->json($payload, $status)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept');
    }
}
