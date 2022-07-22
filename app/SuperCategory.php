<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SuperCategory extends Model
{
    protected $fillable = [
        'super_cat_name','description','icon','image','banner','has_subcat','status','is_banner'
    ];

    public function products()
    {
        return $this->hasMany(ProductMaster::class,'super_cat_id');
    }
}
