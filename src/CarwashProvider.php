<?php namespace Carwash;

use Carwash\Console\Scrub;
use Illuminate\Support\ServiceProvider;

class CarwashProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Scrub::class
            ]);
        }
    }

    public function register()
    {
        
    }
}
