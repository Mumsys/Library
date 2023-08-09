<?php

/**
 * Mumsys_Logger_None Test
 */
class Mumsys_Logger_NoneTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Logger_None
     */
    private $_object;

    /**
     * Version string.
     * @var string
     */
    private $_version;

    /**
     * Current running user
     * @var string
     */
    private $_username;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_username = Mumsys_Php_Globals::getRemoteUser();
        $this->_version = '3.0.1';
        $this->_object = new Mumsys_Logger_None();
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
     * @covers Mumsys_Logger_None::log
     */
    public function testLog()
    {
        $actual1 = $this->_object->log( 'test', 3 );
        $expected1 = date( 'Y-m-d H:i:s', time() ) . ' [' . $this->_username . '] [ERR](3) test' . "\n";

        $actual2 = $this->_object->log( array('test1', 'test2'), 3 );
        $expected2 = date( 'Y-m-d H:i:s', time() ) . ' [' . $this->_username . '] [ERR](3) ["test1","test2"]' . "\n";

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
    }


    /**
     * VERSION check
     */
    public function testVersion()
    {
        $this->assertingEquals( $this->_version, Mumsys_Logger_None::VERSION );
    }

}
