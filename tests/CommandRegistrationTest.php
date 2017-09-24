<?php namespace Carwash;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class CommandRegistrationTest extends TestCase
{
    use DatabaseTransactions;
    const CARWASH_COMMANDS = [
        'carwash:scrub'
    ];

    public function testThatAllComandsGetRegisteredWithTheApplication()
    {
        $registeredCommands = collect(\Artisan::all())->keys();

        collect(static::CARWASH_COMMANDS)->each(function ($carwashCommand) use ($registeredCommands) {
            $this->assertTrue($registeredCommands->contains($carwashCommand));
        });
    }
}
