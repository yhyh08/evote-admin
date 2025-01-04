<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateDocs extends Model
{
    use HasFactory;

    protected $primaryKey = 'cand_doc_id';

    protected $fillable = [
        'cand_doc_id',
        'candidate_id',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'candidate_id');
    }
} 