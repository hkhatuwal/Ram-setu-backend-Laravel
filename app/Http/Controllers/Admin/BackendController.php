<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Validator;
use DB;

class BackendController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function CheckStatus(Request $request,$table,$id){

        $table = "App\\".$table;
        $record = $table::find($id);
        if($record->status =='Yes')
        {   
          $update = $table::where('id',$id)
                  ->update([
                    'status' => 'No',
                    'updated_at' => date('Y-m-d H:i:s') 
                     ]);
            return "false";
        } else {
          $update = $table::where('id',$id)
                 ->update([
                  'status' => 'Yes',
                  'updated_at' => date('Y-m-d H:i:s') 
                   ]);
            return "true";
        }  
    }
   
}
