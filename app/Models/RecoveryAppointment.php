<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;
use Illuminate\\Database\\Eloquent\\Relations\\HasOne;

class RecoveryAppointment extends Model
{
    protected $fillable = ['prospect_id', 'buyer_id', 'lead_campaign_id', 'call_attempt_id', 'status', 'starts_at', 'timezone', 'meeting_method', 'buyer_meeting_reference', 'prospect_confirmed_at', 'data_sharing_consent_at', 'data_sharing_consent_version', 'shared_with_buyer_at', 'agreed_lead_price', 'currency', 'qualification_notes'];

    protected $casts = ['starts_at' => 'datetime', 'prospect_confirmed_at' => 'datetime', 'data_sharing_consent_at' => 'datetime', 'shared_with_buyer_at' => 'datetime', 'agreed_lead_price' => 'decimal:2'];

    public function prospect(): BelongsTo { return $this->belongsTo(Prospect::class); }
    public function buyer(): BelongsTo { return $this->belongsTo(Buyer::class); }
    public function campaign(): BelongsTo { return $this->belongsTo(LeadCampaign::class, 'lead_campaign_id'); }
    public function callAttempt(): BelongsTo { return $this->belongsTo(CallAttempt::class); }
    public function delivery(): HasOne { return $this->hasOne(LeadDelivery::class); }
}
