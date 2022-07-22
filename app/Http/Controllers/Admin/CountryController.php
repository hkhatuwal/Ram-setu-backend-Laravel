<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Country;
use Validator;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $record = Country::orderby('id','DESC')->get();
        return view('admin.country.view',compact('record'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.country.add');
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
            'country_name' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $cate = Country::where('country_name',$request->input('country_name'))->count();
            if($cate > 0){
                Toastr::error('Country name already exist!','Warning');
                return redirect()->back()->withInput(); 
            }else{                 
                Country::insert([
                    'country_name' => $request->input('country_name'),
                    'status' => 'Yes',
                    'created_at' => date('Y-m-d H:i:s') 
                ]);
                Toastr::success('New country successfully added!','Success');
                return $this->index();
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
        $data = Country::find($id);
        return view('admin.country.add',compact('data'));
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
            'country_name' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $getcat = Country::find($id);
            $count = Country::where('country_name',$request->input('country_name'))->where('id','<>', $id)->count();
            if($count > 0){
                Toastr::error('Country name already exist!','Warning');
                return redirect()->back()->withInput(); 
            }else{
                Country::where('id',$id)->update([
                    'country_name'=>$request->input('country_name'),
                    'updated_at' => date('Y-m-d H:i:s') 
                ]);
                Toastr::success('Country name successfully updated!','Success');
                return $this->index();
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
        //
    }
}
