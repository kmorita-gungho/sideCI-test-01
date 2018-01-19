<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * WinningNumbersFixture
 *
 */
class WinningNumbersFixture extends TestFixture
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
        'numbers' => ['type' => 'string', 'length' => 128, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'used_flag' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'winner_numbers_unique1' => ['type' => 'unique', 'columns' => ['official_account_key', 'campaign_key', 'numbers'], 'length' => []],
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
            'numbers' => 'Lorem ipsum dolor sit amet',
            'used_flag' => 1,
            'created' => '2018-01-14 16:52:06',
            'modified' => '2018-01-14 16:52:06'
        ],
    ];
}
