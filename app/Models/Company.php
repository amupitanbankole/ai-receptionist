<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','slug','website','industry','business_type','description','phone','email',
        'address_line_1','address_line_2','city','county','postcode','country',
        'service_areas','business_hours','source','source_url','status','lead_score',
        'emergency_service','appointment_based','phone_prominent','online_booking',
        'small_team','outside_hours_service','high_value_service','live_chat','multiple_locations',
    ];

    protected $casts = [
        'service_areas' => 'array','business_hours' => 'array','lead_score' => 'integer',
        'emergency_service' => 'boolean','appointment_based' => 'boolean','phone_prominent' => 'boolean',
        'online_booking' => 'boolean','small_team' => 'boolean','outside_hours_service' => 'boolean',
        'high_value_service' => 'boolean','live_chat' => 'boolean','multiple_locations' => 'boolean',
    ];

    public function contacts(): HasMany { return $this->hasMany(Contact::class); }
    public function leads(): HasMany { return $this->hasMany(Lead::class); }
    public function activities(): HasMany { return $this->hasMany(Activity::class); }
    public function intelligence(): HasOne { return $this->hasOne(CompanyIntelligence::class); }
    public function receptionistConfig(): HasOne { return $this->hasOne(ReceptionistConfig::class); }
    public function receptionistKnowledge(): HasMany { return $this->hasMany(ReceptionistKnowledge::class); }
    public function appointmentTypes(): HasMany { return $this->hasMany(AppointmentType::class); }
    public function availability(): HasMany { return $this->hasMany(BusinessAvailability::class); }
    public function appointments(): HasMany { return $this->hasMany(Appointment::class); }
    public function salesConversations(): HasMany { return $this->hasMany(SalesConversation::class); }
}
