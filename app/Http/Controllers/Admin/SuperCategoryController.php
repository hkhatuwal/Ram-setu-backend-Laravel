<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\SuperCategoryExport;
use App\SuperCategory;
use App\Category;
use App\ProductMaster;
use Brian2694\Toastr\Facades\Toastr;
use Validator;
use Excel;
use DB;

class SuperCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function isBanner(Request $request)
    {
        $id = $request->input('id');
        $result = SuperCategory::find($id);
        if($result->is_banner =='Yes')
        {   
            SuperCategory::where('id',$id)
                    ->update([
                        'is_banner' => 'No',
                        'updated_at' => date('Y-m-d H:i:s') 
                ]);
            return "false";
        } else {
            SuperCategory::where('id',$id)
                ->update([
                    'is_banner' => 'Yes',
                    'updated_at' => date('Y-m-d H:i:s') 
                ]);
            return "true";
        }  
    }
    public function download(Request $request)
    {
        return Excel::download(new SuperCategoryExport, 'super-category.xlsx');
    }
    public function index()
    {
        $categoryimage = url('public/image/supercategory').'/';
        $default = url('public/image/no-img.jpg');
        $record = SuperCategory::select('super_categories.*',DB::raw('CASE WHEN icon IS NULL OR icon = "" THEN "'.$default.'" ELSE CONCAT("'.$categoryimage.'", icon) END as category_icon'),DB::raw('CASE WHEN image IS NULL OR image = "" THEN "'.$default.'" ELSE CONCAT("'.$categoryimage.'", image) END as category_image'),DB::raw('CASE WHEN banner IS NULL OR banner = "" THEN "'.$default.'" ELSE CONCAT("'.$categoryimage.'", banner) END as category_banner'))->orderby('id','DESC')->get();
        return view('admin.supercategory.view',compact('record'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.supercategory.add');
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
            'super_cat_name' => 'required',
            'super_cat_hindi_name' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $cate = SuperCategory::where('super_cat_name',$request->input('super_cat_name'))->count();
            if($cate > 0){
                Toastr::error('Category name already exist!','Warning');
                return redirect()->back(); 
            }else{
                if($request->hasFile('icon'))
                {     
                    $iconfile = $request->file('icon');
                    $iconextension = $request->file('icon')->getClientOriginalExtension();
                    $iconName = "icon".date('dmYhis',time()) . '.' . $iconextension;
                    $icondestinationPath = base_path().'/public/image/supercategory/';
                    $iconfile->move($icondestinationPath,$iconName);
                }else{
                    $iconName = Null;
                } 
                if($request->hasFile('image'))
                {     
                    $file = $request->file('image');
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $imageName = "image".date('dmYhis',time()) . '.' . $extension;
                    $destinationPath = base_path().'/public/image/supercategory/';
                    $file->move($destinationPath,$imageName);
                }else{
                    $imageName = Null;
                }
                if($request->hasFile('banner'))
                {     
                    $bannerfile = $request->file('banner');
                    $bextension = $request->file('banner')->getClientOriginalExtension();
                    $bannerName = "banner".date('dmYhis',time()) . '.' . $bextension;
                    $bdestinationPath = base_path().'/public/image/supercategory/';
                    $bannerfile->move($bdestinationPath,$bannerName);
                }else{
                    $bannerName = Null;
                }
                $cateadd = SuperCategory::insert([
                    'super_cat_name' => $request->input('super_cat_name'),
                    'super_cat_hindi_name' => $request->input('super_cat_hindi_name'),
                    'description' => $request->input('description'),
                    'icon' => $iconName,
                    'image' => $imageName,
                    'banner' => $bannerName,
                    'status' => 'Yes',
                    'created_at' => date('Y-m-d H:i:s') 
                ]);
                Toastr::success('New category successfully added!','Success');
                return redirect()->to('admin/supercategory');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = SuperCategory::find($id);
        return view('admin.supercategory.add',compact('data'));
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
            'super_cat_name' => 'required',
            'super_cat_hindi_name' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $getcat = SuperCategory::find($id);
            $count = SuperCategory::where('super_cat_name',$request->input('super_cat_name'))->where('id','<>', $id)->count();
            if($count > 0){
                Toastr::error('Category name already exist!','Warning');
                return redirect()->back(); 
            }else{
                if($request->hasFile('icon'))
                {     
                    $iconfile = $request->file('icon');
                    $iconextension = $request->file('icon')->getClientOriginalExtension();
                    $iconName = "icon".date('dmYhis',time()) . '.' . $iconextension;
                    $icondestinationPath = base_path().'/public/image/supercategory/';
                    $iconfile->move($icondestinationPath,$iconName);
                }else{
                    if(!empty($getcat->icon))
                    {
                      $iconName = $getcat->icon;
                    }
                    else {
                      $iconName = Null;
                    }
                } 
                if($request->hasFile('image'))
                {     
                     $file = $request->file('image');
                     $extension = $request->file('image')->getClientOriginalExtension();
                     $imageName = "image".date('dmYhis',time()) . '.' . $extension;
                     $destinationPath = base_path().'/public/image/supercategory/';
                     $file->move($destinationPath,$imageName);
                }else{
                    if(!empty($getcat->image))
                    {
                      $imageName = $getcat->image;
                    }
                    else {
                      $imageName = Null;
                    }
                }
                if($request->hasFile('banner'))
                {     
                    $bannerfile = $request->file('banner');
                    $bextension = $request->file('banner')->getClientOriginalExtension();
                    $bannerName = "banner".date('dmYhis',time()) . '.' . $bextension;
                    $bdestinationPath = base_path().'/public/image/supercategory/';
                    $bannerfile->move($bdestinationPath,$bannerName);
                }else{
                    if(!empty($getcat->banner))
                    {
                      $bannerName = $getcat->banner;
                    }
                    else {
                      $bannerName = Null;
                    }
                }
                $catupdate = SuperCategory::where('id',$id)->update([
                    'super_cat_name'=>$request->input('super_cat_name'),
                    'super_cat_hindi_name' => $request->input('super_cat_hindi_name'),
                    'description'=>$request->input('description'),
                    'icon' => $iconName,
                    'image' => $imageName,
                    'banner' => $bannerName,
                    'updated_at' => date('Y-m-d H:i:s') 
                ]);
                Toastr::success('Super category name successfully updated!','Success');
                return redirect()->to('admin/supercategory');
            }
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
        $existcat = Category::where('super_cat_id',$id)->first();
        if(empty($existcat)){
            $exist =SuperCategory::find($id);
            if(!empty($exist->icon)){
                $fileexistpathicon = url('').'/public/image/supercategory/'.$exist->icon;
                if(file_exists( $fileexistpathicon )){
                     unlink( base_path().'/public/image/supercategory/'.$exist->icon );
                }
            }
            if(!empty($exist->image)){
                $fileexistpath = url('').'/public/image/supercategory/'.$exist->image;
                if(file_exists( $fileexistpath )){
                     unlink( base_path().'/public/image/supercategory/'.$exist->image );
                }
            }
            if(!empty($exist->banner)){
                $fileexistpathbanner = url('').'/public/image/supercategory/'.$exist->banner;
                if(file_exists( $fileexistpathbanner )){
                     unlink( base_path().'/public/image/supercategory/'.$exist->banner );
                }
            }
            SuperCategory::where('id', $id)->delete();
            return response()->json(['status'=>true]);
        }else{
            return response()->json(['status'=>false]);
        }
    }
}
