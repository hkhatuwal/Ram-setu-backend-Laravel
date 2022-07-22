@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
      <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="{{url('/admin/subcategory')}}">Subcategory</a></li>
        </ul>
      </div>
    </div>
    <section class="panel">
        <header class="panel-heading">
          <div class="row">
            <div class="col-sm-6">    
                  <h4 class="form-heading">Subcategory Details</h4>
            </div>      
           <div class="col-sm-6">
               <div class="pull-right"> 
                    <a href="{{url('/admin/subcategory')}}" class="btn btn-sm btn-info" >Back</a>
               </div> 
           </div>           
        </div>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
                        
              @if(!empty($data))
                 {!! Form::model($data,array('route' => ['admin.subcategory.update', $data->id],'method'=>'PATCH','files'=>'true','id' =>'subcategory_form')) !!}
              @else
                {!! Form::open(['url'=>['admin/subcategory'], 'method' => 'POST','id' =>'subcategory_form']) !!}
              @endif 
                   {{ csrf_field() }}
                  
                  <fieldset>   
                  <legend>Subcategory Details</legend>  
                    <div class="row">
                      <div class="col-md-9">
                        <div class="row">
                          <div class="col-md-12 form-group">
                             {!! Form::label('Super Category Name','Super Category Name', array('class' => 'required')) !!}
                             {!!Form::select('super_cat_id',[null => 'Select Super Category'] + $supercategory, null,['class'=>'form-control','id'=>'supercatid']) !!}     
                          </div>
                          <div class="col-md-12 form-group">
                             {!! Form::label('Category Name','Category Name', array('class' => 'required')) !!}
                             {!!Form::select('category_id',[null => 'Select Category'] + $category, null,['class'=>'form-control','id'=>'categoryid']) !!}     
                          </div>
                          <div class="col-md-12 form-group">
                            {{ Form::label('Subcategory Name', 'Subcategory Name', array('class' => 'required')) }}
                            {!! Form::text('subcat_name', Null,array('class'=>'form-control','placeholder'=>'Subcategory Name')) !!} 
                          </div>    
                          <div class="col-md-12 form-group">
                              {{ Form::label('Description', 'Description') }}
                              {!! Form::textarea('description',null,array('class'=>'form-control','rows'=>'3')) !!}
                          </div>
                        </div>
                      </div> 
                      <div class="col-md-3">
                          <div class="form-group author-img-bx">
                          {!! Form::label('Image','Image') !!}
                          <div class="fileupload fileupload-new" data-provides="fileupload">
                              <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                          @if(!empty($data))
                            @if(!empty($data->image))
                            <img src="{{ url('/public/image/subcategory/'.$data->image) }}" alt="" width="175px" height="150px" />
                            @else
                            <img src="{{ url('/public/image/no-img.jpg') }}" alt="" width="175px" height="150px" />
                            @endif
                          @else
                          <img src="{{ url('/public/image/no-img.jpg') }}" alt="" width="175px" height="150px" />
                          @endif
                              </div>
                              <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                              <div class="row">
                                 <div class="col-md-4">
                                    <span class="btn btn-default btn-file"><span class="fileupload-new">Choose image</span>
                                    <span class="fileupload-exists">Change</span>
                                    {{ Form::file('image',null, ['class' => 'form-control']) }}
                                  </div>
                                  <div class="col-md-3">
                                    <a class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                  </div>
                                 <div class="col-md-5">
                                 </div>
                              </div>
                          </div>
                           {!! $errors->has('image')?$errors->first('image','<b class="errorcol"> Browes an Image</b>'):'' !!}
                         </div>
                      </div> 
                    </div>
                    
                </fieldset>
               
                <div class="row">
                    <div class="col-md-12">
                        <div align="center">
                            <button class="btn btn-primary">{{ !empty($empty) ? 'Update':'Submit'}}</button>
                        </div>
                    </div>
                </div>

          {{Form::close()}} 
               
        </div>
      </div>
    </section>
  </section>
</section>   
<script type="text/javascript">
$(document).ready(function(){  
  $('#supercatid').change(function(){
    var supercatID = $(this).val();  
    if(supercatID){
        $.ajax({
           type:"GET",
           url:"{{url('api/get-categorylistbysuperid')}}?super_cat_id="+supercatID,
           success:function(res){ 
                if(res.status == "true"){
                  $('#categoryid').children('option').remove();
                   $('#categoryid').append("<option value=''>Select Category</option>");
                  $.each(res.record, function(index, element) {
                    $('#categoryid').append("<option value='"+ index +"'>" + element + "</option>");
                    });
                }else {
                   $('#categoryid').children('option').remove();
                   $('#categoryid').append("<option value=''>Category not Available</option>");
                }
           }
        });
    }else{
        $("#categoryid").empty();
    }   
    
  });
});  
</script>
@endsection
