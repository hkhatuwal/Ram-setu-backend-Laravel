<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdersFeedback extends Model
{
    protected $fillable = [
        'base_id','user_id','rate','message','status'
    ];
}
