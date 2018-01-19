<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CursorsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CursorsTable Test Case
 */
class CursorsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CursorsTable
     */
    public $Cursors;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cursors'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Cursors') ? [] : ['className' => CursorsTable::class];
        $this->Cursors = TableRegistry::get('Cursors', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Cursors);

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
