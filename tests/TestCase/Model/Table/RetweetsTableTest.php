<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RetweetsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RetweetsTable Test Case
 */
class RetweetsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\RetweetsTable
     */
    public $Retweets;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.retweets',
        'app.tweets',
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
        $config = TableRegistry::exists('Retweets') ? [] : ['className' => RetweetsTable::class];
        $this->Retweets = TableRegistry::get('Retweets', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Retweets);

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
