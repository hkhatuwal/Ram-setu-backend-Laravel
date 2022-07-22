@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
      <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="{{url('/admin/state')}}">State</a></li>
        </ul>
      </div>
    </div>
    <section class="panel">
        <header class="panel-heading">
          <div class="row">
            <div class="col-sm-6">    
                  <h4 class="form-heading">State Details</h4>
            </div>      
           <div class="col-sm-6">
               <div class="pull-right"> 
                    <a href="{{url('/admin/state')}}" class="btn btn-sm btn-info" >Back</a>
               </div> 
           </div>           
        </div>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
                        
              @if(!empty($data))
                 {!! Form::model($data,array('route' => ['admin.state.update', $data->id],'method'=>'PATCH','files'=>'true','id' =>'state_form')) !!}
              @else
                {!! Form::open(['url'=>['admin/state'], 'method' => 'POST','id' =>'state_form']) !!}
              @endif 
                   {{ csrf_field() }}
                  
                  <fieldset>   
                  <legend>State Details</legend>  
                    <div class="row">
                      <div class="col-md-6 form-group">
                         {!! Form::label('Country Name','Country Name', array('class' => 'required')) !!}
                         {!!Form::select('country_id',[null => 'Select Country'] + $country, null,['class'=>'form-control']) !!}     
                      </div>
                      <div class="col-md-6 form-group">
                        {{ Form::label('State Name', 'State Name', array('class' => 'required')) }}
                        {!! Form::text('state_name', Null,array('class'=>'form-control','placeholder'=>'State Name')) !!} 
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
