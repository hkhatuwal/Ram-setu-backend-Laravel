@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
      <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="{{url('/admin/supercategory')}}">Category</a></li>
        </ul>
      </div>
    </div>
    <section class="panel">
        <header class="panel-heading">
          <div class="row">
            <div class="col-sm-6">    
                  <h4 class="form-heading">Category Details</h4>
            </div>      
           <div class="col-sm-6">
               <div class="pull-right"> 
                    <a href="{{url('/admin/supercategory')}}" class="btn btn-sm btn-info" >Back</a>
               </div> 
           </div>           
        </div>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
                        
              @if(!empty($data))
                 {!! Form::model($data,array('route' => ['admin.supercategory.update', $data->id],'method'=>'PATCH','files'=>'true','id' =>'supercategory_form')) !!}
              @else
                {!! Form::open(['url'=>['admin/supercategory'], 'method' => 'POST','files'=>'true','id' =>'supercategory_form']) !!}
              @endif 
                   {{ csrf_field() }}
                 
                  <fieldset>   
                  <legend>Category Details</legend>  
                    <div class="row">
                      <div class="col-md-9">
                        <div class="row">
                          <div class="col-md-12 form-group">
                            {{ Form::label('Category Name', 'Category Name', array('class' => 'required')) }}
                            {!! Form::text('super_cat_name', Null,array('class'=>'form-control','placeholder'=>'Category Name')) !!} 
                          </div>   
                          <div class="col-md-12 form-group">
                            {{ Form::label('Category Hindi Name', 'Category Hindi Name', array('class' => 'required')) }}
                            {!! Form::text('super_cat_hindi_name', Null,array('class'=>'form-control','placeholder'=>'Category Hindi Name')) !!} 
                          </div>    
                          <div class="col-md-12 form-group">
                              {{ Form::label('Description', 'Description') }}
                              {!! Form::textarea('description',null,array('class'=>'form-control','rows'=>'3')) !!}
                          </div>
                        </div>
                      </div> 
                      <div class="col-md-3">
                          <div class="form-group author-img-bx">
                          {!! Form::label('Category Icon','Category Icon') !!}
                          <div class="fileupload fileupload-new" data-provides="fileupload">
                              <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                          @if(!empty($data))
                            @if(!empty($data->icon))
                            <img src="{{ url('/public/image/supercategory/'.$data->icon) }}" alt="" width="175px" height="150px" />
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
                                    <span class="btn btn-default btn-file"><span class="fileupload-new">Choose icon</span>
                                    <span class="fileupload-exists">Change</span>
                                    {{ Form::file('icon',null, ['class' => 'form-control']) }}
                                  </div>
                                  <div class="col-md-3">
                                    <a class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                  </div>
                                 <div class="col-md-5">
                                 </div>
                              </div>
                          </div>
                           {!! $errors->has('icon')?$errors->first('icon','<b class="errorcol"> Browes an Icon</b>'):'' !!}
                         </div>
                      </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                          <div class="form-group author-img-bx">
                          {!! Form::label('Category Image','Category Image') !!}
                          <div class="fileupload fileupload-new" data-provides="fileupload">
                              <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                          @if(!empty($data))
                            @if(!empty($data->image))
                            <img src="{{ url('/public/image/supercategory/'.$data->image) }}" alt="" width="175px" height="150px" />
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
                                    <span class="btn btn-default btn-file"><span class="fileupload-new">Choose Image</span>
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
                        <div class="col-md-3">
                          <div class="form-group author-img-bx">
                          {!! Form::label('Category Banner','Category Banner') !!}
                          <div class="fileupload fileupload-new" data-provides="fileupload">
                              <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                          @if(!empty($data))
                            @if(!empty($data->banner))
                            <img src="{{ url('/public/image/supercategory/'.$data->banner) }}" alt="" width="175px" height="150px" />
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
                                    <span class="btn btn-default btn-file"><span class="fileupload-new">Choose Banner</span>
                                    <span class="fileupload-exists">Change</span>
                                    {{ Form::file('banner',null, ['class' => 'form-control']) }}
                                  </div>
                                  <div class="col-md-3">
                                    <a class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                  </div>
                                 <div class="col-md-5">
                                 </div>
                              </div>
                          </div>
                           {!! $errors->has('banner')?$errors->first('banner','<b class="errorcol"> Browes an Image</b>'):'' !!}
                         </div>
                      </div>
                    </div>
                </fieldset>
               
                <div class="row">
                    <div class="col-md-12">
                        <div align="center">
                            <button class="btn btn-primary">{{ !empty($data) ? 'Update':'Submit'}}</button>
                        </div>
                    </div>
                </div>

          {{Form::close()}} 
               
        </div>
      </div>
    </section>
  </section>
</section>   

@endsection
