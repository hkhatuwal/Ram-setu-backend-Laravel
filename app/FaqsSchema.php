<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FaqsSchema extends Model
{
    protected $fillable = [
        'question','answer','question_hindi','answer_hindi','status'
    ];
}
