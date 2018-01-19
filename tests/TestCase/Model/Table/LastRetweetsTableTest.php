<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LastRetweetsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LastRetweetsTable Test Case
 */
class LastRetweetsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\LastRetweetsTable
     */
    public $LastRetweets;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.last_retweets',
        'app.tweets'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('LastRetweets') ? [] : ['className' => LastRetweetsTable::class];
        $this->LastRetweets = TableRegistry::get('LastRetweets', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->LastRetweets);

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
