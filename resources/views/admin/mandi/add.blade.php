@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
      <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="{{url('/admin/mandirate')}}">Mandirate</a></li>
        </ul>
      </div>
    </div>
    <section class="panel">
        <header class="panel-heading">
          <div class="row">
            <div class="col-sm-6">    
                  <h4 class="form-heading">Mandirate Details</h4>
            </div>      
           <div class="col-sm-6">
               <div class="pull-right"> 
                    <a href="{{url('/admin/mandirate')}}" class="btn btn-sm btn-info" >Back</a>
               </div> 
           </div>           
        </div>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
              @if(!empty($data))
                 {!! Form::model($data,array('route' => ['admin.mandirate.update', $data->id],'method'=>'PATCH','files'=>'true','id' =>'mandirate_form')) !!}
              @else
                {!! Form::open(['url'=>['admin/mandirate'], 'method' => 'POST','files'=>'true','id' =>'mandirate_form']) !!}
              @endif 
                   {{ csrf_field() }}
                 
                  <fieldset>   
                  <legend>Mandirate Details</legend>  
                    <div class="row">
                      <div class="col-md-12">
                        <div class="row">
                          <div class="col-md-6 form-group">
                             {!! Form::label('Category Name','Category Name', array('class' => 'required')) !!}
                             {!!Form::select('commodity_id',[null => 'Select Category'] + $category, null,['class'=>'form-control']) !!}     
                          </div>
                          <div class="col-md-6 form-group">
                            {{ Form::label('Min Amount', 'Min Amount', array('class' => 'required')) }}
                            {!! Form::text('min', Null,array('class'=>'form-control numbervalid','placeholder'=>'')) !!} 
                          </div>    
                          <div class="col-md-6 form-group">
                            {{ Form::label('Max Amount', 'Max Amount', array('class' => 'required')) }}
                            {!! Form::text('max', Null,array('class'=>'form-control numbervalid','placeholder'=>'')) !!} 
                          </div>    
                          <div class="col-md-6 form-group">
                              {{ Form::label('Modal Rate', 'Modal Rate') }}
                              {!! Form::text('modelrate', Null,array('class'=>'form-control numbervalid','placeholder'=>'')) !!}
                          </div>
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
<style type="text/css">
.error {
    color: red;
}

.valid {
    border: 1px solid #56ef5d;
}
</style>  
@endsection
