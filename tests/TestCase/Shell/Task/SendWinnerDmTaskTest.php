<?php
namespace App\Test\TestCase\Shell\Task;

use App\Shell\Task\SendWinnerDmTask;
use Cake\TestSuite\TestCase;

/**
 * App\Shell\Task\SendWinnerDmTask Test Case
 */
class SendWinnerDmTaskTest extends TestCase
{

    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo|\PHPUnit_Framework_MockObject_MockObject
     */
    public $io;

    /**
     * Test subject
     *
     * @var \App\Shell\Task\SendWinnerDmTask
     */
    public $SendWinnerDm;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $this->SendWinnerDm = $this->getMockBuilder('App\Shell\Task\SendWinnerDmTask')
            ->setConstructorArgs([$this->io])
            ->getMock();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SendWinnerDm);

        parent::tearDown();
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
