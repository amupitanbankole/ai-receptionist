<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','status','template_id','subject','body','daily_limit','scheduled_at',
        'started_at','completed_at','unsubscribe_text','reply_to','settings',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'settings' => 'array',
        'daily_limit' => 'integer',
    ];

    public function template(): BelongsTo { return $this->belongsTo(EmailTemplate::class, 'template_id'); }
    public function recipients(): HasMany { return $this->hasMany(CampaignRecipient::class); }
}
