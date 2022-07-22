<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\FaqsSchema;
use Validator;
use Excel;
use DB;

class FaqsController extends Controller
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
       
        $record = FaqsSchema::orderby('id','DESC')->get();
        return view('admin.faqs.view',compact('record'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.faqs.add');
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
            'question' => 'required',
            'answer' => 'required',
            'question_hindi' => 'required',
            'answer_hindi' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            
                $cateadd = FaqsSchema::insert([
                    'question' => $request->input('question'),
                    'answer' => $request->input('answer'),
                    'question_hindi' => $request->input('question_hindi'),
                    'answer_hindi' => $request->input('answer_hindi'),
                    'status' => 'Yes',
                    'created_at' => date('Y-m-d H:i:s') 
                ]);
                Toastr::success('Faqs successfully added!','Success');
                return redirect()->to('admin/faqs');
            
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
        $data = FaqsSchema::find($id);
        return view('admin.faqs.add',compact('data'));
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
            'question' => 'required',
            'answer' => 'required',
            'question_hindi' => 'required',
            'answer_hindi' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $catupdate = FaqsSchema::where('id',$id)->update([
                'question' => $request->input('question'),
                'answer' => $request->input('answer'),
                'question_hindi' => $request->input('question_hindi'),
                'answer_hindi' => $request->input('answer_hindi'),
                'updated_at' => date('Y-m-d H:i:s') 
            ]);
            Toastr::success('Faqs successfully updated!','Success');
            return redirect()->to('admin/faqs');
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
        FaqsSchema::where('id', $id)->delete();
        return response()->json(['status'=>true]);
    }
}
