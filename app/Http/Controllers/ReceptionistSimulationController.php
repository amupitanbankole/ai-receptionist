<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\ReceptionistService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceptionistSimulationController extends Controller
{
    public function show(Request $request, ReceptionistService $service): View
    {
        $company = Company::findOrFail($request->integer('company_id'));
        $service->ensureConfig($company);
        return view('receptionist.simulate', compact('company'));
    }

    public function store(Request $request, ReceptionistService $service): View
    {
        $data = $request->validate([
            'company_id' => ['required','exists:companies,id'],
            'customer_name' => ['required','string','max:255'],
            'customer_email' => ['nullable','email','max:255'],
            'customer_phone' => ['nullable','string','max:50'],
            'message' => ['required','string','max:10000'],
            'requested_start' => ['nullable','date'],
        ]);
        $company = Company::findOrFail($data['company_id']);
        try {
            $result = $service->respond($company, $data);
            return view('receptionist.simulate', compact('company','result'));
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error','Receptionist processing failed. Check storage/logs/laravel.log.');
        }
    }
}