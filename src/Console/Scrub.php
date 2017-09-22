<?php namespace Carwash\Console;

use Illuminate\Console\Command;

class Scrub extends Command
{
    protected $signature = 'carwash:scrub';
    protected $description = 'Scrub data in the database';

    public function handle()
    {
        // Do work here
    }
}
