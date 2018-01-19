<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\WinnersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\WinnersTable Test Case
 */
class WinnersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\WinnersTable
     */
    public $Winners;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.winners',
        'app.twitter_users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Winners') ? [] : ['className' => WinnersTable::class];
        $this->Winners = TableRegistry::get('Winners', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Winners);

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

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
