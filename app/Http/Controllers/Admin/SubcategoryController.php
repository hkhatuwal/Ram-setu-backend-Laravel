<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Exports\SubcategoryExport;
use App\Category;
use App\Subcategory;
use App\SuperCategory;
use App\ProductMaster;
use Validator;
use Excel;
use DB;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        return Excel::download(new SubcategoryExport, 'subcategory.xlsx');
    }
    public function index()
    {
        $imagepath = url('public/image/subcategory').'/';
        $default = url('public/image/no-img.jpg');
        $record = Subcategory::leftjoin('categories', 'categories.id', 'subcategories.category_id')
                    ->leftjoin('super_categories', 'super_categories.id', 'subcategories.super_cat_id')
                    ->select('subcategories.*','categories.category_name','super_categories.super_cat_name',DB::raw('CASE WHEN subcategories.image IS NULL OR subcategories.image = "" THEN "'.$default.'" ELSE CONCAT("'.$imagepath.'", subcategories.image) END as image_path'))
                    ->get();
        return view('admin.subcategory.view',compact('record'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $supercategory = SuperCategory::pluck('super_cat_name','id')->toArray();
        $category = [];
        return view('admin.subcategory.add',compact('category','supercategory'));
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
            'category_id' => 'required',
            'subcat_name' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $exist = Subcategory::where('subcat_name',$request->input('subcat_name'))->where('category_id',$request->input('category_id'))->count();
            if($exist > 0){
                Toastr::error('Sub Category name already exist!','Warning');
                return redirect()->back(); 
            }else{
              if($request->hasFile('image'))
               {     
                 $file = $request->file('image');
                 $extension = $request->file('image')->getClientOriginalExtension();
                 $imageName = "image".date('dmYhis',time()) . '.' . $extension;
                 $destinationPath = base_path().'/public/image/subcategory/';
                 $file->move($destinationPath,$imageName);
               }else{
                  $imageName = Null;
               }
                Subcategory::insert([
                    'super_cat_id'=>$request->input('super_cat_id'),
                    'category_id'=>$request->input('category_id'),
                    'subcat_name'=>$request->input('subcat_name'),
                    'description' => $request->input('description'),
                    'image' => $imageName,
                    'created_at'=>date('Y-m-d H:i:s')
                ]);
                Toastr::success('New subcategory successfully added!','Success');
                return redirect()->to('admin/subcategory');
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
        $data = Subcategory::find($id);
        $supercategory = SuperCategory::pluck('super_cat_name','id')->toArray();
        $category = Category::where('super_cat_id',$data->super_cat_id)->pluck('category_name','id')->toArray();
        return view('admin.subcategory.add',compact('data','supercategory','category'));
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
            'category_id' => 'required',
            'subcat_name' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $getcat = Subcategory::find($id);
            $city = Subcategory::where('subcat_name',$request->input('subcat_name'))->where('category_id',$request->input('category_id'))->where('id','<>', $id)->count();
            if($city > 0){
                Toastr::error('Subcategory name already exist.','Warning');
                return redirect()->back(); 
            }else{
                if($request->hasFile('image'))
                {     
                     $file = $request->file('image');
                     $extension = $request->file('image')->getClientOriginalExtension();
                     $imageName = "image".date('dmYhis',time()) . '.' . $extension;
                     $destinationPath = base_path().'/public/image/subcategory/';
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
                Subcategory::where('id',$id)->update([
                    'super_cat_id'=>$request->input('super_cat_id'),
                    'category_id'=>$request->input('category_id'),
                    'subcat_name'=>$request->input('subcat_name'),
                    'description' => $request->input('description'),
                    'image' => $imageName,
                    'updated_at'=>date('Y-m-d H:i:s')
                ]);
                Toastr::success('Record successfully updated.','Success');
                return redirect()->to('admin/subcategory');
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
        $existpro = ProductMaster::where('subcategory_id',$id)->first();
        if(empty($existpro)){
            $exist =Subcategory::find($id);
            if(!empty($exist->image)){
                $fileexistpath = url('').'/public/image/subcategory/'.$exist->image;
                if(file_exists( $fileexistpath )){
                     unlink( base_path().'/public/image/subcategory/'.$exist->image );
                }
            }
            Subcategory::where('id', $id)->delete();
            return response()->json(['status'=>true]);
        }else{
            return response()->json(['status'=>false]);
        }
    }
}
