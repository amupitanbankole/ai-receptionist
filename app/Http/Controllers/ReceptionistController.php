<?php

namespace App\Http\Controllers;

use App\Models\AppointmentType;
use App\Models\BusinessAvailability;
use App\Models\Company;
use App\Models\ReceptionistKnowledge;
use App\Services\ReceptionistService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceptionistController extends Controller
{
    public function dashboard(Request $request, ReceptionistService $service): View
    {
        $company = $request->filled('company_id')
            ? Company::findOrFail($request->integer('company_id'))
            : Company::latest()->first();

        if ($company) $service->ensureConfig($company);

        $appointments = $company?->appointments()->where('starts_at', '>=', now())->whereIn('status', ['pending','confirmed'])->orderBy('starts_at')->limit(8)->get() ?? collect();
        $conversations = $company?->salesConversations()->where('channel', '!=', 'email')->latest('last_message_at')->limit(8)->get() ?? collect();
        $today = $company?->salesConversations()->whereDate('last_message_at', today())->count() ?? 0;
        $leads = $company?->leads()->count() ?? 0;

        return view('receptionist.dashboard', compact('company','appointments','conversations','today','leads'));
    }

    public function edit(Company $company, ReceptionistService $service): View
    {
        $config = $service->ensureConfig($company);
        $knowledge = $company->receptionistKnowledge()->orderByDesc('priority')->get();
        $types = $company->appointmentTypes()->orderBy('id')->get();
        $availability = $company->availability()->orderBy('day_of_week')->orderBy('start_time')->get();
        return view('receptionist.edit', compact('company','config','knowledge','types','availability'));
    }

    public function update(Request $request, Company $company, ReceptionistService $service)
    {
        $data = $request->validate([
            'enabled' => ['nullable','boolean'],
            'display_name' => ['nullable','string','max:255'],
            'greeting' => ['nullable','string','max:1000'],
            'tone' => ['required','in:professional,friendly,concise,warm'],
            'instructions' => ['nullable','string','max:10000'],
            'services' => ['nullable','string'],
            'service_areas' => ['nullable','string'],
            'business_hours' => ['nullable','string'],
            'booking_enabled' => ['nullable','boolean'],
            'after_hours_message' => ['nullable','string','max:2000'],
            'escalation_phone' => ['nullable','string','max:50'],
            'escalation_email' => ['nullable','email','max:255'],
            'notification_email' => ['nullable','email','max:255'],
            'timezone' => ['required','timezone'],
        ]);

        foreach (['services','service_areas','business_hours'] as $key) {
            $data[$key] = $this->lines($data[$key] ?? '');
        }

        $config = $service->ensureConfig($company);
        $config->update($data);
        $service->ensureConfig($company);

        return back()->with('success', 'AI receptionist configuration saved.');
    }

    public function storeKnowledge(Request $request, Company $company)
    {
        $data = $request->validate([
            'type' => ['required','in:faq,service,policy,general'],
            'title' => ['required','string','max:255'],
            'content' => ['required','string','max:20000'],
            'priority' => ['nullable','integer','min:1','max:100'],
        ]);
        $data['company_id'] = $company->id;
        ReceptionistKnowledge::create($data);
        return back()->with('success','Knowledge item added.');
    }

    public function destroyKnowledge(Company $company, ReceptionistKnowledge $knowledge)
    {
        abort_unless($knowledge->company_id === $company->id, 404);
        $knowledge->delete();
        return back()->with('success','Knowledge item removed.');
    }

    public function storeType(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'duration_minutes' => ['required','integer','min:5','max:480'],
            'buffer_minutes' => ['nullable','integer','min:0','max:240'],
        ]);
        $data['company_id'] = $company->id;
        AppointmentType::create($data);
        return back()->with('success','Appointment type added.');
    }

    public function storeAvailability(Request $request, Company $company)
    {
        $data = $request->validate([
            'day_of_week' => ['required','integer','between:0,6'],
            'start_time' => ['required','date_format:H:i'],
            'end_time' => ['required','date_format:H:i','after:start_time'],
        ]);
        $data['company_id'] = $company->id;
        $data['active'] = true;
        BusinessAvailability::create($data);
        return back()->with('success','Availability window added.');
    }

    public function webhook(Request $request, ReceptionistService $service)
    {
        $expected = (string) config('services.receptionist.webhook_token');
        $provided = (string) $request->bearerToken();

        if (!$expected || !$provided || !hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'company_id' => ['required','integer','exists:companies,id'],
            'customer_name' => ['required','string','max:255'],
            'customer_email' => ['nullable','email','max:255'],
            'customer_phone' => ['nullable','string','max:50'],
            'message' => ['required','string','max:10000'],
            'requested_start' => ['nullable','date'],
            'channel' => ['nullable','in:webchat,voice,whatsapp,sms'],
        ]);

        $company = Company::findOrFail($data['company_id']);
        $result = $service->respond($company, $data);

        return response()->json([
            'success' => true,
            'reply' => $result['message'],
            'conversation_id' => $result['conversation']?->id,
            'appointment_id' => $result['appointment']?->id,
        ], 201);
    }

    private function lines(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $value))));
    }
}