<?php namespace Carwash;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class CommandRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
    }

    public function testThatScrubCommandGetsRegisteredWithTheApplication()
    {
        try {
            $this->artisan('carwash:scrub');
            $registered = true;
        } catch (CommandNotFoundException $e) {
            $registered = false;
        }

        $this->assertTrue($registered);
    }
}
