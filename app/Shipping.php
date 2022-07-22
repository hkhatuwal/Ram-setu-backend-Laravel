<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $fillable = [
        'country_code','state_id','min','max','charges','status'
    ];
}
