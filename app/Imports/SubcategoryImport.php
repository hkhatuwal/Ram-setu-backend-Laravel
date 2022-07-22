<?php

namespace App\Imports;

use App\Subcategory;
use Maatwebsite\Excel\Concerns\ToModel;

class SubcategoryImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Subcategory([
            //
        ]);
    }
}
