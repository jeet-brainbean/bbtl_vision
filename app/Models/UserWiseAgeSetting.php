<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserWiseAgeSetting extends Model
{
    use HasFactory;

    protected $table = 'user_wise_age_setting';

    protected $fillable = [
        'user_id',
        'success_min_age',
        'success_max_age',
        'error_min_age',
        'error_max_age',
        'status',
    ];

    public $timestamps = true;

}
