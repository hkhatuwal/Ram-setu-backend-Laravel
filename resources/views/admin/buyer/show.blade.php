@extends('admin.layouts.view')
@section('content')
<style type="text/css">
.table tbody tr.matching > td {
    background-color: #5af71a80 !important;
}
</style>
<section id="main-content">
  <section class="wrapper">
    <div class="row">
          <div class="col-lg-12">
              <ul class="breadcrumb">
                  <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i>Home</a></li>
                  <li><a href="{{url('/admin/buyer')}}">Buyer</a></li>
                  <li><a href="#">{{ $user->name }}</a></li>
              </ul>
          </div>
    </div>
    <section class="panel">
          <div class="row">
            <div class="col-sm-12 card">
              <div class="card-header">
                  <header class="panel-heading">{!! $user->name.' || '.$user->mobile; !!}
                    <!-- <a href="{{url('/admin/download/city')}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;">
                      <i class="fa fa-download" aria-hidden="true"></i>Download
                    </a> -->
                  </header>
                </div>
              </div>
            </div>
      
        <div class="panel-body">
            <div class="adv-table editable-table ">
                <div class="space15"></div>
                 <div class="table-responsive" style="width: 100%;overflow: auto;">
                
                  <table class="table table-striped table-bordered table-hover">
                      <thead>
                          <tr>
                              <th>Sr No.</th>
                              <th>Product Code</th>
                              <th>Variety Name</th>
                              <th>Category</th>
                              <th>Quantity</th>
                              <th>Moisture</th>
                              <!--<th>Seller Price</th>-->
                              <th>Admin Price</th>
                              <th>Max Bid Price</th>
                              <th>Order Date</th>
                              <th>Bid Close</th>
                              <th>Status</th>
                              <th>Deal</th>
                              <th>View More</th>
                          </tr>
                      </thead>
                      <tbody>
                      <?php 
                        $i=1;
                        foreach ($products as $key) {
                        ?>
                            <tr class='item<?= $key->id;?>'>
                              <td><?= $i;?></td>
                              <td>{{$key->product_code}}</td>
                              <td>{{$key->product_name}}</td>
                              <td>{!! $key->category_name.', '.$key->super_cat_name; !!}</td>
                              <td>{!! $key->quantity.' '.$key->unit; !!}</td>
                              <td>{!! $key->moisture; !!}</td>
                              <!--<td>{{$key->base_price}}</td>-->
                              <td>{{$key->sell_price}}</td>
                              <td>{{$key->max_bid_price}}</td>
                              <td>{{$key->order_date}}</td>
                              <td>{{$key->close_date}}</td>
                              <td>{{$key->status}}</td>
                              <td>{{$key->deal_status}}</td>
                             
                              <td class="visible-md visible-lg">
                                  <div class="">
                                  <a  class="btn btn-sm btn-info show-tooltip act-no" title="View More" href="{{ URL::to('admin/product/'.$key->id.'#ptdetails') }}"  ><i class="fa fa-eye"></i></a>      
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
