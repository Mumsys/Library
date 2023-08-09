<?php

/**
 * Test class for Mumsys_Logger_Abstract using Mumsys_Logger_File
 */
class Mumsys_Logger_AbstractTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Logger_File
     */
    private $_object;

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
     * Version string.
     * @var string
     */
    protected $_version;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '3.3.1';
        $this->_testsDir = realpath( dirname( __FILE__ ) . '/../' );

        $this->_logfile = $this->_testsDir
            . '/tmp/Mumsys_LoggerTest_defaultfile.test';

        $this->_opts = $opts = array(
            'logfile' => $this->_logfile,
            'way' => 'a',
            'logLevel' => 7,
            'maxfilesize' => 80,
            'lineFormat' => '%5$s',
        );
        $this->_object = new Mumsys_Logger_File( $opts );
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
     * @covers Mumsys_Logger_Abstract::__construct
     *
     * @runInSeparateProcess
     */
    public function test__constructor1()
    {
        $_SERVER['PHP_AUTH_USER'] = 'flobee';
        $opts = $this->_opts;
        $opts['compress'] = 'gz';
        $opts['timeFormat'] = 'Y-m-d H:i:s';
        $opts['debug'] = true;
        $opts['verbose'] = false;
        $opts['lf'] = "\n";

        $object = new Mumsys_Logger_File( $opts );

        $this->assertingInstanceOf( 'Mumsys_Logger_Interface', $object );
    }


    /**
     * For 100% code coverage.
     * @covers Mumsys_Logger_Abstract::__construct
     *
     * @runInSeparateProcess
     */
    public function test__constructor2()
    {
        $opts = $this->_opts;
        $opts['username'] = 'flobee';
        unset( $opts['logfile'], $opts['way'] );

        $object = new Mumsys_Logger_File( $opts );

        unset( $opts['username'] );
        $_SERVER['REMOTE_USER'] = 'flobee';
        $object = new Mumsys_Logger_File( $opts );

        unset(
            $_SERVER['REMOTE_USER'], $_SERVER['PHP_AUTH_USER'],
            $_SERVER['USER'], $_SERVER['LOGNAME']
        );
        $object = new Mumsys_Logger_File( $opts );

        $userBak = $_SERVER['LOGNAME'] ?? null;
        $_SERVER['LOGNAME'] = 'God';
        $object = new Mumsys_Logger_File( $opts );

        $this->assertingInstanceOf( 'Mumsys_Logger_File', $object );
        $this->assertingInstanceOf( 'Mumsys_Logger_Abstract', $object );
        $this->assertingInstanceOf( 'Mumsys_Logger_Interface', $object );
        $_SERVER['LOGNAME'] = $userBak;
    }


    /**
     * @covers Mumsys_Logger_Abstract::__construct
     */
    public function test__constructor3()
    {
        $opts = $this->_opts;
        $opts['lineFormat'] = '';
        $this->expectingException( 'Mumsys_Logger_Exception' );
        $this->expectingExceptionMessageRegex( '/(Log format empty)/i' );

        $object = new Mumsys_Logger_File( $opts );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testEmerg()
    {
        $this->assertingEquals(
            'log emergency', trim( $this->_object->emerg( 'log emergency' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testAlert()
    {
        $this->assertingEquals(
            'log alert', trim( $this->_object->alert( 'log alert' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testCrit()
    {
        $this->assertingEquals(
            'log critical', trim( $this->_object->crit( 'log critical' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testErr()
    {
        $this->assertingEquals(
            'log error', trim( $this->_object->err( 'log error' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testWarn()
    {
        $this->assertingEquals(
            'log warning', trim( $this->_object->warn( 'log warning' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testNotice()
    {
        $this->assertingEquals(
            'log notice', trim( $this->_object->notice( 'log notice' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testInfo()
    {
        $this->assertingEquals(
            'log info', trim( $this->_object->info( 'log info' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testDebug()
    {
        $this->assertingEquals(
            'log debug', trim( $this->_object->debug( 'log debug' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function test_callArray()
    {
        $actual = $this->_object->debug( 1, 2, 3, 4, 5 );
        $expected = '[1,2,3,4,5]' . "\n";

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testInvalidMethodeCall()
    {
        $this->expectingException( 'Mumsys_Logger_Exception' );
        $this->expectingExceptionMessage( 'Invalid method call: "invalid"' );
        $this->_object->invalid( 'log message' );
    }


    /**
     * @covers Mumsys_Logger_File::setLogFormat
     */
    public function testSetLogFormat()
    {
        $this->_object->setLogFormat( '%4$s %5$s' );
        $actual1 = $this->_object->log( 'test', 0 );
        $actual2 = $this->_object->log( 'test', 3 );

        $this->assertingEquals( '0 test' . "\n", $actual1 );
        $this->assertingEquals( '3 test' . "\n", $actual2 );
    }


    /**
     * @covers Mumsys_Logger_File::setLoglevel
     */
    public function testSetLogLevel()
    {
        $this->_object->setLoglevel( 1 );
        $actual1 = $this->_object->log( 'test', 0 );
        $actual2 = $this->_object->log( 'test', 1 );

        $this->assertingEquals( 'test' . "\n", $actual1 );
        $this->assertingEquals( 'test' . "\n", $actual2 );

        $this->expectingException( 'Mumsys_Logger_Exception' );
        $this->expectingExceptionMessage( 'Log level "99" unknown. Can not set' );
        $this->_object->setLoglevel( 99 );
    }


    /**
     * @covers Mumsys_Logger_File::checkLevel
     */
    public function testCheckLevel()
    {

        $actual1 = $this->_object->checkLevel( 3 );
        $actual2 = $this->_object->checkLevel( 99 );

        $this->assertingTrue( $actual1 );
        $this->assertingFalse( $actual2 );
    }


    /**
     * @covers Mumsys_Logger_File::getLevelName
     */
    public function testGetLevelName()
    {
        $this->assertingEquals( 'EMERG', $this->_object->getLevelName( 0 ) );
        $this->assertingEquals( 'ALERT', $this->_object->getLevelName( 1 ) );
        $this->assertingEquals( 'CRIT', $this->_object->getLevelName( 2 ) );
        $this->assertingEquals( 'ERR', $this->_object->getLevelName( 3 ) );
        $this->assertingEquals( 'WARN', $this->_object->getLevelName( 4 ) );
        $this->assertingEquals( 'NOTICE', $this->_object->getLevelName( 5 ) );
        $this->assertingEquals( 'INFO', $this->_object->getLevelName( 6 ) );
        $this->assertingEquals( 'DEBUG', $this->_object->getLevelName( 7 ) );

        $this->assertingEquals( 'unknown', $this->_object->getLevelName( 999 ) );
    }


    /**
     * Version check
     */
    public function testVersion()
    {
        $this->assertingEquals( $this->_version, Mumsys_Logger_Abstract::VERSION );
    }

}
