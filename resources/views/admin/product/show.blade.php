@extends('admin.layouts.view')
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/js/plugins/piexif.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/js/plugins/sortable.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/js/plugins/purify.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/js/fileinput.min.js"></script>
<style type="text/css">
/*.file-preview{
    width:130px !important;
    height: 150px !important;
    border:none !important;
          }
 .file-preview-thumbnails{
    width:130px !important;
    height: 150px !important;
    border:none !important;
          } */ 
    .file-preview {
    border:none !important;
   }     

   .file-drop-zone{
          border:none !important;
   }   
   .file-preview .close.fileinput-remove{
    display: none;
          } 
   .file-caption.form-control.kv-fileinput-caption {
    display: none;
   }   
   .file-drop-zone-title{
    display: none;
   }
   .btn.btn-default.btn-secondary.fileinput-upload.fileinput-upload-button {
    display: none;
   }
   .clearfix {
    display: none;
   }
   .file-actions {
    display: none;
   }
   .file-upload-indicator{
    display: none;
   }
</style> 
<section id="main-content">
  <section class="wrapper">
    <div class="row">
      <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="{{url('/admin/product')}}">Products list</a></li>
            <li><a href="{{url('/admin/product/'.$product->id)}}">{{ $product->product_name }}</a></li>
        </ul>
      </div>
    </div>
    <section class="panel">
      <header class="panel-heading">
        <div class="row">
          <div class="col-sm-6">    
                <h4 class="form-heading">{{ $product->product_name }}</h4>
          </div>      
         <div class="col-sm-6">
             <div class="pull-right"> 
             
                @if(!empty($product->max_bid_price) && ($product->deal_status=='open') && ($product->status=='approved'))
                   <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal">Bid Close</button>
                @endif  
                @if($product->deal_status=='close') 
                   <a class="btn btn-info btn-sm" href="{{url('admin/product-invoice/'.$product->id.'/seller')}}">Seller Invoice</a>
                   <a class="btn btn-info btn-sm" href="{{url('admin/product-invoice/'.$product->id.'/buyer')}}">Buyer Invoice</a>
                   <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#sentsms">Sent SMS</button>
                @endif  
                   <!--<a href="{{url('/admin/')}}" class="btn btn-sm btn-info" >Back</a>-->
             </div> 
         </div>           
        </div>
      </header>
      <div class="panel-body">
            
        <div class="row">
          <div class="col-md-12" >
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <a class="nav-link active" id="ptdetails-tab" data-toggle="tab" href="#ptdetails" role="tab" aria-controls="ptdetails" aria-selected="true">Product Details</a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link" id="bids-tab" data-toggle="tab" href="#bids" role="tab" aria-controls="bids" aria-selected="false">Product Bids ({{count($bids)}})</a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link" id="images-tab" data-toggle="tab" href="#images" role="tab" aria-controls="images" aria-selected="false">Product Images ({{count($images)}})</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade active" id="ptdetails" role="tabpanel" aria-labelledby="ptdetails-tab">
                    <p>&nbsp;</p>
                    <div class="row">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-10">
                        <div class="row">
                            <div class="col-sm-6">
                                <ul class="list-group">
                                  <li class="list-group-item list-group-item-info">Product Details</li>    
                                  <li class="list-group-item">Product Code : {{$product->product_code}}</li>
                                  <li class="list-group-item">Variety Name : {{$product->product_name}}</li>
                                  <li class="list-group-item">Category : {!! $product->category_name.', '.$product->super_cat_name; !!}</li>
                                  <li class="list-group-item">Qantity : {{$product->quantity.' '.$product->unit}}</li>
                                  <li class="list-group-item">Moisture : {{ $product->moisture }}</li>
                                  <li class="list-group-item">Quality : {!! implode(',',$product->quality); !!}</li>
                                  <li class="list-group-item">Base Price : {{$product->sell_price}}</li>
                                  <li class="list-group-item">Max Bid Price : {{$product->max_bid_price}}</li>
                                  <li class="list-group-item">Status : {{$product->status}}</li>
                                  <li class="list-group-item">Deal : {{$product->deal_status}}</li>
                                  <li class="list-group-item">Order Date : {{$product->order_date}}</li>
                                  <li class="list-group-item">Bid Close Date : {{$product->close_date}}</li>
                                </ul>
                            </div>
                            <div class="col-sm-6">
                                <ul class="list-group">
                                  <li class="list-group-item list-group-item-info">Seller Details</li>
                                  <li class="list-group-item">Name : {{$product->name}}</li>
                                  <li class="list-group-item">Email : {{$product->email}}</li>
                                  <li class="list-group-item">Mobile : {{$product->mobile}}</li>
                                  <li class="list-group-item">Pincode : {{$product->pincode}}</li>
                                  <li class="list-group-item">Address : {{$product->address}}</li>
                                </ul>
                                <ul class="list-group">
                                  <li class="list-group-item list-group-item-info">Buyer Details</li>
                                  <li class="list-group-item">Name : {{$product->bname}}</li>
                                  <li class="list-group-item">Email : {{$product->bemail}}</li>
                                  <li class="list-group-item">Mobile : {{$product->bmobile}}</li>
                                  <li class="list-group-item">Pincode : {{$product->bpincode}}</li>
                                  <li class="list-group-item">Address : {{$product->baddress}}</li>
                                </ul>
                            </div>
                        </div>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                </div>
                <div class="tab-pane fade active" id="bids" role="tabpanel" aria-labelledby="bids-tab">
                    <div class="table-responsive" style="border: 1px solid #f2f0f0; padding:10px;">
                        <table class="table table-striped table-bordered table-hover" id="biddersection" style="width: 100%;">
                            <thead>
                              <tr>
                                  <th>Buyer Name</th>
                                  <th>Mobile</th>
                                  <th>Email</th>
                                  <th>Pincode</th>
                                  <th>Address</th>
                                  <th>Bid Price</th>
                                  <th>Status</th>
                                  <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach ($bids as $bid) 
                                <tr>
                                   <td>{{$bid->name}}</td>
                                   <td>{{$bid->mobile}}</td>
                                   <td>{{$bid->email}}</td>
                                   <td>{{$bid->pincode}}</td>
                                   <td>{{$bid->address}}</td>
                                   <td>{{$bid->bid_price}}</td>
                                   <td>{{$bid->status}}</td>
                                   <td>
                                       @if($bid->status=='Yes')
                                       <a>Max Bidder</a>
                                       @else
                                       <a class="btn btn-sm btn-info show-tooltip act-no movetomaxbidder" title="Assign as Max Bidder" id="{{$bid->id}}" data-product-id="{{$product->id}}" ><i class="fa fa-move"></i>Enable</a>
                                       @endif
                                   </td>
                                </tr>
                                @endforeach  
                            </tbody>
                        </table>
                    </div>
                </div>
              <div class="tab-pane fade" id="images" role="tabpanel" aria-labelledby="images-tab">
                <div class="table-responsive" style="border: 1px solid #f2f0f0; padding:10px;">
                  <table class="table table-striped table-bordered table-hover" style="width: 100%;">
                      <thead>
                          <tr>
                              <th>Image</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                      <tbody>
                        @foreach ($images as $img) 
                            <tr class='itemimg<?= $img->id;?>'>
                              <td><a href="{{url('public/image/product/'.$img->image)}}" target="_blank">
                                  <div class="fileupload-new img-thumbnail">
                                     <img src="{{url('public/image/product/'.$img->image)}}" style="width: 100px; height: 80px;" alt="" />
                                  </div>
                                  </a>
                              </td>
                              <td class="visible-md visible-lg">
                                  <div class="">
                                  <a class="btn btn-sm btn-danger show-tooltip act-no deleteproductimgattr" title="Delete" data-mode="image" id="{{$img->id}}"><i class="fa fa-trash-o"></i></a>
                                  </div>
                              </td>
                            </tr>
                        @endforeach
                      </tbody>
                  </table>
                </div>
                <div class="">
                  {!! Form::open(['url'=>['admin/product/'.$product->id.'/images'], 'method' => 'POST', 'files'=>'true', 'id' =>'productimage_form']) !!}
                  {{ csrf_field() }}
                  <fieldset>   
                  <legend>Upload Multiple Images</legend> 
                      <div class="row">
                        <div class="col-md-12" >
                          <div class="alert alert-danger" style="font-size: 13px;">
                            <strong>Upload Multiple files.</strong><br>
                            <strong>Note : To upload multiple files please select browse button.</strong><br>
                          </div>  
                        </div>
                      </div>
                      <div class="row">
                        <div class="input-group-btn">
                          <div tabindex="500" class="btn  btn-file">
                            <input type="file" id="image" name="image[]" multiple="true">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div align="center">
                              <button class="btn btn-primary">Submit</button>
                          </div>
                        </div>
                      </div>
                      
                  </fieldset>
                  {{Form::close()}} 
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </section>
</section>


<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Bid Close</h4>
      </div>
      <div class="modal-body">
        {!! Form::open(['url'=>['admin/biding-close/'.$product->id], 'method' => 'POST','id' =>'product_biding_close']) !!}
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                {!! Form::label('Quantity','Quantity', array('class' => 'required')) !!}
                {!! Form::number('quantity', $product->quantity,array('class'=>'form-control')) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                {!! Form::label('Unit','Unit', array('class' => 'required')) !!}
                {!!Form::select('unit',[null => 'Select Unit','Quintal'=>'Quintal','Kg'=>'Kg','Karet'=>'Karet'], $product->unit,['class'=>'form-control','id'=>'unit']) !!}  
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                {!! Form::label('Sub Total','Sub Total', array('class' => 'required')) !!}
                {!! Form::text('subtotal', Null,array('class'=>'form-control floatnumberallow','placeholder'=>'000')) !!}  
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                {!! Form::label('GST Charges','GST Charges', array('class' => 'required')) !!}
                {!! Form::text('gst_charges', Null,array('class'=>'form-control floatnumberallow','placeholder'=>'000')) !!}  
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                {!! Form::label('Grand Total Amount','Grand Total Amount', array('class' => 'required')) !!}
                {!! Form::text('grand_price', Null,array('class'=>'form-control floatnumberallow','placeholder'=>'000')) !!}  
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div align="center">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
        {{Form::close()}} 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div id="sentsms" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Choose SMS Format</h4>
      </div>
      <div class="modal-body">
        {!! Form::open(['url'=>['admin/sent-sms'], 'method' => 'POST','id' =>'product_sentsms']) !!}
        {{ csrf_field() }}
        <input type="hidden" name="mobile" value="">
        <input type="hidden" name="user_id" value="">
        <input type="hidden" name="product_id" value="{{$product->id}}">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                {!! Form::label('Template Name','Template Name', array('class' => 'required')) !!}
                <select class="form-control" id="choosetemplate" name="templatekey">
                  <option value="">Choose Template</option>
                  @foreach($smsformat as $key=>$value)
                   <option value="{{$key}}" data="{{$value->msg}}" data-mobile="{{$value->mobile}}" data-userid="{{$value->user_id}}">{{$value->id}}</option>
                  @endforeach
                </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('Template Data','Template Data') !!}
                    <textarea name="template" id="templatedata" class="form-control" rows="5" readonly="readonly"></textarea>
                </div>
            </div>    
        </div>
        
        
        <br>
        <div class="row">
            <div class="col-md-12">
                <div align="center">
                    <button class="btn btn-primary">Sent SMS</button>
                </div>
            </div>
        </div>
        {{Form::close()}} 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
  $("#image").fileinput({
    uploadUrl: "/demo/corporate",
    uploadAsync: true,
    previewFileIcon: '<i class="fa fa-file"></i>',
    preferIconicZoomPreview: true, // this will force zoom preview thumbnails to display icons for following file extensions
    previewFileIconSettings: { // configure your icon file extensions
        'doc': '<i class="fa fa-file-word-o text-primary"></i>',
        'xls': '<i class="fa fa-file-excel-o text-success"></i>',
        'ppt': '<i class="fa fa-file-powerpoint-o text-danger"></i>',
        'pdf': '<i class="fa fa-file-pdf-o text-danger"></i>',
        'zip': '<i class="fa fa-file-archive-o text-muted"></i>',
        'htm': '<i class="fa fa-file-code-o text-info"></i>',
        'txt': '<i class="fa fa-file-text-o text-info"></i>',
        'mov': '<i class="fa fa-file-movie-o text-warning"></i>',
        'mp3': '<i class="fa fa-file-audio-o text-warning"></i>',
        // note for these file types below no extension determination logic 
        // has been configured (the keys itself will be used as extensions)
        'jpg': '<i class="fa fa-file-photo-o text-danger"></i>', 
        'gif': '<i class="fa fa-file-photo-o text-warning"></i>', 
        'png': '<i class="fa fa-file-photo-o text-primary"></i>'    
    },
    previewFileExtSettings: { // configure the logic for determining icon file extensions
        'doc': function(ext) {
            return ext.match(/(doc|docx)$/i);
        },
        'xls': function(ext) {
            return ext.match(/(xls|xlsx)$/i);
        },
        'ppt': function(ext) {
            return ext.match(/(ppt|pptx)$/i);
        },
        'zip': function(ext) {
            return ext.match(/(zip|rar|tar|gzip|gz|7z)$/i);
        },
        'htm': function(ext) {
            return ext.match(/(htm|html)$/i);
        },
        'txt': function(ext) {
            return ext.match(/(txt|ini|csv|java|php|js|css)$/i);
        },
        'mov': function(ext) {
            return ext.match(/(avi|mpg|mkv|mov|mp4|3gp|webm|wmv)$/i);
        },
        'mp3': function(ext) {
            return ext.match(/(mp3|wav)$/i);
        },
    }
});
$(document).ready(function(){ 
    $('.bidclosenow').on('click',function(){
        var id = $(this).attr('id');
        alert(id);
    });
    $('#choosetemplate').on('change',function(){
        var templatekey = $(this).val();
        var templatedata = $('option:selected', this).attr('data');
        var mobile = $('option:selected', this).attr('data-mobile');
        var userid = $('option:selected', this).attr('data-userid');
        $('#product_sentsms input[name="mobile"]').val(mobile);
        $('#product_sentsms input[name="user_id"]').val(userid);
        $('#product_sentsms #templatedata').val(templatedata);
    });
    
    $("#biddersection").on('click','.movetomaxbidder', function(){
        var id = $(this).attr('id');
        var product_id = $(this).attr('data-product-id');
        $.confirm({
          title: 'Are you sure!',
          content: ' ',
          buttons: {
              confirm: function(){
                $.ajax({
                    type:"GET",
                    url:"{{url('admin/assign-bidder')}}?bid_id="+id+"&product_id="+product_id,
                    success:function(res){ 
                      if(res.status==true){
                          location.reload(); 
                      }else{
                          toastr.error("Sorry something wrong!"); 
                      }
                    }
                });
              },
              cancel: function(){
              },
          }
        });
    });
  $(".table").on('click','.deleteproductimgattr', function(){
        var id = $(this).attr('id');
        var datamode = $(this).attr('data-mode');
        var token = $("meta[name='csrf-token']").attr("content");
        $.confirm({
          title: 'Are you sure!',
          content: ' ',
          buttons: {
              confirm: function(){
                $.ajax({
                    type:"GET",
                    url:"{{url('admin/product-image/delete')}}/"+id,
                    success:function(res){ 
                      if(res.status==true){
                          if(datamode=='image'){
                              $('.itemimg' + id).remove();
                              toastr.success("Product image deleted successfully");
                          }else{
                              $('.itemattr' + id).remove();
                              toastr.success("Product image deleted successfully");
                          }
                      }else{
                          toastr.error("Sorry record not deleted!"); 
                      }
                    }
                });
              },
              cancel: function(){
              },
          }
        });
  });
});       
</script>
@endsection
