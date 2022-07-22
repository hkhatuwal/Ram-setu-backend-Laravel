<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\State;
use App\Country;
use App\City;
use Validator;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $record = City::leftjoin('countries', 'countries.id', 'cities.country_id')
                        ->leftjoin('states', 'states.id', 'cities.state_id')
                        ->select('cities.*','countries.country_name','states.state_name')
                        ->orderby('id','DESC')
                        ->get();
        return view('admin.city.view',compact('record'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $country = Country::pluck('country_name','id')->toArray();
        $state = array();
        return view('admin.city.add',compact('country','state'));
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
            'city_name' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $state = City::where('city_name',$request->input('city_name'))->count();
            if($state > 0){
                Toastr::error('City name already exist.','Warning');
                return redirect()->back()->withInput(); 
            }else{
                if($request->has('state_id')){
                    $state_id = $request->input('state_id');
                }else{
                    $state_id = null;
                }
                City::insert([
                    'country_id'=>$request->input('country_id'),
                    'state_id'=>$state_id,
                    'city_name'=>$request->input('city_name')
                ]);
                Toastr::success('Record successfully added.','Success');
                return redirect('admin/city');
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
        $data = City::find($id);
        $country = Country::pluck('country_name','id')->toArray();
        $state = State::where('country_id',$data->country_id)
                    ->pluck('state_name','id')
                    ->toArray();
        return view('admin.city.add',compact('data','country','state'));
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
            'city_name' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()->withInput()->withErrors($errors);
        }else{
            $exist = City::where('city_name',$request->input('city_name'))->where('id','<>', $id)->count();
            if($exist > 0){
                Toastr::error('City name already exist.','Warning');
                return redirect()->back()->withInput(); 
            }else{
                if($request->has('state_id')){
                    $state_id = $request->input('state_id');
                }else{
                    $state_id = null;
                }
                City::where('id',$id)->update([
                    'country_id'=>$request->input('country_id'),
                    'state_id'=>$state_id,
                    'city_name'=>$request->input('city_name')
                ]);
                Toastr::success('Record successfully added.','Success');
                return redirect('admin/city');
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
