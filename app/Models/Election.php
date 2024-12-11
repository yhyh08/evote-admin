<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    use HasFactory;

    protected $primaryKey = 'election_id';

    protected $fillable = [
        'election_topic',
        'type',
        'position',
        'description',
        'start_date',
        'end_date',
        'nominate_period_start',
        'nominate_period_end',
        'result_release_date',
        'status',
        'org_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'nominate_period_start' => 'date',
        'nominate_period_end' => 'date',
        'result_release_date' => 'date',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id', 'org_id');
    }
} 