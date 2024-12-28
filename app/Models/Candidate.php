<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $primaryKey = 'candidate_id';

    protected $casts = [
        'nominee_ids' => 'array'
    ];

    protected $fillable = [
        'name',
        'phone',
        'email',
        'status',
        'election_id',
        'posiiton',
        'votes_count',
        'candidate_name',
        'reason',
    ];

    public function nominations()
    {
        return $this->hasMany(Nomination::class);
    }

    public function documents()
    {
        return $this->hasMany(CandidateDocs::class, 'candidate_id', 'candidate_id');
    }

    public function getNominees()
    {
        return $this->nominations()
            ->select('nominee_name', 'nominee_email', 'nominee_phone', 'status')
            ->get();
    }

    public function nominees()
    {
        return $this->hasMany(Nomination::class, 'nominee_id')
            ->whereIn('nominee_id', $this->nominee_ids ?? []);
    }

    public function election()
    {
        return $this->belongsTo(Election::class);
    }
}
    