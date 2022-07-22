<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $fillable = [
        'super_cat_id','category_id', 'subcat_name', 'description', 'image', 'has_product','status'
    ];
}
