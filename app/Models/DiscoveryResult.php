<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscoveryResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name','website','phone','email','industry','city','county','postcode',
        'country','source','source_url','discovery_notes','status','company_id',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}
