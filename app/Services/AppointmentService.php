<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\BusinessAvailability;
use App\Models\Company;
use Carbon\Carbon;
use RuntimeException;

class AppointmentService
{
    public function ensureDefaults(Company $company): void
    {
        if (!$company->appointmentTypes()->exists()) {
            AppointmentType::create([
                'company_id' => $company->id,
                'name' => 'Consultation',
                'description' => 'Initial consultation or service enquiry.',
                'duration_minutes' => 30,
                'active' => true,
            ]);
        }

        if (!$company->availability()->exists()) {
            foreach (range(1, 5) as $day) {
                BusinessAvailability::create([
                    'company_id' => $company->id,
                    'day_of_week' => $day,
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'active' => true,
                ]);
            }
        }
    }

    public function slots(Company $company, Carbon $from, int $days = 7, int $interval = 30): array
    {
        $this->ensureDefaults($company);
        $timezone = $company->receptionistConfig?->timezone ?: 'Europe/London';
        $cursor = $from->copy()->setTimezone($timezone)->startOfMinute();
        $endDate = $cursor->copy()->addDays($days);
        $type = $company->appointmentTypes()->where('active', true)->orderBy('id')->first();
        if (!$type) return [];

        $availability = $company->availability()->where('active', true)->get()->groupBy('day_of_week');
        $slots = [];

        while ($cursor->lt($endDate) && count($slots) < 30) {
            $windows = $availability->get($cursor->dayOfWeek, collect());
            foreach ($windows as $window) {
                $start = $cursor->copy()->setTimeFromTimeString($window->start_time);
                $close = $cursor->copy()->setTimeFromTimeString($window->end_time);
                for ($slot = $start->copy(); $slot->copy()->addMinutes($type->duration_minutes)->lte($close); $slot->addMinutes($interval)) {
                    if ($slot->isPast()) continue;
                    $slotEnd = $slot->copy()->addMinutes($type->duration_minutes);
                    if (!$this->isAvailable($company, $slot, $slotEnd)) continue;
                    $slots[] = ['start' => $slot->copy(), 'end' => $slotEnd, 'type' => $type];
                    if (count($slots) >= 30) break 2;
                }
            }
            $cursor->addDay()->startOfDay();
        }

        return $slots;
    }

    public function isAvailable(Company $company, Carbon $start, Carbon $end, ?int $ignoreId = null): bool
    {
        return !Appointment::where('company_id', $company->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('starts_at', '<', $end)
            ->where('ends_at', '>', $start)
            ->exists();
    }

    public function create(Company $company, array $data): Appointment
    {
        $type = !empty($data['appointment_type_id'])
            ? AppointmentType::where('company_id', $company->id)->find($data['appointment_type_id'])
            : $company->appointmentTypes()->where('active', true)->first();

        if (!$type) throw new RuntimeException('No active appointment type is configured.');

        $start = Carbon::parse($data['starts_at'], $data['timezone'] ?? ($company->receptionistConfig?->timezone ?: 'Europe/London'));
        $end = $start->copy()->addMinutes($type->duration_minutes);

        if (!$this->isAvailable($company, $start, $end)) {
            throw new RuntimeException('That appointment slot is no longer available.');
        }

        return Appointment::create([
            'company_id' => $company->id,
            'contact_id' => $data['contact_id'] ?? null,
            'lead_id' => $data['lead_id'] ?? null,
            'appointment_type_id' => $type->id,
            'starts_at' => $start,
            'ends_at' => $end,
            'timezone' => $data['timezone'] ?? ($company->receptionistConfig?->timezone ?: 'Europe/London'),
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'] ?? null,
            'customer_phone' => $data['customer_phone'] ?? null,
            'status' => $data['status'] ?? 'confirmed',
            'source' => $data['source'] ?? 'ai_receptionist',
            'notes' => $data['notes'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);
    }
}