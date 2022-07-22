<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class ReportExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $country;
    protected $fromdate;
    protected $todate;

	function __construct($country,$fromdate,$todate) {
	        $this->country = $country;
            $this->fromdate = $fromdate;
            $this->todate = $todate;
	}
    public function collection()
    {
        if(!empty($this->country) && !empty($this->fromdate) && !empty($this->todate)){
            $query = Order::leftjoin('users','users.id','orders.user_id')
                    ->select('orders.order_id','orders.order_date','orders.delivered_date','orders.currency','users.name','users.email','users.mobile','orders.subtotal','orders.shipping_charges','orders.grand_total');
                    // if(!empty($this->fromdate) && !empty($this->todate)){
                    //   $query= $query->whereBetween('orders.created_at', [$this->fromdate, $this->todate]);
                    // }
                    if(!empty($this->country)){
                      $query= $query->where('orders.currency',$this->country);
                    }
            $record = $query->where('orders.status','delivered')->get();
        }else{
            $record = [];
        }
    	//dd($record);
        return $record;
    }
    public function headings() :array
    {
        return ["Order Id", "Order Date", "Delivered Date", "Country", "Name","Email","Mobile", "Subtotal", "Shipping", "Grand Amount"];
    }
}
