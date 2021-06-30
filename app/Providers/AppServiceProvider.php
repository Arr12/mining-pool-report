<?php

namespace App\Providers;

use App\Http\Controllers\SheetController;
use Illuminate\Support\Facades\Cache;
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
            $cached = Cache::get('data-worksheet', false);
            if(!$cached){
                $s = new SheetController;
                $df = $s->GetAll();
            }else{
                $df = $cached;
            }
            $x = $df->toArray();
            $counter = 0;
            $arr['menus'] = [];
            $arr['owner_name'] = [];
            foreach($x as $key => $data){
                array_push($arr['menus'], $key);
                array_push($arr['owner_name'], isset($x[$key][0][0][4]) ? $x[$key][0][0][4] : 0);
                $counter++;
            }
            $view->with('profile',$arr);
        });
    }
}
