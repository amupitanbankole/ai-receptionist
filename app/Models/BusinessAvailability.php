<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessAvailability extends Model
{
    use HasFactory;

    protected $table = 'business_availability';

    protected $fillable = ['company_id','day_of_week','start_time','end_time','active'];

    protected $casts = ['active' => 'boolean'];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}