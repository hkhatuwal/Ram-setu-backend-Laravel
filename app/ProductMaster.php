<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductMaster extends Model
{
    protected $fillable = [
        'user_id','super_cat_id','category_id', 'product_code', 'product_name', 'image', 'description','moisture','quantity','quality','grade','unit','base_price','sell_price','max_bid_price','max_bid_user_id','grand_price','bid_close_date','daytosales','status','deal_status','noofbid'
    ];
    protected $appends = ['images','number_of_bids'];



    public function getImageAttribute($value)
    {
        $value=ProductImage::where('product_id',$this->id)->first();
        if ($value) {
            return asset('public/image/product/' . $value->image);
        } else {
            return asset('public/images/no-image.png');
        }
    }
    public function getImagesAttribute()
    {
        return ProductImage::where('product_id',$this->id)->get();
         

       
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getNumberOfBidsAttribute()
    {
        return ProductBid::where('product_id',$this->id)->count();
    }
}
