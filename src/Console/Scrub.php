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
            $this->getRecordsFromTable($table)
                ->each(function ($record) use ($fields, $table) {
                    $this->scrubRecord($record, $table, $fields);
                });
        });
    }

    private function scrubRecord($table, $fields, $record)
    {
        $this->makeModel($table, (array)$record)->update($this->getUpdateData($fields));
    }

    private function getUpdateData($fields)
    {
        return collect($fields)->mapWithKeys(function ($fakerKey, $field) {
            return [$field => $this->faker->{$fakerKey}];
        })->toArray();
    }

    private function getRecordsFromTable($table)
    {
        return \DB::table($table)->get();
    }

    private function makeModel($table, $attributes)
    {
        $model = new class extends Model {
            protected $guarded = [];
        };
        $model->setTable($table);
        $model->fill($attributes);
        $model->exists = true;

        return $model;
    }

}
