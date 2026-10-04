<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suppression extends Model
{
    use HasFactory;

    protected $fillable = ['email','reason','suppressed_at'];

    protected $casts = ['suppressed_at' => 'datetime'];
}
