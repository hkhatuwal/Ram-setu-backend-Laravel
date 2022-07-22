<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use Validator;
use Excel;
use DB;

class CouponController extends Controller
{
    
    public function download(Request $request)
    {
        return Excel::download(new CouponExport, 'coupons.xlsx');
    }
    public function index()
    {
        $record = DB::table('coupons')->orderby('id','DESC')->get();
        return view('admin.coupons.view',compact('record'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $autostatus = DB::select("SHOW TABLE STATUS LIKE 'coupons'");
        $autostatus_id = $autostatus[0]->Auto_increment;
        $coupon_code = strtoupper(Str::random(6)).''.$autostatus_id;
        return view('admin.coupons.add',compact('coupon_code'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'discount' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $coupon_code = DB::table('coupons')->where('coupon_code',$request->input('coupon_code'))->count();
            if($coupon_code > 0){
                Toastr::error('Coupon code already exist!','Warning');
                return redirect()->back(); 
            }else{
                
                $cateadd = DB::table('coupons')->insert([
                    'coupon_code' => $request->input('coupon_code'),
                    'title' => $request->input('title'),
                    'discount' => $request->input('discount'),
                    'status' => 'Yes',
                    'created_at' => date('Y-m-d H:i:s') 
                ]);
                Toastr::success('Coupon code successfully added!','Success');
                return redirect()->to('admin/coupon');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = DB::table('coupons')->find($id);
        return view('admin.coupons.add',compact('data'));
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
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'discount' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $cateadd = DB::table('coupons')->where('id',$id)->update([
                'title' => $request->input('title'),
                'discount' => $request->input('discount'),
                'updated_at' => date('Y-m-d H:i:s') 
            ]);
            Toastr::success('Coupon code successfully updated!','Success');
            return redirect()->to('admin/coupon');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('coupons')->where('id', $id)->delete();
        return response()->json(['status'=>true]);
    }
}
