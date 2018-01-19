<?php
use Migrations\AbstractMigration;

class CreateLastRetweets extends AbstractMigration
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
        $table = $this->table('last_retweets', [
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
        $table->addColumn('tweet_id', 'string', [
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
            ['official_account_key', 'campaign_key', 'tweet_id'],
            ['unique' => true, 'name' => 'last_retweets_unique1']
        );

        $table->create();
        $this->execute('ALTER TABLE last_retweets ROW_FORMAT = DYNAMIC;');
    }
}
