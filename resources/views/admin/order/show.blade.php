@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
          <div class="col-lg-12">
              <ul class="breadcrumb">
                  <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i>Home</a></li>
                  <li><a href="#">Order Details</a></li>
              </ul>
          </div>
    </div>
    <section class="panel">
          <div class="row">
            <div class="col-md-12 col-xs-6">
              <header class="panel-heading">Order No. {!! $order->order_id; !!}
                <a href="{{url('/admin/download/order/'.$order->order_id)}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;">
                  <i class="fa fa-arrow-left"></i> Invoice
                </a>
              </header>
            </div>
        </div>
      
        <div class="panel-body">
            <div class="adv-table editable-table ">
                <section class="panel" style="padding: 10px;">
         
      <div class="panel-body">
        <div class="adv-table editable-table ">
          <div class="row">
            <div class="col-sm-12 order-title" >
               <div class="col-md-4">
                   <strong>Order Date # {{$order->order_date_time}} </strong>
               </div>  
               <div class="col-md-4">
                   <strong></strong>
               </div> 
               <div class="col-md-4">
                   <strong class="pull-right">Total Amount : {{$order->grand_total}}</strong>
               </div> 
            </div>
          </div>   
          <div class="row"> 
            <div class="col-sm-12" style="background-color: #f5f0f063;border: 1px solid #2f71ab;border-bottom: none;padding: 15px;" >
             <p></p>
              <div class="col-md-4">
                {{ Form::label('Delivery Address', 'Delivery Address') }}<br>
                   <p>User Name : {!! $order->delivery->name;!!}</p>
                   <p>Email : {!! $order->delivery->email;!!}</p>
                   <p>Mobile : {!! $order->delivery->mobile;!!}</p>
                   @if(!empty($order->delivery->alternate_mobile))
                       <p>Alternate Mobile : {!! $order->delivery->alternate_mobile;!!}</p>
                   @endif
                   <p>{!! $order->delivery->country.' '.$order->delivery->state.' '.$order->delivery->city.' '.$order->delivery->pincode;!!}</p>
                    @if(!empty($order->delivery->area_name))
                       <p>Area Name : {!! $order->delivery->area_name;!!}</p>
                    @endif
                    @if(!empty($order->delivery->street_no))
                       <p>Street No : {!! $order->delivery->street_no;!!}</p>
                    @endif   
                   <p>Address : {!! $order->delivery->address;!!}</p>
              </div>
              <div class="col-md-4">
                 
               </div>
              <div class="col-md-4">
                  {{ Form::label('Order Total', 'Order Total') }}<br>
                  <p>Order Subtotal ({{$order->currency}}) :<span class="pull-right">{{$order->subtotal}}</span></p>
                  <p>Discount ({{$order->currency}}) : <span class="pull-right">33</span></p>
                  <hr>
                  <p>Grand Total  ({{$order->currency}}):<span class="pull-right">{{$order->grand_total}}</span></p> 
              </div> 
            </div>
          </div>
          <div id="statusstatus">        
            <div class="row" style="background-color: #f5f0f063;border: 1px solid #2f71ab;padding: 15px;">
              <div class="col-md-12"> 
                <h4>Manage Status</h4>
              </div> 
              <div class="col-md-2">
                 <div class="form-check pending">
                    <input type="checkbox" class="form-check-input manageOrderstatus" order="{!! $order->order_id;!!}" value="pending" id="pending" {{$order->ordstatus->pending == 'Yes' ? 'disabled checked':''}}>
                    <label class="form-check-label" for="materialUnchecked"> &nbsp;&nbsp;Pending</label>
                </div>
                <p>{{ $order->ordstatus->pending_date }}</p>
              </div>      
              <div class="col-md-2 confirm">
                 <div class="form-check">
                    <input type="checkbox" class="form-check-input manageOrderstatus" order="{!! $order->order_id;!!}" value="confirm" id="confirm" {{$order->ordstatus->confirm == 'Yes' ? 'disabled checked':''}}>
                    <label class="form-check-label" for="materialUnchecked"> &nbsp;&nbsp; Process</label>
                </div>
                @if($order->ordstatus->confirm == 'Yes')
                 <p>{{ $order->ordstatus->confirm_date }}</p>
                @endif
              </div>
              <div class="col-md-3 shipped"> 
                <div class="form-check">
                    <input type="checkbox" class="form-check-input manageOrderstatus" order="{!! $order->order_id;!!}" value="shipped" id="shipped" {{$order->ordstatus->shipped == 'Yes' ? 'disabled checked':''}} {{$order->ordstatus->confirm == 'No' ? 'disabled':''}}>
                    <label class="form-check-label" for="materialUnchecked"> &nbsp;&nbsp;Shipped</label>
                </div>
                <div id="salesmsg">
                @if($order->ordstatus->shipped == 'Yes')
                     <p>{{ $order->ordstatus->shipped_date }}</p>
                @endif
                </div>
              </div>
              <div class="col-md-3 outofdelivery"> 
                <div class="form-check">
                    <input type="checkbox" class="form-check-input manageOrderstatus" order="{!! $order->order_id;!!}" value="outofdelivery" id="outofdelivery" {{$order->ordstatus->outofdelivery == 'Yes' ? 'disabled checked':''}} {{$order->ordstatus->shipped == 'No' ? 'disabled':''}}>
                    <label class="form-check-label" for="materialUnchecked"> &nbsp;&nbsp;Out of delivery</label>
                </div>
                @if($order->ordstatus->outofdelivery == 'Yes')
                 <p>{{ $order->ordstatus->outofdelivery_date }}</p>
                @endif
              </div>
              <div class="col-md-2 delivered"> 
                <div class="form-check">
                    <input type="checkbox" class="form-check-input manageOrderstatus" order="{!! $order->order_id;!!}" value="delivered" id="delivered" {{$order->ordstatus->delivered == 'Yes' ? 'disabled checked':''}} {{$order->ordstatus->outofdelivery == 'No' ? 'disabled':''}}>
                    <label class="form-check-label" for="materialUnchecked"> &nbsp;&nbsp;Delivered</label>
                </div>
                @if($order->ordstatus->delivered == 'Yes')
                 <p>{{ $order->ordstatus->delivered_date }}</p>
                @endif
              </div>
            </div>
          </div>
          
         
           <div class="row" style="background-color: #f5f0f063; border: 1px solid #2f71ab;border-top: none;" >
           <div class="col-md-12">
            
                <div class="col-md-12 itemwisediv" id="" style="padding: 5px; margin-bottom: 7px;">
               <div class="col-md-12"> 
               <br> 
                   <table class="table">
                    <thead>
                      <tr>
                        <th>Product Name</th>
                        <th>Price ( {{$order->currency}} )</th>
                        <th>Quantity</th>
                        <th>Total</th>
                      </tr>
                    </thead>
                    <tbody>
                    @foreach($order->product as $main)
                      <tr>
                        <td>{{ $main->product_name }}</td>
                        <td>{{$main->sell_price}}</td>
                        <td>{{$main->qty}}</td>
                        <td>{{$main->subtotal}}</td>
                      </tr>
                    @endforeach 
                    </tbody>
                    <tfoot>
                      <tr>
                        <td></td>
                        <td></td>
                        <td>Sub Total ({{$order->currency}})</td>
                        <td>{{$order->subtotal}}</td>
                      </tr>
                      <tr>
                        <td style="border-top: 1px solid #f5f5f5 !important;"></td>
                        <td style="border-top: 1px solid #f5f5f5 !important;"></td>
                        <td>Discount ({{$order->currency}})</td>
                        <td>{{$order->shipping_charges}}</td>
                      </tr>
                      <tr>
                        <td style="border-top: 1px solid #f5f5f5 !important;"></td>
                        <td style="border-top: 1px solid #f5f5f5 !important;"></td>
                        <td>Grand Total ({{$order->currency}})</td>
                        <td>{{$order->grand_total}}</td>
                      </tr>
                    </tfoot>
                  </table>
                 </div>
                </div>
                <br>
           </div>  
          </div>  
         </div>
        </div>
      </div>
    </section>
                 
            </div>
        </div>
      </div>
    </section>
  </section>
</section>   
<script type="text/javascript">
$(document).ready(function(){   
  $('.manageOrderstatus').on('change',function(){
      var order = $(this).attr('order');
      var vall = $(this).val();
      var status = $(this).attr('id');
      if(status == 'confirm'){
        $('#shipped').prop("disabled", false);
      }
      if(status == 'shipped'){
        $('#outofdelivery').prop("disabled", false);
      }
      if(status == 'outofdelivery'){
        $('#delivered').prop("disabled", false);
      }
      $.ajax({
          type:"GET",
          url:"{{url('admin/change-order-status')}}/"+order+"?field="+vall,
          success:function(data){
            $('.'+status).append("<p>"+data.date+"</p>");
            toastr.success("Order status change successfully!");
            $('#'+status).prop("disabled", true);
          }
      });
  });
});  
</script>
@endsection
