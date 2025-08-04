<?php

namespace App\Providers;

use App\Models\Categorie;
use App\Models\Courier;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $restaurantId = Auth::user()->id;

                $couriers = Courier::where('status', 'active')
                    ->where('restaurant_id', $restaurantId)
                    ->get();

                $customers = Customer::where('status', 'active')
                    ->where('restaurant_id', $restaurantId)
                    ->get();

                $categories = Categorie::where('status', 'active')
                    ->where('restaurant_id', $restaurantId)
                    ->get();

                $view->with([
                    'couriers' => $couriers,
                    'customers' => $customers,
                    'categories' => $categories,
                ]);
            }
        });
    }
}
