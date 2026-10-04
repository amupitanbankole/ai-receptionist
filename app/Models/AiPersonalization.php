<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiPersonalization extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id','company_id','type','provider','model','subject','content',
        'prompt','status','metadata','generated_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'generated_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
