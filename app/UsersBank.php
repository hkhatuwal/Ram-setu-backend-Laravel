<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersBank extends Model
{
    protected $fillable = [
        'user_id', 'account_holder', 'account_number', 'ifsc', 'bank_name'
    ];
}