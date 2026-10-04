<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id','contact_id','lead_source_id','title','status','score','temperature','source','notes','next_follow_up_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'next_follow_up_at' => 'datetime',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function contact(): BelongsTo { return $this->belongsTo(Contact::class); }
    public function leadSource(): BelongsTo { return $this->belongsTo(LeadSource::class); }
    public function activities(): HasMany { return $this->hasMany(Activity::class); }
    public function followups(): HasMany { return $this->hasMany(Followup::class); }
}