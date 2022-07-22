@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
          <div class="col-lg-12">
              <ul class="breadcrumb">
                  <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i>Home</a></li>
                  <li><a href="{{url('/admin/order-report')}}">Orders Report</a></li>
              </ul>
          </div>
    </div>
    <section class="panel">
          <div class="row panel-heading">
              <div class="col-md-2">
                <h5>Orders Report</h5>
              </div>
              <div class="col-md-8">
                {!! Form::model(request()->query(),['url'=>['admin/order-report'], 'class' => '','method' => 'POST','id' =>'filter_form']) !!}
                {{ csrf_field() }}
                  <div class="row">
                      <div class="col-sm-2"> 
                          {!!Form::select('country',[null => ' Country', 'in'=>'India', 'au'=>'Austrelia'], null,['class'=>'form-control']) !!} 
                      </div>
                      <div class="col-sm-3"> 
                          {!! Form::text('fromdate',null, array('class' => 'form-control', 'id' => 'fromdate', 'placeholder' => 'From Date')) !!}
                      </div>
                      <div class="col-sm-3"> 
                          {!! Form::text('todate', null, array('class' => 'form-control', 'id' => 'todate', 'placeholder' => 'To Date')) !!}
                      </div> 
                      <div class="col-sm-2"> 
                          <strong class="btn btn-raised btn-warning" id="filterwithinput">Search</strong> 
                      </div> 
                      <div class="col-sm-2"> 
                        @if(count(request()->query()) >= 2)
                          <a href="{{ url('admin/order-report') }}" class="btn btn-raised btn-info">Clear Search
                          </a>
                        @endif
                     </div> 
                  </div>  
                {{Form::close()}}
              </div>
              <div class="col-md-2">
                <a href="{{url('/admin/order-report-download?'.Request()->getQueryString())}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;"><i class="fa fa-download" aria-hidden="true"></i>Download
                </a>
              </div>
            
        </div>
      
        <div class="panel-body">
            <div class="adv-table editable-table ">
                <div class="space15"></div>
                @if(!empty($record))
                 <div class="table-responsive" style="padding:10px;">
                
                  <table class="table table-striped table-bordered table-hover" id="" style="width: 100%;">
                      <thead>
                          <tr>
                              <th>Sr No.</th>
                              <th>Order No</th>
                              <th>Order Date</th>
                              <th>Customer Name</th>
                              <th>Email Id</th>
                              <th>Mobile</th>
                              <th>SubTotal</th>
                              <th>Shipping</th>
                              <th>Grand Amount</th>
                              <th>Payment Mode</th>
                          </tr>
                      </thead>
                      <tbody>
                      <?php 
                        $i=1;
                        foreach ($record as $order) {
                        ?>
                            <tr class='item<?= $order->id;?>'>
                              <td><?= $i;?></td>
                              <td>{{$order->order_id}}</td>
                              <td>{{$order->order_date}}</td>
                              <td>{{$order->name}}</td>
                              <td>{{$order->email}}</td>
                              <td>{{$order->mobile}}</td>
                              <td>{{$order->subtotal}}</td>
                              <td>{{$order->shipping_charges}}</td>
                              <td>{{$order->grand_total}}</td>
                              <td>{{$order->payment_mode}}</td>
                              
                          </tr>
                        <?php $i++; } ?>
                      </tbody>
                  </table>
            </div>
            {!! $record->appends(request()->query())->links() !!}
            @endif
        </div>
      </div>
    </section>
  </section>
</section>   
<script type="text/javascript">
$( document ).ready(function() {   
    $( "#fromdate" ).datepicker({'language' : 'en','dateFormat': 'yy-mm-dd','autoClose': true });
    $( "#todate" ).datepicker({'language' : 'en','dateFormat': 'yy-mm-dd','autoClose': true }); 
    $('#filterwithinput').on('click',function(event) {
        let action = $('#filter_form').attr('action');
        var parameterlist = $('#filter_form').serializeArray();
        var queryvariable = parameterlist.filter(function (i) {
            if(i.name != '_token'){
               return i.value;
            }
        });
        var querystring = $.param(queryvariable);
        var findurl = action+'?'+querystring;
        window.location.href = findurl; 
    });  
}); 
</script>
@endsection
