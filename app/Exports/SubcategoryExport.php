<?php

namespace App\Exports;

use App\Subcategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubcategoryExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Subcategory::leftjoin('categories', 'categories.id', 'subcategories.category_id')
                    ->leftjoin('super_categories', 'super_categories.id', 'subcategories.super_cat_id')
                    ->select('subcategories.id','categories.category_name','super_categories.super_cat_name','subcategories.description','subcategories.image')
                    ->get();
    }
    public function headings() :array
    {
        return ["Unique Id", "Subcategory name","Category name","Super category name", "Description", "Image Name"];
    }
}
