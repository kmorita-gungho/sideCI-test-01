<?php
use Migrations\AbstractMigration;

class CreateRetweets extends AbstractMigration
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
        $table = $this->table('retweets', [
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
        $table->addColumn('tweet_text', 'string', [
            'limit' => 512,
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('tweet_created_at', 'datetime', [
            'null' => false,
        ]);
        $table->addColumn('twitter_user_id', 'string', [
            'limit' => 128,
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('name', 'string', [
            'limit' => 128,
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('screen_name', 'string', [
            'limit' => 128,
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('description', 'string', [
            'limit' => 256,
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('protected', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('followers_count', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('friends_count', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('twitter_user_created_at', 'datetime', [
            'null' => false,
        ]);
        $table->addColumn('statuses_count', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('lang', 'string', [
            'limit' => 64,
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('winner_flag', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('mention_send_flag', 'integer', [
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
            ['unique' => true, 'name' => 'retweets_unique1']
        );

        $table->create();
        $this->execute('ALTER TABLE retweets ROW_FORMAT = DYNAMIC;');
    }
}
