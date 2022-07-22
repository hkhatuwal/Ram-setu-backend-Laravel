@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
          <div class="col-lg-12">
              <!--breadcrumbs start -->
              <ul class="breadcrumb">
                  <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i>Home</a></li>
                  <li><a href="{{url('/admin/blog-category')}}">Blog Category</a></li>
              </ul>
              <!--breadcrumbs end -->
          </div>
    </div>
    <section class="panel">
          <div class="row">
             <div class="col-md-12 col-xs-6">
              <header class="panel-heading">Blog Category
                    <a href="{{url('/admin/blog-category/create')}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;">
                        <i class="fa fa-plus" aria-hidden="true"></i>Add New Blog Category
                    </a>
              </header>
             </div>
        </div>
       
        <div class="panel-body">
            <div class="adv-table editable-table ">
                <div class="space15"></div>
                 <div class="table-responsive" style="border: 1px solid #f2f0f0; padding:20px;">
                   <table class="table table-striped table-bordered table-hover users-table" id="data_in_table"  style="width: 100%;">
            <thead>
			    <tr>
			      <th style="width: 10%;">Sr. No</th>
			      <th style="width: 40%;">Blog Category Name</th>
			      <th style="width: 15%;">Status</th>
			      <th style="width: 10%;">Action</th>
			    </tr>
			  </thead>
			<tbody>
             @if(count($record) > 0)
			  @foreach($record as $key => $value)
			    <tr class='item<?= $value->id;?>'>
			      <th>{!! $key+1;!!}</th>
			      <td>{{$value->blog_cat_name}}</td>
			      <td> 
    			      @if($value->status=='Yes')
                       <span class="btn btn-warning btn-sm checkstatus" id="status{{$value->id}}">Inactive</span>
                      @else 
                      <span class="btn btn-info btn-sm checkstatus" id="status{{$value->id}}">Active</span>
                      @endif
                 </td>
			        <td class="visible-md visible-lg">
                        <div class="btn-group">
                        <a class="btn btn-sm show-tooltip " title="Edit" href="{{ URL::to('admin/blog-category/'.$value->id.'/edit') }}"><i class="fa fa-edit"></i></a>
                        <a class="btn btn-sm btn-danger show-tooltip deletebc" title="Delete" id="{{$value->id}}"><i class="fa fa-trash-o"></i></a>
    
                        </div>
                    </td>
			    </tr>
			    @endforeach
			   @endif 
			  </tbody>  
            
           </table>
                 </div>
                </div>
            </div>
    </section>
  </section>
</section>   

    
     

<div id="confirm-delete" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Delete Blog Category</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="confirm_delete_id" id="confirm_delete_id" value="">
        <p>You are going to delete record. Would you like to proceed?</p>
      </div>
      <div class="modal-footer">
        <strong class="btn btn-danger locationdelete"  data-dismiss="modal">Delete</strong >
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
  
    </div>
  </div>
</div>
<script type="text/javascript">
$("#data_in_table").on("click", ".checkstatus", function(){
        var userid = this.id;
        var str = userid.substring(6);
        $.ajax({
          type:"GET",
          url:"{{url('admin/blog-cat-isactive')}}?id="+str,
          success:function(res){ 
            console.log(res);
            if(res=='Yes'){
              $('#'+userid).html('Inactive').toggleClass('btn-info btn-warning');
             }else if(res=='No'){
              $('#'+userid).html('Active').toggleClass('btn-warning btn-info');
             }else{}
         }
      });
    }) 
 $("#data_in_table").on("click", ".deletebc", function(){
       //alert(this.id);
  $('#confirm_delete_id').empty();    
  $('#confirm_delete_id').val(this.id);
  $('#confirm-delete').modal('show');
  });

      $(".locationdelete").on("click", function(){

       var userid=$('#confirm_delete_id').val();
       $.ajax({
           type:"GET",
           url:"{{url('admin/blog-cat-delete')}}/"+userid,
           success:function(data){ 
            //console.log(data);
           $('.item' + userid).remove();
           toastem.success("Blog category deleted successfully!");
         }
       })
      });
   
</script>
@endsection