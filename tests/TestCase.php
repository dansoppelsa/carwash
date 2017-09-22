<?php namespace Carwash;

use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Database\Capsule\Manager as Capsule;

class TestCase extends Orchestra
{
    protected $db;

    public function setUp()
    {
        parent::setUp();
        $this->setupConfig();
        $this->setupDatabaseConnection();
        $this->migrateDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [CarwashProvider::class];
    }

    protected function setupConfig()
    {
        $this->app['config']->set('carwash', [
            'users' => [
                'first_name' => 'firstName',
                'last_name' => 'lastName'
            ]
        ]);
    }

    protected function setupDatabaseConnection()
    {
        $this->db = new Capsule;
        $this->db->addConnection([
            'driver'    => 'mysql',
            'host'      => '192.168.10.10',
            'database'  => 'carwash',
            'username'  => 'homestead',
            'password'  => 'secret',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);
    }

    protected function migrateDatabase()
    {
        $this->db->schema()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
        });
    }
}
