<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallingEligibility extends Model
{
    protected $fillable = ['prospect_id', 'phone_e164', 'buyer_id', 'decision', 'reason_codes', 'evidence_snapshot', 'evaluated_at', 'expires_at'];

    protected $casts = ['reason_codes' => 'array', 'evidence_snapshot' => 'array', 'evaluated_at' => 'datetime', 'expires_at' => 'datetime'];

    public function prospect(): BelongsTo { return $this->belongsTo(Prospect::class); }
    public function buyer(): BelongsTo { return $this->belongsTo(Buyer::class); }
}
