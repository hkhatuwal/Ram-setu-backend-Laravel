<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\ProductMaster;
use App\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $record = (object)[];
        $record->product_counter = ProductMaster::count();
        $record->user_counter = User::where('roles','guest')->count();
        $record->active_order = '10';
        $record->delivered_order = '9';
        
        $date = Carbon::now();

            
        $todaylisting = ProductMaster::leftjoin('users', 'users.id', 'product_masters.user_id')
            ->leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
            ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
            ->select('product_masters.*','categories.category_name','super_categories.super_cat_name','users.name',
                'users.email','users.mobile','users.roles','users.address','users.pincode'
            )
            ->where('product_masters.created_at',$date)
            ->get();
            
        $closelisting = ProductMaster::leftjoin('users', 'users.id', 'product_masters.user_id')
            ->leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
            ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
            ->select('product_masters.*','categories.category_name','super_categories.super_cat_name','users.name',
                'users.email','users.mobile','users.roles','users.address','users.pincode'
            )
            ->where('product_masters.bid_close_date',$date)
            ->get(); 
            
        return view('admin.dashboard.view',compact('record','closelisting','todaylisting'));
    }
}
