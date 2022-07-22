<?php

namespace App\Exports;

use App\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoryExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Category::leftjoin('super_categories', 'super_categories.id', 'categories.super_cat_id')
            ->select('categories.id','categories.category_name','super_categories.super_cat_name','categories.description','categories.image')
            ->get();
    }
    public function headings() :array
    {
        return ["Unique Id", "Category name","Super category name", "Description", "Image Name"];
    }
}
