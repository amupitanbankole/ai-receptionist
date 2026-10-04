<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id','company_id','contact_id','channel','subject','status','intent',
        'priority','next_action','summary','last_message_at','last_inbound_at','metadata',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'last_inbound_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function lead(): BelongsTo { return $this->belongsTo(Lead::class); }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function contact(): BelongsTo { return $this->belongsTo(Contact::class); }
    public function messages(): HasMany { return $this->hasMany(Message::class, 'conversation_id')->latest(); }
    public function aiReplySuggestions(): HasMany { return $this->hasMany(AiReplySuggestion::class, 'conversation_id')->latest(); }
}