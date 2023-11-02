<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    	Paginator::useBootstrap();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        Gate::define('Shukko', function ($user) {
            return $user->kengen11;
        });
        Gate::define('Master', function ($user) {
            return $user->kengen13;
        });
        Gate::define('TestMode', function ($user) {
            return config('app.name')!='Laravel_zaiko';
        });
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $user = Auth::user();
            foreach ($event->menu->menu as &$menu){
                if(array_key_exists('header', $menu)){
                    switch($menu['header']){
                    case '$事務所':
                        $menu['header'] = "事業所[{$user->bumon->jigyosyo->name}]";
                        break;
                    }
                }
                if(array_key_exists('text', $menu)){
                    switch($menu['text']){
                    case '本日出庫':
                        $menu['label'] = $user->bumon->jigyosyo->shukko_date1_cnt;
                        break;
                    case '出庫日経過':
                        $menu['label'] = $user->bumon->jigyosyo->shukko_date2_cnt;
                        break;
                    case '在庫割れ':
                        $menu['label'] = $user->bumon->jigyosyo->zaikoware_cnt;
                        break;
                    }
                }
            }
        });
    }
}
