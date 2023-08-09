<?php declare( strict_types=1 );

/**
 * Mumsys_TimerTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright (c) 2006 by Florian Blasel
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Timer
 */


/**
 * Test class for Mumsys_Timer.
 */
class Mumsys_TimerTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Timer
     */
    private $_object;

    /**
     * Version ID
     * @var string
     */
    private $_version;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '3.2.1';
        $this->_object = new Mumsys_Timer();
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        unset( $this->_object );
    }


    /**
     * @runInSeparateProcess
     */
    public function testConstructor()
    {
        $this->_object = new Mumsys_Timer();
        $this->assertingInstanceOf( 'Mumsys_Timer', $this->_object );
        $this->assertingEquals( 0, $this->_object->startTimeGet() );
        $this->assertingEquals( 0, $this->_object->stopTimeGet() );

        $this->_object = new Mumsys_Timer( true );
        $this->assertingInstanceOf( 'Mumsys_Timer', $this->_object );
        $this->assertingEquals( 0, $this->_object->stopTimeGet() );
        $this->assertingTrue( ( $this->_object->startTimeGet() > 0 ) );

        $this->_object = new Mumsys_Timer( $_SERVER['REQUEST_TIME_FLOAT'] );

        $this->assertingEquals(
            $_SERVER['REQUEST_TIME_FLOAT'], $this->_object->startTimeGet()
        );
    }


    public function testStart()
    {
        $this->_object->start();
        $expected = microtime( true );
        $actual = $this->_object->startTimeGet();
        $this->assertingTrue( ( (int) $expected == (int) $actual ) );
        //echo PHP_EOL.$expected.PHP_EOL.$actual. PHP_EOL;
        //echo round($expected, 3) . PHP_EOL . round($actual, 3) . PHP_EOL;
        $this->assertingEquals( round( $expected, 2 ), round( $actual, 2 ) );
    }


    public function testStop()
    {
        $this->_object->stop();
        $expected = microtime( true );
        $actual = $this->_object->stopTimeGet();

        $this->assertingEquals( round( $expected, 2 ), round( $actual, 2 ) );
    }


    public function testStartTimeGet()
    {
        $this->assertingEquals( 0, $this->_object->startTimeGet() );
    }


    public function testStopTimeGet()
    {
        $this->assertingEquals( 0, $this->_object->stopTimeGet() );
    }


    public function testElapsedTimeGet()
    {
        $this->assertingEquals( 0, $this->_object->elapsedTimeGet() );
    }


    public function test__ToString()
    {
        $this->_object->__toString();
        $expected = microtime( true );
        $actual = $this->_object->stopTimeGet();

        $this->assertingEquals( round( $expected, 1 ), round( $actual, 1 ) );
    }


    // Abstract class

    public function testGetVersion()
    {
        $this->assertingEquals( 'Mumsys_Timer ' . $this->_version, $this->_object->getVersion() );
        $this->assertingEquals( Mumsys_Timer::VERSION, $this->_object->getVersionID() );
        $this->assertingEquals( Mumsys_Timer::VERSION, $this->_version );
    }

}
