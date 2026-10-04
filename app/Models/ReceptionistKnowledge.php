<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceptionistKnowledge extends Model
{
    use HasFactory;

    protected $table = 'receptionist_knowledge';

    protected $fillable = ['company_id','type','title','content','active','priority'];

    protected $casts = ['active' => 'boolean'];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}