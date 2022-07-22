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
                  <li><a href="{{url('/admin/complete-product')}}">Deal Completed Products</a></li>
              </ul>
          </div>
    </div>
    <section class="panel">
          <div class="row">
            <div class="col-sm-12 card">
              <div class="card-header">
                <header class="panel-heading">
                {!! Form::open(['url'=>['admin/complete-product'], 'class' => '','method' => 'POST','id' =>'filter_form']) !!}
                      {{ csrf_field() }}
                          <div class="row" id="searchorder">
                            <div class="col-sm-3">    
                                <h4>Completed Products</h4>
                            </div> 
                            <div class="col-sm-3"> 
                                {!! Form::select('category',[null=>'Select Category'] + $category,Request::has('category') ? Request::input('category'): null,['class' => 'form-control']) !!}
                            </div>
                            <div class="col-sm-2"> 
                            </div>
                            <div class="col-sm-1"> 
                                <button type="button" id="filterwithinput" class="btn btn-raised btn-success add-btn back-btn"><i class="fa fa-search"></i> Search</button>
                            </div> 
                            <div class="col-sm-3">
                              <div class="pull-right"> 
                                <!-- <a href="{{url('/admin/importproduct')}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;">
                                  <i class="fa fa-download" aria-hidden="true"></i>Import
                                </a>
                                <a href="{{url('/admin/download/product?'.Request()->getQueryString())}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;">
                                  <i class="fa fa-download" aria-hidden="true"></i>Download
                                </a> -->
                               
                              </div> 
                            </div>           
                        </div>  
                      {{Form::close()}} 
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
                              <th>Product Name</th>
                              <th>Code</th>
                              <th>Category Name</th>
                              <th>Quantity</th>
                              <!--<th>Seller Price</th>-->
                              <th>Base Price</th>
                              <th>Max Bid Detail</th>
                              <th>Bid Close</th>
                              <th>No of Bid</th>
                              <th>Image</th>
                              <th>Status</th>
                              <th>Invoice</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                      <tbody>
                      <?php 
                        $i=1;
                        foreach ($record as $key) {
                        ?>
                            <tr class='item<?= $key->id;?>'>
                              <td><?= $i;?></td>
                              <td><a href="{{ URL::to('admin/product/'.$key->id.'#attributes') }}" >{{$key->product_name}}</a></td>
                              <td>{{$key->product_code}}</td>
                              <td>{!! $key->category_name.', '.$key->super_cat_name; !!}</td>
                              <td>{{$key->quantity.' '.$key->unit}}</td>
                              <!--<td>{{$key->base_price}}</td>-->
                              <td>{{$key->sell_price}}</td>
                              <td>{{$key->max_bid_price}}</td>
                              <td>{{$key->close_date}}</td>
                              <td>{{$key->bid_counter}}</td>
                              <td>{{$key->image_counter}}</td>
                              <td>{{$key->status}}</td>
                              <td class="visible-md visible-lg">
                                  <div class="">
                                  <a class="btn btn-info btn-sm" href="{{url('admin/product-invoice/'.$key->id.'/seller')}}">Seller</a>
                                  <a class="btn btn-info btn-sm" href="{{url('admin/product-invoice/'.$key->id.'/buyer')}}">Buyer</a>
                                  </div>
                              </td>
                              <!--<td>-->
                              <!--    <div class="fileupload-new img-thumbnail">-->
                              <!--       <img src="{{$key->image_path}}" style="width: 100px; height: 80px;" alt="" />-->
                              <!--    </div>-->
                              <!--</td>-->
                              <!-- <td>
                                  <a  class="btn btn-sm btn-info show-tooltip act-no" title="View Attribute / Images" href="{{ URL::to('admin/product/'.$key->id.'#attributes') }}"  ><i class="fa fa-plus"></i>More</a>
                              </td> 
                              <td>
                                <label class="switch">
                                  <input type="checkbox" id="{{$key->id}}"  class="updateother" data-mode="is_stock" {{ ($key->is_stock == 'Yes' ) ? 'checked' : '' }} >
                                  <span class="slider round"></span>
                                </label>
                              </td>
                              <td>
                                <label class="switch">
                                  <input type="checkbox" id="{{$key->id}}" class="updateother" data-mode="is_feature" {{ ($key->is_feature == 'Yes' ) ? 'checked' : '' }} >
                                  <span class="slider round"></span>
                                </label>
                              </td>-->
                              <!--<td>-->
                              <!--  <label class="switch">-->
                              <!--    <input type="checkbox" id="status{{$key->id}}" class="updatestatus" data-mode="ProductMaster" {{ ($key->status == 'Yes' ) ? 'checked' : '' }} >-->
                              <!--    <span class="slider round"></span>-->
                              <!--  </label>-->
                              <!--</td>-->
                              <td class="visible-md visible-lg">
                                  <div class="">
                                  <a  class="btn btn-sm btn-info show-tooltip act-no" title="View More" href="{{ URL::to('admin/product/'.$key->id.'#ptdetails') }}"  ><i class="fa fa-eye"></i></a>        
                                  <a  class="btn btn-sm btn-info show-tooltip act-no" title="Edit" href="{{ URL::to('admin/product/'.$key->id.'/edit') }}"  ><i class="fa fa-edit"></i></a>

                                  <a class="btn btn-sm btn-danger show-tooltip act-no deleterecord" title="Delete" id="{{$key->id}}"><i class="fa fa-trash-o"></i></a>
                                  </div>
                              </td>
                          </tr>
                        <?php $i++; } ?>
                      </tbody>
                  </table>
                  <div class="row">
                    <div class="col-md-12">
                        {{ $record->appends(Request::except('page'))->links() }}
                    </div>
                  </div>
            </div>
        </div>
      </div>
    </section>
  </section>
</section>   
<script type="text/javascript">
$(document).ready(function(){   
  $('.table').on('change','.updateother', function() {
      var uniqueid = $(this).attr('id');
      var datamode = $(this).attr('data-mode');
      $.ajax({
          type:"GET",
          url: "{{url('/admin/product-stock-feature')}}?id="+uniqueid+'&status='+datamode,
          success:function(res){ 
              if(res=='true'){
                  toastr.success("Update change successfully.");
              }else if(res=='false'){
                  toastr.success("Update change successfully.");
              }else{}
          }
      });
  });
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
