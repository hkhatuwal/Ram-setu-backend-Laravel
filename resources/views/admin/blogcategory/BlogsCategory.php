<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogsCategory extends Model
{
    protected $fillable = [
        'blog_cat_name','blog_cat_url','status'
    ];
}
