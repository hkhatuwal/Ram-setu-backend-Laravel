@extends('admin.layouts.view')
@section('content')
<style type="text/css">
  .listcheck input[type=checkbox] {
      width: 18px;
      height: 16px;
  }
  .listcheck span {
     font-size: 14px;
  }
</style>
<section id="main-content">
  <section class="wrapper">
    <div class="row">
      <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="{{url('/admin/category')}}">Category</a></li>
            <li><a href="#">{{$category->category_name}}</a></li>
        </ul>
      </div>
    </div>
    <section class="panel">
        <header class="panel-heading">
          <div class="row">
            <div class="col-sm-6">    
                  <h4 class="form-heading">Assign Subcategory as Feature</h4>
            </div>      
           <div class="col-sm-6">
               <div class="pull-right"> 
                    <a href="{{url('/admin/category')}}" class="btn btn-sm btn-info" >Back</a>
               </div> 
           </div>           
        </div>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
                {!! Form::open(['url'=>['admin/category-home-feature/'.$category->id], 'method' => 'POST','id' =>'categoryhomefeature_form']) !!}
                {{ csrf_field() }}
                  <fieldset>   
                  <legend>{{$category->category_name}} Subcategory List</legend>  
                      <div class="row">
                        @php $esist = unserialize($category->homelist); @endphp
                        @foreach($subcategory as $key => $value)
                           <div class="col-md-4 listcheck">
                            @if(!empty($category->homelist))
                            {{ Form::checkbox('homelist[]',$key,in_array($key,$esist)? true:false) }}
                            @else
                            {{ Form::checkbox('homelist[]',$key,null) }}
                            @endif
                            <span>{{$value}}</span>
                           </div>
                        @endforeach 
                      </div>
                  </fieldset>
                  <div class="row">
                      <div class="col-md-12">
                          <div align="center">
                              <button class="btn btn-primary">Update</button>
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
