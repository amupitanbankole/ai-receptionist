<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiReplySuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id','inbound_message_id','type','intent','priority','subject','body',
        'rationale','status','provider','model','metadata','generated_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'generated_at' => 'datetime',
    ];

    public function conversation(): BelongsTo { return $this->belongsTo(SalesConversation::class, 'conversation_id'); }
    public function inboundMessage(): BelongsTo { return $this->belongsTo(Message::class, 'inbound_message_id'); }
}