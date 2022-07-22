<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\ReportExport;
use App\Order;
use App\Country;
use App\State;
use App\City;
use App\User;
use Carbon\Carbon;
use Validator;
use DB;
use Auth;
use Excel;

class ReportController extends Controller
{
    public function orderreportdownload(Request $request)
    {
    	
    	if($request->has('fromdate')){
            $fromdate = $request->input('fromdate');
        }else{
            $fromdate = null;
        }  
        if($request->has('todate')){
            $todate = $request->input('todate');
        }else{
            $todate = null;
        }  
		if($request->has('country')){
            $country = $request->input('country');
        }else{
            $country = null;
        }  
		
        return Excel::download(new ReportExport($country,$fromdate,$country), 'report.xlsx');
    }
    public function orderreport(Request $request)
    {
        if($request->has('fromdate') && $request->has('todate') && $request->has('country')){
	        $query = Order::leftjoin('users','users.id','orders.user_id')
	                ->select('orders.*','users.name','users.email','users.mobile');
	                if($request->has('fromdate') && $request->has('todate')){
	                  $query= $query->whereBetween('orders.created_at', [$request->input('fromdate'), $request->input('todate')]);
	                }
	                if($request->has('country')){
	                  $query= $query->where('orders.currency',$request->input('country'));
	                }
	        $record = $query->where('orders.status','delivered')->paginate(20);
        }else{
        	$record = [];
        }
        return view('admin.order.report',compact('record'));
    }
}
