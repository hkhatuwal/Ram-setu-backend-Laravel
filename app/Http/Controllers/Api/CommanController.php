<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MailerTraits;
use App\Mandirate;
use App\SuperCategory;
use App\Category;
use App\Subcategory;
use App\ProductMaster;
use App\ProductAttribute;
use App\ProductImage;
use App\FaqsSchema;
use App\ProductBid;
use App\Country;
use App\State;
use App\City;
use App\Blog;
use Validator;
use DB;
use Auth;
use Carbon;
use \PDF;

class CommanController extends Controller
{
    use MailerTraits;
    
    public function faqs(Request $request)
    {
        $data = FaqsSchema::where('status','Yes')->get();
        return response()->json($data);
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
    public function mandirates(Request $request)
    {
        $data = Mandirate::leftjoin('categories', 'categories.id', 'mandirates.commodity_id')->select('mandirates.*','categories.category_name','categories.category_hindi_name')->where('mandirates.status','Yes')->get();
        return response()->json($data);
    }
    public function testupload(Request $request,$id)
    {
        
        if($request->hasFile('image'))
        {     
            $file = $request->file('image');
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileName = date('dmYhis',time()) . '.' . $extension;
            $destinationPath = base_path().'/public/test';
            $file->move($destinationPath,$fileName);
            $viewpath = url('/').'/public/test/'.$fileName;
                
            return response()->json(["status"=>true,'profile_name'=>$fileName,'profile_path'=>$viewpath]);
        } else {
            return response()->json(["status"=>false,'message'=>'Request image not found']);
        }    
        
    }
    public function searchkeyword(Request $request)
    {
        $default = url('public/image/no-img.jpg');
        $productpath = url('public/image/product').'/';
        $supercatpath = url('public/image/supercategory').'/';
        $catpath = url('public/image/category').'/';
        $subcatpath = url('public/image/subcategory').'/';
        
        $string = $request->keyword;
        $country_code = $request->input('country_code');
        if($country_code == 'in'){
            $country='in'; 
        }else{
            $country='au';   
        }
        // $supercategory = SuperCategory::select('id','super_cat_name as title',DB::raw('"supercategory" as redirectto'),'description',DB::raw('CASE WHEN image IS NULL OR image = "" THEN "'.$default.'" ELSE CONCAT("'.$supercatpath.'", image) END as image'),DB::raw('CASE WHEN icon IS NULL OR icon = "" THEN "'.$default.'" ELSE CONCAT("'.$supercatpath.'", icon) END as icon'))
        //     ->where('super_cat_name', 'LIKE', '%'.$string.'%')
        //     ->where('status','Yes')
        //     ->get();
        
        $category = Category::select('id','category_name as title',DB::raw('"category" as redirectto'),DB::raw('CASE WHEN image IS NULL OR image = "" THEN "'.$default.'" ELSE CONCAT("'.$catpath.'", image) END as image'))
            ->where('category_name', 'LIKE', '%'.$string.'%')
            ->where('status','Yes')
            ->get();
        $subcategory = Subcategory::select('id','super_cat_id','category_id','subcat_name as title','description',DB::raw('"subcategory" as redirectto'),DB::raw('CASE WHEN image IS NULL OR image = "" THEN "'.$default.'" ELSE CONCAT("'.$subcatpath.'", image) END as image'))
            ->where('subcat_name', 'LIKE', '%'.$string.'%')
            ->where('status','Yes')->get();
            
        if($country == 'in'){
            $product = ProductMaster::select('id','product_name as title','product_name','description','is_stock','inr_mrp_price as mrp_price','inr_sell_price as sell_price',DB::raw('"product" as redirectto'),DB::raw('CASE WHEN image IS NULL OR image = "" THEN "'.$default.'" ELSE CONCAT("'.$productpath.'", image) END as image'),'product_masters.default_quantity')
            ->where('product_name', 'LIKE', '%'.$string.'%')
            ->whereNotNull('inr_sell_price')
            ->where('status','Yes')
            ->get(); 
        }else{
            $product = ProductMaster::select('id','product_name as title','product_name','description','is_stock','doller_mrp_price as mrp_price','doller_sell_price as sell_price',DB::raw('"product" as redirectto'),DB::raw('CASE WHEN image IS NULL OR image = "" THEN "'.$default.'" ELSE CONCAT("'.$productpath.'", image) END as image'),'product_masters.default_quantity')
            ->where('product_name', 'LIKE', '%'.$string.'%')
            ->whereNotNull('doller_sell_price')
            ->where('status','Yes')
            ->get(); 
        }
        
        $allItems = new \Illuminate\Database\Eloquent\Collection; 
        //$allItems = $allItems->merge($supercategory);
        $allItems = $allItems->merge($category);
        $allItems = $allItems->merge($subcategory);
        $allItems = $allItems->merge($product);
        $allItems = $allItems->take(15);
        if(!empty($allItems)){
            return response()->json(['status'=>true,'data'=>$allItems]);
        } else {
            return response()->json(['status'=>false,'message'=>'Your search result not found']);
        }
    }
    public function contactmessage(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'message' =>'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status'=>false,'error'=>$errors]);
        } else {
            
            $messages = DB::table('customer_messages')
                      ->insert([
                          'name' => $request->input('name'),
                          'email' => $request->input('email'),
                          'mobile' => $request->input('mobile'),
                          'address' => $request->input('address'),
                          'message' => $request->input('message'),
                          'created_at' => date('Y-m-d H:i:s')
                      ]);
            return response()->json(['status'=>true,'message'=>'Thanks you for contact us.']);           
        }
       
    }
    public function privacypolicy(Request $request){
        $data = DB::table('privacypolicies')->first(); 
        return response()->json($data);
    }
    public function termandcondition(Request $request){
        $data = DB::table('termandconditions')->first(); 
        return response()->json($data);
    }
    
    
    public function getCountry(Request $request)
    {
        $data = Country::select('id','country_name','code')->get();
        return response()->json([ 'status' => true,'data' => $data ]);
    }
    public function getState(Request $request,$id)
    {
        $data = State::leftjoin('countries','countries.id','states.country_id')
                ->select('states.id','states.state_name')
                ->where('countries.code',$id)->get();
        return response()->json([ 'status' => true,'data' => $data ]);
    }
    public function getCity(Request $request,$id)
    {
        $data = City::select('id','city_name')->where('state_id',$id)->get();
        return response()->json([ 'status' => true,'data' => $data ]);
    }
    public function getHomePage(Request $request)
    {
        
        $default = url('public/image/no-img.jpg');
        $productpath = url('public/image/product').'/';
        $supercatpath = url('public/image/supercategory').'/';
        $catpath = url('public/image/category').'/';
        $subcatpath = url('public/image/subcategory').'/';
        
        $banner = SuperCategory::select('id','super_cat_name','super_cat_hindi_name','description',DB::raw('CASE WHEN banner IS NULL OR banner = "" THEN "'.$default.'" ELSE CONCAT("'.$supercatpath.'", banner) END as category_banner'))->where('is_banner','Yes')->where('status','Yes')->get();
        
        return response()->json([ 'banner' => $banner ]);    
    }
    public function pluckcategory()
    {
        $subcategeory = Category::leftjoin('super_categories', 'super_categories.id', 'categories.super_cat_id')
                ->select('categories.id',
                DB::raw('CONCAT(categories.category_name," , ",super_categories.super_cat_name) as category_in_eng'),
                DB::raw('CONCAT(categories.category_hindi_name," , ",super_categories.super_cat_hindi_name) as category_in_hindi')
                )
                ->where('categories.status','Yes')
                ->get();
        return response()->json($subcategeory);        
    }
    public function getSuperCategory()
    {
        $categoryimage = url('public/image/category').'/';
        $default = url('public/image/no-img.jpg');
        
        $recordsuper = SuperCategory::where('status','Yes')->orderby('level','asc')->get();
        $record = [];
        foreach($recordsuper as $super){
            $subcategeory = Category::select('categories.*',DB::raw('CASE WHEN image IS NULL OR image = "" THEN "'.$default.'" ELSE CONCAT("'.$categoryimage.'", categories.image) END as category_image'))
                ->where('super_cat_id',$super->id)
                ->where('status','Yes')
                ->get();
            $record[]=[
                'id'=>$super->id,
                'super_cat_name'=>$super->super_cat_name,
                'super_cat_hindi_name'=>$super->super_cat_hindi_name,
                'image'=>url('public/image/supercategory').'/'.$super->image,
                'subcategory'=>$subcategeory
                ]; 
        }
        
        
        if(count($record) > 0){
            return response()->json(['status'=>true,'data'=>$record]);
        }else{
            return response()->json(['status'=>false,'data'=>$record,'message'=>'Empty super category record found.']);
        }    
    }
    public function getCatebySuperId(Request $request,$super_id)
    {
        if(!empty($super_id)){
            $categoryimage = url('public/image/category').'/';
            $default = url('public/image/no-img.jpg');
            $record = Category::leftjoin('super_categories', 'super_categories.id', 'categories.super_cat_id')
                ->select('categories.*','super_categories.super_cat_name','super_categories.super_cat_hindi_name',DB::raw('CASE WHEN categories.image IS NULL OR categories.image = "" THEN "'.$default.'" ELSE CONCAT("'.$categoryimage.'", categories.image) END as category_image'))
                ->where('categories.super_cat_id',$super_id)
                ->get();
            if(count($record) > 0){
                return response()->json(['status'=>true,'data'=>$record]);
            }else{
                return response()->json(['status'=>false,'data'=>$record,'message'=>'Empty category record found.']);
            }     
        }else{
            return response()->json(['status'=>false,'message'=>'Super Id can not empty']);
        }
    }
    
    public function getProductbySubCatId(Request $request,$super_id)
    {
        $user = Auth::user(); 
        if(!empty($super_id)){
            $imagepath = url('public/image/product').'/';
            $default = url('public/image/no-img.jpg');
            $default_path = url('public/image');
            $date = Carbon\Carbon::now();
             
            // $date = Carbon\Carbon::createFromFormat('Y-m-d', $current_date)->format('Y-m-d');
            //$date = Carbon\Carbon::today()->toDateString();
            
            $temptable = DB::raw("(SELECT x.product_id,
                count(x.id) AS bid_counter
              from product_bids AS x
              GROUP BY x.product_id) as probids");
            $prodtable = \DB::raw("(SELECT x.product_id,
                GROUP_CONCAT(CONCAT('".$imagepath."','/', x.image) SEPARATOR ', ') as image_array,
                COUNT('id') AS image_counter
                from product_images AS x
                GROUP BY x.product_id) as product_img");  
            $record = [];  
            $recordlist = ProductMaster::leftjoin('users', 'users.id', 'product_masters.user_id')
                    ->leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
                    ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
                    ->leftjoin($temptable, 'probids.product_id', 'product_masters.id')
                    ->leftjoin($prodtable, 'product_img.product_id', 'product_masters.id')
                    ->select('product_masters.*','super_categories.super_cat_name','super_categories.super_cat_hindi_name','categories.category_name','categories.category_hindi_name',DB::raw('CASE WHEN product_masters.image IS NULL OR product_masters.image = "" THEN "'.$default.'" ELSE CONCAT("'.$imagepath.'", product_masters.image) END as product_image'),'product_masters.default_quantity','probids.bid_counter','product_img.image_array','product_img.image_counter',
                        DB::raw('SUBSTRING_INDEX(users.name, " ", 1 ) as seller_name'),
                        DB::raw('DATE_FORMAT(product_masters.created_at, "%M %d %Y") as order_date'),
                        DB::raw('DATE_FORMAT(product_masters.bid_close_date, "%l %p %M %d %Y") as close_date'),
                        DB::raw('CONCAT("'.$default_path.'","/","no-img.jpg") as default_image'))
                    ->where('product_masters.status','approved')
                    ->where('product_masters.deal_status','open')
                    ->where('product_masters.bid_close_date', '>', $date)
                    ->where('product_masters.category_id',$super_id)
                    ->get();
                    dd($recordlist);
            foreach ($recordlist as $bid) {
                $checkbidactiveornot = ProductBid::where('user_id',$user->id)->where('product_id',$bid->id)->orderby('id','desc')->first();
                if($checkbidactiveornot){
                    if($checkbidactiveornot->status=='Yes'){
                        $bid->mybid_status = 'Yes';
                        $bid->mybid_price = $checkbidactiveornot->bid_price;
                    }else{
                        $bid->mybid_status = 'No';
                        $bid->mybid_price = $checkbidactiveornot->bid_price;
                    }   
                }else{
                    $bid->mybid_status = 'No';
                    $bid->mybid_price =null;
                }
                
                $record[] = $bid;
            }
            if(count($record)){
                return response()->json(['status'=>true,'data'=>$record]);
            }else{
                $record = array();
                return response()->json(['status'=>false,'data'=>$record,'message'=>'Empty product record found.']);
            }  
        }else{
            return response()->json(['status'=>false,'message'=>'Empty sub category Id.']);
        }
    }
    public function getProductbyCatId(Request $request,$cat_id)
    {
        $user =Auth::user();

        if(!empty($cat_id)){
            $imagepath = url('public/image/product').'/';
            $default = url('public/image/no-img.jpg');
            $default_path = url('public/image');
            $date = Carbon\Carbon::now();
             
            // $date = Carbon\Carbon::createFromFormat('Y-m-d', $current_date)->format('Y-m-d');
            //$date = Carbon\Carbon::today()->toDateString();
            
            $temptable = DB::raw("(SELECT x.product_id,
                count(x.id) AS bid_counter
              from product_bids AS x
              GROUP BY x.product_id) as probids");
            $prodtable = \DB::raw("(SELECT x.product_id,
                GROUP_CONCAT(CONCAT('".$imagepath."','/', x.image) SEPARATOR ', ') as image_array,
                COUNT('id') AS image_counter
                from product_images AS x
                GROUP BY x.product_id) as product_img");  
            $record = [];  
            $recordlist = ProductMaster::leftjoin('users', 'users.id', 'product_masters.user_id')
                    ->leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
                    ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
                    ->leftjoin($temptable, 'probids.product_id', 'product_masters.id')
                    ->leftjoin($prodtable, 'product_img.product_id', 'product_masters.id')
                    ->select('product_masters.*','super_categories.super_cat_name','super_categories.super_cat_hindi_name','categories.category_name','categories.category_hindi_name',DB::raw('CASE WHEN product_masters.image IS NULL OR product_masters.image = "" THEN "'.$default.'" ELSE CONCAT("'.$imagepath.'", product_masters.image) END as product_image'),'product_masters.default_quantity','probids.bid_counter','product_img.image_array','product_img.image_counter',
                        DB::raw('SUBSTRING_INDEX(users.name, " ", 1 ) as seller_name'),
                        DB::raw('DATE_FORMAT(product_masters.created_at, "%M %d %Y") as order_date'),
                        DB::raw('DATE_FORMAT(product_masters.bid_close_date, "%l %p %M %d %Y") as close_date'),
                        DB::raw('CONCAT("'.$default_path.'","/","no-img.jpg") as default_image'))
                    ->where('product_masters.status','approved')
                    ->where('product_masters.deal_status','open')
                    ->where('product_masters.bid_close_date', '>', $date)
                    ->where('product_masters.category_id',$cat_id)
                    ->get();
            foreach ($recordlist as $bid) {
                $checkbidactiveornot = ProductBid::where('user_id',$user->id)->where('product_id',$bid->id)->orderby('id','desc')->first();
                if($checkbidactiveornot){
                    if($checkbidactiveornot->status=='Yes'){
                        $bid->mybid_status = 'Yes';
                        $bid->mybid_price = $checkbidactiveornot->bid_price;
                    }else{
                        $bid->mybid_status = 'No';
                        $bid->mybid_price = $checkbidactiveornot->bid_price;
                    }   
                }else{
                    $bid->mybid_status = 'No';
                    $bid->mybid_price =null;
                }
                
                $record[] = $bid;
            }
            if(count($record)){
                return response()->json(['status'=>true,'data'=>$record]);
            }else{
                $record = array();
                return response()->json(['status'=>false,'data'=>$record,'message'=>'Empty product record found.']);
            }  
        }else{
            return response()->json(['status'=>false,'message'=>'Empty sub category Id.']);
        }
    }
    public function getSuperCatWithProducts(Request $request)
    {
        $user =Auth::user();

        $sup_categories=SuperCategory::with(['products'=>function($product){
$product->with('user');
        }])->get();
        
        return response()->json(['status'=>true,'data'=>$sup_categories]);

       
    }
  
    public function productDetailbyId(Request $request,$id)
    {
        $imagepath = url('public/image/product').'/';
        $default = url('public/image/no-img.jpg');
        $default_path = url('public/image');
        
        $current_date = Carbon\Carbon::now();
        $date = Carbon\Carbon::createFromFormat('Y-m-d', $current_date)->format('Y-m-d');
        $prodtable = \DB::raw("(SELECT x.product_id,
                GROUP_CONCAT(CONCAT('".$imagepath."','/', x.image) SEPARATOR ', ') as image_array,
                COUNT('id') AS image_counter
                from product_images AS x
                GROUP BY x.product_id) as product_img"); 
        $record = ProductMaster::leftjoin('users', 'users.id', 'product_masters.user_id')
                ->leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
                ->select('product_masters.*','super_categories.super_cat_name','super_categories.super_cat_hindi_name',DB::raw('CASE WHEN product_masters.image IS NULL OR product_masters.image = "" THEN "'.$default.'" ELSE CONCAT("'.$imagepath.'", product_masters.image) END as product_image'),'product_masters.default_quantity','product_img.image_array','product_img.image_counter',
                        DB::raw('SUBSTRING_INDEX(users.name, " ", 1 ) as seller_name'),
                        DB::raw('CONCAT("'.$default_path.'","/","no-img.jpg") as default_image'))
                ->where('product_masters.status','approved')
                ->where('product_masters.deal_status','open')
                ->whereDate('bid_close_date', '<=', $date)
                ->where('product_masters.id',$id)
                ->first();
        
        if(!empty($record)){
            return response()->json(['status'=>true,'data'=>$record]);
        }else{
            $record = array();
            return response()->json(['status'=>false,'data'=>$record,'message'=>'Product data not found.']);
        }  
    }
    public function attributesbyproduct($product_id)
    {
        $attributes = array();
        $attributelist = ProductAttribute::where('product_id',$product_id)->where('status','Yes')->get();
        if(!empty($attributelist)){
            $imagepath = url('public/image/product').'/';
            $default = url('public/image/no-img.jpg');
            foreach ($attributelist as $gkey => $gvalue) {
                if($gvalue->attr_type=='Color'){
                    if(!empty($gvalue->image)){
                        $attribute_image = $imagepath.''.$gvalue->image;
                    }else{
                        $attribute_image = $default;
                    }
                    $attributes[$gvalue->attr_type][] = (object)[
                                    "id" => $gvalue->id,
                                    "name" => $gvalue->name,
                                    "description" => $gvalue->description,
                                    "image" => $attribute_image
                                ];
                }else{
                    $attributes[$gvalue->attr_type][] = (object)[
                                    "id" => $gvalue->id,
                                    "name" => $gvalue->name,
                                    "description" => $gvalue->description,
                                ];
                }
            }
        }
        return $attributes;
    }

    public function blogrecord(Request $request)
    {

        $blog = url('public/image/blogs').'/';
        $default = url('public/image/no-img.jpg');
        $record = Blog::select('*',DB::raw('CASE WHEN banner IS NULL OR banner = "" THEN "'.$default.'" ELSE CONCAT("'.$blog.'", banner) END as blog_banner'),DB::raw('DATE_FORMAT(created_at, "%d %M %Y") as post_date'))->where('status','Yes')->get();
        if(count($record) > 0){
            return response()->json(['status'=>true,'data'=>$record]);
        }else{
            return response()->json(['status'=>false,'data'=>$record,'message'=>'Empty blog record found.']);
        }    
    }
    public function blogdetail($id)
    {

        $blog = url('public/image/blogs').'/';
        $default = url('public/image/no-img.jpg');
        $record = Blog::select('*',DB::raw('CASE WHEN banner IS NULL OR banner = "" THEN "'.$default.'" ELSE CONCAT("'.$blog.'", banner) END as blog_banner'),DB::raw('DATE_FORMAT(created_at, "%d %M %Y") as post_date'))->where('id',$id)->first();
        if(!empty($record)){
            return response()->json(['status'=>true,'data'=>$record]);
        }else{
            return response()->json(['status'=>false,'data'=>$record,'message'=>'Empty blog record found.']);
        }    


    }
}
