<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class VotingEligibility extends Model
{
    use HasApiTokens;
    protected $table = 'voting_eligibility';
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'org_id'
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id', 'org_id');
    }
}
