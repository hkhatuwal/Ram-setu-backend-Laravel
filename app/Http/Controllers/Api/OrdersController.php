<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MailerTraits;
use App\ProductMaster;
use App\ProductAttribute;
use App\ProductImage;
use App\Order;
use App\OrdersAddress;
use App\OrdersFeedback;
use App\OrdersItem;
use App\OrdersStatus;
use App\Country;
use App\State;
use App\City;
use App\UsersAddress;
use App\User;
use Carbon\Carbon;
use Validator;
use DB;
use Auth;


class OrdersController extends Controller
{
    use MailerTraits;
    
    public function postOrdersByApp(Request $request)
	{
	    
		$user = Auth::user(); 
	    if(!empty($user->id)){
	        $validator = Validator::make($request->all(), [
	        	    'name' => 'required',
	        	    'country_code' => 'required',
	                'city_id' => 'required',
	                'address' => 'required',
	                'transaction_id' => 'required',
	                'payment_mode' => 'required',
	                'item' => 'required|array|min:1',
	                'item.*.product_id' => 'integer',
	                //'item.*.attribute_id' => 'integer',
	                'item.*.quantity' => 'integer',
	          ]);
	        if ($validator->fails()) {
	            $errors = $validator->errors();
	            return response()->json($errors);
	        } else {
	        	$itemsub_total = array();

	        	$tableord = DB::select("SHOW TABLE STATUS LIKE 'orders'");
	            $generatedidd = $tableord[0]->Auto_increment;
	            $generated_order_id = 'ORDER'.$generatedidd;
	        
	            if($request->input('country_code')=='in'){
			    	$country_name = 'India';
			    	$state = State::find($request->input('state_id'));
			    	$state_id = $request->input('state_id');
			    	$state_name = $state->state_name;
			    	$shipcharge = DB::table('shippings')
                       ->where('country_code','in')
                       ->where('state_id',$state_id)
                       ->where('status','Yes')
                       ->first();
                   
			    }else{
			        $shipcharge = DB::table('shippings')
                       ->where('country_code','au')
                       ->where('status','Yes')
                       ->first();
			    	$country_name = 'Australia';
			    	$state_id = null;
			    	$state_name = null;
			    }
			     
			    if($request->has('city_id')){
			        $city = City::find($request->input('city_id'));
			        $city_id = $request->input('city_id');
                    $city_name = $city->city_name;
                }else{
                    $city_id = null;
                    $city_name = null;
                }
	            
                if($request->has('pincode')){
                    $pincode = $request->input('pincode');
                }else{
                    $pincode = null;
                }
                if($request->has('street_no')){
                    $street_no = $request->input('street_no');
                }else{
                    $street_no = null;
                }
                if($request->has('area_name')){
                    $area_name = $request->input('area_name');
                }else{
                    $area_name = null;
                }
	            $addressarray = [
	                'base_id' => $generated_order_id,
	              	'user_id' => $user->id,
	                'name' => $request->input('name'),
	                'email' => $user->email,
	                'mobile' => $user->mobile,
	                'country' => $country_name,
	                'state' => $state_name,
	                'city' => $city->city_name,
	                'pincode' => $pincode,
	                'street_no' => $street_no,
	                'area_name' => $area_name,
	                'address' => $request->input('address'),
	                'created_at' => date('Y-m-d H:i:s')
	            ];
	            User::where('id', $user->id)
                    ->update([
                        'name' => $request->input('name'),
                        'state_id' => $state_id,
                        'city_id' => $city_id,
                        'pincode' => $pincode,
                        'area_name' => $area_name,
                        'street_no' => $street_no,
                        'address' => $request->input('address'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ]);
	            $orditemarray = [];
	            foreach($request['item'] as $val)
	            {
	            	$productmaster = ProductMaster::find($val['product_id']);
				    if($request->input('country_code')=='in'){
				    	$itemsellprice = $productmaster->inr_sell_price;
                        $itemsubtotal = $itemsellprice * $val['quantity'];
				    }else{
				    	$itemsellprice = $productmaster->doller_sell_price;
                        $itemsubtotal = $itemsellprice * $val['quantity'];
				    }
				    
				    $itemsub_total[] = $itemsubtotal;           
	            	$orditemarray[] = [
	                   'base_id' => $generated_order_id,
	                   'product_id' => $val['product_id'], 
	                   'product_name' => $productmaster->product_name,
	                   'description' => $productmaster->description,
	                   'image' => $productmaster->image,
	                   'currency' => $request->input('country_code'),
	                   'sell_price' => $itemsellprice,
	                   'qty' => $val['quantity'],
	                   'subtotal' => $itemsubtotal,
	                   'created_at' => date('Y-m-d H:i:s')
	            	];
	            	
	            	OrdersItem::insert($orditemarray);
	            }
	            if(!empty($shipcharge)){
                    $shipping_charge = $shipcharge->charges;
                }else{
                    $shipping_charge = null;
                }  
                
	            $total_item = count($itemsub_total); 
	            $subtotal_amount = array_sum($itemsub_total);
	            $grand_total = array_sum($itemsub_total)+$shipping_charge;

	            

	            $orderarray = [
	                  'order_id' => $generated_order_id, 
	                  'user_id' => $user->id, 
	                  'transaction_id' => $request->input('transaction_id'),
	                  'price' => $subtotal_amount,
	                  'coupon_code'  => Null,
	                  'discount_percent' => Null,
	                  'discount' => Null,
	                  'subtotal' => $subtotal_amount,
	                  'gst_percent' => Null,
	                  'gst' => Null,
	                  'shipping_charges' => $shipping_charge,
	                  'grand_total' => $grand_total,
	                  'currency' => $request->input('country_code'),
	                  'payment_mode' => $request->input('payment_mode'),
	                  'payment_by' => 'Null',
	                  'status'=> 'Pending',
	                  'order_date' => date('Y-m-d'),
	                  'created_at' => date('Y-m-d H:i:s')
	            ];
	           
	            Order::insert($orderarray);
	            OrdersAddress::insert($addressarray);
	            OrdersStatus::insert([
	                'base_id' => $generated_order_id,  
	                'pending' => 'Yes',
	                'pending_date' => date('Y-m-d'),
	                'created_at' => date('Y-m-d H:i:s')
	             ]);
	            $order = (object)[
	                  'order' => (object)$orderarray,
	                  'delivery'=> (object)$addressarray,
	                  'product'=> (object)$orditemarray
	               ];
	               
	            $mailstatus = $this->recentOrdermail($order);
	            
	            return response()->json([
		            'responce' => 'Your Order has been place successfully',
		            'order_id' => $generated_order_id,
		            'user_name' => $user->name,
		            'status'=> true
		        ]);
		    }
		}
	}
	public function RecentOrder(Request $request)
	{
	    $user = Auth::user(); 
	    $order = $this->getOrderRecord('Pending',$user->id);
	    return response()->json($order);
	}
	public function OrderHistory(Request $request)
	{
	      $user = Auth::user(); 
	      $order = $this->getOrderRecord('Delivered',$user->id);
	      return response()->json($order);
	}
	public function CancelOrder(Request $request)
	{
	      $user = Auth::user(); 
	      $order = $this->getOrderRecord('Cancelled',$user->id);
	      return response()->json($order);
	}
	public function getOrderRecord($status, $user_id)
	{
	      
	        $order = array(); 
	        if($status == 'Pending'){
	            $query = Order::where('user_id',$user_id)
	                 ->where(function($que){
	                    $que->where('status','<>','Delivered')->where('status','<>','Cancelled');
	                 });
	            $orderlist = $query->orderby('id','desc')->get();
	        }else{
	            $orderlist = Order::where('user_id',$user_id)
	                   ->where('status',$status)
	                   ->orderby('id','desc')->get();
	        }
	        foreach ($orderlist as $value) {
	              $order_date_time = Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->format('j M Y g:i A');  
	              $items = OrdersItem::where('base_id',$value->order_id)->get();
	              if($value->status == 'delivered'){
	                 $ordstatus = OrdersStatus::where('base_id',$value->order_id)->first();
	                 $delivery_date_time = Carbon::createFromFormat('Y-m-d H:i:s', $ordstatus->delivered_date)->format('j M Y g:i A'); 
	                 $delivery_date = Carbon::createFromFormat('Y-m-d H:i:s', $ordstatus->delivered_date)->format('Y-m-d');
	              }else{
	                 $delivery_date = null;
	                 $delivery_date_time = null;
	              }
	              
	              $order[] = (object)[
	                  'id' => $value->id,
	                  'order_id' => $value->order_id,
	                  'order_date' => $value->order_date,
	                  'order_date_time' => $order_date_time,
	                  'delivery' => $this->DeliveryDetails($value->order_id),
	                  'product' => $items,
	                  'subtotal' => $value->subtotal,
	                  'shipping_charges' => $value->shipping_charges,
	                  'grand_total' => $value->grand_total,
	                  'status' => $value->status,
	                  'delivery_date' => $delivery_date,
	                  'delivery_date_time' => $delivery_date_time,
	                  'rating' => $this->OrderRating($value->order_id,$value->user_id)
	                 ];       
	        }         
	        return $order;
	}
	
    public function ViewOrder($order_id)
    {
	    $user = Auth::user(); 
	    $order = array(); 
	    $value = Order::where('order_id',$order_id)->first();
	    $order_date_time = Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->format('j M Y g:i A');  
	    $items = OrdersItem::where('base_id',$order_id)->get(); 
	    $order = (object)[
	            'id' => $value->id,
	            'order_id' => $value->order_id,
	            'order_date' => $value->order_date,
	            'order_date_time' => $order_date_time,
	            'delivery' => $this->DeliveryDetails($value->order_id),
	            'product' => $items,
	            'subtotal' => $value->subtotal,
	            'grand_total' => $value->grand_total,
	            'status' => $value->status,
	            'rating' => $this->OrderRating($value->order_id,$value->user_id),
	            'download_invoice' => $value->grand_total,
	        ]; 
	         
        return response()->json($order);
    } 
    public function trackorder($order_id)
	{
	    $user = Auth::user(); 
	    if(!empty($user)){
	      $data = OrdersStatus::where('base_id',$order_id)->first();
	      $staticarray = [
	          'pending'=> "Order has been place", 
	          'confirm'=> "Order has confirm", 
	          'shipped'=> "Order has shipped",
	          'outofdelivery'=> "Order Out of Delivery",
	          'delivered'=> "Order has Delivered"
	      ];
	      $tracking = array();
	      foreach ($staticarray as $key => $value) {
	        $tdate = $key.'_date';
	        if($data->$key == 'Yes'){
	            $date = Carbon::createFromFormat('Y-m-d H:i:s', $data->$tdate)->format('l j M Y');
	            $time = Carbon::createFromFormat('Y-m-d H:i:s', $data->$tdate)->format('g:i A'); 
	        }else{
	            $date = null;
	            $time = null;
	        }
	        $tracking[] = (object)[
	            'status' => $key,
	            'date'=> $date,
	            'time'=> $time,
	            'track' => $data->$key,
	            'message' => $value,
	        ];
	      }
	      return response()->json(['status'=>true,'data'=>$tracking]);
	    }
	}
	public function orderFeedback(Request $request)
	{
	    $user = Auth::user(); 
	    if(!empty($user)){
	        $validator = Validator::make($request->all(), [
	              'order_id' => 'required',
	              'rate' => 'required',
	              'message' => 'nullable'
	        ]);
	        if ($validator->fails()) {
	            $errors = $validator->errors();
	            return response()->json(['status'=>false,'error'=>$errors]);
	        } else {
	            $order_id = $request->input('order_id');
	            $trueorder = Order::where('order_id',$order_id)->where('status','Delivered')->first();
	            if(!empty($trueorder)){
	               $feedback = OrdersFeedback::where('base_id',$order_id)->first(); 
	               if(!empty($feedback)){
	                 return response()->json(['status'=>false,'message'=>'Feedback already noted!']);
	               }else{
	                  OrdersFeedback::insert([
	                     'base_id' => $order_id, 
	                     'user_id' => $user->id, 
	                     'rate' => $request->input('rate'),
	                     'message' => $request->input('message'),
	                     'status' => 'No'
	                  ]);
	                  return response()->json(['status'=>true,'message'=>'Your feedback save successfully. Thanks you for feedback!']);
	               }
	            }else{
	               return response()->json(['status'=>false,'message'=>'Order not delivered yet!']);
	            }
	        }
	    }
	}
	public function DeliveryDetails($order_id)
	{
	    $delivery = OrdersAddress::where('base_id',$order_id)->first();
	    unset($delivery->id);
	    unset($delivery->base_id);
	    unset($delivery->landmark);
	    unset($delivery->pincode);
	    unset($delivery->created_at);
	    unset($delivery->updated_at);
	    return $delivery; 
	}
	public function OrderRating($order_id,$user_id)
	{
	    $ratingarray = array();
	    $rating = OrdersFeedback::where('base_id',$order_id)->first();  
	    if(!empty($rating)){
	           $ratingarray = (object)[
	               'rate' => $rating->rate,
	               'message' => $rating->message
	               ];
	    }        
	    return $ratingarray;   
	}
}
