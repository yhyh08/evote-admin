<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingEligibility extends Model
{
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
