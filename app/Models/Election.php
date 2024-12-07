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
        'nominate_period',
        'status'
    ];
} 