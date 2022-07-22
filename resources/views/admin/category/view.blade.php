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
                  <li><a href="{{url('/admin/category')}}">Sub Category</a></li>
              </ul>
          </div>
    </div>
    <section class="panel">
          <div class="row">
             <div class="col-md-12 col-xs-6">
              <header class="panel-heading">Sub Category
                    <!-- <a href="{{url('/admin/download/category')}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;">
                      <i class="fa fa-download" aria-hidden="true"></i>Download
                    </a> -->
                    <a href="{{url('/admin/category/create')}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;">
                        <i class="fa fa-plus" aria-hidden="true"></i>Add
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
                              <th>Super Category Name</th>
                              <th>Category Name</th>
                              <th>Category Hindi Name</th>
                              <th>Description</th>
                              <th>Image</th>
                              <!-- <th>Set As Feature</th> -->
                              <th>Status</th>
                              <th style="width:10%;">Action</th>
                          </tr>
                      </thead>
                      <tbody>
                      
                        @foreach ($record as $index=>$key) 
                            <tr class='item<?= $key->id;?>'>
                              <td><?= $index+1;?></td>
                              <td>{{$key->super_cat_name}}</td>
                              <td>{{$key->category_name}}</td>
                              <td>{{$key->category_hindi_name}}</td>
                              <td>{{$key->description}}</td>
                              <td><div class="fileupload-new img-thumbnail">
                                     <img src="{{$key->category_image}}"  style="width: 100px; height: 80px;" alt="" />
                              </div></td>
                              <!-- <td>
                                <label class="switch">
                                  <input type="checkbox" id="{{$key->id}}" class="updatecategoryishome" {{ ($key->is_home == 'Yes' ) ? 'checked' : '' }} >
                                  <span class="slider round"></span>
                                </label>
                              </td> -->
                              <td>
                                <label class="switch">
                                  <input type="checkbox" id="status{{$key->id}}" class="updatestatus" data-mode="Category" {{ ($key->status == 'Yes' ) ? 'checked' : '' }} >
                                  <span class="slider round"></span>
                                </label>
                              </td>
                              <td class="visible-md visible-lg">
                                  <div class="">
                                  <a  class="btn btn-sm btn-info show-tooltip act-no" title="Edit" href="{{ URL::to('admin/category/'.$key->id.'/edit') }}"  ><i class="fa fa-edit"></i></a>

                                  <a class="btn btn-sm btn-danger show-tooltip act-no deleterecord" title="Delete" id="{{$key->id}}"><i class="fa fa-trash-o"></i></a>
                                  </div>
                              </td>
                          </tr>
                        @endforeach
                      </tbody>
                  </table>
            </div>
        </div>
      </div>
    </section>
  </section>
</section>   
<script type="text/javascript">
$(document).ready(function(){   
  $('.table').on('change','.updatecategoryishome', function() {
      var uniqueid = $(this).attr('id');
      $.ajax({
          type:"GET",
          url: "{{url('/admin/category-feature')}}?id="+uniqueid,
          success:function(res){ 
              if(res=='true'){
                  toastr.success("Set as feature category.");
              }else if(res=='false'){
                  toastr.success("Remove from feature category");
              }else{}
              location.reload();
          }
      });
  });
});  
</script>
@endsection
