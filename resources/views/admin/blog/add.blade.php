@extends('admin.layouts.view')
@section('content')
<script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
<section id="main-content">
  <section class="wrapper">
    <div class="row">
      <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="{{url('/admin/blog')}}">Blog</a></li>
        </ul>
      </div>
    </div>
    <section class="panel">
        <header class="panel-heading">
          <div class="row">
            <div class="col-sm-6">    
                  <h4 class="form-heading">Blog Details</h4>
            </div>      
           <div class="col-sm-6">
               <div class="pull-right"> 
                    <a href="{{url('/admin/blog')}}" class="btn btn-sm btn-info" >Back</a>
               </div> 
           </div>           
        </div>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
                        
              @if(!empty($data))
                 {!! Form::model($data,array('route' => ['admin.blog.update', $data->id],'method'=>'PATCH','files'=>'true','id' =>'blog_form')) !!}
              @else
                {!! Form::open(['url'=>['admin/blog'], 'method' => 'POST','files'=>'true','id' =>'blog_form']) !!}
              @endif 
                   {{ csrf_field() }}
                <input type="hidden" name="category_id" value="1"> 
                  <fieldset>   
                  <legend>Blog Details</legend>  
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          {!! Form::label('Blog title here','Blog title here', array('class' => 'required bmd-label-floating')) !!}
                          {!! Form::text('title',null,array('class'=>'form-control')) !!}
                          @if ($errors->has('title'))
                            <div class="error">{{ $errors->first('title') }}</div>
                          @endif
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          {!! Form::label('Auther Name','Auther Name', array('class' => 'required bmd-label-floating')) !!}
                          {!! Form::text('author_name',null,array('class'=>'form-control')) !!}
                          @if ($errors->has('author_name'))
                            <div class="error">{{ $errors->first('author_name') }}</div>
                          @endif
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          {!! Form::label('Designation','Designation', array('class' => ' bmd-label-floating')) !!}
                          {!! Form::text('designation',null,array('class'=>'form-control')) !!}
                          @if ($errors->has('designation'))
                            <div class="error">{{ $errors->first('designation') }}</div>
                          @endif
                        </div>
                      </div>
                      
                      <div class="col-md-12">
                          <div class="col-md-6" > 
                      <div class="form-group author-img-bx">
                      {!! Form::label('Banner Image','Banner Image', array('class' => 'required')) !!}
                      <div class="fileupload fileupload-new" data-provides="fileupload">
                          <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                      @if(!empty($data->banner))
                      <img src="{{ url('/public/blogs/'.$data->banner) }}" alt="" />
                      @else
                      <img src="{{ url('/resources/assets/image/default.jpg') }}" alt="" />
                      @endif
                          </div>
                          <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                         <div class="row">
                             <div class="col-md-2">
                            <span class="btn btn-default btn-file"><span class="fileupload-new">Choose image</span>
                            <span class="fileupload-exists">Change</span>
                             {{ Form::file('banner',null, ['class' => 'form-control']) }}
                                </div>
                             <div class="col-md-2">
                                <a class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                             </div>
                             <div class="col-md-9">
                             </div>
                          </div>
                 
                      </div>
                       {!! $errors->has('banner')?$errors->first('banner','<b class="errorcol"> Browes an Image</b>'):'' !!}
                     </div>
                    </div>
                       
                        
                      </div>
                      <div class="col-md-12">
                        {!! Form::label('Content here','Content here', array('class' => 'required bmd-label-floating')) !!}
                        {!! Form::textarea('description',null,array('class'=>'form-control','rows'=>'5','id'=>'description')) !!}
                        @if ($errors->has('description'))
                            <div class="error">{{ $errors->first('description') }}</div>
                        @endif
                      </div>
                      <p>&nbsp;</p>
                      <div class="col-md-12">
                        <div class="form-group">
                          {!! Form::label('Keyword Tags','Keyword Tags', array('class' => ' bmd-label-floating')) !!}
                          {!! Form::text('keyword',null,array('class'=>'form-control')) !!}
                          @if ($errors->has('keyword'))
                            <div class="error">{{ $errors->first('keyword') }}</div>
                          @endif
                        </div>
                      </div>
                      <div class="col-md-12">
                          <div align="center">
                              <button class="btn btn-primary">{{ !empty($data) ? 'Update':'Submit'}}</button>
                          </div>
                      </div>
                    </div>
                    
                  </fieldset>
               
                  
          {{Form::close()}} 
               
        </div>
      </div>
    </section>
  </section>
</section>   
<script>
  CKEDITOR.replace('description',{
        filebrowserUploadUrl: "{{route('admin.ckeditorblog.upload', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form'
    });
</script>
<style type="text/css">
.error {
    color: red;
}

.valid {
    border: 1px solid #56ef5d;
}
</style>  
@endsection
