<?php namespace Carwash;

use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TestCase extends Orchestra
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [CarwashProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);
    }

    protected function setUpDatabase($app)
    {
        $app->useDatabasePath(__DIR__);
        $this->artisan('migrate');
    }
}
