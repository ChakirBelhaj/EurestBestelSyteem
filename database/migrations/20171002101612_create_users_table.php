<?php


use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
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
        $table = $this->table('users');

        $table->addColumn('firstname', 'string');
        $table->addColumn('middlename', 'string', ['null' => true]);
        $table->addColumn('lastname', 'string');
        $table->addColumn('email', 'string');
        $table->addColumn('mobile', 'string');
        $table->addColumn('placeofresidence', 'string');
        $table->addColumn('zipcode', 'string');
        $table->addColumn('streetname', 'string');
        $table->addColumn('housenumber', 'string');
        $table->addColumn('password', 'string');
        $table->addColumn('resettoken', 'string', ['null' => true]);

        $table->addColumn('role_id', 'integer');
        $table->addForeignKey('role_id', 'roles', 'id');

        $table->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP']);
        $table->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP']);

        $table->create();
    }
}
