<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\BlogsCategory;
use App\Blog;
use Carbon\Carbon;
use DB;
use Validator;


class BlogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function upload(Request $request)
    {
        if($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName.'_'.time().'.'.$extension;
        
            $request->file('upload')->move(public_path('blogs'), $fileName);
   
            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = url('public/image/blogs/'.$fileName); 
            $msg = 'Image uploaded successfully'; 
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
               
            @header('Content-type: text/html; charset=utf-8'); 
            echo $response;
        }
    }
    public function download(Request $request)
    {
        return Excel::download(new UsersExport($skill), 'blogs.xlsx');
    }
    public function index()
    {
        
        $record = Blog::leftjoin('blogs_categories','blogs_categories.id','blogs.category_id')
            ->select('blogs.*','blogs_categories.blog_cat_name')
            ->orderby('id','desc')
            ->get();
        return view('admin.blog.view',compact('record'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = BlogsCategory::pluck('blog_cat_name','id')->toArray();

        return view('admin.blog.add',compact('category'));
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
            'title' => 'required',
            'author_name' => 'required',
            'category_id' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $unique = Blog::where('title',$request->input('title'))->where('category_id',$request->input('category_id'))->count();
            if($unique > 0){
              Toastr::error('Blog title already exist!');    
              return redirect()->back(); 
            }else{
                if($request->hasFile('banner'))
                {    
                    $path = base_path().'/public/image/blogs';
                    $file = $request->file('banner');
                    $extension = $request->file('banner')->getClientOriginalExtension();
                    $bannername = "banner".date('dmYhis',time()).'.'.$extension;
                    $file->move($path,$bannername);
                }else{
                    $bannername = Null;
                }  

                $blog_url = strtolower(preg_replace('/\s+/', '-', $request->input('title')));
                $in = Blog::insert([
                            'category_id' => $request->input('category_id'),
                            'title' => $request->input('title'),
                            'blog_url' => $blog_url,
                            'keyword' => $request->input('keyword'),
                            'designation' => $request->input('designation'),
                            'author_name' => $request->input('author_name'),
                            'banner' => $bannername,
                            'status' => 'Yes',
                            'description' => $request->input('description'),
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                Toastr::success('Blog added successfully!');
                return redirect()->to('admin/blog');
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
        $data = Blog::find($id);
        $category = BlogsCategory::pluck('blog_cat_name','id')->toArray();
        return view('admin.blog.add',compact('data','category'));
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
            'title' => 'required',
            'author_name' => 'required',
            'category_id' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $unique = Blog::where('title',$request->input('title'))
                    ->where('category_id',$request->input('category_id'))
                    ->where('id','<>',$id)
                    ->count();
            if($unique > 0){
              Toastr::error('Blog title already exist!');    
              return redirect()->back(); 
            }else{
                $blog = Blog::find($id);
                if($request->hasFile('banner'))
                {    
                    $path = base_path().'/public/image/blogs';
                    $file = $request->file('banner');
                    $extension = $request->file('banner')->getClientOriginalExtension();
                    $bannername = "banner".date('dmYhis',time()).'.'.$extension;
                    $file->move($path,$bannername);
                }else{
                    if(!empty($blog->banner)){
                        $bannername = $blog->banner;
                    }else{
                        $bannername = Null;
                    }
                }
                $blog_url = strtolower(preg_replace('/\s+/', '-', $request->input('title')));
                $in = Blog::where('id',$id)->update([
                            'category_id' => $request->input('category_id'),
                            'title' => $request->input('title'),
                            'blog_url' => $blog_url,
                            'keyword' => $request->input('keyword'),
                            'designation' => $request->input('designation'),
                            'author_name' => $request->input('author_name'),
                            'banner' => $bannername,
                            'description' => $request->input('description'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                Toastr::success('Blog updated successfully!');        
                return redirect()->to('admin/blog');
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
        
        $delete = Blog::where('id', $id)->delete();
        return response()->json(['status'=>true]);
    }
    public function isActive(Request $request)
    {
       $record = Blog::where('id', $request->input('id'))->first();

      if($record['status']=='Yes')
       {   
         $update = Blog::where('id',$request->id)
                            ->update([
                                'status' => 'No',
                                'updated_at' => date('Y-m-d H:i:s') 
                            ]);
            return "No";
       } else {
        $update = Blog::where('id',$request->id)
                            ->update([
                                'status' => 'Yes',
                                'updated_at' => date('Y-m-d H:i:s') 
                            ]);
            return "Yes";
       }  
     }
}
