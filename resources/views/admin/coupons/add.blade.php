@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
      <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="{{url('/admin/coupon')}}">Coupon</a></li>
        </ul>
      </div>
    </div>
    <section class="panel">
        <header class="panel-heading">
          <div class="row">
            <div class="col-sm-6">    
                  <h4 class="form-heading">Coupon Details</h4>
            </div>      
           <div class="col-sm-6">
               <div class="pull-right"> 
                    <a href="{{url('/admin/coupon')}}" class="btn btn-sm btn-info" >Back</a>
               </div> 
           </div>           
        </div>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
                        
              @if(!empty($data))
                 {!! Form::model($data,array('route' => ['admin.coupon.update', $data->id],'method'=>'PATCH','id' =>'coupon_form')) !!}
              @else
                {!! Form::open(['url'=>['admin/coupon'], 'method' => 'POST','id' =>'coupon_form']) !!}
              @endif
              {{ csrf_field() }}
                  
              <fieldset>   
              <legend>Coupon Details</legend>  
                    <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                             {!! Form::label('Coupon Code','Coupon Code', array('class' => 'required')) !!}
                             @if(!empty($data))
                             {!! Form::text('coupon_code',null,array('class'=>'form-control','readonly'=> 'readonly')) !!}
                             @else
                             {!! Form::text('coupon_code',$coupon_code,array('class'=>'form-control','readonly'=> 'readonly')) !!}
                             @endif
                          </div>
                        </div>  
                        <div class="col-md-6">
                          <div class="form-group">
                             {!! Form::label('Title','Title', array('class' => 'required')) !!}
                             {!! Form::text('title',null,array('class'=>'form-control')) !!}
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                             {!! Form::label('Discount','Discount', array('class' => 'required')) !!}
                             {!! Form::text('discount',null,array('class'=>'form-control numbervalid','maxlength'=>'2')) !!}
                          </div>
                        </div>
                    </div>
                   
                    <div class="row">
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
@endsection
