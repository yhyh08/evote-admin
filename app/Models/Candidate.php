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
        'candidate_name',
        'status',
        'reason',
    ];

    public function nominations()
    {
        return $this->hasMany(Nomination::class, 'candidate_id', 'candidate_id');
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
}
    