<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::select('id','name','email','ref_code')->where('roles','guest')->get();
    }
    public function headings() :array
    {
        return ["Id", "Name", "Email", "Ref Code"];
    }
}
