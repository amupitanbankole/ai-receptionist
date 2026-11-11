<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadDelivery extends Model
{
    protected $fillable = ['recovery_appointment_id', 'buyer_id', 'status', 'delivery_reference', 'rejection_reason', 'delivered_at', 'accepted_at', 'rejected_at'];

    protected $casts = ['delivered_at' => 'datetime', 'accepted_at' => 'datetime', 'rejected_at' => 'datetime'];

    public function appointment(): BelongsTo { return $this->belongsTo(RecoveryAppointment::class, 'recovery_appointment_id'); }
    public function buyer(): BelongsTo { return $this->belongsTo(Buyer::class); }
}
