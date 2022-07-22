<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class ShowProfile extends Controller
{
    //
    public function toasthome()
    {
        Toastr::success('Post added successfully','Success');
        Toastr::error('Post error successfully','error');
        Toastr::warning('Post warning successfully','warning');
        return view('welcome');
    }
    
}
