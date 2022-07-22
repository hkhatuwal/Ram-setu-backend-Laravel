<?php 
$actroute = Request::segment(2);
?>
 <aside>
    <div id="sidebar"  class="nav-collapse ">
        <!-- sidebar menu start-->
        <ul class="sidebar-menu" id="nav-accordion">
            <li class="sub-menu">
                <a href="{{url('admin/dashboard')}}" class="{{ $actroute ==  'dashboard' ? 'active' : ''  }}">
                    <i class="fa fa-laptop"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <!-- <li class="sub-menu">
                <a href="{{url('admin/order-active')}}" class="{{ $actroute ==  'order-active' ? 'active' : ''  }}">
                    <i class="fa fa-laptop"></i>
                    <span>Pending Order</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="{{url('admin/order-delivered')}}" class="{{ $actroute ==  'order-delivered' ? 'active' : ''  }}">
                    <i class="fa fa-laptop"></i>
                    <span>Delivered Order</span>
                </a>
            </li> -->
            <li class="sub-menu">
                <a href="{{url('admin/seller')}}" class="{{ $actroute ==  'seller' ? 'active' : ''  }}">
                    <i class="fa fa-user"></i>
                    <span>Seller</span>
                    <span class="badge bg-info pull-right">{{$setting->seller}}</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="{{url('admin/buyer')}}" class="{{ $actroute ==  'buyer' ? 'active' : ''  }}">
                    <i class="fa fa-user"></i>
                    <span>Buyer</span>
                    <span class="badge bg-info pull-right">{{$setting->buyer}}</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="{{url('admin/supercategory')}}" class="{{ $actroute ==  'supercategory' ? 'active' : ''  }}">
                    <i class="fa fa-user"></i>
                    <span>Category Master</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="{{url('admin/category')}}" class="{{ $actroute ==  'category' ? 'active' : ''  }}">
                    <i class="fa fa-user"></i>
                    <span>Subcategory Master</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="{{url('admin/product/create')}}" >
                    <i class="fa fa-user"></i>
                    <span>Add New Product</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="{{url('admin/pending-product')}}" class="{{ $actroute ==  'pending-product' ? 'active' : ''  }}">
                    <i class="fa fa-user"></i>
                    <span>Pending Products</span>
                    <span class="badge bg-info pull-right">{{$setting->pending}}</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="{{url('admin/product')}}">
                    <i class="fa fa-user"></i>
                    <span>Active Products</span>
                    <span class="badge bg-info pull-right">{{$setting->approved}}</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="{{url('admin/complete-product')}}" class="{{ $actroute ==  'complete-product' ? 'active' : ''  }}">
                    <i class="fa fa-user"></i>
                    <span>Completed Products</span>
                    <span class="badge bg-info pull-right">{{$setting->history}}</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="{{url('admin/expired-product')}}" class="{{ $actroute ==  'expired-product' ? 'active' : ''  }}">
                    <i class="fa fa-user"></i>
                    <span>Declined Products</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="{{url('admin/mandirate')}}" class="{{ $actroute ==  'mandirate' ? 'active' : ''  }}">
                    <i class="fa fa-user"></i>
                    <span>Mandi Rate</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="{{url('admin/blog')}}" class="{{ $actroute ==  'blog' ? 'active' : ''  }}">
                    <i class="fa fa-user"></i>
                    <span>Blogs</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="{{url('admin/faqs')}}" class="{{ $actroute ==  'faqs' ? 'active' : ''  }}">
                    <i class="fa fa-user"></i>
                    <span>Faqs</span>
                </a>
            </li>
            <!-- <li class="sub-menu">
                <a class="{{ in_array($actroute, ['product','supercategory','category','subcategory']) ? 'active' : ''  }}">
                    <i class="fa fa-book"></i>
                    <span>Product Master</span>
                </a>
                <ul class="sub">
                    <li class="{{ $actroute=='supercategory' ? 'active' : ''  }}"><a href="{{url('/admin/supercategory')}}">Super Category Master</a></li>
                    <li class="{{ $actroute=='category' ? 'active' : ''  }}"><a href="{{url('/admin/category')}}">Category Master</a></li>
                    <li class="{{ $actroute=='subcategory' ? 'active' : ''  }}"><a href="{{url('/admin/subcategory')}}">Subcategory Master</a></li>
                    <li class="{{ $actroute=='product' ? 'active' : ''  }}"><a href="{{url('/admin/product')}}">Products</a></li>
                </ul>
            </li> -->
            <!-- <li class="sub-menu">
                <a href="{{url('admin/order-report')}}" class="{{ $actroute ==  'order-report' ? 'active' : ''  }}">
                    <i class="fa fa-user"></i>
                    <span>Report</span>
                </a>
            </li>
            <li class="sub-menu">
                <a class="{{ in_array($actroute, ['country','state','city']) ? 'active' : ''  }}">
                    <i class="fa fa-book"></i>
                    <span>Location Master</span>
                </a>
                <ul class="sub">
                    <li class="{{ $actroute=='country' ? 'active' : ''  }}"><a href="{{url('/admin/country')}}">Country</a></li>
                    <li class="{{ $actroute=='state' ? 'active' : ''  }}"><a href="{{url('/admin/state')}}">State</a></li>
                    <li class="{{ $actroute=='city' ? 'active' : ''  }}"><a href="{{url('/admin/city')}}">City</a></li>
                </ul>
            </li>
            <li class="sub-menu">
                <a href="{{url('admin/coupon')}}" class="{{ $actroute ==  'coupon' ? 'active' : ''  }}">
                    <i class="fa fa-user"></i>
                    <span>Coupon</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="{{url('admin/shipping-charges')}}" class="{{ $actroute ==  'shipping-charges' ? 'active' : ''  }}">
                    <i class="fa fa-user"></i>
                    <span>Shipping Charges</span>
                </a>
            </li> -->
            
                              
        </ul>
        <!-- sidebar menu end-->
    </div>
</aside>
<!--sidebar end-->
      
<style type="text/css">
fieldset {
    margin: 5px 2px;
    font-size: 11px;
}
fieldset {
    display: block;
    -webkit-margin-start: 2px;
    -webkit-margin-end: 2px;
    -webkit-padding-before: 0.35em;
    -webkit-padding-start: 0.75em;
    -webkit-padding-end: 0.75em;
    -webkit-padding-after: 0.625em;
    min-width: -webkit-min-content;
    border-width: 2px;

    border-style: groove;
    border-color: threedface;
    border-image: initial;
}
table {
    display: table;
    border-collapse: separate;
    border-spacing: 2px;
    border-color: grey;
}

table {
    display: table;
    border-collapse: separate;
    border-spacing: 2px;
    border-color: grey;
}
legend {
    border: 1px solid #666666;
    padding: 3px 10px;
    border-radius: 5px;
    background: #566263;
    color: #fff;
    font-size: 14px;
}
legend {
    width: auto !important;
    display: block;
    -webkit-padding-start: 2px;
    -webkit-padding-end: 2px;
    border-width: initial;
    border-style: none;
    border-color: initial;
    border-image: initial;
}
.required:after{ 
    content:'*'; 
    color:red; 
    padding-left:5px;
}
</style>
