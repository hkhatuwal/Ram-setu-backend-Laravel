<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\State;
use App\Country;
use Validator;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $record = State::leftjoin('countries', 'countries.id', 'states.country_id')
                         ->select('states.*','countries.country_name')
                         ->orderby('id','DESC')
                         ->get();
        return view('admin.state.view',compact('record'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $country = Country::pluck('country_name','id')->toArray();
        return view('admin.state.add',compact('country'));
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
            'country_id' => 'required',
            'state_name' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $state = state::where('state_name',$request->input('state_name'))->count();
            if($state > 0){
                Toastr::error('State name already exist.','Warning');
                return redirect()->back()->withInput(); 
            }else{
                $state = state::insert([
                    'country_id'=>$request->input('country_id'),
                    'state_name'=>$request->input('state_name')
                ]);
                Toastr::success('Record successfully added.','Success');
                return redirect()->action('Admin\StateController@index');
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
       $data = state::find($id);
       $country = Country::pluck('country_name','id')->toArray();
       return view('admin.state.add',compact('data','country'));
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
            'country_id' => 'required',
            'state_name' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $state = state::where('state_name',$request->input('state_name'))->where('id','<>', $id)->count();
            if($state > 0){
                Toastr::error('State name already exist.','Warning');
                return redirect()->back()->withInput(); 
            }else{
                $state = state::where('id',$id)->update([
                    'country_id'=>$request->input('country_id'),
                    'state_name'=>$request->input('state_name')
                ]);
                Toastr::success('Record successfully updated.','Success');
                return redirect()->action('Admin\StateController@index');
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
      $data = state::where('id', '=', $id)->first();
       $checkuserassign = \DB::table('cities')->where('state_id',$id)->count();
       if($checkuserassign > 0){
       return response()->json(['status'=>'No']); 
       }else{
       $delete = state::where('id', '=', $id)->delete();
       return response()->json(['status'=>'Yes','data'=>$id]);  
       }
    }
}
