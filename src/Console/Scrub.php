<?php namespace Carwash\Console;

use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

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
        $this->info("Entering Carwash...");
        $this->line("");

        collect(config('carwash'))->each(function ($fields, $table) {
            $this->info("Scrubbing table <error>{$table}</error>...");

            $records = $this->getRecordsFromTable($table);
            $this->info("Found {$records->count()} records...");
            $records->each(function ($record) use ($fields, $table) {
                $this->scrubRecord($record, $table, $fields);
            });

            $this->info("<error>{$table}</error> table scrubbed.");
            $this->line("");
        });

        $this->info("...Exiting Carwash");
    }

    private function scrubRecord($record, $table, $fields)
    {
        $this->makeModel($table, (array)$record)->update($this->getUpdateData($fields));
    }

    private function getUpdateData($fields)
    {
        return collect($fields)->mapWithKeys(function ($fakerKey, $field) {
            if (is_callable($fakerKey)) {
                return [$field => $fakerKey($this->faker)];
            }

            return [$field => $this->faker->{$fakerKey}];
        })->toArray();
    }

    private function getRecordsFromTable($table)
    {
        return \DB::table($table)->get();
    }

    private function makeModel($table, $attributes)
    {
        return tap(new class extends Model {
            public $exists = true;
            protected $guarded = [];
        }, function ($model) use ($table, $attributes) {
            $model->setTable($table);
            $model->fill($attributes);
        });
    }

}
