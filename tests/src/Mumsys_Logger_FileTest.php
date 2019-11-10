<?php

/**
 * Test class for Mumsys_Logger_File
 */
class Mumsys_Logger_FileTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Logger_File
     */
    protected $_object;

    /**
     * @var string
     */
    protected $_testsDir;

    /**
     * Version string.
     * @var string
     */
    protected $_version;

    /**
     * @var array
     */
    protected $_versions;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '3.0.4';
        $this->_versions = array(
            'Mumsys_Logger_File' => '3.0.4',
            'Mumsys_Logger_Abstract' => '3.3.1',
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
        );

        $this->_testsDir = realpath( dirname( __FILE__ ) . '/../' );

        $this->_logfile = $this->_testsDir . '/tmp/Mumsys_LoggerTest_defaultfile.test';
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
        $this->_object = null;
    }


    /**
     * @covers Mumsys_Logger_File::__construct
     * @covers Mumsys_Logger_File::checkMaxFilesize
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

        $this->assertInstanceOf( 'Mumsys_Logger_Interface', $object );
    }


    /**
     * For 100% code coverage
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

        unset( $opts['username'], $_SERVER['REMOTE_USER'] );
        unset( $_SERVER['PHP_AUTH_USER'], $_SERVER['USER'], $_SERVER['LOGNAME'] );
        $object = new Mumsys_Logger_File( $opts );

        $_SERVER['LOGNAME'] = 'God';
        $object = new Mumsys_Logger_File( $opts );

        $this->assertInstanceOf( 'Mumsys_Logger_File', $object );
        $this->assertInstanceOf( 'Mumsys_Logger_Interface', $object );
    }


    public function testGetLogfile()
    {
        $this->assertEquals(
            $this->_opts['logfile'],
            $this->_object->getLogFile()
        );
    }


    public function testLevelNameGet()
    {
        $this->assertEquals( 'EMERG', $this->_object->getLevelName( 0 ) );
        $this->assertEquals( 'ALERT', $this->_object->getLevelName( 1 ) );
        $this->assertEquals( 'CRIT', $this->_object->getLevelName( 2 ) );
        $this->assertEquals( 'ERR', $this->_object->getLevelName( 3 ) );
        $this->assertEquals( 'WARN', $this->_object->getLevelName( 4 ) );
        $this->assertEquals( 'NOTICE', $this->_object->getLevelName( 5 ) );
        $this->assertEquals( 'INFO', $this->_object->getLevelName( 6 ) );
        $this->assertEquals( 'DEBUG', $this->_object->getLevelName( 7 ) );

        $this->assertEquals( 'unknown', $this->_object->getLevelName( 999 ) );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testEmerg()
    {
        $this->assertEquals(
            'log emergency', trim( $this->_object->emerg( 'log emergency' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testAlert()
    {
        $this->assertEquals(
            'log alert', trim( $this->_object->alert( 'log alert' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testCrit()
    {
        $this->assertEquals(
            'log critical', trim( $this->_object->crit( 'log critical' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testErr()
    {
        $this->assertEquals(
            'log error', trim( $this->_object->err( 'log error' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testWarn()
    {
        $this->assertEquals(
            'log warning', trim( $this->_object->warn( 'log warning' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testNotice()
    {
        $this->assertEquals(
            'log notice', trim( $this->_object->notice( 'log notice' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testInfo()
    {
        $this->assertEquals(
            'log info', trim( $this->_object->info( 'log info' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     */
    public function testDebug()
    {
        $this->assertEquals(
            'log debug', trim( $this->_object->debug( 'log debug' ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__call
     * @covers Mumsys_Logger_File::log
     */
    public function testLog()
    {
        $this->assertEquals(
            'long log', trim( $this->_object->log( 'long log', 7 ) )
        );

        $exp = '["long log"]';
        $this->assertEquals(
            $exp, trim( $this->_object->log( array('long log'), 7 ) )
        );
    }


    /**
     * @covers Mumsys_Logger_File::__construct
     * @covers Mumsys_Logger_File::log
     * @covers Mumsys_Logger_File::checkMaxFilesize
     */
    public function testLogException()
    {
        $opts = $this->_opts;

        $writer = new Mumsys_Logger_Writer_Mock();
        $object = new Mumsys_Logger_File( $opts, $writer );
        $writer->close();

        $this->expectException( 'Mumsys_Logger_Exception' );
        $this->expectExceptionMessage( 'File not open. Can not write to file' );

        $object->log( 'error', 3 );
    }


    /**
     * @covers Mumsys_Logger_File::checkMaxFilesize
     */
    public function testCheckMaxFilesize()
    {
        $this->_opts['maxfilesize'] = 10;

        $writer = new Mumsys_Logger_Writer_Mock();
        $object = new Mumsys_Logger_File( $this->_opts, $writer );

        $actual = $object->checkMaxFilesize();
        $this->assertEquals( 'Max filesize (10 Bytes) reached. Log purged now', $actual );
        $writer->close();
    }


    /**
     * Version checks
     */
    public function testVersions()
    {
        $this->assertEquals( $this->_version, Mumsys_Logger_File::VERSION );
        $this->_checkVersionList( $this->_object->getVersions(), $this->_versions );
    }

}
