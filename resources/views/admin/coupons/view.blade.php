@extends('admin.layouts.view')
@section('content')

<section id="main-content">
  <section class="wrapper">
    <div class="row">
          <div class="col-lg-12">
              <ul class="breadcrumb">
                  <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i>Home</a></li>
                  <li><a href="{{url('/admin/coupon')}}">Coupon</a></li>
              </ul>
          </div>
    </div>
    <section class="panel">
          <div class="row">
            <div class="col-md-12 col-xs-6">
              <header class="panel-heading">Coupon
                <!--<a href="{{url('/admin/download/coupon')}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;">-->
                <!--  <i class="fa fa-download" aria-hidden="true"></i>Download-->
                <!--</a>-->
                <a href="{{url('/admin/coupon/create')}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;">
                    <i class="fa fa-plus" aria-hidden="true"></i>Add
                </a>
              </header>
            </div>
        </div>
      
        <div class="panel-body">
            <div class="adv-table editable-table ">
                <div class="space15"></div>
                 <div class="table-responsive" style="border: 1px solid #f2f0f0; padding:10px;">
                
                  <table class="table table-striped table-bordered table-hover" id="data_in_table" style="width: 100%;">
                      <thead>
                          <tr>
                              <th>Sr No.</th>
                              <th>Coupon Code</th>
                              <th>Title</th>
                              <th>Discount</th>
                              <th>Status</th>
                              <th style="width:10%;">Action</th>
                          </tr>
                      </thead>
                      <tbody>
                      <?php 
                        $i=1;
                        foreach ($record as $key) {
                        ?>
                            <tr class='item<?= $key->id;?>'>
                              <td><?= $i;?></td>
                              <td>{{$key->coupon_code}}</td>
                              <td>{{$key->title}}</td>
                              <td>{{$key->discount}}</td>
                              <td>
                                <label class="switch">
                                  <input type="checkbox" id="status{{$key->id}}" class="updatestatus" data-mode="Coupon" {{ ($key->status == 'Yes' ) ? 'checked' : '' }} >
                                  <span class="slider round"></span>
                                </label>
                              </td>
                              <td class="visible-md visible-lg">
                                  <div class="">
                                  <a  class="btn btn-sm btn-info show-tooltip act-no" title="Edit" href="{{ URL::to('admin/coupon/'.$key->id.'/edit') }}"  ><i class="fa fa-edit"></i></a>

                                  <a class="btn btn-sm btn-danger show-tooltip act-no deleterecord" title="Delete" id="{{$key->id}}"><i class="fa fa-trash-o"></i></a>
                                  </div>
                              </td>
                          </tr>
                        <?php $i++; } ?>
                      </tbody>
                  </table>
            </div>
        </div>
      </div>
    </section>
  </section>
</section>   

@endsection
