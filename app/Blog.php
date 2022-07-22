<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'category_id','title','blog_url','keyword','designation','author_name','banner','description','status'
    ];
}
