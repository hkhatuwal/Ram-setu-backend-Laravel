@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
      <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="{{url('/admin/faqs')}}">Faqs</a></li>
        </ul>
      </div>
    </div>
    <section class="panel">
        <header class="panel-heading">
          <div class="row">
            <div class="col-sm-6">    
                  <h4 class="form-heading">Faqs Details</h4>
            </div>      
           <div class="col-sm-6">
               <div class="pull-right"> 
                    <a href="{{url('/admin/faqs')}}" class="btn btn-sm btn-info" >Back</a>
               </div> 
           </div>           
        </div>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
                        
              @if(!empty($data))
                 {!! Form::model($data,array('route' => ['admin.faqs.update', $data->id],'method'=>'PATCH','id' =>'faqs_form')) !!}
              @else
                {!! Form::open(['url'=>['admin/faqs'], 'method' => 'POST','id' =>'faqs_form']) !!}
              @endif 
                   {{ csrf_field() }}
                 
                  <fieldset>   
                  <legend>Faqs Details</legend>  
                    <div class="row">
                      <div class="col-md-12">
                        <div class="row">
                          <div class="col-md-12 form-group">
                            {{ Form::label('Eng Question', 'Eng Question', array('class' => 'required')) }}
                            {!! Form::text('question', Null,array('class'=>'form-control','placeholder'=>'Question')) !!} 
                          </div>   
                          <div class="col-md-12 form-group">
                              {{ Form::label('Eng Answer', 'Eng Answer') }}
                              {!! Form::textarea('answer',null,array('class'=>'form-control','rows'=>'3')) !!}
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12 form-group">
                            {{ Form::label('Hindi Question', 'Hindi Question', array('class' => 'required')) }}
                            {!! Form::text('question_hindi', Null,array('class'=>'form-control','placeholder'=>'Question')) !!} 
                          </div>   
                          <div class="col-md-12 form-group">
                              {{ Form::label('Hindi Answer', 'Hindi Answer') }}
                              {!! Form::textarea('answer_hindi',null,array('class'=>'form-control','rows'=>'3')) !!}
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

@endsection
