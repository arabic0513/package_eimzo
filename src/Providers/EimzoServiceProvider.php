<?php

namespace arabic0513\Eimzo\Providers;

use Illuminate\Support\ServiceProvider;

class EimzoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'eimzo');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
//        $this3->loadViewsFrom(__DIR__.'/views', 'todolist');
        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/arabic0513/eimzo'),
            __DIR__ . '/../resources/assets' => base_path('public/vendor/eimzo/assets'),
            __DIR__.'/../config/config.php' => config_path('eimzo.php'),
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
