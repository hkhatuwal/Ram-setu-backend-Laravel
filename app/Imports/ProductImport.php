<?php

namespace App\Imports;

use App\ProductMaster;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\SuperCategory;
use App\Category;
use App\Subcategory;

class ProductImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    private $inserted = 0;
    private $duplicate = 0;
    private $inserted_array = [];
    private $duplicate_array = [];

    public function model(array $row)
    {
        $product_name = $row[0];
        $checkexist = ProductMaster::where('product_name',$product_name)->first();
        if(empty($checkexist))
        {
            ++$this->inserted;
            $this->inserted_array[] = $row;

            $existsuper = SuperCategory::where('super_cat_name',$row[1])->first();
            if(!empty($existsuper)){
                $super_cat_id =$existsuper->id;
            }else{
                $super_cat_id =SuperCategory::insertGetId([
                    'super_cat_name'=>$row[1],
                    'created_at'=>date('Y-m-d H:i:s')
                ]);
            } 
            $existcat = Category::where('category_name',$row[2])->where('super_cat_id',$super_cat_id)->first();
            if(!empty($existcat)){
                $category_id =$existcat->id;
            }else{
                $category_id = Category::insertGetId([
                    'super_cat_id'=>$super_cat_id,
                    'category_name'=>$row[2],
                    'created_at'=>date('Y-m-d H:i:s')
                ]);
            } 
            $existsubcat = Subcategory::where('subcat_name',$row[3])
                    ->where('super_cat_id',$super_cat_id)
                    ->where('category_id',$category_id)
                    ->first();
            if(!empty($existsubcat)){
                $subcategory_id =$existsubcat->id;
            }else{
                $subcategory_id = Subcategory::insertGetId([
                    'super_cat_id'=>$super_cat_id,
                    'category_id' => $category_id, 
                    'subcat_name'=>$row[3],
                    'created_at'=>date('Y-m-d H:i:s')
                ]);
            } 
            if (preg_match('/^[0-9]+(\\.[0-9]+)?$/', $row[6])){
                $inr_mrp_price = $row[6];
            } else {
                $inr_mrp_price = null;
            }
            if (preg_match('/^[0-9]+(\\.[0-9]+)?$/', $row[7])){
                $inr_sell_price = $row[7];
            } else {
                $inr_sell_price = null;
            }
            if (preg_match('/^[0-9]+(\\.[0-9]+)?$/', $row[8])){
                $doller_mrp_price = $row[8];
            } else {
                $doller_mrp_price = null;
            }
            if (preg_match('/^[0-9]+(\\.[0-9]+)?$/', $row[9])){
                $doller_sell_price = $row[9];
            } else {
                $doller_sell_price = null;
            }
            $data = [
                'product_name'=>$product_name,
                'super_cat_id'=>$super_cat_id,
                'category_id'=>$category_id,
                'subcategory_id'=>$subcategory_id,
                'description' => $row[4],
                'image' => $row[5],
                'inr_mrp_price' => $inr_mrp_price,
                'inr_sell_price' => $inr_sell_price,
                'doller_mrp_price' => $doller_mrp_price,
                'doller_sell_price' => $doller_sell_price,
                'created_at'=>date('Y-m-d H:i:s')
            ]; 
            return new ProductMaster($data); 
        }else{
            ++$this->duplicate;
            $this->duplicate_array[] = $row;
        }           
    }
    public function startRow(): int
    {
        return 2;
    }
    public function getRowCount(): array
    {
        return ['inserted'=>$this->inserted,'duplicate'=>$this->duplicate,'inserted_array'=>$this->inserted_array,'duplicate_array'=>$this->duplicate_array];
    }
}
