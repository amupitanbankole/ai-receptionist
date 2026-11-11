<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;

class ConsentRecord extends Model
{
    protected $fillable = ['prospect_id', 'phone_e164', 'named_seller_legal_name', 'consent_scope', 'consent_method', 'consent_text_version', 'consent_text', 'capture_source_url', 'evidence_reference', 'evidence_metadata', 'verification_status', 'captured_at', 'verified_at', 'revoked_at'];

    protected $casts = ['evidence_metadata' => 'array', 'captured_at' => 'datetime', 'verified_at' => 'datetime', 'revoked_at' => 'datetime'];

    public function prospect(): BelongsTo { return $this->belongsTo(Prospect::class); }
}
