<?php


use Phinx\Seed\AbstractSeed;

class CategorySeeder extends AbstractSeed
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
        $categories = $this->table('categories');

        $categories->insert([
            [
                'name' => 'Dranken'
            ],
            [
                'name' => 'Broodjes'
            ],
            [
                'name' => 'Pizza'
            ],
            [
                'name' => 'Gerechten'
            ]
        ]);

        $categories->save();
    }
}
