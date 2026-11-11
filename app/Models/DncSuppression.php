<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;

class DncSuppression extends Model
{
    protected $fillable = ['phone_e164', 'scope', 'reason', 'source', 'is_active', 'suppressed_at', 'released_at'];

    protected $casts = ['is_active' => 'boolean', 'suppressed_at' => 'datetime', 'released_at' => 'datetime'];
}
