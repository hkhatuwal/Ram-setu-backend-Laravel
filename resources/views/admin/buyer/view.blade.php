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
              </ul>
          </div>
    </div>
    <section class="panel">
          <div class="row">
            <div class="col-sm-12 card">
              <div class="card-header">
                  <header class="panel-heading">Buyer
                    <!-- <a href="{{url('/admin/download/city')}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;">
                      <i class="fa fa-download" aria-hidden="true"></i>Download
                    </a> -->
                    <a href="{{url('/admin/buyer/create')}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;">
                        <i class="fa fa-plus" aria-hidden="true"></i>Add
                    </a>
                  </header>
                </div>
              </div>
            </div>
      
        <div class="panel-body">
            <div class="adv-table editable-table ">
                <div class="space15"></div>
                 <div class="table-responsive" style="width: 100%;overflow: auto;">
                
                  <table class="table table-striped table-bordered table-hover" id="data_in_table" style="width: 100%;">
                      <thead>
                          <tr>
                              <th>Sr No.</th>
                              <th>Name</th>
                              <th>Mobile</th>
                              <th>Pincode</th>
                              <th>Address</th>
                              <th>Deal Counter</th>
                              <th>Image</th>
                              <th>Bank</th>
                              <th>Status</th>
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
                              <td>{{$key->name}}</td>
                              <td>{{$key->mobile}}</td>
                              <td>{{$key->pincode}}</td>
                              <td>{{$key->address}}</td>
                              <td>{{$key->product_counter}}</td>
                              <td>
                                  <div class="fileupload-new img-thumbnail">
                                     <img src="{{$key->profile_path}}" style="width: 100px; height: 80px;" alt="" />
                                  </div>
                              </td>
                              <td><a class="btn btn-sm btn-info show-tooltip act-no addbankac" id="{{$key->id}}"><i class="fa fa-plus"></i> Bank</a></td>
                              <td>
                                <label class="switch">
                                  <input type="checkbox" id="status{{$key->id}}" class="updatestatus" data-mode="User" {{ ($key->status == 'Yes' ) ? 'checked' : '' }} >
                                  <span class="slider round"></span>
                                </label>
                              </td>
                              <td class="visible-md visible-lg">
                                  <div class="">
                                  <a  class="btn btn-sm btn-info show-tooltip act-no" title="View More" href="{{ URL::to('admin/buyer/'.$key->id) }}"  ><i class="fa fa-eye"></i></a>      
                                  <a  class="btn btn-sm btn-info show-tooltip act-no" title="Edit" href="{{ URL::to('admin/buyer/'.$key->id.'/edit') }}"  ><i class="fa fa-edit"></i></a>

                                  <!--<a class="btn btn-sm btn-danger show-tooltip act-no deleterecord" title="Delete" id="{{$key->id}}"><i class="fa fa-trash-o"></i></a>-->
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
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Bank Account</h4>
      </div>
      <div class="modal-body">
        {!! Form::open(['url'=>['admin/add-user-bank'], 'method' => 'POST','id' =>'adduserbank']) !!}
        {{ csrf_field() }}
        <input type="hidden" name="user_id" value="">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                {!! Form::label('Account Holder','Account Holder', array('class' => 'required')) !!}
                {!! Form::text('account_holder', null,array('class'=>'form-control')) !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                {!! Form::label('Account Number','Account Number', array('class' => 'required')) !!}
                {!! Form::text('account_number', null,array('class'=>'form-control')) !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                {!! Form::label('IFSC','IFSC', array('class' => 'required')) !!}
                {!! Form::text('ifsc', null,array('class'=>'form-control')) !!} 
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                {!! Form::label('Bank Name','Bank Name', array('class' => 'required')) !!}
                {!! Form::text('bank_name', Null,array('class'=>'form-control','placeholder'=>'')) !!}  
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div align="center">
                    <button class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
        {{Form::close()}} 
      </div>
     
    </div>
  </div>
</div>
<script>
$(document).ready(function(){ 
    $('.table').on('click','.addbankac',function(){
        var idd = $(this).attr('id');
        $('#adduserbank input[name="user_id"]').val(idd);
        $('#myModal').modal('show');
    })
    $('#adduserbank').validate({
	    rules: {
	        account_holder: {
		        required: true,
		        required: true
	        },
	        account_number: {
		        required: true,
		        required: true
	        },
	        ifsc: {
		        required: true,
		        required: true
	        },
	        bank_name: {
		        required: true,
		        required: true
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    submitHandler: function(form) {
	        var userbank = $('#adduserbank').serialize();
            $.ajax({
                type:"GET",
                url:"{{url('admin/add-bank')}}?"+userbank,
                success:function(res){ 
                  if(res.status==true){
                      toastr.success("Bank data added successfully");
                  }else{
                      toastr.error("Sorry something wrong!"); 
                  }
                  $('#myModal').modal('hide');
                }
            });
        }
	      
	});
});
</script>
@endsection
