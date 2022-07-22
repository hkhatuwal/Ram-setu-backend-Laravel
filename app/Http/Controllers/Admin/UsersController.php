<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Order;
use Carbon\Carbon;
use Validator;
use DB;
use Auth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $order = DB::raw("(select user_id,COUNT(CASE WHEN `status` LIKE '%pending%' THEN 1 END) as order_pending, COUNT(CASE WHEN `status` LIKE '%process%' THEN 1 END) as order_process, COUNT(CASE WHEN `status` LIKE '%delivered%' THEN 1 END) as order_delivered, COUNT(CASE WHEN `status` LIKE '%cancel%' THEN 1 END) as order_cancel, COUNT(`id`) as order_total from `orders` GROUP BY `user_id`) as ordermaster");

        $users = User::leftjoin($order,'ordermaster.user_id','users.id')
                ->select('users.*','ordermaster.order_pending','ordermaster.order_process','ordermaster.order_delivered','ordermaster.order_cancel','ordermaster.order_total')
                ->where('users.roles','customer')
                ->orderBy('users.id', 'DESC')
                ->get();

        return view('admin.users.view',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('roles','customer')->where('id', $id)->first();
        $orders = Order::where('user_id', $id)->get();
        return view('admin.users.show',compact('user','orders')); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
