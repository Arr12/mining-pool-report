<?php

namespace App\Providers;

use App\Http\Controllers\SheetController;
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
        view()->composer('*', function ($view) {
            $ClassMenu = new SheetController;
            $menu = $ClassMenu->GetWorksheet();
            $view->with('menu',$menu);
        });
    }
}
