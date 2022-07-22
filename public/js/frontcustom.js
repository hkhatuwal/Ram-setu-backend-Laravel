$(function(){
	var options = {
        key: "rzp_test_ztmKLGAOBMy8dn",
        amount: "",
        currency: "INR",
        name: 'Event Management',
        description: 'Test Transaction',
        image: 'https://i.imgur.com/n5tjHFD.png',
        order_id: "",
        prefill: {
	        "name": "",
	        "email": "",
	        "contact": ""
	    },
        handler: demoSuccessHandler
    }
   

    $('#register_form').validate({
	    rules: {
	        name: {
		        required: true,
		        required: true
	        },
	        email: {
	         	required: true,
	            email: true
	        },
	        mobile: {
	         	required: true,
	            required: true
	        },
	        slot_time: {
	         	required: true,
	            required: true
	        },
	    },
	    messages: {
	        name: {
	            required: "Enter your full name.",
	        },
	        email: {
	            required: "Valid email-Id is required.",
	        },
	        mobile: {
	            required: "Mobile number field is required.",
	        },
	        slot_time: {
	            required: "Choose available slot time which will suitable for you.",
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    submitHandler: function (form) {
	    	$.ajax({
                    url: $(form).attr('action'),
                    type: 'post',
                    data: $(form).serialize(),
                    success: function(response) {
                    	options.amount = response.payable_amt;
                    	options.order_id = response.order_id;
                    	options.prefill.name = response.name;
                    	options.prefill.email = response.email;
                    	options.prefill.contact = response.mobile;
                        r.open();
                       //console.log(options)
                    }            
                });
	    }
	    // success: function(element) {
	    //     let parameter = $('#register_form').serialize();
	    //     console.log(parameter)
	    // }
	});
    window.r = new Razorpay(options);

});
function demoSuccessHandler(transaction) {
    // You can write success code here. If you want to store some data in database.
    // $("#paymentDetail").removeAttr('style');
    // $('#paymentID').text(transaction.razorpay_payment_id);
    // var paymentDate = new Date();
    // $('#paymentDate').text(
    //         padStart(paymentDate.getDate()) + '.' + padStart(paymentDate.getMonth() + 1) + '.' + paymentDate.getFullYear() + ' ' + padStart(paymentDate.getHours()) + ':' + padStart(paymentDate.getMinutes())
    //         );
    console.log(transaction);
    // $.ajax({
    //     method: 'get',
    //     url: "{{url('dopayment')}}?payment_id="+transaction.razorpay_payment_id,
    //     success: function (res) {
    //       //console.log(res);
    //        var redurl = "{{url('success')}}/"+res.order;
    //        window.location.href = redurl;
    //     }
    // });
}