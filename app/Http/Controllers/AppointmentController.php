<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Company;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $appointments = Appointment::with(['company','appointmentType','lead','contact'])
            ->when($request->filled('company_id'), fn ($q) => $q->where('company_id', $request->integer('company_id')))
            ->latest('starts_at')
            ->paginate(20)->withQueryString();
        return view('appointments.index', compact('appointments'));
    }

    public function create(Request $request, AppointmentService $service): View
    {
        $company = Company::findOrFail($request->integer('company_id'));
        $service->ensureDefaults($company);
        $types = $company->appointmentTypes()->where('active',true)->get();
        $slots = $service->slots($company, now(), 14);
        return view('appointments.create', compact('company','types','slots'));
    }

    public function store(Request $request, AppointmentService $service)
    {
        $data = $request->validate([
            'company_id' => ['required','exists:companies,id'],
            'appointment_type_id' => ['nullable','exists:appointment_types,id'],
            'starts_at' => ['required','date'],
            'customer_name' => ['required','string','max:255'],
            'customer_email' => ['nullable','email','max:255'],
            'customer_phone' => ['nullable','string','max:50'],
            'notes' => ['nullable','string'],
        ]);
        $company = Company::findOrFail($data['company_id']);
        $appointment = $service->create($company, $data);
        return redirect()->route('appointments.index')->with('success','Appointment booked successfully.');
    }

    public function status(Request $request, Appointment $appointment)
    {
        $data = $request->validate(['status'=>['required','in:pending,confirmed,cancelled,completed,no_show']]);
        $appointment->update(['status'=>$data['status']]);
        return back()->with('success','Appointment status updated.');
    }
}