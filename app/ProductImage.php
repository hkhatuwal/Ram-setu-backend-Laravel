<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id', 'name','image', 'status'
    ];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
            return asset('public/image/product/' . $this->image);
    }
}
