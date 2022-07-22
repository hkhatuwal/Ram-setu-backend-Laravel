<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdersStatus extends Model
{
    protected $fillable = [
        'base_id', 'pending','pending_date','confirm','confirm_date','shipped','shipped_date','outofdelivery','outofdelivery_date','delivered','delivered_date'
    ];
}
