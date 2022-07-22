<?php

namespace App\Exports;

use App\SuperCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SuperCategoryExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return SuperCategory::select('id','super_cat_name','description','image')->get();
    }
    public function headings() :array
    {
        return ["Unique Id", "Super category name", "Description", "Image Name"];
    }
}
