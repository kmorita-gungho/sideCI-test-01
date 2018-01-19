<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * WinnersFixture
 *
 */
class WinnersFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'official_account_key' => ['type' => 'string', 'length' => 128, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'campaign_key' => ['type' => 'string', 'length' => 128, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'twitter_user_id' => ['type' => 'string', 'length' => 128, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'winning_numbers' => ['type' => 'string', 'length' => 128, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'dm_send_flag' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'winners_unique1' => ['type' => 'unique', 'columns' => ['official_account_key', 'campaign_key', 'twitter_user_id'], 'length' => []],
            'winners_unique2' => ['type' => 'unique', 'columns' => ['official_account_key', 'campaign_key', 'winning_numbers'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_bin'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'official_account_key' => 'Lorem ipsum dolor sit amet',
            'campaign_key' => 'Lorem ipsum dolor sit amet',
            'twitter_user_id' => 'Lorem ipsum dolor sit amet',
            'winning_numbers' => 'Lorem ipsum dolor sit amet',
            'dm_send_flag' => 1,
            'created' => '2018-01-14 16:51:59',
            'modified' => '2018-01-14 16:51:59'
        ],
    ];
}
