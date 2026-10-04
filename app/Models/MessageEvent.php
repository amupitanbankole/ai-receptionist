<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageEvent extends Model
{
    use HasFactory;

    protected $fillable = ['message_id','event_type','provider_event_id','payload','occurred_at'];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function message(): BelongsTo { return $this->belongsTo(Message::class); }
}
