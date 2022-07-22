<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('clearconfig', 'AuthenticationController@clearConfig');
Route::get('test', function(){
    return "Hello";
});

Route::post('test-post', 'AuthenticationController@testpost');
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('myactbid','BiddingController@myactivebid');
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('test-upload/{id}', 'CommanController@testupload');

Route::post('finduserexist', 'AuthenticationController@findUserExist');
Route::post('checkuserexist', 'AuthenticationController@checkUserExist');
Route::post('otp-request', 'AuthenticationController@MemberOtprequest');
Route::post('otp-verify', 'AuthenticationController@MemberOtpVerify');

Route::get('authorizetokennew', 'AuthenticationController@authorizeTokenCheck');
Route::post('signupnew', 'AuthenticationController@MemberSignUp');
Route::post('signinnew', 'AuthenticationController@MemberSignIn');
Route::post('emailexistnew','AuthenticationController@checkemailAvailable');

Route::post('signup', 'AuthenticateController@MemberSignUp');
Route::post('signin', 'AuthenticateController@MemberSignIn');
Route::post('emailexist','AuthenticateController@checkemailAvailable');
Route::post('mobileexist','AuthenticateController@checkmobileAvailable');
Route::post('user-forgot-password','AuthenticateController@userForgotPassword');

Route::get('get-country', 'CommanController@getCountry');
Route::get('get-state/{id}', 'CommanController@getState');
Route::get('get-state/{id}', 'CommanController@getState');
Route::get('get-city/{id}', 'CommanController@getCity');

Route::get('searchkeyword', 'CommanController@searchkeyword');

Route::get('blog-record', 'CommanController@blogrecord');
Route::get('blog-detail/{id}', 'CommanController@blogdetail');
Route::get('get-banner', 'CommanController@getHomePage');
Route::get('get-super-category', 'CommanController@getSuperCategory');
Route::get('get-category/{super_id}', 'CommanController@getCatebySuperId');
Route::get('get-super-cat-with-products','CommanController@getSuperCatWithProducts');

Route::get('get-pluck-category', 'CommanController@pluckcategory');


Route::post('contact-message', 'CommanController@contactmessage');
Route::get('privacy-policy','CommanController@privacypolicy');
Route::get('term-and-condition', 'CommanController@termandcondition');
Route::get('faqs', 'CommanController@faqs');

Route::get('mandirate', 'CommanController@mandirates');

Route::get('authorizetoken', 'AuthenticateController@authorizeTokenCheck');
Route::get('product-invoice/{id}/{roles}', 'CommanController@Invoice');



Route::group(['middleware' => 'auth:api'], function(){
    
    Route::get('userinfo', 'AuthenticationController@userDetails');
    Route::post('updateprofile','AuthenticationController@updateprofile');
	Route::get('logout','AuthenticationController@logoutApi');
    Route::post('upload-profile','AuthenticationController@uploadprofile');
    Route::post('store-bid','BiddingController@postStoreBid');
    
    Route::get('check-kyc', 'KycController@checkkyc');
    Route::get('user-kyc', 'KycController@userkyc');
    Route::post('user-aadhaar', 'KycController@userAadhaar');
    Route::post('user-pan', 'KycController@userPan');
    Route::post('user-aadhaar-front', 'KycController@userAadhaarFrontImg');
    Route::post('user-aadhaar-back', 'KycController@userAadhaarBackImg');
    Route::post('user-pan-image', 'KycController@userPanImg');
    Route::get('user-banks', 'KycController@userbanks');
    Route::post('post-user-banks', 'KycController@postuserbanks');
    Route::post('update-user-banks/{id}', 'KycController@updateuserbanks');
    Route::get('delete-user-banks/{id}', 'KycController@deleteuserbanks');
    Route::get('user-notification','KycController@notification');
    Route::get('new-notification','KycController@newnotification');
    Route::get('user-notification-status','KycController@notificationstatus');
    
    
    Route::get('get-product/{super_id}', 'CommanController@getProductbySubCatId');
    Route::get('get-product-by-cat-id/{cat_id}', 'CommanController@getProductbyCatId');
    Route::get('product-detail/{id}', 'CommanController@productDetailbyId');
    
    Route::get('getcommodity', 'BiddingController@getcommodity');
    Route::get('getcounter', 'BiddingController@getcounter');
    Route::get('getproduct-images/{product_id}','BiddingController@getproductimages');
    Route::post('post-product','BiddingController@postproduct');
    Route::post('product-image/{id}','BiddingController@productimage');
    Route::get('delete-product-images/{id}','BiddingController@deleteproductimages');
    Route::post('update-product/{id}','BiddingController@updateproduct');
    Route::get('posted-pending-product','BiddingController@postedpendingproduct');
    Route::get('posted-active-product','BiddingController@postedactiveproduct');
    Route::get('posted-product-history','BiddingController@postedproducthistory');

    Route::get('myactive-bid-detail/{product_id}','BiddingController@myactivebiddetail');
    Route::get('myactive-bid','BiddingController@myactivebid');
    Route::get('completed-deal','BiddingController@completeddeal');

	Route::get('userinfoold', 'AuthenticateController@userDetails');
	Route::get('logoutold','AuthenticateController@logoutApi');
	Route::match(['get', 'post'], 'user-address', 'AuthenticateController@userAddress');
	Route::match(['get', 'post'], 'user-profile', 'AuthenticateController@userProfile');
	Route::post('change-password','AuthenticateController@changeProfilePassword');
    
	Route::post('checkout-order','OrdersController@postOrdersByApp');
	Route::get('order-history', 'OrdersController@OrderHistory');
	Route::get('order-recent', 'OrdersController@RecentOrder');
	Route::get('order-cancel', 'OrdersController@CancelOrder');
	Route::get('view-order/{order_id}', 'OrdersController@ViewOrder');
	Route::get('track-order/{order_id}','OrdersController@trackorder');
	Route::post('order-feedback','OrdersController@orderFeedback');
	Route::get('invoice-order/{order_id}', 'OrderController@DownloadOrderDetail');
	
});


