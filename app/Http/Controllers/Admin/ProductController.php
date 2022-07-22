<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Intervention\Image\ImageManagerStatic as Image;
use App\Traits\SentSMSTraits;
use App\Imports\ProductImport;
use App\Exports\ProductExport;
use App\SuperCategory;
use App\Category;
use App\ProductAttribute;
use App\ProductMaster;
use App\ProductPrice;
use App\ProductImage;
use App\ProductBid;
use App\User;
use Validator;
use Excel;
use DB;
use Carbon;
use \PDF;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use SentSMSTraits; 
    public function bidingclose(Request $request,$product_id)
    {
    	$check = ProductMaster::where('id',$product_id)
                ->where('status','approved')
                ->where('deal_status','open')
                ->whereNotNull('max_bid_user_id')
                ->first();
        if(!empty($check)){
            ProductMaster::where('id',$product_id)
                    ->update([
                        'quantity' => $request->input('quantity'),
                        'unit' => $request->input('unit'),
                        'subtotal' => $request->input('subtotal'),
                        'gst_charges' => $request->input('gst_charges'),
                        'grand_price' => $request->input('grand_price'),
                        'deal_status'=>'close',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    
            $buyer = User::find($check->max_bid_user_id);
            $supercategory = SuperCategory::find($check->super_cat_id);
            $category = Category::find($check->category_id);
            $buyername = $buyer->name;
            $amount = $check->max_bid_price;  
            $productno = $check->product_code;
            $ecategory = $category->category_name.' '.$supercategory->super_cat_name;
            $hcategory = $category->category_hindi_name.' '.$supercategory->super_cat_hindi_name;
            $quantity = $request->input('quantity');
            $unit = $request->input('unit');
            
            if(strpos($buyer->mobile, "+") !== false){
                $sendmobileno='91'.$buyer->mobile;
            } else{
                $sendmobileno=str_replace("+","",$buyer->mobile);
            }
            $msg = "You have successfully won the RAAMSETU bid of product no ".$check->product_code." of quantity ".$check->quality." for bid amount of ".$amount." per quintal/kg/piece.";
            $this->otpmaster($msg,$otpmobileno);
            
            $notify = DB::table('system_notifications')->where('title','soldproduct')->first();
            if(!empty($notify)){
                $stringvarhindi = $notify->note_hindi;
                eval("\$stringvarhindi = \"$stringvarhindi\";");
                $hindinotify = $stringvarhindi;
                
                $stringvareng = $notify->note_eng;
                eval("\$stringvareng = \"$stringvareng\";");
                $engnotify = $stringvareng;
            }else{
                $engnotify = "";
                $hindinotify = "";
            }
            
            DB::table('users_messages')
            ->insert([
                'user_id'=>$check->user_id,
                'message'=>$engnotify,
                'msg_hindi'=>$hindinotify,
                'status'=>'No',
                'created_at'=>date('Y-m-d H:i:s')
            ]);        
               
            $buyernotify = DB::table('system_notifications')->where('title','buyerclosebid')->first();
            if(!empty($buyernotify)){
                $bstringvarhindi = $buyernotify->note_hindi;
                eval("\$bstringvarhindi = \"$bstringvarhindi\";");
                $bhindinotify = $bstringvarhindi;
                
                $bstringvareng = $buyernotify->note_eng;
                eval("\$bstringvareng = \"$bstringvareng\";");
                $bengnotify = $bstringvareng;
            }else{
                $bengnotify = "";
                $bhindinotify = "";
            }
            
            DB::table('users_messages')
            ->insert([
                'user_id'=>$check->max_bid_user_id,
                'message'=>$bengnotify,
                'msg_hindi'=>$bhindinotify,
                'status'=>'No',
                'created_at'=>date('Y-m-d H:i:s')
            ]);           
                    
            Toastr::success('Bid close successfully!','Success');
            return redirect()->back();
        }else{
            Toastr::success('Invalid Product Found!','Success');
            $urrr = 'product/'.$product_id.'#ptdetails';
            return redirect()->to($urrr);
        } 
    }
    public function assignbidder(Request $request)
    {
        $bid_id = $request->bid_id;
        $product_id = $request->product_id;
        $date = date('Y-m-d H:i:s');

    	$check = ProductMaster::where('id',$product_id)
                ->where('status','approved')
                ->where('deal_status','open')
                ->first();
                
        if(!empty($check)){
            $bidrow = ProductBid::find($bid_id);

            ProductMaster::where('id',$product_id)
                    ->update([
                        'max_bid_price'=>$bidrow->bid_price,
                        'max_bid_user_id'=>$bidrow->user_id,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            $active = ProductBid::where('product_id',$product_id)->where('status','Yes')->first(); 
            if(!empty($active)){
                ProductBid::where('id',$active->id)
                        ->update(['status'=>'No']);       
            }
            ProductBid::where('id',$bid_id)
                ->update(['status'=>'Yes','updated_at' => date('Y-m-d H:i:s')]);

            return response()->json(['status'=>true]);
             
        }else{
            return response()->json(['status'=>false,'message'=>'Invalid Product Found.']);
        } 
    }
    public function productdetails(Request $request,$product_id)
    {
        $imagepath = url('public/image/product');
        $default = url('public/image/no-img.jpg');
        $default_path = url('public/image');
        $date = Carbon::now();

            
        $images = ProductImage::where('product_id',$product_id)->get();
        $product = ProductMaster::leftjoin('users', 'users.id', 'product_masters.user_id')
            ->leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
            ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
            ->select('product_masters.*','categories.category_name','categories.category_hindi_name','super_categories.super_cat_name','super_categories.super_cat_hindi_name','users.name',
                'users.email','users.mobile','users.roles','users.address','users.pincode',
                DB::raw('DATE_FORMAT(product_masters.created_at, "%M %d %Y") as order_date'),
                DB::raw('DATE_FORMAT(product_masters.bid_close_date, "%l %p %M %d %Y") as close_date')
            )
            ->where('product_masters.id',$product_id)
            ->first();
        $quality = unserialize($product->quality);
        unset($product->quality);
        $product->quality=$quality;
        
        $bids = ProductBid::leftjoin('users', 'users.id', 'product_bids.product_id')
                    ->select('product_bids.*','users.name','users.email','users.mobile',DB::raw('DATE_FORMAT(product_bids.created_at, "%l %p %M %d %Y") as biding_datetime'))
                    ->where('product_id',$product_id)->orderby('id','desc')->get();
        
        return view('admin.product.show',compact('product','images','bids'));    
            
    }
    public function getcatelist(Request $request,$id)
    {
        $category = Category::where('super_cat_id',$id)->pluck('category_name','id')->toArray();
        return response()->json($category);
    }
    public function stockfeature(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $result = ProductMaster::find($id);
        if($result[$status] =='Yes')
        {   
            ProductMaster::where('id',$id)
                    ->update([
                        $status => 'No',
                        'updated_at' => date('Y-m-d H:i:s') 
                ]);
            return "false";
        } else {
            ProductMaster::where('id',$id)
                ->update([
                    $status => 'Yes',
                    'updated_at' => date('Y-m-d H:i:s') 
                ]);
            return "true";
        }  
    }
    public function importproduct(Request $request)
    {
        return view('admin.product.import');
    }
    public function import(Request $request)
    {
        $path = $request->file('selected_file');
        $import = new ProductImport;
        $responcearray = Excel::import($import,$path);
        $responce = $import->getRowCount();
        $responce['status'] = true;
        Toastr::success('Record imported successfully!','Success');
        return redirect()->back()->with('responce',$responce);
    }
    public function download(Request $request)
    {
        if($request->has('category')){
            $category = $request->input('category');
        }else{
            $category = null;
        }  
        return Excel::download(new ProductExport($category), 'product.xlsx');
    }
    public function pendingproduct(Request $request)
    {
        
        $category = SuperCategory::pluck('super_cat_name','id')->toArray();
        $imagepath = url('public/image/product').'/';
        $default = url('public/image/no-img.jpg');
        // $current_date = Carbon\Carbon::now();
            // $date = Carbon\Carbon::createFromFormat('Y-m-d', $current_date)->format('Y-m-d');
        $date = date('Y-m-d');
            
        $temptable = DB::raw("(SELECT x.product_id,
            count(x.id) AS bid_counter
          from product_bids AS x
          GROUP BY x.product_id) as probids");

        $prodtable = \DB::raw("(SELECT x.product_id,
            GROUP_CONCAT(CONCAT('".$imagepath."','/', x.image) SEPARATOR ', ') as image_array,
            COUNT('id') AS image_counter
            from product_images AS x
            GROUP BY x.product_id) as product_img");
            
        $query = ProductMaster::leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
                ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
                ->leftjoin($temptable, 'probids.product_id', 'product_masters.id') 
                ->leftjoin($prodtable, 'product_img.product_id', 'product_masters.id')
                ->select('product_masters.*','super_categories.super_cat_name','super_categories.super_cat_hindi_name','probids.bid_counter','product_img.image_array','product_img.image_counter','categories.category_name','categories.category_hindi_name',
                        DB::raw('DATE_FORMAT(product_masters.created_at, "%M %d %Y") as order_date'),
                        DB::raw('DATE_FORMAT(product_masters.bid_close_date, "%l %p %M %d %Y") as close_date'))
                ->where('product_masters.status','pending')
                ->where('product_masters.deal_status','open')
                ->orderby('product_masters.id','desc');
            if($request->has('category')){
                $query = $query->where('product_masters.super_cat_id',$request->input('category'));
            }    
        $record = $query->paginate(25);        

        return view('admin.product.pending',compact('record','category'));
    }
    public function expiredproduct(Request $request)
    {
        
        $category = SuperCategory::pluck('super_cat_name','id')->toArray();
        $imagepath = url('public/image/product').'/';
        $default = url('public/image/no-img.jpg');
        // $current_date = Carbon\Carbon::now();
            // $date = Carbon\Carbon::createFromFormat('Y-m-d', $current_date)->format('Y-m-d');
        $date = date('Y-m-d');
            
        $query = ProductMaster::leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
                ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
                ->select('product_masters.*','super_categories.super_cat_name','categories.category_name','categories.category_hindi_name')
                ->where('product_masters.status','decline')
                ->where('product_masters.deal_status','open')
                ->orderby('product_masters.id','desc');
            if($request->has('category')){
                $query = $query->where('product_masters.super_cat_id',$request->input('category'));
            }    
        $record = $query->paginate(25);        

        return view('admin.product.expire',compact('record','category'));
    }
    public function completedproduct(Request $request)
    {
        
        $category = SuperCategory::pluck('super_cat_name','id')->toArray();
        $imagepath = url('public/image/product').'/';
        $default = url('public/image/no-img.jpg');
        // $current_date = Carbon\Carbon::now();
            // $date = Carbon\Carbon::createFromFormat('Y-m-d', $current_date)->format('Y-m-d');
        $date = date('Y-m-d');
        $temptable = DB::raw("(SELECT x.product_id,
            count(x.id) AS bid_counter
          from product_bids AS x
          GROUP BY x.product_id) as probids");

        $prodtable = \DB::raw("(SELECT x.product_id,
            GROUP_CONCAT(CONCAT('".$imagepath."','/', x.image) SEPARATOR ', ') as image_array,
            COUNT('id') AS image_counter
            from product_images AS x
            GROUP BY x.product_id) as product_img");
            
        $query = ProductMaster::leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
                ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
                ->leftjoin($temptable, 'probids.product_id', 'product_masters.id') 
                ->leftjoin($prodtable, 'product_img.product_id', 'product_masters.id')
                ->select('product_masters.*','super_categories.super_cat_name','super_categories.super_cat_hindi_name','probids.bid_counter','product_img.image_array','product_img.image_counter','categories.category_name','categories.category_hindi_name',
                        DB::raw('DATE_FORMAT(product_masters.created_at, "%M %d %Y") as order_date'),
                        DB::raw('DATE_FORMAT(product_masters.bid_close_date, "%l %p %M %d %Y") as close_date'))
                ->where('product_masters.status','approved')
                ->where('product_masters.deal_status','close')
                ->orderby('product_masters.id','desc');
            if($request->has('category')){
                $query = $query->where('product_masters.super_cat_id',$request->input('category'));
            }    
        $record = $query->paginate(25);        

        return view('admin.product.completed',compact('record','category'));
    }
    public function index(Request $request)
    {
        $date = date('Y-m-d');
        $category = SuperCategory::pluck('super_cat_name','id')->toArray();
        $imagepath = url('public/image/product').'/';
        $default = url('public/image/no-img.jpg');
        
        $temptable = DB::raw("(SELECT x.product_id,
            count(x.id) AS bid_counter
          from product_bids AS x
          GROUP BY x.product_id) as probids");

        $prodtable = \DB::raw("(SELECT x.product_id,
            GROUP_CONCAT(CONCAT('".$imagepath."','/', x.image) SEPARATOR ', ') as image_array,
            COUNT('id') AS image_counter
            from product_images AS x
            GROUP BY x.product_id) as product_img");
            
        $query = ProductMaster::leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
                ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
                ->leftjoin($temptable, 'probids.product_id', 'product_masters.id') 
                ->leftjoin($prodtable, 'product_img.product_id', 'product_masters.id')
                ->select('product_masters.*','super_categories.super_cat_name','super_categories.super_cat_hindi_name','probids.bid_counter','product_img.image_array','product_img.image_counter','categories.category_name','categories.category_hindi_name',
                        DB::raw('DATE_FORMAT(product_masters.created_at, "%M %d %Y") as order_date'),
                        DB::raw('DATE_FORMAT(product_masters.bid_close_date, "%l %p %M %d %Y") as close_date'))
                ->where('product_masters.status','approved')
                ->where('product_masters.deal_status','open')
               // ->whereDate('product_masters.bid_close_date', '>=', $date)
                ->orderby('product_masters.id','desc');
            if($request->has('category')){
                $query = $query->where('product_masters.super_cat_id',$request->input('category'));
            }    
        $record = $query->paginate(25);        
        //dd($record);
        return view('admin.product.view',compact('record','category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sellers = User::where('roles','seller')->pluck('name','id')->toArray();
        $supercategory = SuperCategory::pluck('super_cat_name','id')->toArray();
        $category = [];
        return view('admin.product.add',compact('supercategory','sellers','category'));
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
            'super_cat_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
              $tableord = DB::select("SHOW TABLE STATUS LIKE 'product_masters'");
              $generatedidd = $tableord[0]->Auto_increment;
              $generated_product_id = 'RSPRD'.$generatedidd;

              
               
                if(count($request->input('quality')) > 0){
                    $quality = serialize($request->input('quality'));
                }else{
                    $quality = null;
                }
                $inputdate = $request->input('bid_close_date');
                $exact_bid_close_date = Carbon\Carbon::createFromFormat('Y-m-d', $inputdate)->format('Y-m-d H:i:s');
                
                $currentdate = date('Y-m-d');
                $currentdate_date = Carbon\Carbon::createFromFormat('Y-m-d', $currentdate)->format('Y-m-d H:i:s');
                
                $formatted_dt1=Carbon\Carbon::parse($exact_bid_close_date);
                $formatted_dt2=Carbon\Carbon::parse($currentdate_date);
                $date_diff=$formatted_dt1->diffInDays($formatted_dt2);

                
                $addparam = [
                    'user_id'=>$request->input('user_id'),
                    'super_cat_id'=>$request->input('super_cat_id'),
                    'category_id'=>$request->input('category_id'),
                    'product_code'=>$generated_product_id,
                    'product_name'=>$request->input('product_name'),
                    'description' => $request->input('description'),
                    'moisture' => $request->input('moisture'),
                    'quality'=>$quality,
                    'quantity' => $request->input('quantity'),
                    'status' => $request->input('status'),
                    'unit' => $request->input('unit'),
                    'bid_close_date' => $exact_bid_close_date,
                    'daytosales'=>$date_diff,
                    'sell_price' => $request->input('sell_price'),
                    'created_at'=>date('Y-m-d H:i:s')
                ];

                $uniqueproductid = ProductMaster::insert($addparam);

                Toastr::success('New Product successfully added!','Success');
                return redirect()->to('admin/product');
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function productimagedelete($id)
    {
        $proimg = ProductImage::find($id);
        if(!empty($proimg->image))
        {
            $file = url('/public/image/product')."/".$proimg->image;
            if(file_exists( $file )){
                unlink( base_path().'/public/image/product/'.$proimg->image );
            }
        }
        ProductImage::where('id',$id)->delete();
        
        return response()->json(["status"=>true]);
    }
    public function show($id)
    {
        $imagepath = url('public/image/product');
        $default = url('public/image/no-img.jpg');
        $default_path = url('public/image');
        $date = Carbon\Carbon::now();

            
        $images = ProductImage::where('product_id',$id)->get();
        $product = ProductMaster::leftjoin('users', 'users.id', 'product_masters.user_id')
            ->leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
            ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
            ->select('product_masters.*','categories.category_name','categories.category_hindi_name','super_categories.super_cat_name','super_categories.super_cat_hindi_name','users.name',
                'users.email','users.mobile','users.roles','users.address','users.pincode',
                DB::raw('DATE_FORMAT(product_masters.created_at, "%M %d %Y") as order_date'),
                DB::raw('DATE_FORMAT(product_masters.bid_close_date, "%l %p %M %d %Y") as close_date')
            )
            ->where('product_masters.id',$id)
            ->first();
        
        $quality = unserialize($product->quality);
        unset($product->quality);
        $product->quality=$quality;
        
        if(!empty($product->max_bid_user_id)){
            $buyer = User::find($product->max_bid_user_id);
            $product->bname = $buyer->name;
            $product->bemail = $buyer->email;
            $product->bmobile = $buyer->mobile;
            $product->bpincode = $buyer->pincode;
            $product->baddress = $buyer->address;
        }else{
            $product->bname = null;
            $product->bemail = null;
            $product->bmobile = null;
            $product->bpincode = null;
            $product->baddress = null;
        }
        $bids = ProductBid::leftjoin('users', 'users.id', 'product_bids.user_id')
                    ->select('product_bids.*','users.name','users.email','users.mobile','users.pincode','users.address',DB::raw('DATE_FORMAT(product_bids.created_at, "%l %p %M %d %Y") as biding_datetime'))
                    ->where('product_bids.product_id',$id)->orderby('id','desc')->get();
        $smsformat= [];   
        if($product->deal_status=='close'){
        $smsformat[] = (object)[
                'id'=>'Bid Buyer Close Product 2',
                'user_id'=>$product->max_bid_user_id,
                'mobile'=>$product->bmobile,
                'msg'=>"You have successfully won the RAAMSETU bid of product no ".$product->product_code." of quantity ".$product->quantity.' '.$product->unit." for bid amount of ".$product->max_bid_price." per quintal/kg/piece."
            ];  
        $smsformat[] = (object)[
                'id'=>'Bid Buyer Close Product',
                'user_id'=>$product->max_bid_user_id,
                'mobile'=>$product->bmobile,
                'msg'=>"You have successfully won the RAAMSETU bid of product no ".$product->product_code." of quantity ".$product->quantity." quintal for bid amount of ".$product->max_bid_price." per quintal.आपने ".$product->max_bid_price." प्रति क्विंटल की बोली राशि के लिए उत्पाद संख्या ".$product->product_code." मात्रा ".$product->quantity." क्विंटल की बोली सफलतापूर्वक जीत ली है।"
            ];       
        $smsformat[] = (object)[
                'id'=>'Seller Bill 2',
                'user_id'=>$product->user_id,
                'mobile'=>$product->mobile,
                'msg'=>"Your RAAMSETU bill amount is transferred to your bank account named ".$product->name." Rs.".$product->grand_price." has been credited."
            ];
        $smsformat[] = (object)[
                'id'=>'Buyer Bill',
                'user_id'=>$product->max_bid_user_id,
                'mobile'=>$product->bmobile,
                'msg'=>"Your RAAMSETU bill amount is received in our bank account. Rs.".$product->grand_price." has been received. आपकी RAAMSETU बिल राशि हमारे बैंक खाते में जमा कर दी गई हैरु.".$product->grand_price." बैंक खाते में जमा हो गया है।"
            ];    
        $smsformat[] = (object)[
                'id'=>'Buyer Bill 2',
                'user_id'=>$product->max_bid_user_id,
                'mobile'=>$product->bmobile,
                'msg'=>"Your RAAMSETU bill amount is received in our bank account. Rs.".$product->grand_price." has been received."
            ];  
        $smsformat[] = (object)[
                'id'=>'Seller Bill',
                'user_id'=>$product->user_id,
                'mobile'=>$product->mobile,
                'msg'=>"Your RAAMSETU bill amount is transferred to your bank account named ".$product->bname." Rs.".$product->grand_price." has been credited. आपकी RAAMSETU बिल राशि आपके ".$product->bname." नाम के बैंक खाते में जमा कर दी गई हैरु.".$product->grand_price." जमा कर दिया गया है।"
            ];      
        $smsformat[] = (object)[
                'id'=>'Seller Notify',
                'user_id'=>$product->user_id,
                'mobile'=>$product->mobile,
                'msg'=>"Your RAAMSETU transportation for product no. ".$product->product_code." of quantity ".$product->quantity." quintal is booked on ".date('Y-m-d')." keep your product packed & ready to ship."
            ];  
        $smsformat[] = (object)[
                'id'=>'Approved Seller Product',
                'user_id'=>$product->user_id,
                'mobile'=>$product->mobile,
                'msg'=>"Your RAAMSETU product no ".$product->product_code." of quantity ".$product->quantity.' '.$product->unit." is approved and active for bidding.आपका उत्पाद नं  ".$product->product_code." मात्रा ".$product->quantity.' '.$product->unit." स्वीकृत एवं बोली के लिए सक्रिय है।"
            ]; 
        $smsformat[] = (object)[
                'id'=>'Buyer Notify',
                'user_id'=>$product->max_bid_user_id,
                'mobile'=>$product->bmobile,
                'msg'=>"Your RAAMSETU transportation for product no. ".$product->product_code." of quantity ".$product->quantity." quintal is booked on ".date('Y-m-d')." your product is packed & ready to ship."
            ];  
        }
        //dd($smsformat);    
        return view('admin.product.show',compact('product','images','bids','smsformat'));  
    }
    public function sentSms(Request $request){
            $inmobile = $request->input('mobile');
            $msg = $request->input('template');
            $user_id = $request->input('user_id');
            
            if(strpos($inmobile, "+") !== false){
                $sendmobileno='91'.$inmobile;
            } else{
                $sendmobileno=str_replace("+","",$inmobile);
            }
            
            $this->otpmaster($msg,$sendmobileno);
            
            DB::table('users_messages')
                        ->insert([
                            'user_id'=>$user_id,
                            'message'=>$msg,
                            'msg_hindi'=>$msg,
                            'status'=>'No',
                            'created_at'=>date('Y-m-d H:i:s')
                        ]);
            Toastr::success('Message sent and notify successfully!','Success');
            $urrl = "admin/product/".$request->input('product_id')."#ptdetails";
        return redirect()->to($urrl);
    }
    public function productAttribute(Request $request, $id)
    {
        if($request->hasFile('attr-image'))
        {     
             $file = $request->file('attr-image');
             $extension = $request->file('attr-image')->getClientOriginalExtension();
             $imageName = $id."attr".date('dmYhis',time()) . '.' . $extension;
             $destinationPath = base_path().'/public/image/product/';
             $file->move($destinationPath,$imageName);
        }else{
          $imageName = null;
        }
        ProductAttribute::insert([
            'product_id'=>$id,
            'attr_type'=>$request->input('attr_type'),
            'name'=>$request->input('name'),
            'description'=>$request->input('description'),
            'image' => $imageName,
            'status' => 'Yes',
            'created_at'=>date('Y-m-d H:i:s')
        ]);
        Toastr::success('Product attribute added successfully!','Success');
        return redirect()->to('admin/product/'.$id.'#attributes');

    }
    public function productImages(Request $request, $id)
    {
        if($request->hasFile('image'))
        {   
            foreach($request->file('image') as $media)
            {
                $file = $media;
                $extension = $media->getClientOriginalExtension();
                $fileName = $id."image".date('dmYhis',time()) . '.' . $extension;
                $destinationPath = base_path().'/public/image/product/';
                //$file->move($destinationPath,$fileName);

                
                $image_resize = Image::make($file->getRealPath());
                $image_resize->resize(400, 400);
                $image_resize->save($destinationPath.'/'.$fileName);
                    
                ProductImage::insert([
                    'product_id'=>$id,
                    'image' => $fileName,
                    'status' => 'Yes',
                    'created_at'=>date('Y-m-d H:i:s')
                ]);
                
            }
        }
        Toastr::success('Product images added successfully!','Success');
        return redirect()->to('admin/product/'.$id.'#images');

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sellers = User::where('roles','seller')->pluck('name','id')->toArray();
        
        $data = ProductMaster::find($id);
        $quality = unserialize($data->quality);
        
        unset($data->quality);
        $data->quality = $quality;
        
        $close_date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->bid_close_date)->format('Y-m-d');
        unset($data->bid_close_date);
        $data->bid_close_date = $close_date;
                
        $supercategory = SuperCategory::pluck('super_cat_name','id')->toArray();
        $category = Category::where('super_cat_id',$data->super_cat_id)->pluck('category_name','id')->toArray();
        
        return view('admin.product.add',compact('data','supercategory','category','sellers'));
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
            'super_cat_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
                $exist = ProductMaster::find($id);
                
                if(count($request->input('quality')) > 0){
                    $quality = serialize($request->input('quality'));
                }else{
                    $quality = null;
                }
                $inputdate = $request->input('bid_close_date');
                $exact_bid_close_date = Carbon\Carbon::createFromFormat('Y-m-d', $inputdate)->format('Y-m-d H:i:s');
                
                $currentdate = date('Y-m-d');
                $currentdate_date = Carbon\Carbon::createFromFormat('Y-m-d', $currentdate)->format('Y-m-d H:i:s');
                
                $formatted_dt1=Carbon\Carbon::parse($exact_bid_close_date);
                $formatted_dt2=Carbon\Carbon::parse($currentdate_date);
                $date_diff=$formatted_dt1->diffInDays($formatted_dt2);

                
                $addparam = [
                    'user_id'=>$request->input('user_id'),
                    'super_cat_id'=>$request->input('super_cat_id'),
                    'category_id'=>$request->input('category_id'),
                    'product_name'=>$request->input('product_name'),
                    'description' => $request->input('description'),
                    'moisture' => $request->input('moisture'),
                    'quality'=>$quality,
                    'quantity' => $request->input('quantity'),
                    'status' => $request->input('status'),
                    'unit' => $request->input('unit'),
                    'bid_close_date' => $exact_bid_close_date,
                    'daytosales'=>$date_diff,
                    'sell_price' => $request->input('sell_price'),
                    'updated_at'=>date('Y-m-d H:i:s')
                ];
                
                $uniqueproductid = ProductMaster::where('id',$id)->update($addparam);
                
                if($exist->status=='pending'){
                    if($request->input('status')=='approved'){
                        $userdetail = User::find($request->input('user_id'));
                        if(strpos($userdetail->mobile, "+") !== false){
                            $sendmobileno='91'.$userdetail->mobile;
                        } else{
                            $sendmobileno=str_replace("+","",$userdetail->mobile);
                        }
                        $msg = "Your RAAMSETU product no ".$exist->product_code." of quantity ".$exist->quality." ".$exist->unit." is approved and active for bidding.";
                        $this->otpmaster($msg,$otpmobileno);
                        
                        $supercategory = SuperCategory::find($request->input('super_cat_id'));
                        $category = Category::find($request->input('category_id'));
                        
                        $productno = $exist->product_code;
                        $ecategory = $category->category_name.' '.$supercategory->super_cat_name;
                        $hcategory = $category->category_hindi_name.' '.$supercategory->super_cat_hindi_name;
                        $quantity = $request->input('quantity');
                        $unit = $request->input('unit');
                        $notify = DB::table('system_notifications')->where('title','activeproduct')->first();
                        
                        if(!empty($notify)){
                            $stringvarhindi = $notify->note_hindi;
                            eval("\$stringvarhindi = \"$stringvarhindi\";");
                            $hindinotify = $stringvarhindi;
                            
                            $stringvareng = $notify->note_eng;
                            eval("\$stringvareng = \"$stringvareng\";");
                            $engnotify = $stringvareng;
                            
                        }else{
                            $engnotify = "";
                            $hindinotify = "";
                        }
                        DB::table('users_messages')
                        ->insert([
                            'user_id'=>$request->input('user_id'),
                            'message'=>$engnotify,
                            'msg_hindi'=>$hindinotify,
                            'status'=>'No',
                            'created_at'=>date('Y-m-d H:i:s')
                        ]);
                    }
                }
                
                Toastr::success('New Product successfully updated!','Success');
                $previous = $request->input('previous_url');
                return redirect()->to($previous);
            
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
        $exist =ProductMaster::find($id);
        if(!empty($exist->image)){
            $fileexistpath = url('').'/public/image/product/'.$exist->image;
            if(file_exists( $fileexistpath )){
                 unlink( base_path().'/public/image/product/'.$exist->image );
            }
        }
        ProductMaster::where('id', $id)->delete();
        $existimg = ProductImage::where('product_id',$id)->get();
        foreach($existimg as $imgg){
            if(!empty($imgg->image)){
                $fileexistpathimg = url('').'/public/image/product/'.$imgg->image;
                if(file_exists( $fileexistpathimg )){
                     unlink( base_path().'/public/image/product/'.$imgg->image );
                }
            } 
        }
        ProductImage::where('product_id',$id)->delete();
        ProductBid::where('product_id',$id)->delete();
                    
        return response()->json(['status'=>true]);
    }
    public function deleteImgAttr(Request $request)
    {
        $id = $request->input('id');
        $mode = $request->input('mode');
        if($mode=='image'){
            $exist = ProductImage::find($id);
            if(!empty($exist->image)){
                $fileexistpath = url('').'/public/image/product/'.$exist->image;
                if(file_exists( $fileexistpath )){
                     unlink( base_path().'/public/image/product/'.$exist->image );
                }
            } 
            ProductImage::where('id', $id)->delete();
        }else{
            $exist = ProductAttribute::find($id);
            if(!empty($exist->image)){
                $fileexistpath = url('').'/public/image/product/'.$exist->image;
                if(file_exists( $fileexistpath )){
                     unlink( base_path().'/public/image/product/'.$exist->image );
                }
            } 
            ProductAttribute::where('id', $id)->delete();
        }
        return response()->json(['status'=>true]);
    }
    public function Invoice(Request $request,$id,$roles)
    {
        
        $date = Carbon\Carbon::now();
        $product = ProductMaster::leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
            ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
            ->select('product_masters.*','categories.category_name','categories.category_hindi_name','super_categories.super_cat_name','super_categories.super_cat_hindi_name',
                DB::raw('DATE_FORMAT(product_masters.created_at, "%M %d %Y") as order_date'),
                DB::raw('DATE_FORMAT(product_masters.bid_close_date, "%l %p %M %d %Y") as close_date')
            )
            ->where('product_masters.id',$id)
            ->first();
        
        $quality = unserialize($product->quality);
        unset($product->quality);
        $product->quality=$quality;
        
        if($roles=='seller'){
            $seller = User::find($product->user_id);
            $product->business = 'Seller';
            $product->bname = $seller->name;
            $product->bemail = $seller->email;
            $product->bmobile = $seller->mobile;
            $product->bpincode = $seller->pincode;
            $product->baddress = $seller->address;
        }else{
            $buyer = User::find($product->max_bid_user_id);
            $product->business = 'Buyer';
            $product->bname = $buyer->name;
            $product->bemail = $buyer->email;
            $product->bmobile = $buyer->mobile;
            $product->bpincode = $buyer->pincode;
            $product->baddress = $buyer->address;
        }
        

        //return view('admin.product.invoice',compact('product'));
        
        $pdf = \PDF::loadView('admin.product.invoice',compact('product'));
        $pdfname = "invoice-".$product->product_code."-".$roles.".pdf";
        return $pdf->download($pdfname);
    }
}
