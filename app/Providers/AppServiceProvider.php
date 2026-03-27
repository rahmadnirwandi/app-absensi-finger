<?php

namespace App\Providers;

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
        // if((bool) env('HTTPS', true)) {
        //     $this->app['url']->forceScheme('https');
        // } else {
        //     $this->app['url']->forceScheme('http');
        // }

        // $this->app['request']->server->set('HTTPS', env('HTTPS'));

        $data=$this->app['request']->server;
        if($data->get('HTTP_X_FORWARDED_PROTO')=='https'){
          $this->app['url']->forceScheme('https');
          $this->app['request']->server->set('HTTPS', env('HTTPS'));
        }else{
          $this->app['url']->forceScheme('http');
          $this->app['request']->server->set('HTTP', env('HTTP'));
        }
    }
}
