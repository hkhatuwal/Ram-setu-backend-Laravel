@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
      <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="{{url('/admin/customers')}}">Customers</a></li>
            <li><a href="{{url('/admin/customers/'.$user->id)}}">{{ $user->name }}</a></li>
        </ul>
      </div>
    </div>
    <section class="panel">
      <header class="panel-heading">
        <div class="row">
          <div class="col-sm-6">    
                <h4 class="form-heading">{{ $user->name }}</h4>
          </div>      
         <div class="col-sm-6">
             <div class="pull-right"> 
                  <a href="{{url('/admin/customers')}}" class="btn btn-sm btn-info" >Back</a>
             </div> 
         </div>           
        </div>
      </header>
      <div class="panel-body">
            
        <div class="row">
          <div class="col-md-12" >
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <a class="nav-link active" id="customers-tab" data-toggle="tab" href="#customers" role="tab" aria-controls="customers" aria-selected="true">Customers</a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link" id="orders-tab" data-toggle="tab" href="#orders" role="tab" aria-controls="orders" aria-selected="false">Orders</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade active" id="customers" role="tabpanel" aria-labelledby="customers-tab">
                <div class="table-responsive" style="border: 1px solid #f2f0f0; padding:10px;">
                  <table class="table table-striped table-bordered table-hover" style="width: 100%;">
                      <tbody>
                          <tr>
                              <td>Full Name</td>
                              <td>{{ $user->name}}</td>
                          </tr>
                          <tr>
                              <td>Email Id</td>
                              <td>{{ $user->email}}</td>
                          </tr>
                          <tr>
                              <td>Mobile</td>
                              <td>{{ $user->mobile}}</td>
                          </tr>
                          <tr>
                              <td>Alter Mobile</td>
                              <td>{{ $user->alternate_mobile}}</td>
                          </tr>
                          <tr>
                              <td>Gender</td>
                              <td>{{ $user->gender}}</td>
                          </tr>
                          <tr>
                              <td>Country</td>
                              <td>{!! $user->country_code=='in'?'India':'Austrelia';!!}</td>
                          </tr>
                          <tr>
                              <td>City</td>
                              <td>{{ $user->city_name}}</td>
                          </tr>
                          <tr>
                              <td>Pincode</td>
                              <td>{{ $user->pincode}}</td>
                          </tr>
                          <tr>
                              <td>Area Name</td>
                              <td>{{ $user->area_name}}</td>
                          </tr>
                          <tr>
                              <td>Street Number</td>
                              <td>{{ $user->street_no}}</td>
                          </tr>
                          <tr>
                              <td>Address</td>
                              <td>{{ $user->address}}</td>
                          </tr>
                          
                      </tbody>
                  </table>
                </div>
              </div>
                <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                    <div class="table-responsive" style="border: 1px solid #f2f0f0; padding:10px;">
                      <table class="table table-striped table-bordered table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                  <th>Invoice Number</th>
                                  <th>Country</th>
                                  <th>Grand Amount</th>
                                  <th>Order Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->order_id}}</td>
                                    <td>{!! $user->currency=='in'?'India':'Austrelia';!!}</td>
                                    <td>{{ $order->grand_total}}</td>
                                    <td>{{ $order->order_date}}</td>
                                </tr>    
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </section>
</section>   

@endsection
