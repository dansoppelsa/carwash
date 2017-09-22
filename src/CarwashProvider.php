<?php namespace Carwash;

use Carwash\Console\Wash;
use Illuminate\Support\ServiceProvider;

class CarwashProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('carwash.php')
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([Wash::class]);
        }
    }

    public function register()
    {
        
    }
}
