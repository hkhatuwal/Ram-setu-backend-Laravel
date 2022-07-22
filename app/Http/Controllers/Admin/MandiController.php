<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Exports\CategoryExport;
use App\Mandirate;
use App\Category;
use Validator;
use Excel;
use DB;

class MandiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function download(Request $request)
    {
        return Excel::download(new CategoryExport, 'category.xlsx');
    }
    public function index()
    {
        $record = Mandirate::leftjoin('categories', 'categories.id', 'mandirates.commodity_id')->select('mandirates.*','categories.category_name','categories.category_hindi_name')->orderBy('mandirates.id','desc')->get();
        return view('admin.mandi.view',compact('record'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = Category::pluck('category_name','id')->toArray();
        return view('admin.mandi.add',compact('category'));
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
            'commodity_id' => 'required',
            'min' => 'required',
            'max' => 'required',
            'modelrate' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $cateadd = Mandirate::insert([
                'commodity_id' => $request->input('commodity_id'),
                'min' => $request->input('min'),
                'max' => $request->input('max'),
                'modelrate' => $request->input('modelrate'),
                'status' => 'Yes',
                'created_at' => date('Y-m-d H:i:s') 
            ]);
            Toastr::success('New mandi rate successfully added!','Success');
            return redirect()->to('admin/mandirate');
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
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Mandirate::find($id);
        $category = Category::pluck('category_name','id')->toArray();
        return view('admin.mandi.add',compact('data','category'));
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
            'commodity_id' => 'required',
            'min' => 'required',
            'max' => 'required',
            'modelrate' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            Mandirate::where('id',$id)->update([
                'commodity_id' => $request->input('commodity_id'),
                'min' => $request->input('min'),
                'max' => $request->input('max'),
                'modelrate' => $request->input('modelrate'),
                'updated_at' => date('Y-m-d H:i:s') 
            ]);
            Toastr::success('Mandi rate successfully updated!','Success');
            return redirect()->to('admin/mandirate');
        
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
        Mandirate::where('id', $id)->delete();
        return response()->json(['status'=>true]);
    }
}
