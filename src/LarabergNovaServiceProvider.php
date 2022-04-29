<?php

namespace VanOns\LarabergNova;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class LarabergNovaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/laraberg-nova.php' => config_path('laraberg-nova.php')], 'config');

        $this->routes();

        Nova::serving(function (ServingNova $event) {
            Nova::script('laraberg-nova', __DIR__.'/../dist/js/field.js');
            Nova::script('laraberg', public_path('/vendor/laraberg/js/laraberg.js'));
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
    }

    public function routes() {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/laraberg-nova')
            ->group(__DIR__.'/../routes/api.php');
    }
}
