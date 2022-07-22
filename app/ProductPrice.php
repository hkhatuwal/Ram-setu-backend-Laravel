<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $fillable = [
        'product_id', 'currency', 'mrp_price', 'sell_price'
    ];
}
