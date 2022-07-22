@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
      <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="{{url('/admin/product')}}">Product</a></li>
        </ul>
      </div>
    </div>
    <section class="panel">
        <header class="panel-heading">
          <div class="row">
            <div class="col-sm-6">    
                  <h4 class="form-heading">Product Details</h4>
            </div>      
           <div class="col-sm-6">
               <div class="pull-right"> 
                    <a href="{{url('/admin/product')}}" class="btn btn-sm btn-info" >Back</a>
               </div> 
           </div>           
        </div>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
                        
              @if(!empty($data))
                 {!! Form::model($data,array('route' => ['admin.product.update', $data->id],'method'=>'PATCH','files'=>'true','id' =>'product_form')) !!}
              @else
                {!! Form::open(['url'=>['admin/product'], 'method' => 'POST','files'=>'true','id' =>'product_form']) !!}
              @endif 
              {{ csrf_field() }}
                  
              <fieldset>   
              <legend>Product Details</legend>  
              <input type="hidden" name="previous_url" value="{{ url()->previous() }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('Seller Name','Seller Name', array('class' => 'required')) !!}
                                {!!Form::select('user_id',[null => 'Select Seller'] + $sellers, null,['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                          {{ Form::label('Variety Name', 'Variety Name') }}
                          {!! Form::text('product_name', Null,array('class'=>'form-control')) !!}
                          </div>
                        </div>    

                          <div class="col-md-6">
                              <div class="form-group">
                             {!! Form::label('Category Name','Category Name', array('class' => 'required')) !!}
                             {!!Form::select('super_cat_id',[null => 'Select Category'] + $supercategory, null,['class'=>'form-control','id'=>'supercatid']) !!} 
                             </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                             {!! Form::label('Subcategory Name','Subcategory Name', array('class' => 'required')) !!}
                             {!!Form::select('category_id',[null => 'Select Subcategory'] + $category, null,['class'=>'form-control','id'=>'categoryid']) !!} 
                             </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                          {!! Form::label('Moisture','Moisture in %') !!}
                          {!! Form::text('moisture', Null,array('class'=>'form-control numbervalid','maxlength'=>'2')) !!}
                          </div>
                          </div>
                         
                          <div class="col-md-6">
                              <div class="form-group">
                              {!! Form::label('Quantity','Quantity', array('class' => 'required')) !!}
                              {!! Form::number('quantity', Null,array('class'=>'form-control')) !!}
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                              {!! Form::label('Unit','Unit', array('class' => 'required')) !!}
                              {!!Form::select('unit',[null => 'Select Unit','Quintal'=>'Quintal','Kg'=>'Kg','Karet'=>'Karet'], null,['class'=>'form-control','id'=>'unit']) !!}  
                              </div>
                          </div>
                        <div class="col-md-6">
                            <div class="form-group">
                              {!! Form::label('Bid Close Date','Bid Close Date', array('class' => 'required')) !!}
                              {!! Form::text('bid_close_date', Null,array('class'=>'form-control','id'=>'datepicker')) !!}
                            </div>
                        </div>  
                        <div class="col-md-6">
                            <div class="form-group">
                             {!! Form::label('Status','Status', array('class' => 'required')) !!}
                             {!!Form::select('status',[null => 'Select Status','pending'=>'Pending','approved'=>'Approved','decline'=>'Decline'], null,['class'=>'form-control']) !!}  
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                            {{ Form::label('Description','Description') }}
                            {!! Form::textarea('description',null,array('class'=>'form-control','rows'=>'3')) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                              {!! Form::label('Select the quality of your produce','Select the quality of your produce') !!}
                              {{ Form::checkbox('quality[]','mitti',null) }}
                              <span>Mitti</span>&nbsp;&nbsp;
                              {{ Form::checkbox('quality[]','kankad',null) }}
                              <span>Kankad</span>&nbsp;&nbsp;
                              {{ Form::checkbox('quality[]','kida',null) }}
                              <span>Kida</span>&nbsp;&nbsp;
                              {{ Form::checkbox('quality[]','clean',null) }}
                              <span>Clean</span>
                            </div>
                        </div>
                    </div>
                  <div class="row">
                    <div class="col-md-12"> 
                      <fieldset>   
                        <legend>Prices</legend>
                        <div class="row form-group">
                          <div class="col-md-6">
                            {{ Form::label('Admin Base Price','Admin Base Price', array('class' => 'required')) }}
                            {!! Form::text('sell_price', Null,array('class'=>'form-control floatnumberallow','placeholder'=>'Sell Price')) !!}
                          </div>
                          <div class="col-md-6">
                            {{ Form::label('User Max Bid Price','User Max Bid Price') }}
                            {!! Form::text('max_bid_price', Null,array('class'=>'form-control floatnumberallow','placeholder'=>'Max Bid Price','readonly'=>'readonly')) !!}
                          </div>
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
<script type="text/javascript">
$(document).ready(function(){  
  $('#supercatid').change(function(){
    var supercatID = $(this).val();  
    if(supercatID){
        $.ajax({
            type:"GET",
            url:"{{url('admin/get-category')}}/"+supercatID,
            success:function(res){ 
                $("#subcategoryid").empty();
                $('#categoryid').children('option').remove();
                $('#categoryid').append("<option value=''>Select Category</option>");
                $.each(res, function(index, element) {
                    $('#categoryid').append("<option value='"+ index +"'>" + element + "</option>");
                });
            }
        });
    }else{
        $("#subcategoryid").empty();
        $("#categoryid").empty();
    }   
    
  });
//   $('#categoryid').change(function(){
//     var catID = $(this).val();  
//     if(catID){
//         $.ajax({
//           type:"GET",
//           url:"{{url('api/get-subcategorylistbycatid')}}?cat_id="+catID,
//           success:function(res){ 
//                 if(res.status == "true"){
//                   $('#subcategoryid').children('option').remove();
//                   $('#subcategoryid').append("<option value=''>Select Subcategory</option>");
//                   $.each(res.record, function(index, element) {
//                     $('#subcategoryid').append("<option value='"+ element.id +"'>" + element.subcat_name + "</option>");
//                     });
//                 }else {
//                   $('#subcategoryid').children('option').remove();
//                   $('#subcategoryid').append("<option value=''>Subcategory not Available</option>");
//                 }
//           }
//         });
//     }else{
//         $("#subcategoryid").empty();
//     }   
    
//   });
});    
</script>
@endsection
