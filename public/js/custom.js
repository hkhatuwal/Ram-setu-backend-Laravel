$(function(){
	$('.floatnumberallow').keyup(function () {
	      var $th = $(this);
	      $th.val($th.val().replace(/[^0-9.]/g, function (str) {
	        return '';
	      }));
	});  
	$('#product_sentsms').validate({
	    rules: {
	        templatekey: {
		        required: true,
		        required: true
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	      
	});
	$('#categoryhomefeature_form').validate({
	    rules: {
	        'homelist[]': {
		        required: true,
		        required: true
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	      
	});
	$('#mandirate_form').validate({
	    rules: {
	        commodity_id: {
		        required: true,
		        required: true
	        },
	        min: {
		        required: true,
		        required: true
	        },
	        max: {
		        required: true,
		        required: true
	        },
	        modelrate: {
		        required: true,
		        required: true
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	});
	$('#adduserbank').validate({
	    rules: {
	        account_holder: {
		        required: true,
		        required: true
	        },
	        account_number: {
		        required: true,
		        required: true
	        },
	        ifsc: {
		        required: true,
		        required: true
	        },
	        bank_name: {
		        required: true,
		        required: true
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	      
	});
	
	$('#notification').validate({
	    rules: {
	        message: {
		        required: true,
		        required: true
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	      
	});
	$('#product_biding_close').validate({
	    rules: {
	        quantity: {
		        required: true,
		        required: true
	        },
	        unit: {
		        required: true,
		        required: true
	        },
	        subtotal: {
		        required: true,
		        required: true
	        },
	        gst_charges: {
		        required: true,
		        required: true
	        },
	        grand_price: {
		        required: true,
		        required: true
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	      
	});
	$('#faqs_form').validate({
	    rules: {
	        question: {
		        required: true,
		        required: true
	        },
	        answer: {
		        required: true,
		        required: true
	        },
	        question_hindi: {
		        required: true,
		        required: true
	        },
	        answer_hindi: {
		        required: true,
		        required: true
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	      
	});
	$('#importproduct_form').validate({
	    rules: {
	        selected_file: {
		        required: true,
		        required: true
	        },
	    },
	    messages: {
	        selected_file: {
	            required: "Import file must be in .xlsx, .xls, .csv format.",
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	      
	});
	$('#supercategory_form').validate({
	    rules: {
	        super_cat_name: {
		        required: true,
		        required: true
	        },
	        super_cat_hindi_name: {
		        required: true,
		        required: true
	        },
	    },
	    messages: {
	        super_cat_name: {
	            required: "Super category name field is required.",
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	      
	});
	
	$('#blog_form').validate({
	    rules: {
	    	title: {
		        required: true,
		        required: true
	        },
	        author_name: {
		        required: true,
		        required: true
	        },
	        description: {
		        required: true,
		        required: true
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	      
	});
	$('#category_form').validate({
	    rules: {
	    	super_cat_id: {
		        required: true,
		        required: true
	        },
	        category_name: {
		        required: true,
		        required: true
	        },
	        category_hindi_name: {
		        required: true,
		        required: true
	        },
	    },
	    messages: {
	    	super_cat_id: {
	            required: "Super Category name field is required.",
	        },
	        category_name: {
	            required: "Category name field is required.",
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	      
	});
    $('#subcategory_form').validate({
	    rules: {
	        subcat_name: {
		        required: true,
		        required: true
	        },
	        category_id: {
	         	required: true,
	            required: true
	        },
	        super_cat_id: {
	         	required: true,
	            required: true
	        },
	    },
	    messages: {
	        subcat_name: {
	            required: "Subcategory name field is required.",
	        },
	        category_id: {
	            required: "Please choose category.",
	        },
	        super_cat_id: {
	            required: "Please choose super category.",
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	});
	$('#product_form').validate({
	    rules: {
	    	user_id:{
	    		required: true,
	            required: true
	    	},
	    	super_cat_id: {
	         	required: true,
	            required: true
	        },
	        category_id: {
	         	required: true,
	            required: true
	        },
	        quantity: {
		        required: true,
		        required: true
	        },
	        quality: {
		        required: true,
		        required: true
	        },
	        unit: {
		        required: true,
		        required: true
	        },
	        base_price: {
		        required: true,
		        required: true
	        },
	        sell_price: {
		        required: true,
		        required: true
	        },
	        bid_close_date: {
		        required: true,
		        required: true
	        },
	    },
	    messages: {
	    	super_cat_id: {
	            required: "Please choose super category.",
	        },
	        product_name: {
	            required: "Product name field is required.",
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	});
	$('#attribute_form').validate({
	    rules: {
	    	attr_type: {
	         	required: true,
	            required: true
	        },
	        name: {
	         	required: true,
	            required: true
	        },
	    },
	    messages: {
	    	attr_type: {
	            required: "Choose attribute type.",
	        },
	        name: {
	            required: "Enter attribute name.",
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	});
	$('#productimage_form').validate({
	    rules: {
	    	'image[]': {
	         	required: true,
	            required: true
	        },
	    },
	    messages: {
	    	image: {
	            required: "Choose product images.",
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	});
    $('#country_form').validate({
	    rules: {
	        country_name: {
		        required: true,
		        required: true
	        },
	    },
	    messages: {
	        country_name: {
	            required: "Country name field is required.",
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	      
	});
    $('#state_form').validate({
	    rules: {
	        country_id: {
		        required: true,
		        required: true
	        },
	        state_name: {
	         	required: true,
	            required: true
	        },
	    },
	    messages: {
	        country_id: {
	            required: "Country name field is required.",
	        },
	        state_name: {
	            required: "State name field is required.",
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	});
	$('#city_form').validate({
	    rules: {
	        country_id: {
		        required: true,
		        required: true
	        },
	        state_id: {
	         	required: true,
	            required: true
	        },
	        city_name: {
	         	required: true,
	            required: true
	        },
	    },
	    messages: {
	        country_id: {
	            required: "Country name field is required.",
	        },
	        state_id: {
	            required: "State name field is required.",
	        },
	        city_name: {
	            required: "City name field is required.",
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	});
	$('#coupon_form').validate({
	    rules: {
	        title: {
		        required: true,
		        required: true
	        },
	        discount: {
	         	required: true,
	            required: true
	        },
	    },
	    messages: {
	        title: {
	            required: "Coupon title field is required.",
	        },
	        discount: {
	            required: "Coupon discount in % is required.",
	        },
	    },
	    highlight: function(element,error) {
	        $(element).closest('.control-group').removeClass('success').addClass('error');
	    },
	    success: function(element) {
	        return true;
	    }
	});
});