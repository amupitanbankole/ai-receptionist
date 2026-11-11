<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallAttempt extends Model
{
    protected $fillable = ['prospect_id', 'lead_campaign_id', 'calling_eligibility_id', 'phone_e164', 'provider_call_id', 'status', 'outcome', 'provider', 'recording_reference', 'transcript_reference', 'summary', 'started_at', 'ended_at'];

    protected $casts = ['started_at' => 'datetime', 'ended_at' => 'datetime'];

    public function prospect(): BelongsTo { return $this->belongsTo(Prospect::class); }
    public function campaign(): BelongsTo { return $this->belongsTo(LeadCampaign::class, 'lead_campaign_id'); }
    public function eligibility(): BelongsTo { return $this->belongsTo(CallingEligibility::class, 'calling_eligibility_id'); }
}
