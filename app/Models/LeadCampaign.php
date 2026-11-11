<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadCampaign extends Model
{
    protected $fillable = ['us_state_id', 'buyer_id', 'name', 'status', 'channel', 'monthly_appointment_target', 'description', 'approved_for_launch_at'];

    protected $casts = ['monthly_appointment_target' => 'integer', 'approved_for_launch_at' => 'datetime'];

    public function state(): BelongsTo { return $this->belongsTo(UsState::class, 'us_state_id'); }
    public function buyer(): BelongsTo { return $this->belongsTo(Buyer::class); }
    public function callAttempts(): HasMany { return $this->hasMany(CallAttempt::class); }
    public function appointments(): HasMany { return $this->hasMany(RecoveryAppointment::class); }
}
