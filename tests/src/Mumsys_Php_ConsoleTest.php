<?php declare(strict_types=1);

/**
 * Test class for php class.
 */
class Mumsys_Php_ConsoleTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Php_Console
     */
    private $_object;

    /**
     * @var string
     */
    private $_testsDir;

    /**
     * Test are made vor version: ...
     * @var string
     */
    private $_version;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '3.0.1';

        $this->_testsDir = MumsysTestHelper::getTestsBaseDir();
        $this->_object = new Mumsys_Php_Console();
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
     * @covers Mumsys_Php_Console::check_disk_free_space
     */
    public function testCheck_disk_free_space()
    {
        $logfile = $this->_testsDir . '/logs/' . __FUNCTION__ . '.log';
        $logOpts = array('logfile' => $logfile);
        $logger = new Mumsys_Logger_File( $logOpts );
        // for debugging
//        $logger = new Mumsys_Logger_Decorator_Messages(
//            $logger, array('msglogLevel' => 7, 'msgColors' => true)
//        );

        $cmdLine = 'df -a %1$s';
        if ( Mumsys_Php_Console::$os === 'WIN' ) {
            $cmdLine = 'c:/cygwin/bin/df.exe -a %1$s';
        }

        $dir = $this->_testsDir . '/tmp';

        // basic call
        $basicCall = $this->_object->check_disk_free_space(
            $dir, $secCmp = 2, $maxSize = 92, $logger, $cmdLine
        );

        // check cache return inside secCmp=60sec.
        $chkCache = $this->_object->check_disk_free_space(
            $dir, $secCmp = 60, $maxSize = 92, $logger, $cmdLine
        );

        //disk space overflow in cache if disk usage < 1%
        $overflow = $this->_object->check_disk_free_space(
            $dir, $secCmp = 1, $maxSize = 1, $logger, $cmdLine
        );

        // diskOverflowFirstRun
        $tmp = $this->_object->check_disk_free_space(
            $path = '/var', $secCmp = 60, $maxSize = 2, $logger, $cmdLine
        );

        // wrong path
        $tmp = $this->_object->check_disk_free_space(
            $path = '/123', $secCmp = 60, $maxSize = 2, $logger, $cmdLine
        );

        // error accessing a path
        $err = $this->_object->check_disk_free_space(
            $path = '/root', $secCmp = 60, $maxSize = 2, $logger, 'test %1$s'
        );

        @unlink( $logfile );

        $this->assertingFalse( $basicCall );
        $this->assertingFalse( $chkCache );
        $this->assertingTrue( $overflow );
        $this->assertingTrue( $err );
    }


    public function testVersion()
    {
        $this->assertingEquals( $this->_version, Mumsys_Php_Console::VERSION );
        $this->assertingEquals( '3.2.1', Mumsys_Php::VERSION );
    }

}
