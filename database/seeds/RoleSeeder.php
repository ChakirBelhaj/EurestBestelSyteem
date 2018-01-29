<?php


use Phinx\Seed\AbstractSeed;

class RoleSeeder extends AbstractSeed
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
        $data = [
            [
                'id'      => 1,
                'name'    => 'student',
            ],
            [
                'id'      => 2,
                'name'    => 'teacher',
            ],
            [
                'id'      => 3,
                'name'    => 'employee',
            ],
            [
                'id'      => 4,
                'name'    => 'admin',
            ],
        ];

        $roles = $this->table('roles');

        $roles->insert($data)
            ->save();
    }
}
