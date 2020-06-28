<?php namespace Carwash;

use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ScrubTest extends TestCase
{
    use DatabaseTransactions;

    public function testThatDesiredUserDataGetsScrubbed()
    {
        $this->addConfig();
        $this->addUser([
            'id' => 1,
            'first_name' => 'George',
            'last_name' => 'Costanza',
            'email' => 'gcostanza@hotmail.com',
        ]);
        $this->addUser([
            'id' => 2,
            'first_name' => 'Cosmo',
            'last_name' => 'Kramer',
            'email' => 'cosmo@kramerica.com',
        ]);

        $this->artisan('carwash:scrub');

        $user1 = $this->findUser(1);
        $this->assertNotEquals('George', $user1->first_name);
        $this->assertNotEquals('Costanza', $user1->last_name);
        $this->assertNotEquals('gcostanza@hotmail.com', $user1->email);
        $user2 = $this->findUser(2);
        $this->assertNotEquals('Cosmo', $user2->first_name);
        $this->assertNotEquals('Kramer', $user2->last_name);
        $this->assertNotEquals('cosmo@kramerica.com', $user2->email);
    }

    public function testThatFormattersCanBeAnInvokableClass()
    {
        $formatter = new class ($this)
        {
            private $test;

            public function __construct(TestCase $test)
            {
                $this->test = $test;
            }

            public function __invoke($faker, $attribute)
            {
                $this->test->assertEquals('George', $attribute);

                return 'Foo';
            }
        };

        $this->app->config['carwash'] = [
            'users' => [
                'first_name' => $formatter,
            ],
        ];

        $this->addUser([
            'id' => 1,
            'first_name' => 'George',
            'last_name' => 'Costanza',
            'email' => 'gcostanza@hotmail.com',
        ]);

        $this->artisan('carwash:scrub');

        $user1 = $this->findUser(1);
        $this->assertEquals('Foo', $user1->first_name);
    }

    public function testThatArgumentsCanBePassedToFormatters()
    {
        $this->app->config['carwash'] = [
            'users' => [
                'first_name' => 'words:3,true',
            ],
        ];
        $this->addUser([
            'id' => 1,
            'first_name' => 'George',
            'last_name' => 'Costanza',
            'email' => 'gcostanza@hotmail.com',
        ]);

        $this->artisan('carwash:scrub');

        $user1 = $this->findUser(1);

        $this->assertEquals(3, str_word_count($user1->first_name));
    }

    public function testThatTheTableConfigurationCanBeAnInvokableClass()
    {
        $user = [
            'id' => 1,
            'first_name' => 'George',
            'last_name' => 'Costanza',
            'email' => 'gcostanza@hotmail.com',
        ];

        $this->app['config']['carwash'] = [
            'users' => new class ($this, $user)
            {
                private $test;
                private $user;

                public function __construct(TestCase $test, array $user)
                {
                    $this->test = $test;
                    $this->user = $user;
                }

                public function __invoke($faker, $record)
                {
                    $this->test->assertInstanceOf(Generator::class, $faker);
                    $this->test->assertEquals($this->user['id'], $record['id']);
                    $this->test->assertEquals($this->user['first_name'], $record['first_name']);
                    $this->test->assertEquals($this->user['last_name'], $record['last_name']);
                    $this->test->assertEquals($this->user['email'], $record['email']);

                    return [
                        'first_name' => 'Foo'
                    ];
                }
            }
        ];

        $this->addUser($user);

        $this->artisan('carwash:scrub');

        $user1 = $this->findUser(1);

        $this->assertEquals('Foo', $user1->first_name);
    }

    public function testThatTheTableConfigurationCanBeAnAnonymousFunction()
    {
        $user = [
            'id' => 1,
            'first_name' => 'George',
            'last_name' => 'Costanza',
            'email' => 'gcostanza@hotmail.com',
        ];

        $this->app['config']['carwash'] = [
            'users' => function ($faker, $record) use ($user) {
                $this->assertInstanceOf(Generator::class, $faker);
                $this->assertEquals($user['id'], $record['id']);
                $this->assertEquals($user['first_name'], $record['first_name']);
                $this->assertEquals($user['last_name'], $record['last_name']);
                $this->assertEquals($user['email'], $record['email']);

                return [
                    'first_name' => 'Foo',
                ];
            }
        ];

        $this->addUser($user);

        $this->artisan('carwash:scrub');

        $user1 = $this->findUser(1);

        $this->assertEquals('Foo', $user1->first_name);
    }

    private function addConfig()
    {
        $this->app->config['carwash'] = [
            'users' => [
                'first_name' => 'firstName',
                'last_name' => 'lastName',
                'email' => 'safeEmail',
                'password' => function ($faker) {
                    return $faker->password;
                },
            ],
        ];
    }

    private function addUser($user)
    {
        \DB::table('users')->insert($user);
    }

    private function findUser($id)
    {
        return \DB::table('users')->find($id);
    }

}
