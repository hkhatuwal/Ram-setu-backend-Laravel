<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Exports\CategoryExport;
use App\Category;
use App\SuperCategory;
use App\Subcategory;
use Validator;
use Excel;
use DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function categoryhomefeature(Request $request,$id)
    {
        
        if($request->has('homelist')){
            if(count($request->input('homelist')) > 0){
                $homelist = serialize($request->input('homelist'));
            }else{
                $homelist = null;
            }
        }else{
            $homelist = null;
        }
        Category::where('id',$id)
            ->update([
                'homelist' => $homelist,
                'updated_at' => date('Y-m-d H:i:s') 
        ]);
        Toastr::success('Update feature record successfully!','Success');
        return redirect()->to('admin/category');     
    }
    public function categoryfeature(Request $request)
    {
        $id = $request->input('id');
        $result = Category::find($id);
        if($result->is_home =='Yes')
        {   
            Category::where('id',$id)
                    ->update([
                        'is_home' => 'No',
                        'updated_at' => date('Y-m-d H:i:s') 
                ]);
            return "false";
        } else {
            Category::where('id',$id)
                ->update([
                    'is_home' => 'Yes',
                    'updated_at' => date('Y-m-d H:i:s') 
                ]);
            return "true";
        }  
    }
    public function download(Request $request)
    {
        return Excel::download(new CategoryExport, 'category.xlsx');
    }
    public function index()
    {
        $categoryimage = url('public/image/category').'/';
        $default = url('public/image/no-img.jpg');
        $record = Category::leftjoin('super_categories', 'super_categories.id', 'categories.super_cat_id')
            ->select('categories.*','super_categories.super_cat_name',DB::raw('CASE WHEN categories.image IS NULL OR categories.image = "" THEN "'.$default.'" ELSE CONCAT("'.$categoryimage.'", categories.image) END as category_image'))
            ->orderby('id','DESC')->get();
        return view('admin.category.view',compact('record'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $supercategory = SuperCategory::pluck('super_cat_name','id')->toArray();
        return view('admin.category.add',compact('supercategory'));
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
            'category_name' => 'required',
            'category_hindi_name' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $cate = Category::where('category_name',$request->input('category_name'))->count();
            if($cate > 0){
                Toastr::error('Category name already exist!','Warning');
                return redirect()->back(); 
            }else{
                 
              if($request->hasFile('image'))
               {     
                 $file = $request->file('image');
                 $extension = $request->file('image')->getClientOriginalExtension();
                 $imageName = "image".date('dmYhis',time()) . '.' . $extension;
                 $destinationPath = base_path().'/public/image/category/';
                 $file->move($destinationPath,$imageName);
               }
              else{
                  $imageName = Null;
                }
                $cateadd = Category::insert([
                    'super_cat_id' => $request->input('super_cat_id'),
                    'category_name' => $request->input('category_name'),
                    'category_hindi_name' => $request->input('category_hindi_name'),
                    'description' => $request->input('description'),
                    'image' => $imageName,
                    'status' => 'Yes',
                    'created_at' => date('Y-m-d H:i:s') 
                ]);
                Toastr::success('New category successfully added!','Success');
                return redirect()->to('admin/category');
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
        $category = Category::find($id);
        $subcategory = Subcategory::where('category_id',$id)->pluck('subcat_name','id')->toArray();
        if(count($subcategory) > 0){
            return view('admin.category.show',compact('category','subcategory'));
        }else{
            Toastr::error('Subcategory not exist!','Warning');
            return redirect()->back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Category::find($id);
        $supercategory = SuperCategory::pluck('super_cat_name','id')->toArray();
        return view('admin.category.add',compact('data','supercategory'));
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
            'category_name' => 'required',
            'category_hindi_name' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $getcat = Category::find($id);
            $count = Category::where('category_name',$request->input('category_name'))->where('id','<>', $id)->count();
            if($count > 0){
                Toastr::error('Category name already exist!','Warning');
                return redirect()->back(); 
            }else{
                if($request->hasFile('image'))
                {     
                     $file = $request->file('image');
                     $extension = $request->file('image')->getClientOriginalExtension();
                     $imageName = "image".date('dmYhis',time()) . '.' . $extension;
                     $destinationPath = base_path().'/public/image/category/';
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
              
                $catupdate = Category::where('id',$id)->update([
                    'super_cat_id' => $request->input('super_cat_id'),
                    'category_name' => $request->input('category_name'),
                    'category_hindi_name' => $request->input('category_hindi_name'),
                    'description'=>$request->input('description'),
                    'image' => $imageName,
                    'updated_at' => date('Y-m-d H:i:s') 
                ]);
                Toastr::success('Category name successfully updated!','Success');
                return redirect()->to('admin/category');
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
        $existcat = Subcategory::where('category_id',$id)->first();
        if(empty($existcat)){
            $exist =Category::find($id);
            if(!empty($exist->image)){
                $fileexistpath = url('').'/public/image/category/'.$exist->image;
                if(file_exists( $fileexistpath )){
                     unlink( base_path().'/public/image/category/'.$exist->image );
                }
            }
            Category::where('id', $id)->delete();
            return response()->json(['status'=>true]);
        }else{
            return response()->json(['status'=>false]);
        }
    }
}
