<?php

namespace App\Providers;

use App\Models\ActivityOrdersItems;
use App\Models\Wxuser;
use App\Observers\ActivityOrdersItemsObserver;
use App\Observers\WxuserObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);
        Wxuser::observe(WxuserObserver::class);
        ActivityOrdersItems::observe(ActivityOrdersItemsObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
