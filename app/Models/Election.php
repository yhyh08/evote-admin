<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    protected $fillable = [
        'name',
        'status',
        'start_date',
        'end_date',
        'election_topic',
        'type',
        'position',
        'description',
        'nominate_period_start',
        'nominate_period_end',
        'org_id',
        'result_release_date'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'nominate_period_start' => 'datetime',
        'nominate_period_end' => 'datetime',
        'position' => 'array',
    ];

    protected $primaryKey = 'election_id';

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id', 'org_id');
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'election_id');
    }

    public function nominations()
    {
        return $this->hasMany(Nomination::class);
    }
} 