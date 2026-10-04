<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id','lead_id','contact_id','company_id','email','first_name','last_name',
        'status','scheduled_at','sent_at','replied_at','error',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'replied_at' => 'datetime',
    ];

    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function lead(): BelongsTo { return $this->belongsTo(Lead::class); }
    public function contact(): BelongsTo { return $this->belongsTo(Contact::class); }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function messages(): HasMany { return $this->hasMany(Message::class); }
}
