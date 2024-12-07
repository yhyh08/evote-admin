<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionCommittee extends Model
{
    use HasFactory;

    protected $primaryKey = 'com_id';

    protected $fillable = [
        'com_name',
        'com_phone',
        'com_email',
        'isApprove'
    ];
} 