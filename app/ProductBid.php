<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductBid extends Model
{
    protected $fillable = [
        'user_id','product_id','base_price', 'bid_price', 'status'
    ];
}
