<?php

namespace App\Exports;

use App\ProductMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $category;
    function __construct($category) {
            $this->category = $category;
    }
    public function collection()
    {
    	$que = ProductMaster::leftjoin('super_categories', 'super_categories.id', 'product_masters.super_cat_id')
                ->leftjoin('categories', 'categories.id', 'product_masters.category_id')
                ->leftjoin('subcategories', 'subcategories.id', 'product_masters.subcategory_id')
                ->select('product_masters.id','product_masters.product_name','super_categories.super_cat_name','categories.category_name','subcategories.subcat_name','product_masters.description','product_masters.image','product_masters.inr_mrp_price','product_masters.inr_sell_price','product_masters.doller_mrp_price','product_masters.doller_sell_price')
                ->orderby('product_masters.id','desc');
            if(!empty($this->category)){
                $que = $que->where('product_masters.super_cat_id',$this->category);
            }    
        $record = $que->get();
        return $record;
    }
    public function headings() :array
    {
        return ["Unique Id","Product Name","Super category name","Category name","Subcategory name","Description","Image Name","INR Mrp","INR Sell","Doller Mrp","Doller Sell"];
    }

}
