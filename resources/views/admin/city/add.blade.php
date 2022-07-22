@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
      <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="{{url('/admin/city')}}">City</a></li>
        </ul>
      </div>
    </div>
    <section class="panel">
        <header class="panel-heading">
          <div class="row">
            <div class="col-sm-6">    
                  <h4 class="form-heading">City Details</h4>
            </div>      
           <div class="col-sm-6">
               <div class="pull-right"> 
                    <a href="{{url('/admin/city')}}" class="btn btn-sm btn-info" >Back</a>
               </div> 
           </div>           
        </div>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
              @if(!empty($data))
                 {!! Form::model($data,array('route' => ['admin.city.update', $data->id],'method'=>'PATCH','files'=>'true','id' =>'city_form')) !!}
              @else
                {!! Form::open(['url'=>['admin/city'], 'method' => 'POST','id' =>'city_form']) !!}
              @endif 
              {{ csrf_field() }}
                  <fieldset>   
                  <legend> State Details </legend>  
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                            <div class="row">
                              <div class="col-md-12 form-group">
                                {!! Form::label('Country Name','Country Name', array('class' => 'required')) !!}
                                @if(!empty($data)) 
                                {!!Form::select('country_id',[null => 'Select Country'] + $country, null,['class'=>'form-control','id'=>'countryids','readonly'=>'readonly']) !!} 
                                @else
                                {!!Form::select('country_id',[null => 'Select Country'] + $country, null,['class'=>'form-control','id'=>'countryids']) !!} 
                                @endif
                              </div>
                              <div class="col-md-12 form-group" id="mmstate_id">
                              @if(!empty($data))
                                @if($data->country_id==1)
                                {!! Form::label('State Name','State Name', array('class' => 'required')) !!}
                                {!!Form::select('state_id',[null => 'Select State'] + $state, null,['class'=>'form-control','id'=>'stateids']) !!} 
                                 @endif
                              @else
                                {!! Form::label('State Name','State Name', array('class' => 'required')) !!}
                                {!!Form::select('state_id',[null => 'Select State'] + $state, null,['class'=>'form-control','id'=>'stateids']) !!}
                              @endif
                              </div>
                              <div class="col-md-12 form-group">
                                {{ Form::label('City Name', 'City Name', array('class' => 'required')) }}
                                {!! Form::text('city_name', Null,array('class'=>'form-control','placeholder'=>'City Name')) !!} 
                              </div> 
                            </div>
                        </div>
                        <div class="col-md-3"></div>
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
<script type="text/javascript">
$(document).ready(function(){  
  $('#countryids').change(function(){
    var countryID = $(this).val();
     if(countryID){
         if(countryID==1){
            $('#mmstate_id').show(); 
            $.ajax({
                type:"GET",
                url:"{{url('admin/get-state')}}?country_id="+countryID,
                success:function(res){ 
                    if(res.status == "true"){
                      $('#stateids').children('option').remove();
                      $('#stateids').append("<option value=''>Select State</option>");
                      $.each(res.statelist, function(index, element) {
                        $('#stateids').append("<option value='"+ index +"'>" + element + "</option>");
                        });
                    }else {
                      $('#stateids').children('option').remove();
                      $('#stateids').append("<option value=''>States not Available</option>");
                    }
                }
            });
         }else{
            $('#mmstate_id').hide();
            $('#stateids').children('option').remove();
         }
    }else{
        $("#stateids").empty();
    }   
    
  });
});  
</script>
@endsection
