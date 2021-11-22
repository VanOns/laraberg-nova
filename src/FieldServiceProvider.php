<?php

namespace Vanons\LarabergNova;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::remoteScript('https://unpkg.com/react@16.8.6/umd/react.production.min.js');
            Nova::remoteScript('https://unpkg.com/react-dom@16.8.6/umd/react-dom.production.min.js');

            // Nova::style('laraberg-nova-core', __DIR__.'/../../../../laraberg/public/css/laraberg.css');

            Nova::script('laraberg-nova', __DIR__.'/../dist/js/field.js');
            Nova::style('laraberg-nova', __DIR__.'/../dist/css/field.css');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\VanOns\Laraberg\LarabergServiceProvider::class);
    }
}
