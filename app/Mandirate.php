<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mandirate extends Model
{
    protected $fillable = [
        'commodity_id','min','max','modelrate','status'
    ];
}
