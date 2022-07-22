<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Carbon\Carbon;
use App\SlotMaster;
use App\Event;
use App\User;
use App\Order;
use Session;
use DB;

class FrontendController extends Controller
{
	
	public function paymentsuccess(Request $request)
    {
           
        if(Session::has('cart')){
            $order = Session::get('cart');
            $event_id = Session::get('cart')['event_id'];
            //session()->forget('cart');
            $event = Event::find($event_id);
            dd($event);
            return view('front.success',compact('order','event')); 
        }else{
            return view('front.home'); 
        }
    	
    }
	public function checkpayment(Request $request)
    {

    	$api_key = "rzp_live_lIjOyMZK0DikUS"; 
    	$api_secret = "bYD1O15Ie2phP54EuaC1mN6L";
    	$api = new Api($api_key, $api_secret);

    	$cart = Session::get('cart');
    	$razorpay_payment_id = $request->payment_id;
    	$payment  = $api->payment->fetch($razorpay_payment_id); 
       
    	Order::insert([
                'order_id' => $cart['order_id'],
                'payment_id' => $razorpay_payment_id,
                'event_id' => $cart['event_id'],
                'event_mode' => 'paid',
                'amount' => $cart['amount'],
                'name' => $cart['name'],
                'email' => $cart['email'],
                'mobile' => $cart['mobile'],
                'slot_time' => $cart['slot_time'],
                'ref_code' => $cart['ref_code'],
                'status' => 'Yes',
                'created_at' => date('Y-m-d H:i:s')
	    	]);
    	return response()->json(['status'=>true]);
    }
	public function registerevent(Request $request)
    {
    	$data = array();
    	$tablestatus = DB::select("SHOW TABLE STATUS LIKE 'orders'");
        $autoid = $tablestatus[0]->Auto_increment;
        $order_id = rand(11111,99999).''.$autoid;

        $refcode = $request->input('ref_code');
        if($request->has('ref_code')){
            $existrefcode = User::where('ref_code',$refcode)->where('status','Yes')->first();
            if(!empty($existrefcode)){
                $referralcode = $refcode;
            }else{
            	$referralcode = null;
            }
        }else{
        	$referralcode = null;
        }
        $event_id = $request->input('event_id');
        $event = Event::find($event_id);
        if($event->is_payment == 'paid'){
            $data = [
	        	'order_id' => $order_id,
	        	'event_id' => $event_id,
	            'amount' => $request->input('amount'),
	            'name' => $request->input('name'),
				'email' => $request->input('email'),
				'mobile' => $request->input('mobile'),
				'slot_time' => $request->input('slot_time'),
				'ref_code' => $referralcode,
	        ];

	        $data['is_payable'] = 'paid';
		    $data['existstatus'] = 'Nothing';
	        Session::put('cart', $data);

            return response()->json($data);
        }else{
        	$existemail = Order::where('event_id',$event_id)->where('email',$request->input('email'))->first();
        	if(!empty($existemail)){
                $data = [
                    'is_payable' => 'free',
                    'existstatus' => 'Yes'
                ];
                Session::flash('message', 'Email address already registered.'); 
                Session::flash('alert-class', 'alert-danger'); 
                return redirect()->back()->withInput();
        	}else{
                $data = [
		        	'order_id' => $order_id,
		        	'event_id' => $event_id,
                    'event_mode' => 'free',
		            'amount' => null,
		            'name' => $request->input('name'),
					'email' => $request->input('email'),
					'mobile' => $request->input('mobile'),
					'slot_time' => $request->input('slot_time'),
					'ref_code' => $referralcode,
					'status' => 'Yes',
					'created_at' => date('Y-m-d H:i:s')
		        ];
		    	Order::insert($data);
		    	$data['is_payable'] = 'free';
		    	$data['existstatus'] = 'No';

                Session::put('cart', $data);
                return redirect()->to('/success');
        	}
        }

        
    }
    public function index()
    {
    	// $currentdate = Carbon::now();
     //    $events = Event::whereDate('start_date','<=',$currentdate)
     //            ->whereDate('end_date','>=',$currentdate)
     //            ->get();

        return view('front.home');
    }
    public function eventManager($event_title_url,$refcode=null)
    {
    	$inslot = array();
    	$currentdate = Carbon::now();
        $event = Event::where('title_url',$event_title_url)
                ->whereDate('start_date','<=',$currentdate)
                ->whereDate('end_date','>=',$currentdate)
                ->first();
        if(!empty($event)){
            $time_slot_ids = unserialize($event->time_slot_ids); 
	        $slotmaster = SlotMaster::wherein('id',$time_slot_ids)->get();   
	        foreach ($slotmaster as $key) {
	        	$start = Carbon::createFromFormat('H:i:s', $key->start_time)->format('h A');
	        	$end = Carbon::createFromFormat('H:i:s', $key->end_time)->format('h A'); 
	            $inslot[$key->id] = $start.'-'.$end;
	        }    
	        $event->availableslot = $inslot;

	        if(!empty($refcode)){
	            $valid = User::where('ref_code',$refcode)->where('status','Yes')->first();
	            if(!empty($valid)){
	                 $ref_code = $refcode;
	            }else{
	                $ref_code = null;
	            }
	        }else{
	            $ref_code = null;
	        }

	        return view('front.detail',compact('event','ref_code'));
        }else{
            return redirect()->to('/');
        }
    }
}
