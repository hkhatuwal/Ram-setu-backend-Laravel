@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
      <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="{{url('/admin/importproduct')}}">Import Products</a></li>
        </ul>
      </div>
    </div>
    <section class="panel">
        <header class="panel-heading">
          <div class="row">
            <div class="col-sm-6">    
                  <h4 class="form-heading">Import Products</h4>
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
              {!! Form::open(['url'=>['admin/import/product'], 'method' => 'POST', 'files'=>'true', 'id' =>'importproduct_form']) !!}
              {{ csrf_field() }}
                  
              <fieldset>   
              <legend>Import Product File</legend> 
                  <div class="row">
                    <div class="col-md-12" >
                      <div class="alert alert-danger" style="font-size: 13px;">
                        <strong>Before Importing read following information.</strong><br>
                        <strong>Import file must be in .xlsx, .xls, .csv format.</strong><br>
                        <strong>Import Excel file column must be in proper sequence.</strong><br>
                        <strong>Sequence : Product Name,  Super category name, Category name, Subcategory name,  Description, Image Name,  INR Mrp Price, INR Sell Price,  Doller Mrp Price,  Doller Sell Price</strong><br>
                        <p style="font-size: 16px;"><strong>To Download demo file in proper sequence</strong> <a class="btn btn-success" href="{{url('public/image/product-demo-excel.xlsx')}}">Download Now</a></p>
                      </div>  
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12" >
                      <div class="form-group" align="center">
                        <input type="file" name="selected_file" class="form-control" accept=".xlsx, .xls, .csv"/>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div align="center">
                          <button class="btn btn-primary">Submit</button>
                      </div>
                    </div>
                  </div>
                  
                </fieldset>
               
            {{Form::close()}} 
               
        </div>
        @if(session()->has('responce'))
        @php $responce = session()->get('responce'); @endphp
        <div class="row">
          <div class="col-md-12" >
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <a class="nav-link active" id="inserted-tab" data-toggle="tab" href="#inserted" role="tab" aria-controls="inserted" aria-selected="true">Inserted Data ({{$responce['inserted']}})</a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link" id="duplicate-tab" data-toggle="tab" href="#duplicate" role="tab" aria-controls="duplicate" aria-selected="false">Duplicate Data ({{$responce['duplicate']}})</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade active" id="inserted" role="tabpanel" aria-labelledby="inserted-tab">
                <div class="table-responsive" style="border: 1px solid #f2f0f0; padding:10px;">
                  <table class="table table-striped table-bordered table-hover" style="width: 100%;">
                      <thead>
                          <tr>
                              <th>Product Name</th>
                              <th>Super Category Name</th>
                              <th>Category Name</th>
                              <th>Subcategory Name</th>
                          </tr>
                      </thead>
                      <tbody>
                        @foreach ($responce['inserted_array'] as $key) 
                            <tr>
                              <td>{{$key[0]}}</td>
                              <td>{{$key[1]}}</td>
                              <td>{{$key[2]}}</td>
                              <td>{{$key[3]}}</td>
                          </tr>
                        @endforeach
                      </tbody>
                  </table>
                </div>
              </div>
              <div class="tab-pane fade" id="duplicate" role="tabpanel" aria-labelledby="duplicate-tab">
                <div class="table-responsive" style="border: 1px solid #f2f0f0; padding:10px;">
                  <table class="table table-striped table-bordered table-hover" style="width: 100%;">
                      <thead>
                          <tr>
                              <th>Product Name</th>
                              <th>Super Category Name</th>
                              <th>Category Name</th>
                              <th>Subcategory Name</th>
                          </tr>
                      </thead>
                      <tbody>
                        @foreach ($responce['duplicate_array'] as $key) 
                            <tr>
                              <td>{{$key[0]}}</td>
                              <td>{{$key[1]}}</td>
                              <td>{{$key[2]}}</td>
                              <td>{{$key[3]}}</td>
                          </tr>
                        @endforeach
                      </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
      </div>
    </section>
  </section>
</section>   

@endsection
