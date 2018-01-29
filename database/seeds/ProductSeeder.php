<?php


use Phinx\Seed\AbstractSeed;

class ProductSeeder extends AbstractSeed
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
        $categories = $this->fetchAll('SELECT * FROM categories');

        $productsData = [
            'Broodjes' => [
                [
                    'name' => 'Broodje zalm',
                    'image' => '',
                    'price' => 250,
                    'description' => 'Lekker broodje zalm',
                    'sale_price' => 230,
                ],
                [
                    'name' => 'Broodje eiersalade',
                    'image' => '',
                    'price' => 250,
                    'description' => 'Lekker broodje eiersalade',
                    'sale_price' => null,
                ]
            ],
            'Dranken' => [
                [
                    'name' => 'Heineken pils',
                    'image' => '',
                    'price' => 280,
                    'description' => 'Halve liter bier',
                    'sale_price' => 220,
                ],
                [
                    'name' => 'Chocomel',
                    'image' => '',
                    'price' => 190,
                    'description' => 'Lekkere chocomel',
                    'sale_price' => null,
                ]
            ],
            'Pizza' => [
                [
                    'name' => 'Pizza salami',
                    'image' => '',
                    'price' => 450,
                    'description' => 'Verse pizza salami',
                    'sale_price' => 430,
                ],
                [
                    'name' => 'Pizza shoarma',
                    'image' => '',
                    'price' => 1200,
                    'description' => '',
                    'sale_price' => null,
                ],
            ],
            'Gerechten' => [
                [
                    'name' => 'Kapsalon',
                    'image' => '',
                    'price' => 450,
                    'description' => 'Lekkere kapsalon',
                    'sale_price' => null,
                ],
            ]
        ];

        $finalProductsData = [];
        foreach ($categories as $category) {
            $productData = $productsData[$category['name']];
            foreach ($productData as $product) {
                $product['category_id'] = $category['id'];
                $finalProductsData[] = $product;
            }
        }

        $products = $this->table('products');
        $products->insert($finalProductsData);
        $products->save();
    }
}
