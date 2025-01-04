<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $primaryKey = 'candidate_id';

    protected $casts = [
        'nominee_id' => 'json',
        'cand_doc_id' => 'json',
    ];

    protected $fillable = [
        'candidate_name',
        'candidate_image',
        'candidate_phone',
        'candidate_email',
        'candidate_gender',
        'candidate_ic',
        'candidate_address',
        'nationality',
        'religion',
        'job',
        'income',
        'position',
        'marriage_status',
        'short_biography',
        'manifesto',
        'reason',
        'sign',
        'election_id',
        'status',
        'votes_count',
        'user_id',
        'nominee_id',
        'cand_doc_id'
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

    public function election() {
        return $this->belongsTo(Election::class, 'election_id', 'election_id');
    }

    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
    