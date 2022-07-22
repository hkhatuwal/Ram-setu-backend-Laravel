<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'super_cat_id', 'category_name', 'description', 'image', 'has_subcat', 'status', 'is_home', 'homelist'
    ];
}
