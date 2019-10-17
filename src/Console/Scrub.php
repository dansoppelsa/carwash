<?php namespace Carwash\Console;

use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Scrub extends Command
{
    protected $signature = 'carwash:scrub';
    protected $description = 'Scrub data in the database';
    protected $faker;

    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
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
                $this->scrubRecord((array)$record, $table, $fields);
            });

            $this->info("<error>{$table}</error> table scrubbed.");
            $this->line("");
        });

        $this->info("...Exiting Carwash");
    }

    private function scrubRecord($record, $table, $fields)
    {
        $this->makeModel($table, $record)->update($this->getUpdateData($fields, $record));
    }

    private function getUpdateData($fields, $record)
    {
        if (is_callable($fields)) {
            return $fields($this->faker, $record);
        }

        return collect($fields)->mapWithKeys(function ($fakerKey, $field) use ($record) {
            if (is_callable($fakerKey)) {
                return [$field => $fakerKey($this->faker, $record[$field])];
            }

            if (Str::contains($fakerKey, ':')) {
                $formatter = explode(":", $fakerKey)[0];
                $arguments = explode(",", explode(":", $fakerKey)[1]);

                return [$field => $this->faker->{$formatter}(...$arguments)];
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
        return tap(new class extends Model
        {
            public $exists = true;
            protected $guarded = [];
        }, function ($model) use ($table, $attributes) {
            $model->setTable($table);
            $model->fill($attributes);
        });
    }

}
