<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;
use Hash;
use DateTime;
use Carbon\Carbon;
use DB;
use Auth;
use Validator;
use App\User;
use App\UsersBank;

class KycController extends Controller
{
    public function notification(Request $request)
	{
	    
		$user = Auth::user(); 
	    if(!empty($user->id)){
	        $data = DB::table('users_messages')->select('*',DB::raw('DATE_FORMAT(created_at, "%l %p %M %d %Y") as created_date'))
    	        ->where('user_id',$user->id)
    	        ->orderBy('id','desc')
    	        ->get();
            return response()->json($data);
	    }
	}
	public function newnotification(Request $request)
	{
	    
		$user = Auth::user(); 
	    if(!empty($user->id)){
	        $counter = DB::table('users_messages')->where('user_id',$user->id)
	            ->where('status','No')
    	        ->count();
            return response()->json(['counter'=>$counter]);
	    }
	}
	public function notificationstatus(Request $request)
	{
	    
		$user = Auth::user(); 
	    if(!empty($user->id)){
	        DB::table('users_messages')
    	        ->where('user_id',$user->id)
    	        ->update([
    	            'status'=>'Yes'
    	            ]);
            return response()->json(['status'=>true]);
	    }
	}
    public function deleteuserbanks(Request $request,$id) 
    { 
        $user = Auth::user(); 
        if(!empty($user->id)){
            UsersBank::where('id',$id)->delete();
            return response()->json(['status' => true]);
        }
    }
    public function postuserbanks(Request $request) 
    { 
        $user = Auth::user(); 
        if(!empty($user->id)){
            $validator = Validator::make($request->all(), [
                'account_holder' => 'required',
                'account_number' => 'required',
                'ifsc' => 'required',
                'bank_name' => 'required',
            ]);
            if ($validator->fails()) {
               $errors = $validator->errors();
               return response()->json(['status'=>false,'error'=>$errors]);
            } else {
                UsersBank::insert([
                        'user_id' => $user->id,
                        'account_holder' => $request->input('account_holder'),
                        'account_number' => $request->input('account_number'),
                        'ifsc' => $request->input('ifsc'),
                        'bank_name' => $request->input('bank_name'),
                        "created_at" => date('Y-m-d H:i:s')
                    ]);
                return response()->json(['status' => true, 'message'=>'Your bank updated successfully!']);
            }             
        }    
    } 
    public function updateuserbanks(Request $request,$id) 
    { 
        $user = Auth::user(); 
        if(!empty($user->id)){
            $validator = Validator::make($request->all(), [
                'account_holder' => 'required',
                'account_number' => 'required',
                'ifsc' => 'required',
                'bank_name' => 'required',
            ]);
            if ($validator->fails()) {
               $errors = $validator->errors();
               return response()->json(['status'=>false,'error'=>$errors]);
            } else {
                UsersBank::where('id',$id)->update([
                        'account_holder' => $request->input('account_holder'),
                        'account_number' => $request->input('account_number'),
                        'ifsc' => $request->input('ifsc'),
                        'bank_name' => $request->input('bank_name'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ]);
                return response()->json(['status' => true, 'message'=>'Your bank updated successfully!']);
            }             
        }    
    } 
    public function userbanks(Request $request) 
    { 
        $user = Auth::user(); 
        if(!empty($user->id)){
            $record = UsersBank::where('user_id', $user->id)->orderby('id','desc')->get();
            if(!$record->isEmpty()){
                return response()->json(['status'=>true,'data'=>$record]);
            }else{
                $record = array();
                return response()->json(['status'=>false,'data'=>$record,'message'=>'Empty bank record found.']);
            }                  
        }    
    } 
    
    public function checkkyc(Request $request) 
    { 
        $user = Auth::user(); 
        if(!empty($user->id)){
            if($user->roles=='seller'){
                if(!empty($user->aadhaar_number) && !empty($user->aadhaar_front) && !empty($user->aadhaar_back)){
                    $status = true;
                }else{
                    $status = false;
                }
            }else{
                if(!empty($user->aadhaar_number) && !empty($user->aadhaar_front) && !empty($user->aadhaar_back) && !empty($user->pan_number) && !empty($user->pan_card)){
                    $status = true;
                }else{
                    $status = false;
                }
            }
            return response()->json($status);       
        }
    }
    public function userkyc(Request $request) 
    { 
        $user = Auth::user(); 
        if(!empty($user->id)){
            $imagepath = url('public/image/profile').'/';
            $data = User::select('aadhaar_number','pan_number',
                   DB::raw('CASE WHEN aadhaar_front IS NULL OR aadhaar_front = "" THEN "" ELSE CONCAT("'.$imagepath.'", aadhaar_front) END as aadhaar_front'),
                   DB::raw('CASE WHEN aadhaar_back IS NULL OR aadhaar_back = "" THEN "" ELSE CONCAT("'.$imagepath.'", aadhaar_back) END as aadhaar_back'),
                   DB::raw('CASE WHEN pan_card IS NULL OR pan_card = "" THEN "" ELSE CONCAT("'.$imagepath.'", pan_card) END as pan_card')
                   )->where('id', $user->id)->first();
                       
            return response()->json($data);
        }    
    } 
    public function userAadhaar(Request $request) 
    { 
        $user = Auth::user(); 
        if(!empty($user->id)){
            $validator = Validator::make($request->all(), [
                'aadhaar_number' => 'required',
            ]);
            if ($validator->fails()) {
               $errors = $validator->errors();
               return response()->json(['status'=>false,'error'=>$errors]);
            } else {
                User::where('id', $user->id)
                    ->update([
                        'aadhaar_number' => $request->input('aadhaar_number'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ]);
                return response()->json(['status' => true, 'message'=>'Your aadhaar updated successfully!']);
            }
        }    
    } 
    public function userPan(Request $request) 
    { 
        $user = Auth::user(); 
        if(!empty($user->id)){
            $validator = Validator::make($request->all(), [
                'pan_number' => 'required',
            ]);
            if ($validator->fails()) {
               $errors = $validator->errors();
               return response()->json(['status'=>false,'error'=>$errors]);
            } else {
                User::where('id', $user->id)
                    ->update([
                        'pan_number' => $request->input('pan_number'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ]);
                return response()->json(['status' => true, 'message'=>'Your pan card updated successfully!']);
            }
        }    
    } 
    public function userAadhaarFrontImg(Request $request)
	{
	    $user = Auth::user(); 
        if(!empty($user->id)){
            
                if($request->hasFile('aadhaar_front'))
                {     
                    if(!empty($user->aadhaar_front))
                    {
                        $filepp = url('/public/image/profile')."/".$user->aadhaar_front;
                        if(file_exists( $filepp )){
                            unlink( base_path().'/public/image/profile/'.$user->aadhaar_front );
                        }
                    }
                    $file = $request->file('aadhaar_front');
                    $extension = $request->file('aadhaar_front')->getClientOriginalExtension();
                    $fileName = date('dmYhis',time()) . '.' . $extension;
                    $destinationPath = base_path().'/public/image/profile';
                    //$file->move($destinationPath,$fileName);
                    
                    $image_resize = Image::make($file->getRealPath());
                    $image_resize->resize(460, 460);
                    $image_resize->save($destinationPath.'/'.$fileName);
                    
                    User::where('id', $user->id)
                    ->update([
                        'aadhaar_front' => $fileName,
                        "updated_at" => date('Y-m-d H:i:s')
                    ]);
                    
                    $originalpath = url('/public/image/profile').'/'.$fileName;
                    return response()->json(["status"=>true,'message'=>'Updated successfully','aadhaar_front'=>$originalpath]);
                } else {
                    return response()->json(["status"=>false,'message'=>'Request image not found']);
                }    
        }
	}
	public function userAadhaarBackImg(Request $request)
	{
	    $user = Auth::user(); 
        if(!empty($user->id)){
            
                if($request->hasFile('aadhaar_back'))
                {     
                    if(!empty($user->aadhaar_back))
                    {
                        $filepp = url('/public/image/profile')."/".$user->aadhaar_back;
                        if(file_exists( $filepp )){
                            unlink( base_path().'/public/image/profile/'.$user->aadhaar_back );
                        }
                    }
                    $file = $request->file('aadhaar_back');
                    $extension = $request->file('aadhaar_back')->getClientOriginalExtension();
                    $fileName = date('dmYhis',time()) . '.' . $extension;
                    $destinationPath = base_path().'/public/image/profile';
                    //$file->move($destinationPath,$fileName);
                    
                    $image_resize = Image::make($file->getRealPath());
                    $image_resize->resize(460, 460);
                    $image_resize->save($destinationPath.'/'.$fileName);
                    
                    User::where('id', $user->id)
                    ->update([
                        'aadhaar_back' => $fileName,
                        "updated_at" => date('Y-m-d H:i:s')
                    ]);
                    
                    $originalpath = url('/public/image/profile').'/'.$fileName;
                    
                    return response()->json(["status"=>true,'message'=>'Updated successfully','aadhaar_back'=>$originalpath]);
                } else {
                    return response()->json(["status"=>false,'message'=>'Request image not found']);
                }    
        }
	}
	public function userPanImg(Request $request)
	{
	    $user = Auth::user(); 
        if(!empty($user->id)){
            
                if($request->hasFile('pan_card'))
                {     
                    if(!empty($user->pan_card))
                    {
                        $filepp = url('/public/image/profile')."/".$user->pan_card;
                        if(file_exists( $filepp )){
                            unlink( base_path().'/public/image/profile/'.$user->pan_card );
                        }
                    }
                    $file = $request->file('pan_card');
                    $extension = $request->file('pan_card')->getClientOriginalExtension();
                    $fileName = date('dmYhis',time()) . '.' . $extension;
                    $destinationPath = base_path().'/public/image/profile';
                    //$file->move($destinationPath,$fileName);
                    
                    $image_resize = Image::make($file->getRealPath());
                    $image_resize->resize(460, 460);
                    $image_resize->save($destinationPath.'/'.$fileName);
                    
                    User::where('id', $user->id)
                    ->update([
                        'pan_card' => $fileName,
                        "updated_at" => date('Y-m-d H:i:s')
                    ]);
                    $originalpath = url('/public/image/profile').'/'.$fileName;
                    return response()->json(["status"=>true,'message'=>'Updated successfully','panimage'=>$originalpath]);
                } else {
                    return response()->json(["status"=>false,'message'=>'Request image not found']);
                }    
        }
	}

}
