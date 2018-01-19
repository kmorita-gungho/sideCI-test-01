<?php
use Migrations\AbstractMigration;

class CreateCursors extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('cursors', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'charset' =>'utf8mb4',
            'collation' => 'utf8mb4_bin',
        ]);
        $table->addColumn('id', 'biginteger', [
            'autoIncrement' => true,
            'limit' => 20,
            'null' => false,
        ]);
        $table->addColumn('official_account_key', 'string', [
            'limit' => 128,
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('api_type', 'string', [
            'limit' => 128,
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('cursor', 'string', [
            'limit' => 128,
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => 'CURRENT_TIMESTAMP',
            'null' => true,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => 'CURRENT_TIMESTAMP',
            'null' => true,
        ]);
        
        $table->addIndex(
            ['official_account_key', 'api_type'],
            ['unique' => true, 'name' => 'cursors_unique1']
        );

        $table->create();
        $this->execute('ALTER TABLE cursors ROW_FORMAT = DYNAMIC;');
    }
}
