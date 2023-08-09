<?php

/**
 * Mumsys_Logger_Default Test
 */
class Mumsys_Logger_DefaultTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Logger_Default
     */
    private $_object;
    /**
     *
     * @var Mumsys_File|Mumsys_Logger_Writer_Interface
     */
    private $_writer;

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
    private $_version;

    /**
     * @var array
     */
    private $_versions;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '3.0.0';
        $this->_versions = array(
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
            'Mumsys_Logger_Default' => $this->_version,
            'Mumsys_Logger_Abstract' => '3.3.1'
        );

        $this->_testsDir = realpath( dirname( __FILE__ ) . '/../' );

        $this->_logfile = $this->_testsDir . '/tmp/Mumsys_LoggerTest_defaultfile.test';
        $this->_opts = $opts = array(
            'logLevel' => 7,
            'lineFormat' => '%5$s',
        );
        $fopts = array(
            'file' => $this->_logfile,
            'way' => 'a',
        );
        $this->_writer = new Mumsys_File( $fopts );
        $this->_object = new Mumsys_Logger_Default( $opts, $this->_writer );
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        unset( $this->_object, $this->_writer );
    }


    public function test__constructor1()
    {
        $_SERVER['PHP_AUTH_USER'] = 'flobee';
        $opts = $this->_opts;
        $opts['compress'] = 'gz';
        $opts['timeFormat'] = 'Y-m-d H:i:s';
        $opts['debug'] = true;
        $opts['verbose'] = false;
        $opts['lf'] = "\n";

        $object = new Mumsys_Logger_Default( $opts, $this->_writer );

        $this->assertingInstanceOf( 'Mumsys_Logger_File', $object );
        $this->assertingInstanceOf( 'Mumsys_Logger_Default', $object );
        $this->assertingInstanceOf( 'Mumsys_Logger_Interface', $object );
    }


    /**
     * For 100% code coverage
     */
    public function test__constructor2()
    {
        $opts = $this->_opts;
        $opts['username'] = 'flobee';
        unset( $opts['logfile'], $opts['way'] );

        $object = new Mumsys_Logger_Default( $opts, $this->_writer );

        unset( $opts['username'] );
        $_SERVER['REMOTE_USER'] = 'flobee';
        $object = new Mumsys_Logger_Default( $opts, $this->_writer );

        unset(
            $_SERVER['REMOTE_USER'], $_SERVER['PHP_AUTH_USER'],
            $_SERVER['USER'], $_SERVER['LOGNAME']
        );
        $object = new Mumsys_Logger_Default( $opts, $this->_writer );

        $_SERVER['LOGNAME'] = 'God';
        $object = new Mumsys_Logger_Default( $opts, $this->_writer );

        $this->assertingInstanceOf( 'Mumsys_Logger_File', $object );
        $this->assertingInstanceOf( 'Mumsys_Logger_Default', $object );
        $this->assertingInstanceOf( 'Mumsys_Logger_Interface', $object );
    }


    /**
     * Version checks
     */
    public function testVersions()
    {
        $this->assertingEquals( $this->_version, Mumsys_Logger_Default::VERSION );
        $this->checkVersionList(
            $this->_object->getVersions(),
            $this->_versions
        );
    }

}
