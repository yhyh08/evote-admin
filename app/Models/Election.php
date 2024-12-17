<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    protected $fillable = [
        'election_topic',
        'type',
        'position',
        'description',
        'start_date',
        'end_date',
        'nominate_period_start',
        'nominate_period_end',
        'status',
        'org_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'nominate_period_start' => 'datetime',
        'nominate_period_end' => 'datetime'
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
} 