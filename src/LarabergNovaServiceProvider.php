<?php

namespace VanOns\LarabergNova;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use VanOns\Laraberg\LarabergServiceProvider;

class LarabergNovaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->routes();

        Nova::serving(function (ServingNova $event) {
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
