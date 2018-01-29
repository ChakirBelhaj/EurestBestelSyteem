<?php


use Phinx\Seed\AbstractSeed;

class OrderSeeder extends AbstractSeed
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
        $products = $this->fetchAll('SELECT * FROM products');
        $user = $this->fetchAll('SELECT * FROM users LIMIT 1')[0];

        $orders = $this->table('orders');
        $orders->insert([
            'user_id' => $user['id']
        ]);
        $orders->save();

        $order = $this->fetchAll('SELECT * FROM orders LIMIT 1')[0];

        $orderItemsData = [];
        foreach ($products as $product) {
            $orderItemsData[] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'sale_price' => $product['sale_price'],
                'image' => $product['image'],
                'item_id' => $product['id'],
                'amount' => rand(1, 9),
                'order_id' => $order['id']
            ];
        }

        $orderItems = $this->table('order_items');
        $orderItems->insert($orderItemsData);
        $orderItems->save();
    }
}
