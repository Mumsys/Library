<?php

/**
 *  Test class for tests
 */
class Mumsys_Logger_Decorator_Abstract_TestClass
    extends Mumsys_Logger_Decorator_Abstract
    implements Mumsys_Logger_Decorator_Interface
{
    public function testGetObject()
    {
        return $this->_getObject();
    }

}


/**
 * Mumsys_Logger_Decorator_Abstract Test
 */
class Mumsys_Logger_Decorator_AbstractTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Logger_Decorator_Abstract_TestClass
     */
    private $_object;

    /**
     * @var Mumsys_Logger_File
     */
    private $_logger;

    /**
     * @var string
     */
    private $_testsDir;

    /**
     * @var string
     */
    private $_logfile;

    /**
     * Logger options
     * @var array
     */
    private $_opts;

    /**
     * @var string
     */
    private $_version;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '3.0.0';
        $this->_testsDir = MumsysTestHelper::getTestsBaseDir();
        $this->_logfile = $this->_testsDir . '/tmp/' . basename( __FILE__ ) . '.test';

        $this->_opts = $opts = array(
            'logfile' => $this->_logfile,
            'way' => 'a',
            'logLevel' => 7,
            'msglogLevel' => 999,
            'maxfilesize' => 1024 * 2,
            'msgLineFormat' => '%5$s',
        );
        $this->_logger = new Mumsys_Logger_File( $this->_opts );

        $this->_object = new Mumsys_Logger_Decorator_Abstract_TestClass( $this->_logger );
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
     * @covers Mumsys_Logger_Decorator_Abstract::__construct
     */
    public function test__construct()
    {
        $this->_object = new Mumsys_Logger_Decorator_Abstract_TestClass( $this->_logger );
        $this->assertingInstanceOf( 'Mumsys_Logger_Decorator_Interface', $this->_object );
    }


    /**
     * @covers Mumsys_Logger_Decorator_Abstract::__clone
     */
    public function test__clone()
    {
        $obj = clone $this->_object;
        $this->assertingInstanceOf( 'Mumsys_Logger_Decorator_Interface', $obj );
        $this->assertingInstanceOf( 'Mumsys_Logger_Decorator_Interface', $this->_object );
        $this->assertingNotSame( $obj, $this->_object );
    }


    /**
     * @covers Mumsys_Logger_Decorator_Abstract::getLevelName
     */
    public function testGetLevelName()
    {
        $obj = clone $this->_object;
        $actual = $this->_object->getLevelName( 3 );
        $expected = 'ERR';

        $this->assertingEquals( $actual, $expected );
    }


    /**
     * @covers Mumsys_Logger_Decorator_Abstract::log
     */
    public function testLog()
    {
        $obj = clone $this->_object;
        $actual = $obj->log( __METHOD__, 6 );

        $regex = '/(' . __METHOD__ . ')/i';
        $this->assertingTrue( ( preg_match( $regex, $actual ) === 1 ) );
    }


    /**
     * @covers Mumsys_Logger_Decorator_Abstract::checkLevel
     */
    public function testCheckLevel()
    {
        $obj = clone $this->_object;
        $actual1 = $obj->checkLevel( 3 );
        $actual2 = $obj->checkLevel( 99 );

        $this->assertingTrue( $actual1 );
        $this->assertingFalse( $actual2 );
    }


    /**
     * @covers Mumsys_Logger_Decorator_Abstract::_getObject
     */
    public function test_GetObject()
    {
        $obj = $this->_object->testGetObject();
        $this->assertingSame( $obj, $this->_logger );
    }


    /**
     * Version check
     */
    public function testVersion()
    {
        $this->assertingEquals(
            $this->_version, Mumsys_Logger_Decorator_Abstract::VERSION
        );
    }

}
