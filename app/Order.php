<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'transaction_id', 'price', 'coupon_code', 'discount_percent', 'discount', 'subtotal', 'gst_percent', 'gst', 'shipping_charges', 'grand_total', 'currency', 'payment_mode', 'payment_by','status','order_date','delivered_date'
    ];
}
