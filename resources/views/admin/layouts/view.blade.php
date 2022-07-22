<!DOCTYPE html>
<html lang="en">
  
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Mosaddek">
    <meta name="keyword" content="FlatLab, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <link rel="shortcut icon" href="{{ url('public/image/logo-icon.png') }}">

    <title>Raam Setu</title>
    
    <link href="{{url('/public/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{url('/public/css/bootstrap-reset.css')}}" rel="stylesheet">
    <link href="{{url('/public/css/font-awesome.css')}}" rel="stylesheet"/>
    <link href="{{url('/public/css/owl.carousel.css')}}" rel="stylesheet" type="text/css">
    <link href="{{url('/public/css/slidebars.css')}}" rel="stylesheet">
    <link href="{{url('/public/css/style.css')}}" rel="stylesheet">
    <link href="{{url('/public/css/style-responsive.css')}}" rel="stylesheet"/>
    <link href="{{url('/public/css/dataTables.bootstrap.css')}}" rel="stylesheet"/>
    <link href="{{url('/public/css/dataTables.responsive.css')}}" rel="stylesheet"/>
    <link href="{{url('/public/css/bootstrap-fileupload.css')}}" rel="stylesheet"/>
    <link href="{{url('public/css/toastr.css')}}" rel="stylesheet"/> 
    <link href="{{url('/public/css/datepicker.css')}}" rel="stylesheet"/>
    <link href="{{url('/public/css/jquery-confirm.min.css')}}" rel="stylesheet"/>
    <link href="{{url('/public/css/jquery-ui.css')}}" rel="stylesheet"/>

    <script type="text/javascript" src="{{url('/public/js/jquery.js')}}"></script>
    <script type="text/javascript" src="{{url('/public/js/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{url('/public/js/jquery.validate.js')}}"></script>
    <script type="text/javascript" src="{{url('/public/js/jquery.validate.min.js')}}"></script>
    <script type="text/javascript" src="{{url('/public/js/bootstrap-fileupload.min.js')}}"></script>
    <script type="text/javascript" src="{{url('/public/js/bootstrap-datepicker.js')}}"></script>
    <script type="text/javascript" src="{{url('/public/js/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{url('public/js/toastr.min.js')}}"></script>
    <script type="text/javascript" src="{{url('/public/js/jquery-confirm.min.js')}}"></script>
    <style type="text/css">
        label.error {
            font-weight: bold;
            color: red;
            padding: 2px 8px;
            margin-top: 2px;
            background-color: #FFF;
        } 
        .ui-widget-content {
            border: 1px solid #b9cd6d;
        }
        .ui-widget-header,.ui-state-default, ui-button{  
            background:#b9cd6d;  
            border: 1px solid #b9cd6d;  
            color: #FFFFFF;  
            font-weight: bold;  
        } 
    </style>
  </head>

  <body>

  <section id="container" >
      {!! Toastr::message() !!}
      <!--header start-->
      <div id="dialog1123" title="Confirmation" >Are you sure?</div> 
      <header class="header white-bg">
              <div class="sidebar-toggle-box">
                  <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
              </div>
            <a href="{{url('/dashboard')}}" class="logo" >RaamSetu</a>
            <div class="nav notify-row" id="top_menu">
                <ul class="nav top-menu"></ul>
            </div>
            <div class="top-nav ">
                <!--search & user info start-->
                <ul class="nav pull-right top-menu">
                   
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="username">{{ Auth::user()->name }}</span>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu extended logout">
                            <div class="log-arrow-up"></div>
                            <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                   Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                   
                </ul>
            </div>
        </header>
      
        @include('admin.layouts.sidebar') 

        @yield('content')
   </section>

    <!-- Scripts -->
    <script src="{{url('/public/js/jquery.dataTables.min.js')}}"  type="text/javascript"></script>
    <script src="{{url('/public/js/dataTables.bootstrap.min.js')}}"  type="text/javascript"></script>
    <script src="{{url('/public/js/dataTables.responsive.js')}}"  type="text/javascript"></script>
    <script src="{{url('/public/js/jquery.dcjqaccordion.2.7.js')}}" type="text/javascript" ></script>
    <script src="{{url('/public/js/jquery.scrollTo.min.js')}}"  type="text/javascript"></script>
    <script src="{{url('/public/js/jquery.sparkline.js')}}" type="text/javascript"></script>
    <script src="{{url('/public/js/owl.carousel.js')}}"   type="text/javascript"></script>
    <script src="{{url('/public/js/jquery.customSelect.min.js')}}"  type="text/javascript"></script>
    <script src="{{url('/public/js/respond.min.js')}}"   type="text/javascript"></script>
    <script src="{{url('/public/js/slidebars.min.js')}}"  type="text/javascript"></script>
    <script src="{{url('/public/js/common-scripts.js')}}"  type="text/javascript"></script>
    <script src="{{url('/public/js/count.js')}}"  type="text/javascript"></script>
    <script src="{{url('/public/js/custom.js')}}"  type="text/javascript"></script>

<script  type="text/javascript">

$(document).ready(function(){ 
  $("#dialog1123").dialog({
    autoOpen: false
  }); 
  $('#datepicker').datepicker({
      dateFormat: 'yy-mm-dd',
      autoclose: true
  });
  $('#startdatepicker').datepicker({
      dateFormat: 'yy-mm-dd',
      autoclose: true
  });
  $('#enddatepicker').datepicker({
      dateFormat: 'yy-mm-dd',
      autoclose: true
  });
  $('.numbervalid').keyup(function () {
      var $th = $(this);
      $th.val($th.val().replace(/[^0-9]/g, function (str) {
        return '';
      }));
  });  
  var hash = window.location.hash;
  hash && $('ul.nav a[href="' + hash + '"]').tab('show');
  $('.nav-tabs a').click(function (e) {
    $(this).tab('show');
    var scrollmem = $('body').scrollTop() || $('html').scrollTop();
    window.location.hash = this.hash;
    $('html,body').scrollTop(scrollmem);
  });
  $('#data_in_table').DataTable({
      scrollY: true,
      scrollX: true,
      language: {
        searchPlaceholder: "Search records"
      }
  });
  $("#owl-demo").owlCarousel({
      navigation : true,
      slideSpeed : 300,
      paginationSpeed : 400,
      singleItem : true,
      autoPlay:true
  });
  $('.table').on('change','.updatestatus', function() {
      var uniqueid = $(this).attr('id');
      var catid = uniqueid.substr(6); 
      var table = $(this).attr('data-mode');
      $.ajax({
          type:"GET",
          url: "{{url('/admin/status-manage')}}"+'/'+table+'/'+catid,
          success:function(res){ 
              if(res=='true'){
                  toastr.success("Active status successfully.");
              }else if(res=='false'){
                  toastr.success("Deactive status successfully.");
              }else{}
          }
      });
  });
  $(".table").on('click','.deleterecord', function(){
        var id = $(this).attr('id');
        var token = $("meta[name='csrf-token']").attr("content");
        var segment = "{{ Request::segment(2) }}";
        var newurl = "{{url('/admin')}}/"+segment+"/"+id;
        $.confirm({
          title: 'Are you sure!',
          content: ' ',
          buttons: {
              confirm: function(){
                $.ajax({
                    type:"delete",
                    data: {
                         "id": id,
                         "_token": token,
                    },
                    url:newurl,
                    success:function(res){ 
                      if(res.status==true){
                          $('.item' + id).remove();
                          toastr.success("Record deleted successfully");  
                      }else{
                          toastr.error("Sorry record not deleted!"); 
                      }
                    }
                });
              },
              cancel: function(){
              },
          }
        });
  });      
});

</script>

</body>
</html>