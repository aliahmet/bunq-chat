<?php


use Phinx\Migration\AbstractMigration;

class CreateChatTable extends AbstractMigration
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
        $message_table = $this->table('messages');
        $message_table
            ->addColumn('sender', 'integer')
            ->addColumn('receiver', 'integer', ["null"=>true])
            ->addColumn('group', 'integer', ["null"=>true])
            ->addColumn('sent_date', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('body', 'text', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('sender', 'users', 'id', [
                'delete'=> 'CASCADE'
            ])
            ->addForeignKey('receiver', 'users', 'id', [
                'delete'=> 'CASCADE'
            ])
            ->addForeignKey('group', 'groups', 'id', [
                'delete'=> 'CASCADE'
            ])
            ->create();

        $report_table = $this->table('reports');
        $report_table
            ->addColumn('delivered_date', 'datetime', ['null' => true])
            ->addColumn('seen_date', 'datetime', ['null' => true])
            ->addForeignKey('message', 'messages', 'id', [
                'delete'=> 'CASCADE'
            ])
            ->create();


    }

}
