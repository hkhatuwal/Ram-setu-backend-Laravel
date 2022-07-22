<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Toast;
use App\BlogsCategory;
use App\Blog;
use Carbon\Carbon;
use DB;
use Validator;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $record = BlogsCategory::orderby('id','desc')->get();
        return view('admin.blogcategory.view',compact('record'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('admin.blogcategory.add');
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
            'blog_cat_name' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $unique = BlogsCategory::where('blog_cat_name',$request->input('blog_cat_name'))->count();
            if($unique > 0){
                toast('Blogs category already exist!','warning','top-right');     
              return redirect()->back(); 
            }else{
                $blog_cat_url = strtolower(preg_replace('/\s+/', '-', $request->input('blog_cat_name')));
                $in = BlogsCategory::insert([
                            'blog_cat_name' => $request->input('blog_cat_name'),
                            'blog_cat_url' => $blog_cat_url,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                toast('Blog category added successfully!','success','top-right');
                return redirect()->to('admin/blog-category');
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
        $data = BlogsCategory::find($id);
        return view('admin.blogcategory.add',compact('data'));
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
            'blog_cat_name' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $unique = BlogsCategory::where('blog_cat_name',$request->input('blog_cat_name'))
                    ->where('id','<>',$id)
                    ->count();
            if($unique > 0){
                toast('Blog category already exist!','warning','top-right'); 
              return redirect()->back(); 
            }else{
                $blog_cat_url = strtolower(preg_replace('/\s+/', '-', $request->input('blog_cat_name')));
                BlogsCategory::where('id',$id)->update([
                    'blog_cat_name' => $request->input('blog_cat_name'),
                    'blog_cat_url' => $blog_cat_url,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                toast('Blog category updated successfully!','success','top-right');
                return redirect()->to('admin/blog-category');
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
        
        $delete = BlogsCategory::where('id', $id)->delete();
        return response()->json(['status'=>'true']);
    }
    public function isActive(Request $request)
    {
       $record = BlogsCategory::where('id', $request->input('id'))->first();

      if($record['status']=='Yes')
       {   
         $update = BlogsCategory::where('id',$request->id)
                            ->update([
                                'status' => 'No',
                                'updated_at' => date('Y-m-d H:i:s') 
                            ]);
            return "No";
       } else {
        $update = BlogsCategory::where('id',$request->id)
                            ->update([
                                'status' => 'Yes',
                                'updated_at' => date('Y-m-d H:i:s') 
                            ]);
            return "Yes";
       }  
     }
}
