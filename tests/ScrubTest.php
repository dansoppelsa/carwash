<?php namespace Carwash;

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
            'email' => 'gcostanza@hotmail.com'
        ]);
        $this->addUser([
            'id' => 2,
            'first_name' => 'Cosmo',
            'last_name' => 'Kramer',
            'email' => 'cosmo@kramerica.com'
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

    private function addConfig()
    {
        $this->app->config['carwash'] = [
            'users' => [
                'first_name' => 'firstName',
                'last_name' => 'lastName',
                'email' => 'safeEmail'
            ]
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
