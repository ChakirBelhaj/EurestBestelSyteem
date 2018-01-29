<?php


use Phinx\Migration\AbstractMigration;

class CreateOrderItemsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('order_items');

        $table->addColumn('name', 'string');
        $table->addColumn('image', 'string');
        $table->addColumn('price', 'integer');

        $table->addColumn('item_id', 'integer');
        $table->addForeignKey('item_id', 'products', 'id');

        $table->addColumn('order_id', 'integer');
        $table->addForeignKey('order_id', 'orders', 'id');

        $table->create();
    }
}
