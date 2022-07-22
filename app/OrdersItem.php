<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdersItem extends Model
{
    protected $fillable = [
        'base_id','product_id','product_name','description','image','currency','mrp_price','sell_price','qty','subtotal'
    ];
}
