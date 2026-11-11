<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;

class BuyerAuthorization extends Model
{
    protected $fillable = ['buyer_id', 'us_state_id', 'service_category', 'authorization_type', 'registration_reference', 'status', 'verified_at', 'expires_at', 'verification_notes'];

    protected $casts = ['verified_at' => 'datetime', 'expires_at' => 'datetime'];

    public function buyer(): BelongsTo { return $this->belongsTo(Buyer::class); }
    public function state(): BelongsTo { return $this->belongsTo(UsState::class, 'us_state_id'); }
}
