@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
          <div class="col-lg-12">
              <ul class="breadcrumb">
                  <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i>Home</a></li>
                  <li><a href="{{url('/admin/order-delivered')}}">Delivered Orders</a></li>
              </ul>
          </div>
    </div>
    <section class="panel">
          <div class="row">
            <div class="col-md-12 col-xs-6">
              <header class="panel-heading">Delivered Orders
                <a href="{{url('/admin/download/order-delivered')}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;">
                  <i class="fa fa-download" aria-hidden="true"></i>Download
                </a>
              </header>
            </div>
        </div>
      
        <div class="panel-body">
            <div class="adv-table editable-table ">
                <div class="space15"></div>
                 <div class="table-responsive" style="border: 1px solid #f2f0f0; padding:10px;">
                
                  <table class="table table-striped table-bordered table-hover" id="data_in_table" style="width: 100%;">
                      <thead>
                          <tr>
                              <th>Sr No.</th>
                              <th>Order No</th>
                              <th>Order Date</th>
                              <th>Customer Name</th>
                              <th>Email Id</th>
                              <th>Mobile</th>
                              <th>Country</th>
                              <th>Grand Amount</th>
                              <th>Payment Mode</th>
                              <th>Status</th>
                              <th style="width:10%;">Action</th>
                          </tr>
                      </thead>
                      <tbody>
                      <?php 
                        $i=1;
                        foreach ($orders as $order) {
                        ?>
                            <tr class='item<?= $order->id;?>'>
                              <td><?= $i;?></td>
                              <td>{{$order->order_id}}</td>
                              <td>{{$order->order_date}}</td>
                              <td>{{$order->name}}</td>
                              <td>{{$order->email}}</td>
                              <td>{{$order->mobile}}</td>
                              <td>{!! $order->currency=='in'?'India':'Austrelia'; !!}</td>
                              <td>{{$order->grand_total}}</td>
                              <td>{{$order->payment_mode}}</td>
                              <td>{{$order->status}}</td>
                              <td class="visible-md visible-lg">
                                  <div class="">
                                  <a  class="btn btn-sm btn-info show-tooltip act-no" title="Show Details" href="{{ URL::to('admin/order/'.$order->order_id) }}"  ><i class="fa fa-edit"></i></a>
                                  </div>
                              </td>
                          </tr>
                        <?php $i++; } ?>
                      </tbody>
                  </table>
            </div>
        </div>
      </div>
    </section>
  </section>
</section>   

@endsection
