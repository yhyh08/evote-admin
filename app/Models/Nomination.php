<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nomination extends Model
{
    use HasFactory;

    protected $primaryKey = 'nominee_id';

    protected $fillable = [
        'nominee_name',
        'nominee_phone',
        'nominee_email',
        'reason',
        'candidate_id',
        'election_id',
        'org_id',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class, 'election_id', 'election_id');
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'candidate_id');
    }
}
