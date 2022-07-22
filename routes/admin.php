<?php
	
Route::name('admin.')->group(function () {
	Route::middleware(['App\Http\Middleware\AdminMiddleware'])->group(function(){	
		
		Route::get('/', function(){
		     return redirect('admin-login');
	    });
	    Route::get('/dashboard', 'AdminController@index')->name('dashboard');

        Route::get('add-bank', 'BuyerController@addbank');
        Route::resource('seller', 'SellerController');
        Route::resource('buyer', 'BuyerController');
        Route::resource('faqs', 'FaqsController');
        
        Route::resource('mandirate', 'MandiController');
        
        Route::post('ckeditorblog/upload', 'BlogController@upload')->name('ckeditorblog.upload');
        Route::resource('blog-category', 'BlogCategoryController');
        Route::resource('blog', 'BlogsController');
		
		Route::post('biding-close/{product_id}', 'ProductController@bidingclose');
		Route::get('product-invoice/{id}/{roles}', 'ProductController@Invoice');
		Route::get('assign-bidder', 'ProductController@assignbidder');
		Route::get('product-image/delete/{id}', 'ProductController@productimagedelete');
		Route::get('get-category/{id}', 'ProductController@getcatelist');
        Route::get('pending-product', 'ProductController@pendingproduct');
        Route::get('expired-product', 'ProductController@expiredproduct');
        Route::get('complete-product', 'ProductController@completedproduct');
        
        Route::post('sent-sms', 'ProductController@sentSms'); 
        Route::delete('pending-product/{id}', 'ProductController@destroy');
        Route::delete('complete-product/{id}', 'ProductController@destroy');
        Route::delete('expired-product/{id}', 'ProductController@destroy');
		Route::resource('product', 'ProductController');
		Route::resource('supercategory', 'SuperCategoryController');
		Route::resource('category', 'CategoryController');
		Route::resource('subcategory', 'SubcategoryController');
        Route::get('status-manage/{table}/{id}', 'CommanController@CheckStatus');
        Route::post('sent-notification/{user_id}', 'CommanController@sentnotification');
        
        Route::post('category-home-feature/{id}', 'CategoryController@categoryhomefeature');  
        Route::get('category-feature', 'CategoryController@categoryfeature');
        Route::get('supercategory-isbanner', 'SuperCategoryController@isBanner');
        Route::post('product/imgattr-delete', 'ProductController@deleteImgAttr'); 
        Route::post('product-attribute/{id}', 'ProductController@productAttribute');
        Route::post('product/{id}/images', 'ProductController@productImages');
        Route::get('product-stock-feature', 'ProductController@stockfeature');
        Route::post('import/product', 'ProductController@import');
        Route::get('importproduct', 'ProductController@importproduct');
        Route::get('download/product', 'ProductController@download');
        Route::get('download/supercategory', 'SuperCategoryController@download');
        Route::get('download/category', 'CategoryController@download');
        Route::get('download/subcategory', 'SubcategoryController@download');

        Route::get('get-state', 'CommanController@getstate'); 
		Route::resource('country', 'CountryController');
		Route::resource('state', 'StateController');
		Route::resource('city', 'CityController');
		
		Route::resource('customers', 'UsersController');
		
		Route::get('order-active', 'OrderController@RecentOrder'); 
		Route::get('order-delivered', 'OrderController@OrderHistory');
		Route::get('order-cancel', 'OrderController@CancelOrder'); 
		Route::get('order/{order_id}', 'OrderController@OrderDetail'); 
		Route::get('download/order/{order_id}', 'OrderController@DownloadOrderDetail'); 

		Route::get('change-order-status/{order_id}', 'OrderController@changeOrderStatus');
		
		Route::resource('coupon', 'CouponController');
		Route::match(['get','post'],'shipping-charges', 'CommanController@ShippingCharges');

		Route::get('order-report', 'ReportController@orderreport');
        Route::get('order-report-download', 'ReportController@orderreportdownload');
		
		

		
    });
});