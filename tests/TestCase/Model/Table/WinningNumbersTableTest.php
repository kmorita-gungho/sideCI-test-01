<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\WinningNumbersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\WinningNumbersTable Test Case
 */
class WinningNumbersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\WinningNumbersTable
     */
    public $WinningNumbers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.winning_numbers'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('WinningNumbers') ? [] : ['className' => WinningNumbersTable::class];
        $this->WinningNumbers = TableRegistry::get('WinningNumbers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->WinningNumbers);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
