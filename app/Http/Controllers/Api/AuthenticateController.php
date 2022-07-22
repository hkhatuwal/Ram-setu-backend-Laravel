<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use Auth;
use DateTime;
use App\User;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Illuminate\Support\Facades\Route;
use App\Traits\MailerTraits;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DB;
use Validator;
use App\UsersAddress;
use Mail;

class AuthenticateController extends Controller
{
    public $successStatus = 200;
    protected $server;
    protected $tokens;
    use MailerTraits;
    
    public function __construct(ResourceServer $server, TokenRepository $tokens) {
        $this->server = $server;
        $this->tokens = $tokens;
    }
    
    public function userForgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'mobile' => 'required'
        ]);
        if ($validator->fails()) 
        {
            $errors = $validator->errors();
            return response()->json($errors);
        }else{

            $email = $request->input('email');
            $mobile = $request->input('mobile');
            $users = User::where('email',$email)
                ->where('mobile',$mobile)
                ->first();
            if(!empty($users)){
                $password =  Str::random(6);
                $data = array(
                    'email'  => $users->email,
                    'name' => $users->name,
                    'password' => $password,
                    'id' => $users->id
                );
                $mailstatus = $this->forgetPassword($data);

                if( $mailstatus == false ) {
                    return response()->json(['status'=> false, 'message'=>'Email not sent in your entered email id. Please contact administrator.']);
                } else {
                    $ddd = User::where('email', $request->input('email'))
                        ->update([
                            'password' => bcrypt($password),
                        ]);
                    return response()->json(['status'=> true, 'message'=>'You have successfully generate new password, New Password is sent to your entered email id.']);  
                } 
            }else{
                return response()->json(['status'=>false, 'error'=>'Email address or Mobile number not exist. Please try again..']);
            }
        }
    }
    public function authorizeTokenCheck(Request $request, $localCall = false) {
        $ddd = Auth()->check();
        return response()->json([ 'status' => $ddd ]);
    }
    public function authorizeTokenCheck12(Request $request, ResourceServer $server, TokenRepository $tokens)
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
                 return json_encode(array('status'=>true));
            }
            else {
                return json_encode(array('status'=>true));
            }
        } catch (OAuthServerException $e) {
            if($localCall) {
                return false;
            }
            else {
                return json_encode(array('status'=>false,'error' => 'Something went wrong with authenticating. Please logout and login again.'));
            }
        }
                       
    }
    public function MemberSignUp(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'roles' => 'required',
            'mobile' => 'required',
            'password' => 'required|min:4|max:15',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status'=>false,'message'=>'Validation Error','error'=>$errors]);
        } else {
            $roles = $request->input('roles');
            $mobile= $request->input('mobile');
            $findemail = User::where('mobile',$mobile)->where('roles',$roles)->first();
	        
	        if(empty($findemail)){
	            $add = User::insert([
	            	    'name' => $request->input('name'),
                        'mobile' => $request->input('mobile'),
	                    'password' => bcrypt($request->input('password')),
	                    'roles' => $roles,
                        'status'=>'Yes',
                        "created_at" => date('Y-m-d H:i:s')
	                ]);
	            $credentials = [
	                'mobile' => $request->input('mobile'),
	                'password' => $request->input('password'),
	                'roles' => $roles
	            ];
	            if(Auth::attempt($credentials))
	            {
	               $user = Auth::User();
	               $token = $user->createToken('Passport')->accessToken; 
	               return response()->json(['status'=>true,'token'=>$token,'user'=>$user]); 
	            } else {
	               return response()->json(['status'=>false,'message'=>'User Register. Please login now.']);
	            }
	        }else{
	            return response()->json(['status'=>false,'message'=>'Enter email id or mobile number already exist.']);
	        }
        }
    } 
    public function MemberSignIn(Request $request) 
    { 
    	$validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'password' => 'required',
            'roles' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status'=>false,'message'=>'Validation Error','error'=>$errors]);
        } else {
            $roles = $request->input('roles');
            $username = $request->input('mobile');
            $findusername = User::where('mobile',$username)->where('roles',$roles)->where('status','Yes')->first();
            
            if(!empty($findusername)){
                $credentials = [
                    'mobile' => $request->input('mobile'),
                    'password' => $request->input('password'),
                    'roles' => $roles,
                    'status' => 'Yes',
                ];
               if(Auth::attempt($credentials))
                {
                    $user = Auth::User();
                    $token = $user->createToken('Passport')->accessToken; 
                    return response()->json(['status'=>true,'token'=>$token,'user'=>$user]); 
                } else {
                    return response()->json(['status'=>false,'message'=>'Enter credencial not match.']);
                } 
            }else{
                return response()->json(['status'=>false,'message'=>'Enter credencial not match.']);
            }       
        }
    } 
    public function userDetails(Request $request) 
    { 
      
          $user = Auth::user(); 
          if(!empty($user->id)){
                $record = User::select('id','name','email','mobile')->where('id',$user->id)->first();
                $record->status=true;
                return response()->json($record); 
          } else {
            return response()->json(['status'=>false]);
          } 
    } 
    public function checkemailAvailable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status'=>false,'message'=>'Please enter valid email-ID.']);
        } else { 
            $exist = User::where('email',$request->input('email'))->where('roles','customer')->first();
            if(!empty($exist)){
                return response()->json(['status'=>true,'message'=>'Email id exist.']);
            }else{
                return response()->json(['status'=>false,'message'=>'Entered email-ID available to register.']);
            }
        }
    }
    public function checkmobileAvailable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status'=>false,'message'=>'Please enter valid mobile number.']);
        } else { 
            $exist = User::where('mobile',$request->input('mobile'))->where('roles','customer')->first();
            if(!empty($exist)){
                return response()->json(['status'=>true,'message'=>'Mobile number exist.']);
            }else{
                return response()->json(['status'=>false,'message'=>'Entered mobile number available to register.']);
            }
        }
    }
    public function changeProfilePassword(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:4|max:15'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status'=>false,'error'=>$errors]);
        }  else {
                $newpassword = $request->input('password');

                $users = User::where('id', $user->id)
                    ->update([
                        'password' => bcrypt($newpassword)
                    ]);
                return response()->json(['status'=>true, 'success' => 'Your account password has been changed successfuly!']);
        }
                        
    } 
    public function userProfile(Request $request)
    {
        $user = Auth::user(); 
        if($request->isMethod('post'))
        {
            if($user->country_code=='in'){
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email',
                    'mobile' => 'required',
                ]);
            }else{
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email',
                    'mobile' => 'required',
                ]);
            }
            
            if ($validator->fails()) {
                   $errors = $validator->errors();
                   return response()->json(['status'=>false,'error'=>$errors]);
            } else {
                
                    $mobile = $request->input('mobile');
                    $email = $request->input('email');
                    $query = User::where(function($que) use ($mobile,$email){
                          $que->where('mobile',$mobile)->orwhere('email',$email);
                    });
                    $existdata = $query->where('id','<>',$user->id)->first();
                    
                    if(!empty($existdata)){
                        return response()->json(['status' => false, 'message'=>'Enter email id or mobile number already used. Try unique email id or mobile number!']);
                    }else{
                        if($request->has('address')){
                            $address = $request->input('address');
                        }else{
                            $address = null;
                        }
                        if($request->has('state_id')){
                            $state_id = $request->input('state_id');
                        }else{
                            $state_id = null;
                        }
                        if($request->has('city_id')){
                            $city_id = $request->input('city_id');
                        }else{
                            $city_id = null;
                        }
                        if($request->has('street_no')){
                            $street_no = $request->input('street_no');
                        }else{
                            $street_no = null;
                        }
                        if($request->has('pincode')){
                            $pincode = $request->input('pincode');
                        }else{
                            $pincode = null;
                        }
                        if($request->has('area_name')){
                            $area_name = $request->input('area_name');
                        }else{
                            $area_name = null;
                        }
                        if($request->has('gender')){
                            $gender = $request->input('gender');
                        }else{
                            $gender = null;
                        }
                        if($request->has('dob')){
                            $dob = $request->input('dob');
                        }else{
                            $dob = null;
                        }
                        if($request->has('alternate_mobile')){
                            $alternate_mobile = $request->input('alternate_mobile');
                        }else{
                            $alternate_mobile = null;
                        }
                        User::where('id', $user->id)
                            ->update([
                                'name' => $request->input('name'),
                                'email' => $request->input('email'),
                                'mobile' => $request->input('mobile'),
                                'alternate_mobile' => $alternate_mobile,
                                'gender' => $gender,
                                'dob' => $dob,
                                'state_id' => $state_id,
                                'city_id' => $city_id,
                                'pincode' => $pincode,
                                'area_name' => $area_name,
                                'street_no' => $street_no,
                                'address' => $address,
                                "updated_at" => date('Y-m-d H:i:s')
                            ]);
                           
                        return response()->json(['status' => true, 'message'=>'Your Profile updated successfully!']);     
                    }
            }
        }
        if($request->isMethod('get'))
        {
            if($user->country_code=='in'){
                $data = User::leftjoin('countries','countries.code','users.country_code')
                    ->leftjoin('states', 'states.id', 'users.state_id')
                    ->leftjoin('cities', 'cities.id', 'users.city_id')
                    ->select('users.id','users.name','users.email','users.mobile','users.country_code','users.alternate_mobile','users.gender','users.dob','users.state_id','users.city_id','users.address','countries.country_name','states.state_name','cities.city_name','users.pincode','users.area_name')
                    ->where('users.id', $user->id)
                    ->first();
            }else{
                $data = User::leftjoin('countries','countries.code','users.country_code')
                    ->leftjoin('cities', 'cities.id', 'users.city_id')
                    ->select('users.id','users.name','users.email','users.mobile','users.country_code','users.alternate_mobile','users.gender','users.dob','users.street_no','users.city_id','users.address','countries.country_name','cities.city_name','users.pincode','users.area_name')
                    ->where('users.id', $user->id)
                    ->first();
            }
            return response()->json($data);
        }
    }
    public function userAddress(Request $request)
    {
        $user = Auth::user(); 
        if(!empty($user->id)){
            if ($request->isMethod('get'))
            { 
                $data = UsersAddress::leftjoin('countries', 'countries.code', 'users_addresses.country_id')
                    ->leftjoin('states', 'states.id', 'users_addresses.state_id')
                    ->leftjoin('cities', 'cities.id', 'users_addresses.city_id')
                    ->select('users_addresses.id','users_addresses.user_id','users_addresses.country_id','users_addresses.state_id','users_addresses.city_id','users_addresses.address','countries.country_name','states.state_name','cities.city_name')
                    ->where('users_addresses.user_id', $user->id)
                    ->first();
                    
                if(!empty($data)){
                    $data->status = true; 
                    return response()->json($data);
                } else {
                    $data = (object)[
                      'status'=>false,'user_id'=>null,'country_id'=>null,'country_name'=>null,'state_id' => null,'state_name' => null,'city_id' => null,'city_name' => null,'address' => null
                    ];
                    return response()->json($data);
                }        
            }
            if ($request->isMethod('post'))
            {
                $validator = Validator::make($request->all(), [
                    'country_id' => 'required',
                    'state_id' => 'required',
                    'city_id' => 'required',
                    'address' => 'required'
                ]);
                if ($validator->fails()) {
                       $errors = $validator->errors();
                       return response()->json(['status'=>false,'error'=>$errors]);
                } else {
                       
                        $exist = UsersAddress::where('user_id',$user->id)->first();
                        if(!empty($exist)){
                            $up = UsersAddress::where('user_id',$user->id)
                                ->update([
                                    'country_id' => $request->input('country_id'),
                                    'state_id' => $request->input('state_id'),
                                    'city_id' => $request->input('city_id'),
                                   // 'pincode' => $request->input('pincode'),
                                   // 'landmark' => $request->input('landmark'),
                                    'address' => $request->input('address'),
                                    'updated_at' => date('Y-m-d H:i:s')
                                 ]);
                            $msg = "User address updated successfully";    
                        }else{
                            $in = UsersAddress::insert([
                                    'user_id' => $user->id,
                                    'country_id' => $request->input('country_id'),
                                    'state_id' => $request->input('state_id'),
                                    'city_id' => $request->input('city_id'),
                                   // 'pincode' => $request->input('pincode'),
                                   // 'landmark' => $request->input('landmark'),
                                    'address' => $request->input('address'),
                                    'created_at' => date('Y-m-d H:i:s')
                                 ]);
                            $msg = "User address added successfully";
                        }
                        $data = UsersAddress::leftjoin('countries', 'countries.code', 'users_addresses.country_id')
                            ->leftjoin('states', 'states.id', 'users_addresses.state_id')
                            ->leftjoin('cities', 'cities.id', 'users_addresses.city_id')
                            ->select('users_addresses.id','users_addresses.user_id','users_addresses.country_id','users_addresses.state_id','users_addresses.city_id','users_addresses.address','countries.country_name','states.state_name','cities.city_name')
                            ->where('users_addresses.user_id', $user->id)
                            ->first(); 
                        $data->status = true; 
                        $data->message = $msg;           
                    return response()->json($data);    
                }
            }
        }else{
            return response()->json(['status'=>false]);
        } 
    }
    public function logoutApi(Request $request)
    { 
        //$accessToken = Auth::user()->token();
        // DB::table('oauth_refresh_tokens')
        //     ->where('access_token_id', $accessToken->id)
        //     ->update([
        //         'revoked' => true
        //     ]);

        //$accessToken->revoke();
        Auth::logout();
        return response()->json(['status'=>true]);
    }
}
