<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\State;
use App\User;
use DB;

class CommanController extends Controller
{
    
    public function CustomerList(Request $request)
    {
    	$customers = User::where('roles','customer')->get();
    	    
        return view('admin.customer.view',compact('customers'));
    }
    
    public function sentnotification(Request $request,$user_id)
    {
        $data = DB::table('users_messages')->insert([
                    'user_id' => $user_id,
                    'message' => $request->input('message'),
                    'status' => 'No',
                    'created_at' => date('Y-m-d H:i:s')
            ]);
        
        Toastr::success('Message sent successfully!','Success');
        $previous = $request->input('previous_url');
        return redirect()->to($previous);    
    }
    public function getstate(Request $request)
    {
    	$state = State::where('country_id',$request->input('country_id'))
    	        ->pluck('state_name','id')
    	        ->toArray();
    	if(count($state) > 0){
            $status = 'true';
    	}else{
            $status = 'false';
    	}         
        return response()->json([ 'status' => $status,'statelist' => $state ]);
    }
    public function CheckStatus($table,$id)
    {
        $table = "App\\".$table;
        $record = $table::find($id);
        if($record->status =='Yes')
        {   
          $update = $table::where('id',$id)
                  ->update([
                    'status' => 'No',
                    'updated_at' => date('Y-m-d H:i:s') 
                     ]);
            return "false";
        } else {
          $update = $table::where('id',$id)
                 ->update([
                  'status' => 'Yes',
                  'updated_at' => date('Y-m-d H:i:s') 
                   ]);
            return "true";
        }  
    }
    public function ShippingCharges(Request $request)
    {
        if ($request->isMethod('get'))
        {
            
            $aushipping = DB::table('shippings')->select('shippings.id as shippings_id','shippings.country_code','shippings.charges','shippings.status')
              ->where('country_code','au')->first();
            $aushipping->id = 'Austrelia'; 
            $aushipping->country_id = 'Austrelia'; 
            $aushipping->state_name = 'Austrelia';      
    
            $shipping = DB::table('states')->leftjoin('shippings','shippings.state_id','states.id')
                        ->select('states.id','states.country_id','states.state_name','shippings.id as shippings_id','shippings.country_code','shippings.charges','shippings.status')
                        ->get();
            $collection = collect($shipping);
            $collection->prepend($aushipping);

            return view('admin.shipping.view',compact('collection'));
        }
        if ($request->isMethod('post'))
        {

            $shippings_id = $request->input('shippings_id');
            $charges = $request->input('charges');
            $state_id = $request->input('state_id');
            
            foreach($shippings_id as $shipkey=>$shipvalue){
                if(!empty($shipvalue)){
                    DB::table('shippings')->where('id',$shipvalue)
                    ->update([
                           'charges'=>$charges[$shipkey],
                           'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    
                }else{
                    DB::table('shippings')->insert([
                           'country_code' => 'in',
                           'state_id' => $state_id[$shipkey],
                           'charges'=>$charges[$shipkey],
                           'created_at' => date('Y-m-d H:i:s')
                        ]);
                }
            }
            
            return redirect()->to('admin/shipping-charges');
           
        }
    }
}
