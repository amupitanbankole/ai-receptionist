<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_recipient_id','direction','channel','to_address','from_address','reply_to',
        'subject','body','status','provider_message_id','sent_at','metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function recipient(): BelongsTo { return $this->belongsTo(CampaignRecipient::class, 'campaign_recipient_id'); }
    public function events(): HasMany { return $this->hasMany(MessageEvent::class); }
}
