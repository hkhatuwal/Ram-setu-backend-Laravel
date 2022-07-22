<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Auth;
use DB;
use App\User;
use App\ProductMaster;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*',function($view) {
            if (Auth::user()) {
                $user = Auth::user();
                if($user->roles == 'admin'){
                    $setting = User::select(DB::raw('COUNT(CASE WHEN `roles` LIKE "%seller%" THEN 1 END) as seller'),DB::raw('COUNT(CASE WHEN `roles` LIKE "%buyer%" THEN 1 END) as buyer'))->first();

                    $product = ProductMaster::select(DB::raw('COUNT(CASE WHEN `status` LIKE "%pending%" THEN 1 END) as pending_product'),DB::raw('COUNT(CASE WHEN `deal_status` LIKE "%close%" THEN 1 END) as product_history'),DB::raw('COUNT(CASE WHEN `status` LIKE "%approved%" THEN 1 END) as approved_product'))
                    ->first();
                    
                    $setting->pending = $product->pending_product;
                    $setting->history = $product->product_history;
                    $setting->approved = $product->approved_product;
                    
                    $view->with('setting', $setting);
                }
            }
        });
    }
}
