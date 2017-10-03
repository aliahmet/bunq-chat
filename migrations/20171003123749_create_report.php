<?php


use Phinx\Migration\AbstractMigration;

class CreateReport extends AbstractMigration
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
        $report_table = $this->table('reports');
        $report_table
            ->addColumn('delivered_date', 'datetime', ['null' => true])
            ->addColumn('seen_date', 'datetime', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addColumn('message_id', 'integer')
            ->addForeignKey('message_id', 'messages', 'id', [
                'delete'=> 'CASCADE'
            ])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete'=> 'CASCADE'
            ])
            ->create();
    }
}
