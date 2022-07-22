@extends('admin.layouts.view')
@section('content')

<section id="main-content">
  <section class="wrapper">
    <div class="row">
          <div class="col-lg-12">
              <ul class="breadcrumb">
                  <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i>Home</a></li>
                  <li><a href="{{url('/admin/shipping-charges')}}">shipping-charges</a></li>
              </ul>
          </div>
    </div>
    <section class="panel">
          <div class="row">
             <div class="col-md-12 col-xs-6">
              <header class="panel-heading">shipping-charges</header>
             </div>
        </div>
      
        <div class="panel-body">
            <div class="adv-table editable-table ">
                <div class="space15"></div>
                 <div class="table-responsive" style="padding:10px;">
                {!! Form::open(['url'=>['admin/shipping-charges'], 'class' => 'form-horizontal','method' => 'POST']) !!}
                {!! csrf_field() !!} 
                  <table class="table table-striped table-bordered table-hover" style="width: 100%;">
                      <thead>
                          <tr>
                              <th>Sr No.</th>
                              <th>State Name</th>
                              <th>Country</th>
                              <th>Charges</th>
                              <!--<th>Status</th>-->
                          </tr>
                      </thead>
                      <tbody>
                      
                        @foreach ($collection as $index=>$key) 
                        <input type="hidden" name="shippings_id[]" value="{{ $key->shippings_id }}">
                        <input type="hidden" name="state_id[]" value="{{ $key->id }}">
                       
                            <tr class='item<?= $key->id;?>'>
                              <td><?= $index+1;?></td>
                              <td>{!! $key->country_id==1? $key->state_name:'--'; !!}</td>
                              <td>{!! $key->country_id==1?'India':'Austrelia'; !!}</td>
                              <td><input type="text" name="charges[]" value="{{ $key->charges }}" class="numbervalid"></td>
                              <!--<td>-->
                              <!--  <label class="switch">-->
                              <!--    <input type="checkbox" id="status{{$key->id}}" class="updatestatus" data-mode="Shipping" {{ ($key->status == 'Yes' ) ? 'checked' : '' }} >-->
                              <!--    <span class="slider round"></span>-->
                              <!--  </label>-->
                              <!--</td>-->
                          </tr>
                        @endforeach
                        
                      </tbody>
                  </table>
                  
                    <div class="col-md-12" id="profile-btn" align="center">
                        <input type="submit" class="btn btn-primary " name="submit" value="Update" >
                               
                    </div> 
                {{Form::close()}}     
            </div>
        </div>
      </div>
    </section>
  </section>
</section>   

@endsection
