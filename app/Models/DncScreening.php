<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DncScreening extends Model
{
    protected $fillable = ['phone_e164', 'screening_type', 'status', 'source', 'result_reference', 'checked_at', 'expires_at', 'evidence_metadata'];

    protected $casts = ['checked_at' => 'datetime', 'expires_at' => 'datetime', 'evidence_metadata' => 'array'];
}
