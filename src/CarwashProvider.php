<?php namespace Carwash;

use Illuminate\Support\ServiceProvider;

class CarwashProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('carwash.php')
        ]);
    }

    public function register()
    {
        
    }
}
