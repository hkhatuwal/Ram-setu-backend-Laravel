@extends('admin.layouts.view')
@section('content')
<section id="main-content">
  <section class="wrapper">
    <div class="row state-overview">
        <div class="col-lg-3 col-sm-6">
            <a href="{{ url('admin/seller') }}">
                <section class="panel">
                    <div class="symbol terques">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="value">
                        <h1>{{$setting->seller}}</h1>
                        <p>Seller</p>
                    </div>
                </section>
            </a>
        </div>
        <div class="col-lg-3 col-sm-6">
            <a href="{{ url('admin/buyer') }}">
                <section class="panel">
                    <div class="symbol terques">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="value">
                        <h1>{{$setting->buyer}}</h1>
                        <p>Buyer</p>
                    </div>
                </section>
            </a>
        </div>
        <div class="col-lg-3 col-sm-6">
            <a href="{{ url('admin/pending-product') }}">
                <section class="panel">
                    <div class="symbol red">
                        <i class="fa fa-tags"></i>
                    </div>
                    <div class="value">
                        <h1>{{$setting->pending}}</h1>
                        <p>Pending Product</p>
                    </div>
                </section>
            </a>
        </div>
        <div class="col-lg-3 col-sm-6">
            <a href="{{ url('admin/product') }}">
                <section class="panel">
                    <div class="symbol blue">
                        <i class="fa fa-bar-chart-o"></i>
                    </div>
                    <div class="value">
                        <h1>{{$setting->approved}}</h1>
                        <p>Approved Product</p>
                    </div>
                </section>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <ul class="list-group">
                <li class="list-group-item list-group-item-info">Today Product Listing <strong class="pull-right">{!! count($todaylisting); !!}</strong></li> 
                @if(count($todaylisting)>0)
                @foreach($todaylisting as $listing)
                <li class="list-group-item">{!! $listing->category_name.', '.$listing->super_cat_name; !!}  ||  <b>Seller : </b>{!! $listing->name.'-'.$listing->mobile; !!} <b>Qty : </b>{!! $listing->quantity.' '.$listing->unit; !!} <strong class="pull-right"><a  class="show-tooltip" title="View More" href="{{ URL::to('admin/product/'.$listing->id.'#ptdetails') }}"  >View</a></strong></li> 
                @endforeach
                @else
                <li class="list-group-item">Today product listing not found yet</li>
                @endif
            </ul>
        </div>
        <div class="col-sm-6">
            <ul class="list-group">
                <li class="list-group-item list-group-item-info">Today Biding Close Listing <strong class="pull-right">{!! count($todaylisting); !!}</strong></li>    
                @if(count($closelisting)>0)
                @foreach($closelisting as $listing)
                <li class="list-group-item">{!! $listing->category_name.', '.$listing->super_cat_name; !!}  ||  <b>Seller : </b>{!! $listing->name.'-'.$listing->mobile; !!} <b>Qty : </b>{!! $listing->quantity.' '.$listing->unit; !!} <strong class="pull-right"><a  class="show-tooltip" title="View More" href="{{ URL::to('admin/product/'.$listing->id.'#ptdetails') }}"  >View</a></strong></li> 
                @endforeach
                @else
                <li class="list-group-item">Close biding listing not found yet</li>
                @endif 
            </ul>
        </div>
    </div>
  </section>
</section> 


@endsection