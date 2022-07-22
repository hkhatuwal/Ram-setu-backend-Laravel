<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
use \PDF;

class OrderController extends Controller
{
    public function changeOrderStatus(Request $request,$order_id)
    {
            $data = [
                $request->field => 'Yes',
                $request->field.'_date' => date('Y-m-d H:i:s')
            ];
            $status = OrdersStatus::where('base_id',$order_id)
                  ->update($data);
            $up = Order::where('order_id',$order_id)
                ->update([
                     'status' => $request->field,
                     'updated_at' => date('Y-m-d H:i:s')
                ]);

            $dddate = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('j-M-Y g:i A'); 
        return response()->json(['status' => 'true','date' => $dddate]);
    }
    public function RecentOrder(Request $request)
	{
	    $query = Order::leftjoin('orders_addresses as ordadd','ordadd.base_id','orders.order_id')
	        ->select('orders.*','ordadd.name','ordadd.email','ordadd.mobile','ordadd.address')
            ->where(function($que){
                $que->where('orders.status','<>','delivered')->where('orders.status','<>','cancelled');
            });
        $orders = $query->orderby('orders.id','desc')->get();
        
        return view('admin.order.pending',compact('orders'));
	}
	public function OrderHistory(Request $request)
	{
	    $orders = Order::leftjoin('orders_addresses as ordadd','ordadd.base_id','orders.order_id')
	        ->select('orders.*','ordadd.name','ordadd.email','ordadd.mobile','ordadd.address')
            ->where('orders.status','delivered')
            ->orderby('orders.id','desc')
            ->get();
        
	    return view('admin.order.delivered',compact('orders'));
	}
	public function CancelOrder(Request $request)
	{
	    $orders = Order::leftjoin('orders_addresses as ordadd','ordadd.base_id','orders.order_id')
	        ->select('orders.*','ordadd.name','ordadd.email','ordadd.mobile','ordadd.address')
            ->where('orders.status','cancelled')
            ->orderby('orders.id','desc')
            ->get();
        
	    return view('admin.order.cancel',compact('orders'));
	}
	public function OrderDetail3232(Request $request,$order_id)
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
	                    $que->where('status','<>','delivered')->where('status','<>','cancelled');
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
	                 $delivery_date = Carbon::createFromFormat('Y-m-d H:i:s', $ordstatus->delivered_date)->format('j M Y g:i A');  
	              }else{
	                 $delivery_date = null;
	              }
	              
	              $order[] = (object)[
	                  'id' => $value->id,
	                  'order_id' => $value->order_id,
	                  'order_date' => $value->order_date,
	                  'order_date_time' => $order_date_time,
	                  'delivery' => $this->DeliveryDetails($value->order_id),
	                  'product' => $items,
	                  'subtotal' => $value->subtotal,
	                  'grand_total' => $value->grand_total,
	                  'status' => $value->status,
	                  'delivery_date' => $delivery_date,
	                  'rating' => $this->OrderRating($value->order_id,$value->user_id)
	                 ];       
	        }         
	        return $order;
	}
	
	public function DownloadOrderDetail(Request $request,$order_id)
    {
	    $user = Auth::user(); 
	    $order = array(); 
	    $value = Order::where('order_id',$order_id)->first();
	    $order_date_time = Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->format('j M Y g:i A');  
	    $items = OrdersItem::where('base_id',$order_id)->get(); 
	    if($value->currency == 'in'){
	        $country = 'India';
	        $currency = 'INR';
	    }else{
	        $country = 'Austrelia';
	        $currency = 'Doller';
	    }
	    $order = (object)[
	            'id' => $value->id,
	            'order_id' => $value->order_id,
	            'order_date' => $value->order_date,
	            'order_date_time' => $order_date_time,
	            'country'=>$country, 
	            'currency'=>$currency, 
	            'delivery' => $this->DeliveryDetails($value->order_id),
	            'product' => $items,
	            'shipping_charges' => $value->shipping_charges,
	            'subtotal' => $value->subtotal,
	            'grand_total' => $value->grand_total,
	            'status' => $value->status,
	            'rating' => $this->OrderRating($value->order_id,$value->user_id),
	            'ordstatus' => $this->OrdStMngt($value->order_id)
	        ];
	    //return view('invoice', compact('order'));    
	   $pdf = \PDF::loadView('invoice',compact('order'));
       return $pdf->download('invoice.pdf');    
    } 
    public function OrderDetail(Request $request,$order_id)
    {
	    $user = Auth::user(); 
	    $order = array(); 
	    $value = Order::where('order_id',$order_id)->first();
	    $order_date_time = Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->format('j M Y g:i A');  
	    $items = OrdersItem::where('base_id',$order_id)->get(); 
	    if($value->currency == 'in'){
	        $country = 'India';
	        $currency = 'INR';
	    }else{
	        $country = 'Austrelia';
	        $currency = 'Doller';
	    }
	    $order = (object)[
	            'id' => $value->id,
	            'order_id' => $value->order_id,
	            'order_date' => $value->order_date,
	            'order_date_time' => $order_date_time,
	            'country'=>$country, 
	            'currency'=>$currency, 
	            'delivery' => $this->DeliveryDetails($value->order_id),
	            'product' => $items,
	            'shipping_charges' => $value->shipping_charges,
	            'subtotal' => $value->subtotal,
	            'grand_total' => $value->grand_total,
	            'status' => $value->status,
	            'rating' => $this->OrderRating($value->order_id,$value->user_id),
	            'ordstatus' => $this->OrdStMngt($value->order_id)
	        ];
	    //dd($order);     
        return view('admin.order.show',compact('order'));
    } 
    public function OrdStMngt($order_id=null)
    {
         
           $osm = OrdersStatus::where('base_id',$order_id)
                  ->first();
            if(!empty($osm->pending_date)){
               $pendingdate = Carbon::createFromFormat('Y-m-d H:i:s', $osm->pending_date)->format('j-M-Y g:i A'); 
             }else{
               $pendingdate = null; 
             }   
              
            if(!empty($osm->confirm_date)){
               $confirmdate = Carbon::createFromFormat('Y-m-d H:i:s', $osm->confirm_date)->format('j-M-Y g:i A'); 
            }else{
               $confirmdate = null; 
            }   
            if(!empty($osm->shipped_date)){
               $shippeddate = Carbon::createFromFormat('Y-m-d H:i:s', $osm->shipped_date)->format('j-M-Y g:i A'); 
            }else{
               $shippeddate = null; 
            }   
            if(!empty($osm->outofdelivery_date)){
              $outofdeliverydate = Carbon::createFromFormat('Y-m-d H:i:s', $osm->outofdelivery_date)->format('j-M-Y g:i A'); 
            }else{
              $outofdeliverydate = null;  
            } 
            if(!empty($osm->delivered_date)){
              $deliverydate = Carbon::createFromFormat('Y-m-d H:i:s', $osm->delivered_date)->format('j-M-Y g:i A'); 
            }else{
              $deliverydate = null;  
            }  
            $track = (object)[
                    'pending' => $osm->pending,
                    'pending_date' => $pendingdate,
                    'confirm' => $osm->confirm,
                    'confirm_date' => $confirmdate,
                    'shipped' => $osm->shipped,
                    'shipped_date' => $shippeddate,
                    'outofdelivery' => $osm->outofdelivery,
                    'outofdelivery_date' => $outofdeliverydate,
                    'delivered' => $osm->delivered,
                    'delivered_date' => $deliverydate
                 ];
        
         return $track;
       
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
