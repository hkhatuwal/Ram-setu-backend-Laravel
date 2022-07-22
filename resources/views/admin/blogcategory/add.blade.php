@extends('admin.layouts.view')
@section('content')
<style type="text/css">
input[type=checkbox] {
    height: 20px;
    width: 20px;
}
.error {
    
    font-size:14px;
}

.valid {
    border: 1px solid #56ef5d;
}
.js-errors {
    background:#f93337;
    border-radius:4px;
    color:#FFF;
    font-size:.8em;
    list-style-type:square;
    margin-bottom:1em;
    padding:1em;
}

.js-errors {
	  display:none;
}

.js-errors li {
	  margin-left:1em;
  margin-bottom:.5em;
  padding-left:0;
}
ul.error input[type="checkbox"] + label::before,
ul.error input[type="radio"] + label::before {
  	border-color:#F93337;
}

ul.error input[type="checkbox"] + label,
ul.error input[type="radio"] + label {
	  color:#F93337;
}



</style>  
<section id="main-content">
  <section class="wrapper">
    <div class="row">
          <div class="col-lg-12">
              <!--breadcrumbs start -->
              <ul class="breadcrumb">
                  <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
                  <li><a href="{{url('/admin/blog-category')}}">Blog Category</a></li>
              </ul>
              <!--breadcrumbs end -->
          </div>
    </div>
    <section class="panel">
        <header class="panel-heading">
          <div class="row">
            <div class="col-sm-6">    
                  <h4 class="form-heading">@if(!empty($data))
			  			       Update Blog Category
			  			    @else
			  			       Add Blog Category
			  			    @endif</h4>
            </div>      
           <div class="col-sm-6">
               <div class="pull-right"> 
                    <a href="{{url('/admin/blog-category')}}" class="btn btn-sm btn-info" >Back</a>
               </div> 
           </div>           
        </div>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
              
    @if(!empty($data))
       {!! Form::model($data,array('route' => ['admin.blog-category.update', $data->id],'method'=>'PATCH','class'=>'','files'=>'true','id' =>'blogcat_form')) !!}
    @else
      {!! Form::open(['url'=>['admin/blog-category'], 'class' => '','method' => 'POST','id' =>'blogcat_form']) !!}
    @endif 
         {{ csrf_field() }}
        
        
      <fieldset>   
      <legend>Blog Category</legend> 
        
        <div class="">
                  	  <div class="row">
                  	  	<div class="col-md-12">
						  <div class="form-group margin-top15">
						  	{!! Form::label('Category Name','Category Name', array('class' => 'required bmd-label-floating')) !!}
                            {!! Form::text('blog_cat_name',null,array('class'=>'form-control')) !!}
                            @if ($errors->has('blog_cat_name'))
							    <div class="error">{{ $errors->first('blog_cat_name') }}</div>
							@endif
						  </div>
                  	  	</div>
                  	  </div>
                  	 @if(!empty($data->id))
                       <input type="submit" class="btn btn-raised btn-primary" name="submit" value="Update" >
                     @else
                       <input type="submit" class="btn btn-raised btn-primary" name="submit" value="Submit" >
                     @endif
                  </div>
     
      </fieldset>  
       
    

    

        {{Form::close()}} 
               
                </div>
            </div>
    </section>
  </section>
</section>   



<script type="text/javascript">
 $('#blogcat_form').validate({
    rules: {
    blog_cat_name: {
      required: true,
      required: true
    },
    
    },
    highlight: function(element) {
      $(element).closest('.control-group').removeClass('success').addClass('error');
    },
    success: function(element) {
       element
       .text('').addClass('valid')
       .closest('.control-group').removeClass('error').addClass('success');
    }
});  

</script>
@endsection