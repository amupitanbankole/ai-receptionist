<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'website',
        'industry',
        'business_type',
        'description',
        'phone',
        'email',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'postcode',
        'country',
        'service_areas',
        'business_hours',
        'source',
        'source_url',
        'status',
        'lead_score',
    ];

    protected $casts = [
        'service_areas' => 'array',
        'business_hours' => 'array',
        'lead_score' => 'integer',
    ];

    public function contacts(): HasMany
{
    return $this->hasMany(Contact::class);
}
}