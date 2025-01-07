<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $primaryKey = 'org_id';
    
    protected $fillable = [
        'org_name',
        'org_desc',
        'org_cat',
        'org_address',
        'org_img',
        'org_website',
        'org_email',
        'org_size',
        'pic_name',
        'pic_phone',
        'pic_email',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
