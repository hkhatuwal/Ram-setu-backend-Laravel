<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;
use App\ProductMaster;
use App\ProductBid;
use App\UsersBank;
use App\User;
use Validator;
use DB;

class BuyerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addbank(Request $request)
    {
        UsersBank::insert([
            'user_id'=> $request->input('user_id'),
            'account_holder' => $request->input('account_holder'),
            'account_number' => $request->input('account_number'),
            'ifsc' => $request->input('ifsc'),
            'bank_name' => $request->input('bank_name'),
            "created_at" => date('Y-m-d H:i:s')
            ]);
        return response()->json(['status'=>true]);    
    }
    public function index()
    {

        $imagepath = url('public/image/profile').'/';
        $default = url('public/image/no-img.jpg');
        $temptable = DB::raw("(SELECT x.max_bid_user_id, count(x.id) AS product_counter from product_masters AS x
              GROUP BY x.max_bid_user_id) as prodmster");

        $record = User::leftjoin($temptable, 'prodmster.max_bid_user_id', 'users.id')
            ->select('users.*','prodmster.product_counter',DB::raw('CASE WHEN profile_pic IS NULL OR profile_pic = "" THEN "'.$default.'" ELSE CONCAT("'.$imagepath.'", profile_pic) END as profile_path'))
            ->where('users.roles','buyer')
            ->orderby('users.id','DESC')
            ->paginate(25);

        return view('admin.buyer.view',compact('record'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.buyer.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required',
            'pincode' => 'required',
            'address' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $buyerexist = User::where('mobile',$request->input('mobile'))->where('roles','buyer')->count();
            if($buyerexist > 0){
                Toastr::error('Buyer already exist.','Warning');
                return redirect()->back()->withInput(); 
            }else{
                if($request->hasFile('profile_pic'))
                {     
                    $picfile = $request->file('profile_pic');
                    $picextension = $request->file('profile_pic')->getClientOriginalExtension();
                    $picimageName = "pic".date('dmYhis',time()) . '.' . $picextension;
                    $picdestinationPath = base_path().'/public/image/profile/';

                    $picimage_resize = Image::make($picfile->getRealPath());
                    $picimage_resize->resize(460, 460);
                    $picimage_resize->save($picdestinationPath.'/'.$picimageName);
                }else{
                    $picimageName = null;
                }
                
                if($request->hasFile('aadhaar_front'))
                {     
                    $frontfile = $request->file('aadhaar_front');
                    $frontextension = $request->file('aadhaar_front')->getClientOriginalExtension();
                    $frontfileName = date('dmYhis',time()) . '.' . $frontextension;
                    $frontdestinationPath = base_path().'/public/image/profile';

                    $frontimage_resize = Image::make($frontfile->getRealPath());
                    $frontimage_resize->resize(460, 460);
                    $frontimage_resize->save($frontdestinationPath.'/'.$frontfileName);
                }else{
                    $frontfileName = null;
                }
                if($request->hasFile('aadhaar_back'))
                {     
                    $backfile = $request->file('aadhaar_back');
                    $backextension = $request->file('aadhaar_back')->getClientOriginalExtension();
                    $backfileName = date('dmYhis',time()) . '.' . $backextension;
                    $backdestinationPath = base_path().'/public/image/profile';

                    $backimage_resize = Image::make($backfile->getRealPath());
                    $backimage_resize->resize(460, 460);
                    $backimage_resize->save($backdestinationPath.'/'.$backfileName);
                }else{
                    $backfileName = null;
                }
                if($request->hasFile('pan_card'))
                {     
                    $panfile = $request->file('pan_card');
                    $panextension = $request->file('pan_card')->getClientOriginalExtension();
                    $panfileName = date('dmYhis',time()) . '.' . $panextension;
                    $pandestinationPath = base_path().'/public/image/profile';

                    $panimage_resize = Image::make($panfile->getRealPath());
                    $panimage_resize->resize(460, 460);
                    $panimage_resize->save($pandestinationPath.'/'.$panfileName);
                }else{
                    $panfileName = null;
                }
                
                
                User::insert([
                    'name'=>$request->input('name'),
                    'mobile'=>$request->input('mobile'),
                    'pincode'=>$request->input('pincode'),
                    'address'=>$request->input('address'),
                    'roles'=>'buyer',
                    'profile_pic'=>$picimageName,
                    'aadhaar_number'=>$request->input('aadhaar_number'),
                    'aadhaar_front' => $frontfileName,
                    'aadhaar_back' => $backfileName,
                    'pan_number'=>$request->input('pan_number'),
                    'pan_card' => $panfileName,
                    'status'=>'Yes',
                    'created_at'=>date('Y-m-d H:i:s')
                ]);
                Toastr::success('Record successfully added.','Success');
                return redirect()->to('admin/buyer');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        $imagepath = url('public/image/product');
        $default = url('public/image/no-img.jpg');
        $default_path = url('public/image');
        
        $temptable = DB::raw("(SELECT x.product_id,
            count(x.id) AS bid_counter
          from product_bids AS x
          GROUP BY x.product_id) as probids");
        $prodtable = \DB::raw("(SELECT x.product_id,
            GROUP_CONCAT(CONCAT('".$imagepath."','/', x.image) SEPARATOR ', ') as image_array,
            COUNT('id') AS image_counter
            from product_images AS x
            GROUP BY x.product_id) as product_img");
            
        $recordlist = ProductMaster::leftjoin('users', 'users.id', 'product_masters.user_id')
                ->leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
                ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
                ->leftjoin($temptable, 'probids.product_id', 'product_masters.id')
                ->leftjoin($prodtable, 'product_img.product_id', 'product_masters.id')
                ->select('product_masters.*','categories.category_name','categories.category_hindi_name','super_categories.super_cat_name','super_categories.super_cat_hindi_name','probids.bid_counter','product_img.image_array','product_img.image_counter',
                    DB::raw('SUBSTRING_INDEX(users.name, " ", 1 ) as seller_name'),
                    DB::raw('DATE_FORMAT(product_masters.created_at, "%M %d %Y") as order_date'),
                    DB::raw('DATE_FORMAT(product_masters.bid_close_date, "%l %p %M %d %Y") as close_date'),
                    DB::raw('CONCAT("'.$default_path.'","/","no-img.jpg") as default_image')
                )
                ->where('product_masters.deal_status','close')
                ->where('product_masters.max_bid_user_id',$user->id)
                ->get();

        $products = [];        
        if(!$recordlist->isEmpty()){
            foreach($recordlist as $one){
                $quality = unserialize($one->quality);
                unset($one->quality);
                $one->quality=$quality;
                $products[] = (object)$one;
            }
        }
        return view('admin.buyer.show',compact('user','products'));    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $data = User::find($id);
       $data->banks = UsersBank::where('user_id',$id)->get();
       return view('admin.buyer.add',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required',
            'pincode' => 'required',
            'address' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
                $exist = User::find($id);
                
                if($request->hasFile('profile_pic'))
                {    
                    if(!empty($exist->profile_pic))
                    {
                        $picfilepp = url('/public/image/profile')."/".$exist->profile_pic;
                        if(file_exists( $picfilepp )){
                            unlink( base_path().'/public/image/profile/'.$exist->profile_pic );
                        }
                    }
                    $picfile = $request->file('profile_pic');
                    $picextension = $request->file('profile_pic')->getClientOriginalExtension();
                    $picimageName = "pic".date('dmYhis',time()) . '.' . $picextension;
                    $picdestinationPath = base_path().'/public/image/profile/';

                    $picimage_resize = Image::make($picfile->getRealPath());
                    $picimage_resize->resize(460, 460);
                    $picimage_resize->save($picdestinationPath.'/'.$picimageName);
                }else{
                    if(!empty($exist->profile_pic))
                    {
                      $picimageName = $exist->profile_pic;
                    }
                    else {
                      $picimageName = null;
                    }
                }
                
                if($request->hasFile('aadhaar_front'))
                {     
                    if(!empty($exist->aadhaar_front))
                    {
                        $filepp = url('/public/image/profile')."/".$exist->aadhaar_front;
                        if(file_exists( $filepp )){
                            unlink( base_path().'/public/image/profile/'.$exist->aadhaar_front );
                        }
                    }
                    $frontfile = $request->file('aadhaar_front');
                    $frontextension = $request->file('aadhaar_front')->getClientOriginalExtension();
                    $frontfileName = date('dmYhis',time()) . '.' . $frontextension;
                    $frontdestinationPath = base_path().'/public/image/profile';

                    $frontimage_resize = Image::make($frontfile->getRealPath());
                    $frontimage_resize->resize(460, 460);
                    $frontimage_resize->save($frontdestinationPath.'/'.$frontfileName);
                }else{
                    if(!empty($exist->aadhaar_front))
                    {
                      $frontfileName = $exist->aadhaar_front;
                    }
                    else {
                      $frontfileName = null;
                    }
                }
                if($request->hasFile('aadhaar_back'))
                {     
                    if(!empty($exist->aadhaar_back))
                    {
                        $backfilepp = url('/public/image/profile')."/".$exist->aadhaar_back;
                        if(file_exists( $backfilepp )){
                            unlink( base_path().'/public/image/profile/'.$exist->aadhaar_back );
                        }
                    }
                    $backfile = $request->file('aadhaar_back');
                    $backextension = $request->file('aadhaar_back')->getClientOriginalExtension();
                    $backfileName = date('dmYhis',time()) . '.' . $backextension;
                    $backdestinationPath = base_path().'/public/image/profile';

                    $backimage_resize = Image::make($backfile->getRealPath());
                    $backimage_resize->resize(460, 460);
                    $backimage_resize->save($backdestinationPath.'/'.$backfileName);
                }else{
                    if(!empty($exist->aadhaar_back))
                    {
                      $backfileName = $exist->aadhaar_back;
                    }
                    else {
                      $backfileName = null;
                    }
                }
                if($request->hasFile('pan_card'))
                {     
                    if(!empty($exist->pan_card))
                    {
                        $panfilepp = url('/public/image/profile')."/".$exist->pan_card;
                        if(file_exists( $panfilepp )){
                            unlink( base_path().'/public/image/profile/'.$exist->pan_card );
                        }
                    }
                    $panfile = $request->file('pan_card');
                    $panextension = $request->file('pan_card')->getClientOriginalExtension();
                    $panfileName = date('dmYhis',time()) . '.' . $panextension;
                    $pandestinationPath = base_path().'/public/image/profile';

                    $panimage_resize = Image::make($panfile->getRealPath());
                    $panimage_resize->resize(460, 460);
                    $panimage_resize->save($pandestinationPath.'/'.$panfileName);
                }else{
                    if(!empty($exist->pan_card))
                    {
                      $panfileName = $exist->pan_card;
                    }
                    else {
                      $panfileName = null;
                    }
                }
                User::where('id',$id)->update([
                    'name'=>$request->input('name'),
                    'pincode'=>$request->input('pincode'),
                    'address'=>$request->input('address'),
                    'profile_pic'=>$picimageName,
                    'aadhaar_number'=>$request->input('aadhaar_number'),
                    'aadhaar_front' => $frontfileName,
                    'aadhaar_back' => $backfileName,
                    'pan_number'=>$request->input('pan_number'),
                    'pan_card' => $panfileName,
                    'updated_at'=>date('Y-m-d H:i:s')
                ]);
                if($request->has('bank_id'))
                {  
                    if(count($request->input('bank_id'))>0){
                        foreach($request->input('bank_id') as $bankkey=>$valuee){
                            UsersBank::where('id',$valuee)->update([
                                'account_holder' => $request->input('account_holder')[$bankkey],
                                'account_number' => $request->input('account_number')[$bankkey],
                                'ifsc' => $request->input('ifsc')[$bankkey],
                                'bank_name' => $request->input('bank_name')[$bankkey],
                                "updated_at" => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
                Toastr::success('Record successfully updated.','Success');
                return redirect()->to('admin/buyer');
            
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $data = User::where('id',$id)->first();
       $checkuserassign = \DB::table('product_bids')->where('user_id',$id)->count();
       if($checkuserassign > 0){
       return response()->json(['status'=>'No']); 
       }else{
       $delete = User::where('id',$id)->delete();
       return response()->json(['status'=>'Yes','data'=>$id]);  
       }
    }
}
