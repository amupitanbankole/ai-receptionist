<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\HasMany;

class UsState extends Model
{
    protected $fillable = ['code', 'name', 'finder_status', 'calling_status', 'compliance_notes', 'legal_review_reference', 'reviewed_at'];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function propertySources(): HasMany { return $this->hasMany(PropertySource::class); }
    public function prospects(): HasMany { return $this->hasMany(Prospect::class); }
    public function propertyRecords(): HasMany { return $this->hasMany(PropertyRecord::class); }
    public function buyerAuthorizations(): HasMany { return $this->hasMany(BuyerAuthorization::class); }
    public function campaigns(): HasMany { return $this->hasMany(LeadCampaign::class); }
}
