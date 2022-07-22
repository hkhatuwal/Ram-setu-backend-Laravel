<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MailerTraits;
use Intervention\Image\ImageManagerStatic as Image;
use App\ProductMaster;
use App\ProductImage;
use App\ProductBid;
use App\Category;
use App\SuperCategory;
use App\User;
use Carbon\Carbon;
use Validator;
use DB;
use Auth;


class BiddingController extends Controller
{
    use MailerTraits;
    
    public function getcommodity(Request $request)
	{
	    $user = Auth::user(); 
        if(!empty($user->id)){
            if($user->roles=='seller'){
                $datalist = ProductMaster::select('category_id')
                    ->where('user_id',$user->id)
                    ->groupBy('category_id')
                    ->get();
                $data=[];    
                foreach($datalist as $each){
                    $subcategeory = Category::find($each->category_id);
                    if(!empty($subcategeory)){
                        $image = url('public/image/category').'/'.$subcategeory->image;
                    }else{
                        $image = url('public/image/no-img.jpg');
                    }
                    $data[] = (object)[
                        'id'=>$each->category_id,
                        'image'=>$image,
                        'name'=>$subcategeory->category_name,
                        'hindi_name'=>$subcategeory->category_hindi_name,
                        ];
                }    
            }else{
                $datalist = ProductMaster::select('category_id')
                    ->where('max_bid_user_id',$user->id)
                    ->groupBy('category_id')
                    ->get();
                $data=[];    
                foreach($datalist as $each){
                    $subcategeory = Category::find($each->category_id);
                    if(!empty($subcategeory)){
                        $image = url('public/image/category').'/'.$subcategeory->image;
                    }else{
                        $image = url('public/image/no-img.jpg');
                    }
                    $data[] = (object)[
                        'id'=>$each->category_id,
                        'image'=>$image,
                        'name'=>$subcategeory->category_name,
                        'hindi_name'=>$subcategeory->category_hindi_name,
                        ];
                }    
            }
            return response()->json($data);
        }
	}
    public function getcounter(Request $request)
	{
	    $user = Auth::user(); 
        if(!empty($user->id)){
            if($user->roles=='seller'){
                $record = ProductMaster::select(DB::raw('COUNT(CASE WHEN `status` LIKE "%pending%" THEN 1 END) as pending_product'),DB::raw('COUNT(CASE WHEN `deal_status` LIKE "%close%" THEN 1 END) as product_history'),DB::raw('COUNT(CASE WHEN `status` LIKE "%approved%" THEN 1 END) as approved_product'))
                    ->where('user_id',$user->id)
                    ->first();
                $data = [
                    'pending_product' => $record->pending_product,
                    'approved_product' => $record->approved_product,
                    'product_history' => $record->product_history,
                    'active_bid' => null,
                    'confirmed_deal' => null,
                    ];
                
            }else{
                $active_bid = 0;
                $date = Carbon::now();
                $prbidlist = ProductBid::select('product_id')->where('user_id',$user->id)->groupBy('product_id')->get();
                foreach($prbidlist as $prodd){
                    $productlist = ProductMaster::where('deal_status','open')
                        ->where('bid_close_date', '>', $date)
                        ->where('id',$prodd->product_id)
                        ->first();
                    if(!empty($productlist)) {
                        $active_bid += 1;
                    }
                }
                
                $confirmed_deal = ProductMaster::where('deal_status','close')
                    ->where('max_bid_user_id',$user->id)
                    ->count();
                $data = [
                    'pending_product' => null,
                    'approved_product' => null,
                    'product_history' => null,
                    'active_bid' => $active_bid,
                    'confirmed_deal' => $confirmed_deal,
                    ];
                
            }
            return response()->json($data);
        }
	}
    public function getproductimages(Request $request,$product_id)
	{
	    $user = Auth::user(); 
        if(!empty($user->id)){
            $imagepath = url('public/image/product').'/';
            $default = url('public/image/no-img.jpg');
            $images = ProductImage::select('id','product_id',DB::raw('CASE WHEN image IS NULL OR image = "" THEN "'.$default.'" ELSE CONCAT("'.$imagepath.'", image) END as product_image'))->where('product_id',$product_id)->orderby('id','desc')->get();
            
            if(!$images->isEmpty()){
                return response()->json(["status"=>true,'images'=>$images,'counter'=>count($images)]);
            } else {
                return response()->json(["status"=>false,'message'=>'Request image not found']);
            }    
        }
	}
    public function productimage(Request $request,$id)
	{
	    $user = Auth::user(); 
        if(!empty($user->id)){
            
                if($request->hasFile('image'))
                {     
                    
                    $file = $request->file('image');
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $fileName = date('dmYhis',time()) . '.' . $extension;
                    $destinationPath = base_path().'/public/image/product';
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
                    $imagepath = url('public/image/product').'/';
                    $default = url('public/image/no-img.jpg');
                    
                    $images = ProductImage::select('id','product_id',DB::raw('CASE WHEN image IS NULL OR image = "" THEN "'.$default.'" ELSE CONCAT("'.$imagepath.'", image) END as product_image'))->where('product_id',$id)->orderby('id','desc')->get();
                    return response()->json(["status"=>true,'images'=>$images,'counter'=>count($images)]);
                } else {
                    return response()->json(["status"=>false,'message'=>'Request image not found']);
                }    
        }
	}
	public function deleteproductimages(Request $request,$image_id)
	{
	    $user = Auth::user(); 
        if(!empty($user->id)){
            $proimg = ProductImage::find($image_id);
            if(!empty($proimg->image))
            {
                $file = url('/public/image/product')."/".$proimg->image;
                if(file_exists( $file )){
                    unlink( base_path().'/public/image/product/'.$proimg->image );
                }
            }
            ProductImage::where('id',$image_id)->delete();
            
            $imagepath = url('public/image/product').'/';
            $default = url('public/image/no-img.jpg');
            
            $images = ProductImage::select('id','product_id',DB::raw('CASE WHEN image IS NULL OR image = "" THEN "'.$default.'" ELSE CONCAT("'.$imagepath.'", image) END as product_image'))->where('product_id',$proimg->product_id)->orderby('id','desc')->get();
            return response()->json(["status"=>true,'images'=>$images,'counter'=>count($images)]);
        }
	}
    public function postproduct(Request $request)
	{
		$user = Auth::user(); 
	    if(!empty($user->id)){
	      
	    	$validator = Validator::make($request->all(), [
	        	    'super_cat_id' => 'required',
                    'product_name' => 'required',
                    'quantity' => 'required',
                    'bid_close_date' => 'required',
	          ]);
	        if ($validator->fails()) {
	            $errors = $validator->errors();
	            return response()->json($errors);
	        } else {
	        	$tableord = DB::select("SHOW TABLE STATUS LIKE 'product_masters'");
                $generatedidd = $tableord[0]->Auto_increment;
                $generated_product_id = 'RSPRD'.$generatedidd;

                $onlydaysclosebid = $request->input('bid_close_date');
                $date = new Carbon(date('Y-m-d H:i:s')); 
                $exact_bid_close_date = Carbon::createFromFormat('Y-m-d H:i:s', $date)->addDays($onlydaysclosebid)->format('Y-m-d H:i:s');

                // if($request->hasFile('image'))
                // {     
	               // $file = $request->file('image');
	               // $extension = $request->file('image')->getClientOriginalExtension();
	               // $imageName = "image".date('dmYhis',time()) . '.' . $extension;
	               // $destinationPath = base_path().'/public/image/product/';
	               // $file->move($destinationPath,$imageName);
                // }else{
                //     $imageName = null;
                // }
                // if($request->hasFile('image1'))
                // {     
	               // $file = $request->file('image1');
	               // $extension = $request->file('image1')->getClientOriginalExtension();
	               // $imageName1 = "image".date('dmYhis',time()) . '.' . $extension;
	               // $destinationPath = base_path().'/public/image/product/';
	               // $file->move($destinationPath,$imageName1);
                // }else{
                //     $imageName1 = null;
                // }
                if(count($request->input('quality')) > 0){
                    $quality = serialize($request->input('quality'));
                }else{
                    $quality = null;
                }
                $category = Category::find($request->input('super_cat_id'));
                
                $store = [
                    'user_id'=>$user->id,
                    'super_cat_id'=>$category->super_cat_id,
                    'category_id'=>$request->input('super_cat_id'),
                    'product_code'=>$generated_product_id,
                    'product_name'=>$request->input('product_name'),
                    'quantity' => $request->input('quantity'),
                    'moisture' => $request->input('moisture'),
                    'quality'=>$quality,
                    'unit' => $request->input('unit'),
                    'bid_close_date' => $exact_bid_close_date,
                    'daytosales'=>$onlydaysclosebid,
                    'base_price' => $request->input('base_price'),
                    'status' => 'pending',
                    'deal_status' => 'open',
                    'created_at'=>date('Y-m-d H:i:s')
                ];
                
                $supercategory = SuperCategory::find($category->super_cat_id);

                $productno = $generated_product_id;
                $ecategory = $category->category_name.' '.$supercategory->super_cat_name;
                $hcategory = $category->category_hindi_name.' '.$supercategory->super_cat_hindi_name;
                $quantity = $request->input('quantity');
                $unit = $request->input('unit');
                $notify = DB::table('system_notifications')->where('title','addproduct')->first();
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
    	            'user_id'=>$user->id,
    	            'message'=>$engnotify,
    	            'msg_hindi'=>$hindinotify,
    	            'status'=>'No',
    	            'created_at'=>date('Y-m-d H:i:s')
    	        ]);
    	        
    	        
    	        $buyeruser = User::select('id','deal_in')->where('roles','buyer')->get();
        	    foreach($buyeruser as $buyer){
        	        if(!empty($buyer->deal_in)){
            	        $super = SuperCategory::where('super_cat_name',$buyer->deal_in)->first();
            	        if(!empty($super)){
            	            $fnotify = DB::table('system_notifications')->where('title','readybiding')->first();
                            if(!empty($fnotify)){
                                $adstringvarhindi = $fnotify->note_hindi;
                                eval("\$adstringvarhindi = \"$adstringvarhindi\";");
                                $adhindinotify = $adstringvarhindi;
                                
                                $adstringvareng = $fnotify->note_eng;
                                eval("\$adstringvareng = \"$adstringvareng\";");
                                $adengnotify = $adstringvareng;
                            }else{
                                $adengnotify = "";
                                $adhindinotify = "";
                            }
                            DB::table('users_messages')
                	        ->insert([
                	            'user_id'=>$buyer->id,
                	            'message'=>$adengnotify,
                	            'msg_hindi'=>$adhindinotify,
                	            'status'=>'No',
                	            'created_at'=>date('Y-m-d H:i:s')
                	        ]);
            	        }
        	        }
        	    }     

                $pmid = ProductMaster::insertGetId($store);
                return response()->json(['status'=>true,'id'=>$pmid]); 
	        }
	    }
	}
	public function notifypreferedbuyer($sentdata)
	{
	    $buyeruser = User::select('id','deal_in')->where('roles','buyer')->get();
	    foreach($buyeruser as $buyer){
	        if(!empty($buyer->deal_in)){
    	        $super = SuperCategory::where('super_cat_name',$buyer->deal_in)->first();
    	        if(!empty($super)){
    	            $notify = DB::table('system_notifications')->where('title','readybiding')->first();
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
        	            'user_id'=>$buyer->id,
        	            'message'=>$engnotify,
        	            'msg_hindi'=>$hindinotify,
        	            'status'=>'No',
        	            'created_at'=>date('Y-m-d H:i:s')
        	        ]);
    	        }
	        }
	    }
	    return true;
	}
	public function updateproduct(Request $request,$id)
	{
	    
		$user = Auth::user(); 
	    if(!empty($user->id)){
	    	$validator = Validator::make($request->all(), [
	        	    'super_cat_id' => 'required',
                    'product_name' => 'required',
                    'quantity' => 'required',
                    'bid_close_date' => 'required',
	          ]);
	        if ($validator->fails()) {
	            $errors = $validator->errors();
	            return response()->json($errors);
	        } else {
	        	$onlydaysclosebid = $request->input('bid_close_date');
                $date = new Carbon(date('Y-m-d H:i:s')); 
                $exact_bid_close_date = Carbon::createFromFormat('Y-m-d H:i:s', $date)->addDays($onlydaysclosebid)->format('Y-m-d H:i:s');

                // if($request->hasFile('image'))
                // {     
	               // $file = $request->file('image');
	               // $extension = $request->file('image')->getClientOriginalExtension();
	               // $imageName = "image".date('dmYhis',time()) . '.' . $extension;
	               // $destinationPath = base_path().'/public/image/product/';
	               // $file->move($destinationPath,$imageName);
                // }else{
                //     $imageName = null;
                // }
                // if($request->hasFile('image1'))
                // {     
	               // $file = $request->file('image1');
	               // $extension = $request->file('image1')->getClientOriginalExtension();
	               // $imageName1 = "image".date('dmYhis',time()) . '.' . $extension;
	               // $destinationPath = base_path().'/public/image/product/';
	               // $file->move($destinationPath,$imageName1);
                // }else{
                //     $imageName1 = null;
                // }
                if(count($request->input('quality')) > 0){
                    $quality = serialize($request->input('quality'));
                }else{
                    $quality = null;
                }
                $category = Category::find($request->input('super_cat_id'));
                
                ProductMaster::where('id',$id)->update([
                    'super_cat_id'=>$category->super_cat_id,
                    'category_id'=>$request->input('super_cat_id'),
                    'product_name'=>$request->input('product_name'),
                    'quantity' => $request->input('quantity'),
                    'moisture' => $request->input('moisture'),
                    'quality' => $quality,
                    'bid_close_date' => $exact_bid_close_date,
                    'daytosales'=>$onlydaysclosebid,
                    'base_price' => $request->input('base_price'),
                    'updated_at'=>date('Y-m-d H:i:s')
                ]);
                return response()->json(['status'=>true]); 
	        }
	    }
	}
	public function postedactiveproduct(Request $request)
	{
	    
		$user = Auth::user(); 
	    if(!empty($user->id)){
	    	$imagepath = url('public/image/product');
            $default = url('public/image/no-img.jpg');
            $default_path = url('public/image');
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
                
            $recordlist = ProductMaster::leftjoin('users', 'users.id', 'product_masters.max_bid_user_id')
                    ->leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
                    ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
                    ->leftjoin($temptable, 'probids.product_id', 'product_masters.id') 
                    ->leftjoin($prodtable, 'product_img.product_id', 'product_masters.id')
                    ->select('product_masters.*','super_categories.super_cat_name','super_categories.super_cat_hindi_name','probids.bid_counter','product_img.image_array','product_img.image_counter','categories.category_name','categories.category_hindi_name',
                        DB::raw('SUBSTRING_INDEX(users.name, " ", 1 ) as buyer_name'),
                        DB::raw('DATE_FORMAT(product_masters.created_at, "%M %d %Y") as order_date'),
                        DB::raw('DATE_FORMAT(product_masters.bid_close_date, "%l %p %M %d %Y") as close_date'),
                        DB::raw('CONCAT("'.$default_path.'","/","no-img.jpg") as default_image')
                    )
                    ->where('product_masters.deal_status','open')
                    ->where('product_masters.status','approved')
                    //->whereDate('product_masters.bid_close_date', '', $date)
                    ->where('product_masters.user_id',$user->id)
                    ->orderby('product_masters.id','desc')
                    ->get();
            $record = array();         
            if(!$recordlist->isEmpty()){
                foreach($recordlist as $one){
                    $quality = unserialize($one->quality);
                    unset($one->quality);
                    $one->quality=$quality;
                    $record[] = $one;
                }
                return response()->json(['status'=>true,'data'=>$record]);
            }else{
                $record = array();
                return response()->json(['status'=>false,'data'=>$record,'message'=>'Empty product record found.']);
            }          
	    }
	}
	public function postedpendingproduct(Request $request)
	{
	    
		$user = Auth::user(); 
	    if(!empty($user->id)){
	    	$imagepath = url('public/image/product');
            $default = url('public/image/no-img.jpg');
            $default_path = url('public/image');
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
                
            $recordlist = ProductMaster::leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
                    ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
                    ->leftjoin($temptable, 'probids.product_id', 'product_masters.id') 
                    ->leftjoin($prodtable, 'product_img.product_id', 'product_masters.id')
                    ->select('product_masters.*','super_categories.super_cat_name','super_categories.super_cat_hindi_name','probids.bid_counter','product_img.image_array','product_img.image_counter','categories.category_name','categories.category_hindi_name',
                        DB::raw('DATE_FORMAT(product_masters.created_at, "%M %d %Y") as order_date'),
                        DB::raw('DATE_FORMAT(product_masters.bid_close_date, "%l %p %M %d %Y") as close_date'),
                        DB::raw('CONCAT("'.$default_path.'","/","no-img.jpg") as default_image')
                    )
                    ->where('product_masters.deal_status','open')
                    ->where('product_masters.status','pending')
                    //->whereDate('product_masters.bid_close_date', '', $date)
                    ->where('product_masters.user_id',$user->id)
                    ->orderby('product_masters.id','desc')
                    ->get();
            $record = array();        
            if(!$recordlist->isEmpty()){
                foreach($recordlist as $one){
                    $quality = unserialize($one->quality);
                    unset($one->quality);
                    $one->quality=$quality;
                    $record[] = $one;
                }
                return response()->json(['status'=>true,'data'=>$record]);
            }else{
                $record = array();
                return response()->json(['status'=>false,'data'=>$record,'message'=>'Empty product record found.']);
            }          
	    }
	}
	
	public function postedproducthistory(Request $request)
	{
	    
		$user = Auth::user(); 
	    if(!empty($user->id)){
	    	$imagepath = url('public/image/product');
            $default = url('public/image/no-img.jpg');
            $default_path = url('public/image');
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
                
            $recordlist = ProductMaster::leftjoin('users', 'users.id', 'product_masters.max_bid_user_id')
                    ->leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
                    ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
                    ->leftjoin($temptable, 'probids.product_id', 'product_masters.id')
                    ->leftjoin($prodtable, 'product_img.product_id', 'product_masters.id')
                    ->select('product_masters.*','super_categories.super_cat_name','super_categories.super_cat_hindi_name','probids.bid_counter','product_img.image_array','product_img.image_counter','categories.category_name','categories.category_hindi_name',
                        DB::raw('SUBSTRING_INDEX(users.name, " ", 1 ) as buyer_name'),
                        DB::raw('DATE_FORMAT(product_masters.created_at, "%M %d %Y") as order_date'),
                        DB::raw('DATE_FORMAT(product_masters.bid_close_date, "%l %p %M %d %Y") as close_date'),
                        DB::raw('CONCAT("'.$default_path.'","/","no-img.jpg") as default_image')
                    )
                    ->where('product_masters.deal_status','close')
                    ->where('product_masters.user_id',$user->id)
                    ->get();
            $record = array();        
            if(!$recordlist->isEmpty()){
                foreach($recordlist as $one){
                    $quality = unserialize($one->quality);
                    unset($one->quality);
                    $one->quality=$quality;
                    $record[] = $one;
                }
                return response()->json(['status'=>true,'data'=>$record]);
            }else{
                $record = array();
                return response()->json(['status'=>false,'data'=>$record,'message'=>'Empty product record found.']);
            }          
	    }
	}
	public function myactivebiddetail(Request $request,$product_id)
    {
        
        $user = Auth::user(); 
        if(!empty($user->id)){
            $imagepath = url('public/image/product');
            $default = url('public/image/no-img.jpg');
            $default_path = url('public/image');
            $date = Carbon::now();
            $bid_counter = ProductBid::where('product_id',$product_id)->count();
            
            $prodtable = \DB::raw("(SELECT x.product_id,
                GROUP_CONCAT(CONCAT('".$imagepath."','/', x.image) SEPARATOR ', ') as image_array,
                COUNT('id') AS image_counter
                from product_images AS x
                GROUP BY x.product_id) as product_img");
                
            $product = ProductMaster::leftjoin('users', 'users.id', 'product_masters.user_id')
                ->leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
                ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
                ->leftjoin($prodtable, 'product_img.product_id', 'product_masters.id')
                ->select('product_masters.*','categories.category_name','categories.category_hindi_name','super_categories.super_cat_name','super_categories.super_cat_hindi_name','product_img.image_array','product_img.image_counter',
                    DB::raw('SUBSTRING_INDEX(users.name, " ", 1 ) as seller_name'),
                    DB::raw('DATE_FORMAT(product_masters.created_at, "%M %d %Y") as order_date'),
                    DB::raw('DATE_FORMAT(product_masters.bid_close_date, "%l %p %M %d %Y") as close_date'),
                    DB::raw('CONCAT("'.$default_path.'","/","no-img.jpg") as default_image')
                )
                ->where('product_masters.deal_status','open')
                ->where('product_masters.bid_close_date', '>', $date)
                ->where('product_masters.id',$product_id)
                ->first();
            $quality = unserialize($product->quality);
            unset($product->quality);
            $product->quality=$quality;
            
            $checkbidactiveornot = ProductBid::where('user_id',$user->id)->where('product_id',$product_id)->orderby('id','desc')->first();
            if($checkbidactiveornot->status=='Yes'){
                $product->mybid_status = 'Yes';
                $product->mybid_price = $checkbidactiveornot->bid_price;
            }else{
                $product->mybid_status = 'No';
                $product->mybid_price = $checkbidactiveornot->bid_price;
            }   
            $product->bid_counter = $bid_counter;
            
            
            if(!empty($product)){
                return response()->json(['status'=>true,'data'=>$product]);
            }else{
                $record = array();
                return response()->json(['status'=>false,'data'=>$record,'message'=>'Empty product record found.']);
            }          
        }
    }
    public function myactivebid(Request $request)
    {
        
        $user = Auth::user(); 
        if(!empty($user->id)){
            $imagepath = url('public/image/product');
            $default = url('public/image/no-img.jpg');
            $default_path = url('public/image');
            
            $date = Carbon::now();
            $prbidlist = ProductBid::select('product_id')->where('user_id',$user->id)->groupBy('product_id')->get();
            
            $temptable = DB::raw("(SELECT x.product_id,
                count(x.id) AS bid_counter
              from product_bids AS x
              GROUP BY x.product_id) as probids");
            $prodtable = \DB::raw("(SELECT x.product_id,
                GROUP_CONCAT(CONCAT('".$imagepath."','/', x.image) SEPARATOR ', ') as image_array,
                COUNT('id') AS image_counter
                from product_images AS x
                GROUP BY x.product_id) as product_img");  
            $recordlist = [];
            foreach ($prbidlist as $bid) {
                $productlist = ProductMaster::leftjoin('users', 'users.id', 'product_masters.user_id')
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
                    ->where('product_masters.deal_status','open')
                    ->where('product_masters.bid_close_date', '>', $date)
                    ->where('product_masters.id',$bid->product_id)
                    ->first();
                if(!empty($productlist)) {
                    
                    $quality = unserialize($productlist->quality);
                    unset($productlist->quality);
                    $productlist->quality=$quality;

                    $checkbidactiveornot = ProductBid::where('user_id',$user->id)->where('product_id',$bid->product_id)->orderby('id','desc')->first();
                    if($checkbidactiveornot->status=='Yes'){
                        $productlist->mybid_status = 'Yes';
                        $productlist->mybid_price = $checkbidactiveornot->bid_price;
                    }else{
                        $productlist->mybid_status = 'No';
                        $productlist->mybid_price = $checkbidactiveornot->bid_price;
                    }   
                    
                    $recordlist[] = $productlist;
                }   
            }
            if(count($recordlist) > 0){
                return response()->json(['status'=>true,'data'=>$recordlist]);
            }else{
                $record = array();
                return response()->json(['status'=>false,'data'=>$record,'message'=>'Empty product record found.']);
            }          
        }
    }
    public function completeddeal(Request $request)
    {
        
        $user = Auth::user(); 
        if(!empty($user->id)){
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
            $record = [];        
            if(!$recordlist->isEmpty()){
                foreach($recordlist as $one){
                    $quality = unserialize($one->quality);
                    unset($one->quality);
                    $one->quality=$quality;
                    $record[] = $one;
                }
                return response()->json(['status'=>true,'data'=>$record]);
            }else{
                $record = array();
                return response()->json(['status'=>false,'data'=>$record,'message'=>'Empty product record found.']);
            }          
        }
    }
	
	public function postStoreBid(Request $request)
	{
	    
		$user = Auth::user(); 
	    if(!empty($user->id)){
	    	$validator = Validator::make($request->all(), [
	        	    'product_id' => 'required',
	        	    'bid_price' => 'required',
	          ]);
	        if ($validator->fails()) {
	            $errors = $validator->errors();
	            return response()->json($errors);
	        } else {
	        	// $current_date = Carbon::now();
          //       $date = Carbon::createFromFormat('Y-m-d', $current_date)->format('Y-m-d');
                $date = date('Y-m-d H:i:s');

	        	$check = ProductMaster::where('id',$request->input('product_id'))
	                    ->where('status','approved')
	                    ->where('deal_status','open')
	                    ->whereDate('bid_close_date', '>', $date)
	                    ->first();
	                    
	            if(!empty($check)){
	                if(!empty($check->max_bid_price)){
	                    if($request->input('bid_price') > $check->max_bid_price){
	                        $varify=true;
	                    }else{
	                        $varify=false;
	                    }
	                }else{
	                    $varify=true;
	                }
	                if($varify==true){
	                    if(!empty($check->max_bid_user_id)){
                        ProductBid::where('product_id',$check->id)
                                ->where('user_id',$check->max_bid_user_id)
                                ->update(['status'=>'No','updated_at' => date('Y-m-d H:i:s')]);
                        }
                        ProductMaster::where('id',$request->input('product_id'))
                                ->update([
                                    'max_bid_price'=>$request->input('bid_price'),
                                    'max_bid_user_id'=>$user->id,
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                        ProductBid::insert([
                            'user_id'=>$user->id,
                            'product_id'=>$request->input('product_id'),
                            'base_price'=>$check->sell_price,
                            'bid_price'=>$request->input('bid_price'),
                            'status'=>'Yes',
                            'created_at' => date('Y-m-d H:i:s')
                        ]);  
                        $responce = $this->postbidresponse($user->id,$request->input('product_id'));
                        return response()->json(['status'=>true,'data'=>$responce]);
	                }else{
	                    return response()->json(['status'=>false,'message'=>'Your enter bid amount will be less than maximum bid amount.']);
	                }
                          
	            }else{
                    return response()->json(['status'=>false,'message'=>'Invalid Product Found.']);
	            }        
	        }
	    }
	}
	public function postbidresponse($user_id,$product_id)
    {
        
        $imagepath = url('public/image/product').'/';
        $default = url('public/image/no-img.jpg');
        $date = Carbon::now();
        $bid_counter = ProductBid::where('product_id',$product_id)->count();
        
        $product = ProductMaster::leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
            ->select('product_masters.*','super_categories.super_cat_name','super_categories.super_cat_hindi_name',DB::raw('CASE WHEN product_masters.image IS NULL OR product_masters.image = "" THEN "'.$default.'" ELSE CONCAT("'.$imagepath.'", product_masters.image) END as product_image'),
                DB::raw('DATE_FORMAT(product_masters.created_at, "%M %d %Y") as order_date'),
                DB::raw('DATE_FORMAT(product_masters.bid_close_date, "%l %p %M %d %Y") as close_date')
            )
            ->where('product_masters.deal_status','open')
            ->where('product_masters.bid_close_date', '>', $date)
            ->where('product_masters.id',$product_id)
            ->first();
        $checkbidactiveornot = ProductBid::where('user_id',$user_id)->where('product_id',$product_id)->orderby('id','desc')->first();
        if($checkbidactiveornot->status=='Yes'){
            $product->mybid_status = 'Yes';
            $product->mybid_price = $checkbidactiveornot->bid_price;
        }else{
            $product->mybid_status = 'No';
            $product->mybid_price = $checkbidactiveornot->bid_price;
        }   
        $product->bid_counter = $bid_counter;
        
        return $product;    
       
    }
    
	
}
