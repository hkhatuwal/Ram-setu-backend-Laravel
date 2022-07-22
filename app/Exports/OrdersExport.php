<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class OrdersExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $event_id;
    protected $referral;

	function __construct($event_id,$referral) {
	        $this->event_id = $event_id;
            $this->referral = $referral;
	}
    public function collection()
    {
    	$orders = Order::leftjoin('slot_masters','slot_masters.id','orders.event_id')
                ->leftjoin('users','users.ref_code','orders.ref_code')
                ->select('orders.id','orders.name','orders.email','orders.mobile','orders.order_id','orders.payment_id','orders.amount','orders.ref_code','users.name as Ref Name',DB::raw('DATE_FORMAT(slot_masters.start_time, "%h:%i %p") as StartDate'),DB::raw('DATE_FORMAT(slot_masters.end_time, "%h:%i %p") as EndTime'))
                ->where('orders.event_id',$this->event_id);
            if(!empty($this->referral)){
                $orders = $orders->where('orders.ref_code',$this->referral);
            }
    	$orderslist = $orders->get();
        return $orderslist;
    }
    public function headings() :array
    {
        return ["Id", "Name", "Email","Mobile", "Order Id", "Payment Id", "Amount", "Ref Code", "Ref Name", "Start Date", "End Time"];
    }
}
