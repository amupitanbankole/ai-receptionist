<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buyer extends Model
{
    protected $fillable = ['legal_name', 'display_name', 'buyer_category', 'website', 'business_email', 'business_phone', 'business_address', 'verification_status', 'is_active', 'internal_notes'];

    protected $casts = ['is_active' => 'boolean'];

    public function authorizations(): HasMany { return $this->hasMany(BuyerAuthorization::class); }
    public function campaigns(): HasMany { return $this->hasMany(LeadCampaign::class); }
    public function appointments(): HasMany { return $this->hasMany(RecoveryAppointment::class); }
    public function leadDeliveries(): HasMany { return $this->hasMany(LeadDelivery::class); }
}
