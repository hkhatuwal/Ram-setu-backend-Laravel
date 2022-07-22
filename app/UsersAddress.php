<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersAddress extends Model
{
    protected $fillable = [
        'user_id', 'country_id', 'state_id', 'city_id', 'pincode', 'landmark', 'address', 'status'
    ];
}
