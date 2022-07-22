@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row">
          <div class="col-lg-12">
              <ul class="breadcrumb">
                  <li><a href="{{url('/admin/dashboard')}}"><i class="fa fa-home"></i>Home</a></li>
                  <li><a href="{{url('/admin/customers')}}">Customers</a></li>
              </ul>
          </div>
    </div>
    <section class="panel">
          <div class="row">
            <div class="col-md-12 col-xs-6">
              <header class="panel-heading">Customers
                <a href="{{url('/admin/download/customers')}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;">
                  <i class="fa fa-download" aria-hidden="true"></i>Download
                </a>
                <a href="{{url('/admin/customers/create')}}" class="pull-right btn btn-sm btn-info" style="margin: 2px;">
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
                              <th>Full Name</th>
                              <th>Email Id</th>
                              <th>Mobile</th>
                              <th>Alter Mobile</th>
                              <th>Gender// DOB</th>
                              <th>Created Date</th>
                              <th>Pending Order</th>
                              <!--<th>Process Order</th>-->
                              <th>Delivered Order</th>
                              <th>Total Order</th>
                              <th>Status</th>
                              <th style="width:10%;">Action</th>
                          </tr>
                      </thead>
                      <tbody>
                      <?php 
                        $i=1;
                        foreach ($users as $key) {
                        ?>
                            <tr class='item<?= $key->id;?>'>
                              <td><?= $i;?></td>
                              <td>{{$key->name}}</td>
                              <td>{{$key->email}}</td>
                              <td>{{$key->mobile}}</td>
                              <td>{{$key->alternate_mobile}}</td>
                              <td>{!! $key->gender.'<br>'.$key->dob;!!}</td>
                              <td>{{$key->created_at}}</td>
                              <td>{{$key->order_pending}}</td>
                              <!--<td>{{$key->order_process}}</td>-->
                              <td>{{$key->order_delivered}}</td>
                              <td>{{$key->order_total}}</td>
                              <td>
                                <label class="switch">
                                  <input type="checkbox" id="status{{$key->id}}" class="updatestatus" data-mode="Users" {{ ($key->status == 'Yes' ) ? 'checked' : '' }} >
                                  <span class="slider round"></span>
                                </label>
                              </td>
                              <td class="visible-md visible-lg">
                                  <div class="">
                                  <a  class="btn btn-sm btn-info show-tooltip act-no" title="Show Details" href="{{ URL::to('admin/customers/'.$key->id) }}"  ><i class="fa fa-edit"></i></a>
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
