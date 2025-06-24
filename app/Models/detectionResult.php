<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class detectionResult extends Model
{
    //
       protected $fillable = [
        'user_id',
        'photo',
        'result',
        'credited'
    ];

}
