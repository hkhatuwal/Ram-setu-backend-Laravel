<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdersAddress extends Model
{
    protected $fillable = [
        'base_id','user_id','name','mobile','email','alternate_mobile','country','state','city','pincode','street_no','area_name','address'
    ];
}
