<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;
use App\Traits\SentSMSTraits;
use Input;
use Hash;
use DateTime;
use Carbon\Carbon;
use DB;
use Auth;
use Validator;
use App\User;
use App\UsersOtp;

class AuthenticationController extends Controller
{
    public $successStatus = 200;
    protected $server;
    protected $tokens;
    use SentSMSTraits;
    
    public function clearConfig()
    {
        \Artisan::call('config:clear');
        \Artisan::call('config:cache');
        return "Cache is cleared";
    }
    public function testpost(Request $request)
    {
        return response()->json(['status'=>'checkkkk','respo'=>$request->input('name')]);
    }
    public function uploadprofile(Request $request)
    {

        $user = Auth::user(); 
        if(!empty($user->id)){
            
                if($request->hasFile('image'))
                {   
                    if(!empty($user->profile_pic))
                    {
                        $filepp = url('/public/image/profile')."/".$user->profile_pic;
                        if(file_exists( $filepp )){
                            unlink( base_path().'/public/image/profile/'.$user->profile_pic );
                        }
                    }
                   
                    $file = $request->file('image');
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $fileName = date('dmYhis',time()) . '.' . $extension;
                    $destinationPath = base_path().'/public/image/profile';
                    //$file->move($destinationPath,$fileName);
                    
                    $image_resize = Image::make($file->getRealPath());
                    $image_resize->resize(400, 400);
                    $image_resize->save($destinationPath.'/'.$fileName);
                    
                    $viewpath = url('/').'/public/image/profile/'.$fileName;
                    User::where('user_id',$user->id)
                        ->update([
                            'profile_pic' => $fileName
                        ]);

                    return response()->json(["status"=>true,'profile_name'=>$fileName,'profile_path'=>$viewpath]);
                } else {
                    return response()->json(["status"=>false,'message'=>'Request image not found']);
                }    
        }
    }
    public function checkUserExist(Request $request){
         $validator = Validator::make($request->all(), [
            'username' => 'required',
            'roles' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status'=>false,'error'=>'Username and Otp field must be required']);
        } else {
            $username = $request->input('username');
            $roles = $request->input('roles');
            $userexistornot = User::where('mobile',$username)->where('roles',$roles)->first();
            
            if(!empty($userexistornot)){
                $useruniqueid = $userexistornot->id;
                if(Auth::loginUsingId($useruniqueid)) 
                {
                    $user = Auth::User();
                    $token = $user->createToken('Passport')->accessToken; 
                    return response()->json(['status'=>true,'token'=>$token,'user'=>$user]); 
                } else {
                    return response()->json(['status'=>false,'message'=>'Enter credencial not match.']);
                }    
            }
            
        }
    }
    public function findUserExist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'is_username' => 'required',
            'roles' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status'=>false,'error'=>'Username field must be required']);
        }else{
            $username = $request->input('username');
            $is_username = $request->input('is_username');
            $roles = $request->input('roles');
            $exist = (object)[];
            if($is_username == 'email'){
                $exist = User::where('email', $username)->first();
            }else{
                if(strpos($username, "+") !== false){
                    $mobileno='+91'.$username;
                } else{
                    $mobileno=$username;
                }
                $exist = User::where('mobile', $mobileno)->where('roles', $roles)->first();
            }

            if(!empty($exist)){
                if($exist->email==$username){
                    $currentdate = date('Y-m-d');
                    
                        $generatedotp = rand ( 1000 , 9999 );
                        $sentto = [
                            'email' => $username,
                            'name' => $exist->name,
                        ]; 
                        $mailstatus = $this->sentotpmail($generatedotp,$sentto);
                        if( $mailstatus == false ) {
                            return response()->json(['status'=>false,'data'=>$exist,'message'=>'Otp not send in your email-Id, reason due to invalid email-Id or process issue. Please try again.']);
                        }else{
                            UsersOtp::insert([
                                'email' => $username,
                                'otp'=>$generatedotp,
                                'status' => 'Yes',
                                'created_at'=>date('Y-m-d H:i:s') 
                            ]);
                            return response()->json(['status'=>true,'data'=>$exist,'message'=>'Otp send in your email-Id']);
                        }
                    
                }elseif($exist->mobile==$username){

                    $currentdate = date('Y-m-d');
                    if(strpos($username, "+") !== false){
                        $otpmobileno='91'.$username;
                    } else{
                        $otpmobileno=str_replace("+","",$username);
                    }
                    $checksentcounter = UsersOtp::where('mobile', $mobileno)->where('roles', $roles)->where('sending_date',$currentdate)->count();
                    if($checksentcounter > 5){
                        return response()->json(['status'=>false,'error'=>'You cross deily limit to login in panel.']);
                    }else{
                        $generatedotp = rand ( 100000 , 999999 );
                        $otpmsg = "Your otp varification code for RAAMSETU is ".$generatedotp.".The otp is valid for 5 minutes.";
                        $record = $this->otpmaster($otpmsg,$otpmobileno);
                        UsersOtp::insert([
                            'mobile' => $username,
                            'otp'=>$generatedotp,
                            'status' => 'Yes',
                            'roles'=>$roles,
                            'sending_date' => $currentdate,
                            'created_at'=>date('Y-m-d H:i:s') 
                        ]);
                        
                        return response()->json(['status'=>true,'data'=>$exist,'message'=>'Otp send in your mobile number.']);
                    }
                }
                
            }else{
                return response()->json(['status'=>false,'error'=>'Provided username does not exist. Please enter valid mobile number.']);
            }
        }
    }
    public function ddcheckUserExist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'is_username' => 'required',
            'roles' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status'=>false,'error'=>'Username field must be required']);
        }else{
            $username = $request->input('username');
            $is_username = $request->input('is_username');
            $roles = $request->input('roles');
            $exist = (object)[];
            if($is_username == 'email'){
                $exist = User::where('email', $username)->first();
            }else{
                if(strpos($username, "+") !== false){
                    $mobileno='+91'.$username;
                } else{
                    $mobileno=$username;
                }
                $exist = User::where('mobile', $mobileno)->where('roles', $roles)->first();
            }

            if(!empty($exist)){
                if($exist->email==$username){
                    $currentdate = date('Y-m-d');
                    
                        $generatedotp = rand ( 1000 , 9999 );
                        $sentto = [
                            'email' => $username,
                            'name' => $exist->name,
                        ]; 
                        $mailstatus = $this->sentotpmail($generatedotp,$sentto);
                        if( $mailstatus == false ) {
                            return response()->json(['status'=>false,'data'=>$exist,'message'=>'Otp not send in your email-Id, reason due to invalid email-Id or process issue. Please try again.']);
                        }else{
                            UsersOtp::insert([
                                'email' => $username,
                                'otp'=>$generatedotp,
                                'status' => 'Yes',
                                'created_at'=>date('Y-m-d H:i:s') 
                            ]);
                            return response()->json(['status'=>true,'data'=>$exist,'message'=>'Otp send in your email-Id']);
                        }
                    
                }elseif($exist->mobile==$username){

                    $currentdate = date('Y-m-d');
                    if(strpos($username, "+") !== false){
                        $otpmobileno='91'.$username;
                    } else{
                        $otpmobileno=str_replace("+","",$username);
                    }
                    $record = $this->otpmaster('sentotp',$otpmobileno,null);
                    return response()->json(['status'=>true,'data'=>$exist,'message'=>'Otp send in your mobile number.']);
                }
                
            }else{
                return response()->json(['status'=>false,'error'=>'Provided username does not exist. Please enter valid mobile number.']);
            }
        }
    }
    
    public function MemberOtprequest(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'is_username' => 'required',
            'name' => 'required',
            'roles' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status'=>false,'error'=>'Username field must be required']);
        } else {
            $username = $request->input('username');
            $is_username = $request->input('is_username');
            $roles = $request->input('roles');
            if($is_username == 'email'){
                $currentdate = date('Y-m-d');
                $countdailyotp = UsersOtp::where('email',$username)
                        ->where('status','Yes')
                        ->whereDate('created_at', $currentdate)
                        ->count();
                if($countdailyotp > 10){
                    return response()->json(['status'=>false,'message'=>'You crossed the maximum limit of OTPs then you will wait for next day']);
                }else{
                    $generatedotp = rand(1000,9999);
                    $sentto = [
                        'email' => $username,
                        'name' => $request->input('name'),
                    ]; 
                    $mailstatus = $this->sentotpmail($generatedotp,$sentto);
                    if( $mailstatus == false ) {
                        return response()->json(['status'=>false,'message'=>'Otp not send in your email-Id, reason due to invalid email-Id or process issue. Please try again.']);
                    }else{
                        UsersOtp::insert([
                            'email' => $username,
                            'otp'=>$generatedotp,
                            'status' => 'Yes',
                            'created_at'=>date('Y-m-d H:i:s') 
                        ]);
                        return response()->json(['status'=>true,'message'=>'Otp send in your email-Id']);
                    }
                }
            }else{
                $currentdate = date('Y-m-d');
                if(strpos($username, "+") !== false){
                    $otpmobileno='91'.$username;
                } else{
                    $otpmobileno=str_replace("+","",$username);
                }
                $generatedotp = rand ( 100000 , 999999 );
                $otpmsg = "Your otp varification code for RAAMSETU is ".$generatedotp.".The otp is valid for 5 minutes.";
                $record = $this->otpmaster($otpmsg,$otpmobileno);
                UsersOtp::insert([
                            'mobile' => $username,
                            'otp'=>$generatedotp,
                            'status' => 'Yes',
                            'roles'=>$roles,
                            'sending_date' => $currentdate,
                            'created_at'=>date('Y-m-d H:i:s') 
                        ]);

                return response()->json(['status'=>true,'message'=>'Otp send in your mobile number.']);
                
            }
        }
    } 
    public function MemberOtpVerify(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'is_username' => 'required',
            'otp' => 'required',
            'roles' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status'=>false,'error'=>'Username and Otp field must be required']);
        } else {
            $username = $request->input('username');
            $is_username = $request->input('is_username');
            $otp = $request->input('otp');
            $roles = $request->input('roles');
            
            if($is_username == 'email'){
                $currentdate = date('Y-m-d');
                $valid = UsersOtp::where('email',$username)
                        ->where('otp',$otp)
                        ->where('status','Yes')
                        ->whereDate('created_at', $currentdate)
                        ->count();
                if($valid > 0){
                    $userexistornot = User::where('email',$username)->first();
                    if(empty($userexistornot)){
                        $useruniqueid = User::insertGetId([
                            'name' => $request->input('name'),
                            'email' => $username,
                            'roles' => 'student',
                            'status' => 'Yes',
                            'created_at'=>date('Y-m-d H:i:s') 
                        ]);
                    }else{
                        $useruniqueid = $userexistornot->id;
                    }
                    
                    if(Auth::loginUsingId($useruniqueid)) 
                    {
                        $user = Auth::User();
                        $token = $user->createToken('Passport')->accessToken; 
                        return response()->json(['status'=>true,'token'=>$token,'user'=>$user]); 
                    } else {
                        return response()->json(['status'=>false,'message'=>'Enter credencial not match.']);
                    }           
                }else{
                    return response()->json(['status'=>false,'message'=>'Invalid otp or otp expired']); 
                }        
                 
            } else {
                if(strpos($username, "+") !== false){
                    $otpmobileno='91'.$username;
                } else{
                    $otpmobileno=str_replace("+","",$username);
                }
                $currentdate = date('Y-m-d');
                $valid = UsersOtp::where('mobile',$username)
                        ->where('otp',$otp)
                        ->where('status','Yes')
                        ->where('roles',$roles)
                        ->where('sending_date',$currentdate)
                        ->count();
                        
                if(!empty($valid)){
                    $userexistornot = User::where('mobile',$username)->where('roles',$roles)->first();
                    if(empty($userexistornot)){
                        if(count($request->input('deal_in')) > 0){
                            $deal_in = serialize($request->input('deal_in'));
                        }else{
                            $deal_in = null;
                        }
                        
                        $useruniqueid =User::insertGetId([
                            'name' => $request->input('name'),
                            'mobile' => $username,
                            'roles' => $roles,
                            'pincode' => $request->input('pincode'),
                            'address' => $request->input('address'),
                            'deal_in' => $deal_in,
                            'status' => 'Yes',
                            'created_at'=>date('Y-m-d H:i:s') 
                        ]);
                    }else{
                        $useruniqueid = $userexistornot->id;
                    }
                    if(Auth::loginUsingId($useruniqueid)){ 
                        $user = Auth::user();
                        $token = $user->createToken('Passport')->accessToken; 
                        return response()->json(['status'=>true,'token'=>$token,'user'=>$user]);   
                    } else {
                        return response()->json(['status'=>false,'message'=>'Authentication error. Please try again.']); 
                    }
                }else{
                    return response()->json(['status'=>false,'message'=>'Entered otp not varified or timeout. Please add valid otp.']);     
                }
                
            }
        }
    } 
    public function authorizeTokenCheck(Request $request, ResourceServer $server, TokenRepository $tokens)
    {
        $this->server = $server;
        $this->tokens = $tokens;
        $localCall = false;  
        $psr = (new DiactorosFactory)->createRequest($request);
        try {
            $psr = $this->server->validateAuthenticatedRequest($psr);

            
            $token = $this->tokens->find(
                $psr->getAttribute('oauth_access_token_id')
            );

            $currentDate = new DateTime();
            $tokenExpireDate = new DateTime($token->expires_at);

            $isAuthenticated = $tokenExpireDate > $currentDate ? true : false;

            if($localCall) {
                 return json_encode(array('status'=>'true'));
            }
            else {
                return json_encode(array('status'=>'true'));
            }
        } catch (OAuthServerException $e) {
            if($localCall) {
                return false;
            }
            else {
                return json_encode(array('status'=>'false','error' => 'Something went wrong with authenticating. Please logout and login again.'));
            }
        }
                       
    }
    public function validateToken(Request $request, $localCall = false) {
        
        $psr = (new DiactorosFactory)->createRequest($request);

        try {
            $psr = $this->server->validateAuthenticatedRequest($psr);

            
            $token = $this->tokens->find(
                $psr->getAttribute('oauth_access_token_id')
            );

            $currentDate = new DateTime();
            $tokenExpireDate = new DateTime($token->expires_at);

            $isAuthenticated = $tokenExpireDate > $currentDate ? true : false;

            if($localCall) {
                return $isAuthenticated;
            }
            else {
                return json_encode(array('authenticated' => $isAuthenticated));
            }
        } catch (OAuthServerException $e) {
            if($localCall) {
                return false;
            }
            else {
                return json_encode(array('error' => 'Something went wrong with authenticating. Please logout and login again.'));
            }
        }
    }
    public function logoutApi(Request $request)
    { 
        $accessToken = Auth::user()->token();
        // DB::table('oauth_refresh_tokens')
        //     ->where('access_token_id', $accessToken->id)
        //     ->update([
        //         'revoked' => true
        //     ]);
        foreach($accessToken as $token) {
            $token->revoke();   
        }
        //$accessToken->revoke();
        return response()->json(['status'=>'true']);
    }
    public function MemberSignUp(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors);
        } else {
	        $findemail = User::where('email',$request->input('email'))->first();
	        if(empty($findemail)){
	            $add = User::insert([
	            	    'name' => $request->input('name'),
	                    'email' => $request->input('email'),
	                    'password' => bcrypt($request->input('password')),
	                    'roles' => 'student',
	                ]);
	            $credentials = [
	                'email' => $request->input('email'),
	                'password' => $request->input('password'),
	                'roles' => 'student',
	            ];
	           
	            if(Auth::attempt($credentials))
	            {
	               $user = Auth::User();
	               $token = $user->createToken('Passport')->accessToken; 
	               return response()->json(['status'=>'true','token'=>$token]); 
	            } else {
	               return response()->json(['status'=>'false','message'=>'Enter credencial not match.']);
	            }
	        }else{
	            return response()->json(['status'=>'false','message'=>'Enter email id already exist.']);
	        }
        }
    } 
    public function MemberSignIn(Request $request) 
    { 
    	$validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors);
        } else {
        	$credentials = [
	                'email' => $request->input('email'),
	                'password' => $request->input('password'),
	                'roles' => 'student',
	            ];
	        if(Auth::attempt($credentials))
            {
                $user = Auth::User();
                $token = $user->createToken('Passport')->accessToken; 
                return response()->json(['status'=>'true','token'=>$token,'user_name'=>strstr($user->name, ' ',true)]); 
            } else {
                return response()->json(['status'=>'false','message'=>'Enter credencial not match.']);
            }    
        }
    } 
    public function userDetails(Request $request) 
    { 
           $user = Auth::user(); 
           if(!empty($user->id)){
                $imagepath = url('public/image/profile').'/';
                $default = url('public/image/profile/user-picture.png');
                $record = User::select('id','name','email','mobile','pincode','address','roles','deal_in','rating','status',DB::raw('CASE WHEN profile_pic IS NULL OR profile_pic = "" THEN "'.$default.'" ELSE CONCAT("'.$imagepath.'", profile_pic) END as profile_image'))->where('id',$user->id)->first();
                $record['status']='true';
                return response()->json($record); 
           } else {
            return response()->json(['status'=>"false"]);
           } 
    } 
    public function checkemailAvailable(Request $request)
      {
        $validator = Validator::make($request->all(), [
                'email' => 'required'
          ]);
        if ($validator->fails()) {
               $errors = $validator->errors();
               return response()->json(['status'=>'false','message'=>'Please enter valid email-ID.']);
               }
        else { 
         $exist = User::where('email',$request->input('email'))->first();
         if(!empty($exist)){
            return response()->json(['status'=>'true','message'=>'Email id exist.']);
         }else{
            return response()->json(['status'=>'false','message'=>'Entered email-ID available to register.']);
         }
        }
    }
    public function updateprofile(Request $request) 
    { 
           $user = Auth::user(); 
           if(!empty($user->id)){
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'nullable',
                    'mobile' => 'required',
                    'pincode' => 'required',
                    'address' => 'required',
                    'deal_in' => 'nullable'
                ]);
                if ($validator->fails()) {
                   $errors = $validator->errors();
                   return response()->json(['status'=>false,'error'=>$errors]);
                } else {
                    
                    User::where('id', $user->id)
                        ->update([
                            'name' => $request->input('name'),
                            'email' => $request->input('email'),
                            'pincode' => $request->input('pincode'),
                            'address' => $request->input('address'),
                            'deal_in' => $request->input('deal_in'),
                            "updated_at" => date('Y-m-d H:i:s')
                        ]);
                       
                    return response()->json(['status' => true, 'message'=>'Your Profile updated successfully!']);  
                }
           } else {
            return response()->json(['status'=>"false"]);
           } 
    } 

}
