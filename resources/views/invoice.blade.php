<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<!-- If you delete this tag, the sky will fall on your head -->
	<meta name="viewport" content="width=device-width" />

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Inddianstore</title>
	
	<style>
		
		* { 
			margin:0;
			padding:0;
		}
		* { font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; }

		img { 
			max-width: 100%; 
		}
		.collapse {
			margin:0;
			padding:0;
		}
		body {
			margin-top: 50px;
			-webkit-font-smoothing:antialiased; 
			-webkit-text-size-adjust:none; 
			width: 100%!important; 
			height: 100%;
		}
		
		a { color: #2BA6CB;}

		.btn {
			text-decoration:none;
			color: #FFF;
			background-color: #666;
			padding:10px 16px;
			font-weight:bold;
			margin-right:10px;
			text-align:center;
			cursor:pointer;
			display: inline-block;
		}

		p.callout {
			padding:15px;
			background-color:#ECF8FF;
			margin-bottom: 15px;
		}
		.callout a {
			font-weight:bold;
			color: #2BA6CB;
		}

		table.social {
		/* 	padding:15px; */
			background-color: #ebebeb;
			
		}
		.social .soc-btn {
			padding: 3px 7px;
			font-size:12px;
			margin-bottom:10px;
			text-decoration:none;
			color: #FFF;font-weight:bold;
			display:block;
			text-align:center;
		}
		a.fb { background-color: #3B5998!important; }
		a.tw { background-color: #1daced!important; }
		a.gp { background-color: #DB4A39!important; }
		a.ms { background-color: #000!important; }

		.sidebar .soc-btn { 
			display:block;
			width:100%;
		}

		table.head-wrap { width: 100%;}

		.header.container table td.logo { padding: 15px; }
		.header.container table td.label { padding: 15px; padding-left:0px;}

		table.body-wrap { width: 100%;}

		table.footer-wrap { width: 100%;	clear:both!important;
		}
		.footer-wrap .container td.content  p { border-top: 1px solid rgb(215,215,215); padding-top:15px;}
		.footer-wrap .container td.content p {
			font-size:10px;
			font-weight: bold;
			
		}

		h1,h2,h3,h4,h5,h6 {
		font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; line-height: 1.1; margin-bottom:15px; color:#000;
		}
		h1 small, h2 small, h3 small, h4 small, h5 small, h6 small { font-size: 60%; color: #6f6f6f; line-height: 0; text-transform: none; }

		h1 { font-weight:200; font-size: 44px;}
		h2 { font-weight:200; font-size: 37px;}
		h3 { font-weight:500; font-size: 27px;}
		h4 { font-weight:500; font-size: 23px;}
		h5 { font-weight:900; font-size: 17px;}
		h6 { font-weight:900; font-size: 14px; text-transform: uppercase; color:#444;}

		.collapse { margin:0!important;}

		p, ul { 
			margin-bottom: 10px; 
			font-weight: normal; 
			font-size:14px; 
			line-height:1.6;
		}
		p.lead { font-size:14px; }
		p.last { margin-bottom:0px;}

		ul li {
			margin-left:5px;
			list-style-position: inside;
		}

		ul.sidebar {
			background:#ebebeb;
			display:block;
			list-style-type: none;
		}
		ul.sidebar li { display: block; margin:0;}
		ul.sidebar li a {
			text-decoration:none;
			color: #666;
			padding:10px 16px;
		/* 	font-weight:bold; */
			margin-right:10px;
		/* 	text-align:center; */
			cursor:pointer;
			border-bottom: 1px solid #777777;
			border-top: 1px solid #FFFFFF;
			display:block;
			margin:0;
		}
		ul.sidebar li a.last { border-bottom-width:0px;}
		ul.sidebar li a h1,ul.sidebar li a h2,ul.sidebar li a h3,ul.sidebar li a h4,ul.sidebar li a h5,ul.sidebar li a h6,ul.sidebar li a p { margin-bottom:0!important;}

		.container {
			display:block!important;
			max-width:700px!important;
			margin:0 auto!important; /* makes it centered */
			clear:both!important;
		}

		.content {
			padding:15px;
			max-width:700px;
			margin:0 auto;
			display:block; 
		}

		.content table { width: 100%; }
		.column {
			width: 300px;
			float:left;
		}
		.column tr td { padding: 15px; }
		.column-wrap { 
			padding:0!important; 
			margin:0 auto; 
			max-width:600px!important;
		}
		.column table { width:100%;}
		.social .column {
			width: 280px;
			min-width: 279px;
			float:left;
		}

	
		.clear { display: block; clear: both; }

        .pull-right {
            float:right;
        }
	
		@media only screen and (max-width: 600px) {
			
			a[class="btn"] { display:block!important; margin-bottom:10px!important; background-image:none!important; margin-right:0!important;}

			div[class="column"] { width: auto!important; float:none!important;}
			
			table.social div[class="column"] {
				width:auto!important;
			}

		}
	</style>

</head>
 
<body>

<!-- HEADER -->
<div style="margin: 0px !important;text-align: center;"><img src="{{ url('public/image/logo.png') }}"/></div> 
<!-- /HEADER -->


<!-- BODY -->
<table class="body-wrap">
	<tr>
		<td></td>
		<td class="container">

			<div class="content">
			<table>
				<tr>
					<td>
					<p class="lead">
						Your Order Number : {{$order->order_id}}<br>
				        Order Date : {{$order->order_date}}<br>
				        Order Status : {{$order->status}}
					</p>	
					<table>
					    <tr width="100%">
					        <td width="60%"> {{ Form::label('Delivery Address', 'Delivery Address') }}<br>
					           <p>User Name : {!! $order->delivery->name;!!}</p>
					           <p>Email : {!! $order->delivery->email;!!}</p>
					           <p>Mobile : {!! $order->delivery->mobile;!!}</p>
					           @if(!empty($order->delivery->alternate_mobile))
					               <p>Alternate Mobile : {!! $order->delivery->alternate_mobile;!!}</p>
					           @endif
					           <p>{!! $order->delivery->country.' '.$order->delivery->state.' '.$order->delivery->city.' '.$order->delivery->pincode;!!}</p>
					            @if(!empty($order->delivery->area_name))
					               <p>Area Name : {!! $order->delivery->area_name;!!}</p>
					            @endif
					            @if(!empty($order->delivery->street_no))
					               <p>Street No : {!! $order->delivery->street_no;!!}</p>
					            @endif   
					           <p>Address : {!! $order->delivery->address;!!}</p>
					        </td>
					                		            
					        <td width="40%">
					            {{ Form::label('Order Total', 'Order Total') }}<br>
				                <p>Order Subtotal ({{$order->currency}}) :<span class="pull-right">{{$order->subtotal}}</span></p>
				                <p>Shipping ({{$order->currency}}) : <span class="pull-right">{{$order->shipping_charges}}</span></p>
				                  <hr>
				                <p>Grand Total  ({{$order->currency}}):<span class="pull-right">{{$order->grand_total}}</span></p> 
					        </td>
					    </tr>
					</table>      
				<table>
                    <thead>
                      <tr>
                        <th>Product Name</th>
                        <th>Price ( {{$order->currency}} )</th>
                        <th>Quantity</th>
                        <th>Total</th>
                      </tr>
                    </thead>
                    <tbody>
                    @foreach($order->product as $main)
                      <tr style="text-align: center;">
                        <td>{{ $main['product_name'] }}</td>
                        <td>{{$main['sell_price']}}</td>
                        <td>{{$main['qty']}}</td>
                        <td>{{$main['subtotal']}}</td>
                      </tr>
                    @endforeach 
                    </tbody>
                    
                </table>
					
						<br/>							
						
					</td>
				</tr>
			</table>
			</div>
									
		</td>
		<td></td>
	</tr>
</table><!-- /BODY -->



</body>
</html>