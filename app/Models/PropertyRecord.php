<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;

class PropertyRecord extends Model
{
    protected $fillable = ['prospect_id', 'us_state_id', 'property_source_id', 'external_reference', 'owner_name', 'property_type', 'reported_holder', 'reported_amount', 'currency', 'official_record_url', 'verification_status', 'source_recorded_at', 'last_verified_at', 'source_metadata'];

    protected $casts = ['reported_amount' => 'decimal:2', 'source_recorded_at' => 'datetime', 'last_verified_at' => 'datetime', 'source_metadata' => 'array'];

    public function prospect(): BelongsTo { return $this->belongsTo(Prospect::class); }
    public function state(): BelongsTo { return $this->belongsTo(UsState::class, 'us_state_id'); }
    public function source(): BelongsTo { return $this->belongsTo(PropertySource::class, 'property_source_id'); }
}
