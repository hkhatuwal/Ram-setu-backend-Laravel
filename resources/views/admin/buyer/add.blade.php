@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
      <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="{{url('/admin/buyer')}}">Buyer</a></li>
        </ul>
      </div>
    </div>
    <section class="panel">
        <header class="panel-heading">
          <div class="row">
            <div class="col-sm-6">    
                  <h4 class="form-heading">Buyer Details</h4>
            </div>      
           <div class="col-sm-6">
               <div class="pull-right"> 
                    <a href="{{url('/admin/buyer')}}" class="btn btn-sm btn-info" >Back</a>
               </div> 
           </div>           
        </div>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
                        
              @if(!empty($data))
                 {!! Form::model($data,array('route' => ['admin.buyer.update', $data->id],'method'=>'PATCH','files'=>'true','id' =>'buyer_form')) !!}
              @else
                {!! Form::open(['url'=>['admin/buyer'], 'method' => 'POST','files'=>'true','id' =>'buyer_form']) !!}
              @endif 
              {{ csrf_field() }}
                  
              <fieldset>   
              <legend>Seller Details</legend>  
                  <div class="row">
                    <div class="col-md-9">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            {!! Form::label('Seller Name','Seller Name', array('class' => 'required')) !!}
                            {!! Form::text('name', Null,array('class'=>'form-control')) !!}    
                          </div>
                        </div>
                        <div class="col-md-12 form-group">
                          {{ Form::label('Mobile', 'Mobile', array('class' => 'required')) }}
                          {!! Form::text('mobile', Null,array('class'=>'form-control')) !!}
                        </div>   
                        <div class="col-md-12 form-group">
                          {{ Form::label('Pincode', 'Pincode', array('class' => 'required')) }}
                          {!! Form::text('pincode', Null,array('class'=>'form-control')) !!}
                        </div> 
                        <div class="col-md-12 form-group">
                          {{ Form::label('Address', 'Address', array('class' => 'required')) }}
                          {!! Form::textarea('address',null,array('class'=>'form-control','rows'=>'3')) !!}
                        </div>    
                      </div>
                      
                    </div>
                    <div class="col-md-3">
                      <div class="form-group author-img-bx">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                              @if(!empty($data->profile_pic))    
                                <img src="{{url('/public/image/profile'.$data->profile_pic)}}" alt="" width="175px" height="150px" />
                              @else
                                <img src="{{ url('/public/image/no-img.jpg') }}" alt="" width="175px" height="150px" />
                              @endif  
                                </div>
                                <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                <div class="row">
                                   <div class="col-md-4">
                                      <span class="btn btn-default btn-file"><span class="fileupload-new">Choose image</span>
                                      <span class="fileupload-exists">Change</span>
                                      {{ Form::file('profile_pic',null, ['class' => 'form-control']) }}
                                    </div>
                                    <div class="col-md-3">
                                      <a class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                    </div>
                                   <div class="col-md-5">
                                   </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
                       
                       
                   <div class="row">
                        <div class="col-md-6">
                            <fieldset>   
                            <legend>Aadhaar Details</legend> 
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {{ Form::label('Aadhaar Number', 'Aadhaar Number', array('class' => 'required')) }}
                                        {!! Form::text('aadhaar_number', Null,array('class'=>'form-control')) !!}
                                    </div>
                                </div> 
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group author-img-bx">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                                          @if(!empty($data->aadhaar_front))    
                                            <img src="{{url('/public/image/profile/'.$data->aadhaar_front)}}" alt="" width="175px" height="150px" />
                                          @else
                                            <img src="{{ url('/public/image/no-img.jpg') }}" alt="" width="175px" height="150px" />
                                          @endif  
                                            </div>
                                            <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                            <div class="row">
                                               <div class="col-md-4">
                                                  <span class="btn btn-default btn-file"><span class="fileupload-new">Aadhaar Front</span>
                                                  <span class="fileupload-exists">Change</span>
                                                  {{ Form::file('aadhaar_front',null, ['class' => 'form-control']) }}
                                                </div>
                                                <div class="col-md-3">
                                                  <a class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                </div>
                                               <div class="col-md-5">
                                               </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="form-group author-img-bx">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                                          @if(!empty($data->aadhaar_back))    
                                            <img src="{{url('/public/image/profile/'.$data->aadhaar_back)}}" alt="" width="175px" height="150px" />
                                          @else
                                            <img src="{{ url('/public/image/no-img.jpg') }}" alt="" width="175px" height="150px" />
                                          @endif  
                                            </div>
                                            <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                            <div class="row">
                                               <div class="col-md-4">
                                                  <span class="btn btn-default btn-file"><span class="fileupload-new">Aadhaar Back</span>
                                                  <span class="fileupload-exists">Change</span>
                                                  {{ Form::file('aadhaar_back',null, ['class' => 'form-control']) }}
                                                </div>
                                                <div class="col-md-3">
                                                  <a class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                </div>
                                               <div class="col-md-5">
                                               </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset>   
                            <legend>Pan Details</legend> 
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {{ Form::label('Pan Number', 'Pan Number', array('class' => 'required')) }}
                                        {!! Form::text('pan_number', Null,array('class'=>'form-control')) !!}
                                    </div>
                                </div> 
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group author-img-bx">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                                          @if(!empty($data->pan_card))    
                                            <img src="{{url('/public/image/profile/'.$data->pan_card)}}" alt="" width="175px" height="150px" />
                                          @else
                                            <img src="{{ url('/public/image/no-img.jpg') }}" alt="" width="175px" height="150px" />
                                          @endif  
                                            </div>
                                            <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                            <div class="row">
                                               <div class="col-md-4">
                                                  <span class="btn btn-default btn-file"><span class="fileupload-new">Pan Image</span>
                                                  <span class="fileupload-exists">Change</span>
                                                  {{ Form::file('pan_card',null, ['class' => 'form-control']) }}
                                                </div>
                                                <div class="col-md-3">
                                                  <a class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                </div>
                                               <div class="col-md-5">
                                               </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            </fieldset>
                        </div>
                        <div class="col-md-12">  
                            <fieldset>   
                            <legend>Bank Details</legend> 
                                <div class="table-responsive" style="padding:20px;">
                                    <table class="table table-striped table-bordered table-hover users-table" id=""  style="width: 100%;">
                                        <thead>
                        			    <tr>
                        			      <th style="width: 10%;">Sr. No</th>
                        			      <th style="width: 30%;">Ac. Holder</th>
                        			      <th style="width: 20%;">Ac. No</th>
                        			      <th style="width: 20%;">IFSC</th>
                        			      <th style="width: 20%;">Bank</th>
                        			    </tr>
                        			    </thead>
                            			<tbody>
                            			    @if(!empty($data))
                            			    @foreach($data->banks as $bk=>$bank)
                            			     <tr class='<?= $bk;?>'>
                                			      <th><input type="text" class="form-control" name="bank_id[]" value="{{$bank->id}}" readonly></th>
                                			      <td><input type="text" class="form-control" name="account_holder[]" value="{{$bank->account_holder}}"></td>
                                			      <td><input type="text" class="form-control" name="account_number[]" value="{{$bank->account_number}}"></td>
                                			      <td><input type="text" class="form-control" name="ifsc[]" value="{{$bank->ifsc}}"></td>
                                			      <td><input type="text" class="form-control" name="bank_name[]" value="{{$bank->bank_name}}"></td>
                                			 </tr> 
                                			@endforeach 
                                			@endif 
                            			</tbody>  
                                    </table>
                                </div>
                            </fieldset>
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
