<?php namespace Carwash\Console;

use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use \Schema;

class Scrub extends Command
{
    protected $signature = 'carwash:scrub';
    protected $description = 'Scrub data in the database';
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
        parent::__construct();
    }

    public function handle()
    {
        collect(config('carwash'))->each(function ($fields, $table) {
            \DB::table($table)->get()
                ->each(function ($record) use ($fields, $table) {
                    $record = $this->makeModel($table, (array)$record);

                    $update = collect($fields)->mapWithKeys(function ($fakerKey, $field) use ($record) {
                        return [$field => $this->faker->{$fakerKey}];
                    })->toArray();

                    $record->update($update);
                });
        });
    }

    private function makeModel($table, $attributes)
    {
        $model = new class extends Model {
            protected $guarded = [];
        };
        // TODO: Find a way to make this dynamic
        $model->setKeyName('id');
        $model->setKeyType('int');
        $model->setTable($table);
        $model->fill($attributes);
        $model->exists = true;

        return $model;
    }

}
