<?php


use Phinx\Migration\AbstractMigration;

class CreateGroupTable extends AbstractMigration
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
        $group_table = $this->table('groups');
        $group_table->addColumn('name', 'string')
            ->addColumn('created', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();

        $group_memeber_table = $this->table("group_user");
        $group_memeber_table->addColumn("user_id", 'integer')
            ->addColumn("group_id", 'integer')
            ->addForeignKey('user_id', 'users', 'id', [
                'delete'=> 'CASCADE'
            ])
            ->addForeignKey('group_id', 'groups', 'id', [
                'delete'=> 'CASCADE'
            ])->create();

    }
}
