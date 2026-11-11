<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;
use Illuminate\\Database\\Eloquent\\Relations\\HasMany;

class PropertySource extends Model
{
    protected $fillable = ['us_state_id', 'name', 'official_url', 'source_type', 'access_method', 'terms_reviewed', 'terms_reviewed_at', 'is_active'];

    protected $casts = ['terms_reviewed' => 'boolean', 'terms_reviewed_at' => 'datetime', 'is_active' => 'boolean'];

    public function state(): BelongsTo { return $this->belongsTo(UsState::class, 'us_state_id'); }
    public function propertyRecords(): HasMany { return $this->hasMany(PropertyRecord::class); }
}
