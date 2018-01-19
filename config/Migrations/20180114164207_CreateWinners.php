<?php
use Migrations\AbstractMigration;

class CreateWinners extends AbstractMigration
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
        $table = $this->table('winners', [
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
        $table->addColumn('campaign_key', 'string', [
            'limit' => 128,
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('twitter_user_id', 'string', [
            'limit' => 128,
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('winning_numbers', 'string', [
            'limit' => 128,
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('dm_send_flag', 'integer', [
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
            ['official_account_key', 'campaign_key', 'twitter_user_id'],
            ['unique' => true, 'name' => 'winners_unique1']
        );
        $table->addIndex(
            ['official_account_key', 'campaign_key', 'winning_numbers'],
            ['unique' => true, 'name' => 'winners_unique2']
        );

        $table->create();
        $this->execute('ALTER TABLE winners ROW_FORMAT = DYNAMIC;');
    }
}
