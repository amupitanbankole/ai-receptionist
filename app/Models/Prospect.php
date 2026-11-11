<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;
use Illuminate\\Database\\Eloquent\\Relations\\HasMany;

class Prospect extends Model
{
    protected $fillable = ['us_state_id', 'first_name', 'last_name', 'email', 'phone_e164', 'address_line_1', 'address_line_2', 'city', 'region', 'postal_code', 'country_code', 'status', 'public_record_last_verified_at', 'do_not_contact_at', 'internal_notes'];

    protected $casts = ['public_record_last_verified_at' => 'datetime', 'do_not_contact_at' => 'datetime'];

    public function state(): BelongsTo { return $this->belongsTo(UsState::class, 'us_state_id'); }
    public function propertyRecords(): HasMany { return $this->hasMany(PropertyRecord::class); }
    public function consentRecords(): HasMany { return $this->hasMany(ConsentRecord::class); }
    public function eligibilityDecisions(): HasMany { return $this->hasMany(CallingEligibility::class); }
    public function callAttempts(): HasMany { return $this->hasMany(CallAttempt::class); }
    public function recoveryAppointments(): HasMany { return $this->hasMany(RecoveryAppointment::class); }
}
