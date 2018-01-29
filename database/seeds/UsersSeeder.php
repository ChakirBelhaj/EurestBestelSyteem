<?php


use Phinx\Seed\AbstractSeed;

class UsersSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $roles = $this->table('users');

        $roles->insert([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'johndoe@example.com',
            'mobile' => '3939239200',
            'placeofresidence' => 'Doeville',
            'zipcode' => '1111JD',
            'streetname' => 'Superstreet',
            'housenumber' => 9,
            'password' => password_hash('password', PASSWORD_BCRYPT),
            'role_id' => 1

        ])
            ->save();
    }
}
