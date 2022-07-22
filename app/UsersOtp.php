<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersOtp extends Model
{
    protected $fillable = [
        'email', 'mobile', 'otp', 'roles', 'status','sending_date' 
    ];
}
