<?php declare(strict_types = 1);

/**
 * Mumsys_Multirename
 * for MUMSYS Library for Multi User Management System
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright (c) 2015 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Multirename
 */


/**
 * 4CC - Test class will be init by autoloader and the version will be fetched
 * by eg: getVersionLong(). To take a fallback version string
 */
class Mumsys_MultirenameTest_LimitedVersionString extends Mumsys_Multirename
{
    const VERSION = '9.8.7.6.5';
}


/**
 * Test class for Mumsys_Multirename.
 */
class Mumsys_MultirenameTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Multirename
     */
    private $_object;

    /**
     * @var Mumsys_Logger_Decorator_Interface
     */
    private $_logger;

    /**
     * Logfile location
     * @var string
     */
    private static $_logFile;

    /**
     * @var Mumsys_FileSystem
     */
    private $_oFiles;
    private $_version;
    private $_versions;
    private $_testFiles = array();

    /**
     * root path for tests
     * @var string
     */
    private $_testsDir;

    /**
     * list of tmp dir created by tests an to delete right after
     * @var array
     */
    private $_testDirs = array();

    /**
     * @var array<mixed,mixed>
     */
    private $_config;
    private $_oldHome;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_oldHome = $_SERVER['HOME'];
        $this->_version = '2.4.6';
        $this->_versions = array(
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
            'Mumsys_Multirename' => $this->_version,
        );

        $this->_testsDir = MumsysTestHelper::getTestsBaseDir();

        self::$_logFile = $this->_testsDir . '/tmp/test_' . basename( __FILE__ ) . '.log';
        $_SERVER['HOME'] = $this->_testsDir . '/tmp';

        for ( $i = 10; $i <= 19; $i++ ) {
            $file = $this->_testsDir . '/tmp/multirenametestfile_-_' . $i . '.txt';
            @touch( $file );
            $this->_testFiles[] = $file;
            $this->_testFiles[] = $this->_testsDir . '/tmp/unittest_testfile_-_' . $i . '.txt';
        }

        @touch( $this->_testsDir . '/tmp/unittest_testfile_-_10.txt' );
        @touch( $this->_testsDir . '/tmp/multirenametestfile' );
        @touch( $this->_testsDir . '/tmp/multirenametestfile_toHide' );
        $this->_testFiles[] = $this->_testsDir . '/tmp/multirenametestfile';
        $this->_testFiles[] = $this->_testsDir . '/tmp/unittest_testfile';
        $this->_testFiles[] = $this->_testsDir . '/tmp/multirenametestfile_toHide';
        $this->_testFiles[] = $this->_testsDir . '/tmp/unittest_testfile_toHide';

        $this->_config = array(
            'program',
            'path' => $this->_testsDir . '/tmp',
            'fileextensions' => '*',
            'substitutions' => 'doNotFind=doNotReplace;regex:/doNotFind/i',
            'loglevel' => 7,
            'history-size' => 3,
        );

        $opts = array('way' => 'a', 'logfile' => self::$_logFile, 'msglogLevel' => -1);

        $fileLogger = new Mumsys_Logger_File( $opts );
        $this->_logger = new Mumsys_Logger_Decorator_None( $fileLogger, $opts );
        $this->_oFiles = new Mumsys_FileSystem();

        $this->_object = new Mumsys_Multirename( $this->_config, $this->_oFiles, $this->_logger );
    }


    /**
     * Execute after all tests. Deletes the log file
     */
    public static function tearDownAfterClass(): void
    {
        if ( !headers_sent() ) {
            return;
        }

        if ( file_exists( self::$_logFile ) ) {
            unlink( self::$_logFile );
        }
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        @unlink( $this->_config['path'] . '/.multirename/config' );
        @unlink( $this->_config['path'] . '/.multirename/collection' );
        @unlink( $this->_config['path'] . '/.multirename/lastactions' );
        @unlink( $this->_config['path'] . '/multirenametestfile' );
        @rmdir( $this->_config['path'] . '/.multirename/' );

        foreach ( $this->_testFiles as $target ) {
            @unlink( $target );
        }
        foreach ( $this->_testDirs as $target ) {
            @rmdir( $target );
        }
        $_SERVER['HOME'] = $this->_oldHome;
    }


    /**
     * Test and also fill data for the code coverage.
     * @runInSeparateProcess
     */
    public function testConstructor()
    {
        $this->_config['undo'] = true;
        $this->_config['del-config'] = true;
        $this->_config['save-config'] = true;
        $this->_config['show-config'] = true;
        $this->_object = new Mumsys_Multirename( $this->_config, $this->_oFiles, $this->_logger );
        $this->assertingInstanceOf( 'Mumsys_Multirename', $this->_object );

        // for CC
        $tmp = $_SERVER['USER'];
        $_SERVER['USER'] = 'root';
        //$regex = '/(Something which belongs to "root" is forbidden. Sorry! Use a different user!)/' . PHP_EOL;
        new Mumsys_Multirename( $this->_config, $this->_oFiles, $this->_logger );
        $_SERVER['USER'] = $tmp;
    }


    /**
     * Test show version
     * @covers Mumsys_Multirename::run
     * @covers Mumsys_Multirename::showVersion
     *
     * Must be set to get only the classes for this. Otherwise all unittest
     * classes and implementation comes in
     * @runInSeparateProcess
     */
    public function testConstructorGetShowVersionA()
    {
        $this->_config['version'] = true;
        ob_start();
        new Mumsys_Multirename( $this->_config, $this->_oFiles, $this->_logger );
        $current = ob_get_clean();

        // this needs in the single test
        $expected = array(
            'multirename ' . Mumsys_Multirename::VERSION . ' by Florian Blasel' . PHP_EOL,
        );

        foreach ( $expected as $toCheck ) {
            $res = ( preg_match( '/' . $toCheck . '/im', $current ) ? true : false );
            $this->assertingTrue( $res, $toCheck . ' ' . $current );
        }
    }


    /**
     * Test show version
     * @covers Mumsys_Multirename::run
     * @covers Mumsys_Multirename::getVersionID
     * @covers Mumsys_Multirename::getVersion
     * @covers Mumsys_Multirename::getVersionLong
     * @covers Mumsys_Multirename::getVersionShort
     * @covers Mumsys_Multirename::showVersion
     *
     * Must be set to get only the classes for this. Otherwise all unittest
     * classes and implementation comes in
     * @runInSeparateProcess
     */
    public function testConstructorGetShowVersionLong()
    {
        ob_start();
        $this->_config['version-long'] = true;
        $object = new Mumsys_Multirename(
            $this->_config,
            $this->_oFiles,
            $this->_logger
        );
        $current = ob_get_clean();
        // this needs in the single test
        $expected = array(
            'multirename ' . Mumsys_Multirename::VERSION . ' by Florian Blasel' . PHP_EOL . PHP_EOL,
            'Mumsys_Abstract                     ' . Mumsys_Abstract::VERSION . PHP_EOL,
            'Mumsys_FileSystem_Common_Abstract   ' . Mumsys_FileSystem_Common_Abstract::VERSION . PHP_EOL,
            'Mumsys_FileSystem                   ' . Mumsys_FileSystem::VERSION . PHP_EOL,
            'Mumsys_Logger_File                  ' . Mumsys_Logger_File::VERSION . PHP_EOL,
            'Mumsys_File                         ' . Mumsys_File::VERSION . PHP_EOL,
            'Mumsys_Multirename                  ' . Mumsys_Multirename::VERSION . PHP_EOL,
        );

        $current2 = $object->getVersionID();
        $expected2 = Mumsys_Multirename::VERSION;

        $current3 = $object->getVersion();
        $expected3 = 'Mumsys_Multirename ' . Mumsys_Multirename::VERSION;

        foreach ( $expected as $key => $toCheck ) {
            $res = ( preg_match( '/' . $toCheck . '/im', $current ) ? true : false );
            $this->assertingTrue( $res, "$key: $toCheck , out: $current" );
        }
        $this->assertingEquals( $expected2, $current2 );
        $this->assertingEquals( $expected3, $current3 );
        $this->assertingInstanceOf( 'Mumsys_Multirename', $object );
    }


    /**
     * Tests run and for max. code coverage.
     */
    public function testExecute()
    {
        /* TEST mode */
        $this->_logger->log( __METHOD__ . ' TEST MODE Test 1', 6 );
        // test mode, mostly run through everything with is possible for max. code coverage!
        $config = array(
            'test' => true,
            'fileextensions' => ';txt;*',
            'keepcopy' => false,
            'hidden' => true,
            'recursive' => true,
            'sub-paths' => true,
            //  Mumsys_Multirename::_substitutePaths for 100% code coverage
            'substitutions' => 'm=XX;XX=X%path1%X;regex:/%path1%/i=xTMPx;regex:/xTMPx/i=%path1%;%path1%=xTMPx',
            'find' => 'm;regex:/m/i',
            'exclude' => 'regex:/toHide/;Hide',
            'history' => true,
            'show-history' => true,
        );
        $config += $this->_config;
        $this->_object->run( $config );

        // code coverage with existing targets
        $this->_logger->log( __METHOD__ . ' TEST MODE Test 2', 6 );
        $config['substitutions'] = 'multirenametestfile_-_10=unittest_testfile_-_10';
        $config['find'] = false;
        $this->_object->run( $config );

        // code coverage with existing test targets with keepcopy
        $this->_logger->log( __METHOD__ . ' TEST MODE Test 3', 6 );
        $config['keepcopy'] = true;
        $this->_object->run( $config );

        // real rename tests with keepcopy
        $config['test'] = false;
        $this->_logger->log( __METHOD__ . ' RENAME MODE: rename 1', 6 );
        $config['substitutions'] = 'multirenametestfile_-_11=unittest_testfile_-_11';
        $this->_object->run( $config );
        $this->assertingTrue( file_exists( $this->_testsDir . '/tmp/unittest_testfile_-_11.txt' ) );
        $this->assertingFalse( file_exists( $this->_testsDir . '/tmp/unittest_testfile_-_11.txt.1' ) );

        // real rename tests with keepcopy again target exists
        $this->_logger->log( __METHOD__ . ' RENAME MODE: rename 2', 6 );
        $config['substitutions'] = 'multirenametestfile_-_12=unittest_testfile_-_11';
        $this->_object->run( $config );
        $this->assertingTrue( file_exists( $this->_testsDir . '/tmp/unittest_testfile_-_11.txt' ) );
        $this->assertingTrue( file_exists( $this->_testsDir . '/tmp/unittest_testfile_-_11.txt.1' ) );
        $this->_testFiles[] = $this->_testsDir . '/tmp/unittest_testfile_-_11.txt.1';

        // real symlink rename tests with keepcopy
        $this->_logger->log( __METHOD__ . ' RENAME MODE: symlink rename 1', 6 );
        $config['substitutions'] = 'multirenametestfile_-_13=unittest_testfile_-_13';
        $config['link'] = 'soft';
        $config['linkway'] = 'abs';
        $this->_object->run( $config );
        //$this->assertingTrue(file_exists($this->_testsDir . '/tmp/unittest_testfile_-_13.txt'), "file not found");
        $this->assertingTrue( is_link( $this->_testsDir . '/tmp/unittest_testfile_-_13.txt' ) );

        // test exception, just for code coverage
        $this->_logger->log( __METHOD__ . ' RENAME MODE: rename exception 1', 6 );
        $config['substitutions'] = 'multirenametestfile_-_14=/root/unittest_testfile_-_14';
        $this->_object->run( $config );

        $this->_logger->log( __METHOD__ . ' RENAME MODE: rename exception 1', 6 );
        $config['substitutions'] = 'multirenametestfile_-_14=/root/unittest_testfile_-_14';
        $this->_object->run( $config );

        // test _getRelevantFiles: look for txt extension
        $this->_logger->log( __METHOD__ . ' Code Coverage MODE: chk _getRelevantFiles: txt extension', 6 );
        $config['fileextensions'] = 'txt';
        $config['find'] = 'doNotFind';
        $config['stats'] = true;
        $this->_object->run( $config );

        $this->_object->removeActionHistory( $config['path'] );
        $this->expectingException( 'Mumsys_Multirename_Exception' );
        $this->expectingExceptionMessageRegex( '/(Removing history failed)/' );
        $this->_object->removeActionHistory( $config['path'] );
    }


    /**
     * Execute and undo
     */
    public function testExecuteAndUndo()
    {

        $config = array(
            'test' => false,
            //'link' => 'soft:abs',
            'fileextensions' => 'txt',
            'keepcopy' => true,
            'recursive' => false,
            'substitutions' => 'multirenametestfile_-_15=unittest_testfile_-_15',
            'find' => 'multirenametestfile_-_15',
            'exclude' => 'regex:/toHide/;Hide',
            'history' => true,
            'path' => $this->_config['path'],
        );

        /*
         *  do rename now and undo then: rename mode
         */
        $this->_logger->log( __METHOD__ . ' RENAME and UNDO: rename mode check 1', 6 );
        $config['run'] = true;
        $this->_object->run( $config );
        $actual1 = file_exists( $this->_testsDir . '/tmp/unittest_testfile_-_15.txt' );
        $actual2 = file_exists( $this->_testsDir . '/tmp/multirenametestfile_-_15.txt' );

        $config['undo'] = true;
        $this->_object->run( $config );
        $actual3 = file_exists( $this->_testsDir . '/tmp/unittest_testfile_-_15.txt' );
        $actual4 = file_exists( $this->_testsDir . '/tmp/multirenametestfile_-_15.txt' );

        $this->assertingTrue( $actual1 );
        $this->assertingFalse( $actual2 );
        $this->assertingFalse( $actual3 );
        $this->assertingTrue( $actual4 );

        /*
         *  do rename now and undo in test mode
         */
        $this->_logger->log( __METHOD__ . ' RENAME and UNDO: rename mode check 2', 6 );
        $config['run'] = true;
        $config['undo'] = false;
        $this->_object->run( $config );
        $actual1 = file_exists( $this->_testsDir . '/tmp/unittest_testfile_-_15.txt' );
        $actual2 = file_exists( $this->_testsDir . '/tmp/multirenametestfile_-_15.txt' );

        $config['undo'] = true;
        $config['test'] = true;
        $this->_object->run( $config );
        $actual3 = file_exists( $this->_testsDir . '/tmp/unittest_testfile_-_15.txt' );
        $actual4 = file_exists( $this->_testsDir . '/tmp/multirenametestfile_-_15.txt' );
        // ... and revert for the next test
        $config['undo'] = true;
        $config['test'] = false;
        $this->_object->run( $config );

        /*
         * do rename now and undo then: symlink mode
         */
        $this->_logger->log( __METHOD__ . ' RENAME and UNDO: symlink mode check 3', 6 );
        $config['undo'] = false;
        $config['run'] = true;
        $config['link'] = 'soft:abs';
        $this->_object->run( $config );
        $actual1 = is_link( $this->_testsDir . '/tmp/unittest_testfile_-_15.txt' );
        $actual2 = file_exists( $this->_testsDir . '/tmp/multirenametestfile_-_15.txt' );
        // undo link test mode
        $config['undo'] = true;
        $config['test'] = true;
        $this->_object->run( $config );

        $config['undo'] = true;
        $config['test'] = false;
        $this->_object->run( $config );
        $actual3 = !file_exists( $this->_testsDir . '/tmp/unittest_testfile_-_15.txt' );
        $actual4 = file_exists( $this->_testsDir . '/tmp/multirenametestfile_-_15.txt' );

        $this->assertingTrue( $actual1 );
        $this->assertingTrue( $actual2 );
        $this->assertingTrue( $actual3 );
        $this->assertingTrue( $actual4 );

        /*
         * do rename now in invalid mode,
         */
        $this->_logger->log( __METHOD__ . ' RENAME and UNDO: invalid mode check 4', 6 );
        $config['run'] = true;
        $config['undo'] = false;
        $config['link'] = 'invalid:abs';
        $this->_object->run( $config );
        $actual1 = !file_exists( $this->_testsDir . '/tmp/unittest_testfile_-_15.txt' );
        $actual2 = file_exists( $this->_testsDir . '/tmp/multirenametestfile_-_15.txt' );

        $this->assertingTrue( $actual1 );
        $this->assertingTrue( $actual2 );

        /*
         * do rename now rename mode with keepcopy but exists, cover _undoRename
         */
        $this->_logger->log( __METHOD__ . ' RENAME and UNDO: rename mode with keepcopy check 5', 6 );
        $config['undo'] = false;
        $config['link'] = false;
        $config['keepcopy'] = true;
        @touch( $this->_testsDir . '/tmp/unittest_testfile_-_15.txt' );
        $this->_object->run( $config );

        $actual1 = file_exists( $this->_testsDir . '/tmp/unittest_testfile_-_15.txt' );
        $actual2 = !file_exists( $this->_testsDir . '/tmp/multirenametestfile_-_15.txt' );
        $actual3 = file_exists( $this->_testsDir . '/tmp/unittest_testfile_-_15.txt.1' );
        $this->_testFiles[] = $this->_testsDir . '/tmp/unittest_testfile_-_15.txt.1';

        $this->assertingTrue( $actual1 );
        $this->assertingTrue( $actual2 );
        $this->assertingTrue( $actual3 );
        // undo and target exists
        @touch( $this->_testsDir . '/tmp/multirenametestfile_-_15.txt' );
        $this->_testFiles[] = $this->_testsDir . '/tmp/multirenametestfile_-_15.txt.1';
        $config['undo'] = true;
        $config['keepcopy'] = true;
        $this->_object->run( $config );

        /*
         *  _undo exception
         */
        $this->_logger->log( __METHOD__ . ' RENAME and UNDO: _undo() exception/error check 6', 6 );
        $config['undo'] = true;
        $config['keepcopy'] = true;
        $config['substitutions'] = 'multirenametestfile_-_15=../home/unittest_testfile';
        $data = '[{"name":"history 2000-01-01","date":"2000-01-01 23:59:59","history":{'
            . '"invalidMode":{"multirenametestfile":"unittest_testfile"}}}]'
        ;
        $file = $this->_testsDir . '/tmp/.multirename/lastactions';
        file_put_contents( $file, $data );
        $this->_object->run( $config );

        /*
         *  _undoRename exception
         */
        $this->_logger->log( __METHOD__ . ' RENAME and UNDO: _undoRename() exception check 7', 6 );
        $config['undo'] = true;
        $config['keepcopy'] = true;
        $config['substitutions'] = 'multirenametestfile_-_15=../home/unittest_testfile';
        $data = '[{"name":"history 2000-01-01","date":"2000-01-01 23:59:59","history":{'
            . '"rename":{"invalidsource":"invalidtarget"}}}]'
        ;
        $file = $this->_testsDir . '/tmp/.multirename/lastactions';
        file_put_contents( $file, $data );
        $this->_object->run( $config );

        /*
         *  _undoLink exception
         */
        $this->_logger->log( __METHOD__ . ' RENAME and UNDO: _undoLink() error check 8', 6 );
        $config['undo'] = true;
        $config['keepcopy'] = true;
        $config['substitutions'] = 'multirenametestfile_-_15=../home/unittest_testfile';
        @touch( $this->_testsDir . '/tmp/invalidsource' );
        symlink( $this->_testsDir . '/tmp/invalidsource', $this->_testsDir . '/tmp/invalidtarget' );
        @chmod( $this->_testsDir . '/tmp/', 0500 );
        $this->_testFiles[] = $this->_testsDir . '/tmp/invalidsource';
        $this->_testFiles[] = $this->_testsDir . '/tmp/invalidtarget';
        $data = '[{"name":"history 2000-01-01","date":"2000-01-01 23:59:59","history":'
            . '{"symlink":{"' . $this->_testsDir . '/tmp/invalidsource":"'
            . $this->_testsDir . '/tmp/invalidtarget"}}}]';
        $file = $this->_testsDir . '/tmp/.multirename/lastactions';
        file_put_contents( $file, $data );
        $this->_object->run( $config );
        @chmod( $this->_testsDir . '/tmp/', 0755 );
    }


    /**
     * For code coverage in _addActionHistory()
     */
    public function testRun4history()
    {
        $this->_logger->log( __METHOD__ . ' _addActionHistory check 1', 6 );
        $config = $this->_config;
        $config['substitutions'] = 'multirenametestfile_-_16=multirenametestfile_-_17';
        $config['keepcopy'] = false;
        $config['test'] = false;
        $config['fileextensions'] = '*';
        $config['history'] = true;
        $config['history-size'] = 2;

        $this->_object->run( $config );

        $config['substitutions'] = 'multirenametestfile_-_17=multirenametestfile_-_16';
        $this->_object->run( $config );

        $config['substitutions'] = 'multirenametestfile_-_16=multirenametestfile_-_17';
        $this->_object->run( $config );

        $config['substitutions'] = 'multirenametestfile_-_17=multirenametestfile_-_16';
        $this->_object->run( $config );

        $this->assertingTrue( true );
    }

//    public function testRemoveHistory()
//    {
//
//    }


    /**
     * Test initSetup for max code coverage
     */
    public function testInitSetup()
    {
        $config = $this->_config;
        $config += array(
            'keepcopy' => true,
            'hidden' => false,
            'test' => false,
            'link' => 'soft:rel',
            'linkway' => 'rel',
            'recursive' => true,
            'sub-paths' => true,
            'find' => 'a;c;t',
            'exclude' => 'xxx;yyy',
            'history' => true,
            'history-size' => 2,
        );
        $actual1 = $this->_object->initSetup( $config );
        $expected1 = $config;
        $expected1['fileextensions'] = array('*');
        $expected1['link'] = 'soft';
        $expected1['linkway'] = 'rel';
        $expected1['find'] = array('a', 'c', 't');
        $expected1['exclude'] = array('xxx', 'yyy');

        // from config test + hidden=true
        //$this->_object->initSetup($this->_config['path']);

        $config['hidden'] = true;
        $actual2 = $this->_object->initSetup( $config );
        $expected2 = $expected1;
        $expected2['hidden'] = $config['hidden'];

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );

        // config dir error
        $regex = '/(Invalid --path <your value>)/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Multirename_Exception' );
        $config['path'] = $this->_testsDir . '/tmp/dirNotExists';
        $this->_object->initSetup( $config );
    }


    /**
     * Test initSetup for max code coverage
     */
    public function testInitSetupException2()
    {
        $regex = '/(Invalid --test value)/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Multirename_Exception' );
        $this->_config['test'] = 'wrongValue';
        $this->_object->initSetup( $this->_config );
    }


    /**
     * Test initSetup for max code coverage
     */
    public function testInitSetupException3()
    {
        $regex = '/(Missing --fileextensions "<your value\/s>")/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Multirename_Exception' );
        $this->_config['fileextensions'] = null;
        $this->_object->initSetup( $this->_config );
    }


    /**
     * Test initSetup for max code coverage
     */
    public function testInitSetupException4()
    {
        $regex = '/(Missing --substitutions "<your value\/s>")/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Multirename_Exception' );
        $this->_config['substitutions'] = null;
        $this->_object->initSetup( $this->_config );
    }

    /**
     * Walk through the code for code coverage
     */
//    public function testUndoHackInvalidMode()
//    {
//        // create config path
//        $this->_object->setConfig($this->_config['path']);
//
//        // no history
//        $this->_object->undo($this->_config['path']);
//
//        // invalid history
//        $file = $this->_config['path'] . '/.multirename/lastactions';
//        $history = array(
//            array(
//                'name' => 'history name',
//                'date' => date('Y-m-d H:i:s', time()),
//                'history' => array(
//                    'mode' => array(
//                        $this->_testsDir . '/tmp/multirenametestfile' => $this->_testsDir . '/tmp/unittest_testfile'
//                    )
//                ),
//            ),
//        );
//
//        $data = json_encode($history);
//        $result = file_put_contents($file, $data);
//        $this->_object->undo($this->_config['path']);
//    }


    /**
     * See at testInitSetup() for more tests.
     */
    public function testSaveGetConfig()
    {
        $actual = $this->_object->saveConfig( $this->_config['path'] );

        $this->assertingTrue( ( is_numeric( $actual ) && $actual > 0 ) );

        $this->assertingFalse( $this->_object->saveConfig( '/root/' ) );

        $actual = $this->_object->getConfig( $this->_config['path'] );
        $expected = array($this->_config);
        unset( $expected[0]['loglevel'] );
        $this->assertingEquals( $expected, $actual );

        // Version < 1.3.3
        $path = $this->_testsDir . '/testfiles/Domain/Multirename/version-lt-1.3.3/';
        $actual = $this->_object->getConfig( $path );

        $this->assertingTrue( is_array( $actual ) );

        // test _gethistory
//        $this->_testFiles[] = $this->_testsDir . '/tmp/tmp2/.multirename/config';
//        $this->_testDirs[] = $this->_testsDir . '/tmp/tmp2/.multirename/';
//        $actual = $this->_object->saveConfig($this->_testsDir . '/tmp/tmp2/');
//        $this->assertingTrue(($actual >= 1291), 'Error, current value: ' . $actual);
    }


    public function testGetConfigException()
    {
        $regex = '/(Could not read config in path: "' . str_replace( '/', '\/', $this->_config['path'] ) . '")/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Multirename_Exception' );
        $this->_object->getConfig( $this->_config['path'] );
    }


    public function testMergerConfig()
    {
        $actual = $this->_object->saveConfig( $this->_config['path'] );
        $this->assertingTrue( ( is_numeric( $actual ) && $actual > 0 ) );

        $config['from-config'] = $this->_config['path'];
        $this->_object->run( $config );

        // invalid path
        $regex = '/(Invalid --from-config <your value> parameter. Path not found)/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Multirename_Exception' );
        $config['from-config'] = '/hello/';
        $this->_object->run( $config );
    }


    public function testDeleteConfig()
    {
        $this->_object->saveConfig( $this->_config['path'] );

        @chmod( $this->_config['path'] . '/.multirename/', 0500 );
        $actual1 = $this->_object->deleteConfig( $this->_config['path'] );

        @chmod( $this->_config['path'] . '/.multirename/', 0700 );
        $actual2 = $this->_object->deleteConfig( $this->_config['path'] );
        //config not found
        $actual3 = $this->_object->deleteConfig( $this->_config['path'] );
        $this->assertingFalse( $actual1 );
        $this->assertingTrue( $actual2 );
        $this->assertingFalse( $actual3 );
    }


    /**
     * showConfigs
     */
    public function testShowConfig()
    {
        ob_start();

        $opts = array(
            'msgEcho' => true,
            'msgLineFormat' => '%5$s',
            'logfile' => $this->_testsDir . '/tmp/test_' . basename( __FILE__ ) . '.log'
        );
        $this->_logger = new Mumsys_Logger_Decorator_Messages( $this->_logger, $opts );
        $this->_object = new Mumsys_Multirename( $this->_config, $this->_oFiles, $this->_logger );

        $this->_object->showConfigs();
        $output = ob_get_clean();

        $results = explode( "\n", $output );

        $actual = $results[count( $results ) - 2];
        $expected = "cmd#> multirename --path '" . $this->_testsDir . "/tmp' --fileextensions '*' "
            . "--substitutions 'doNotFind=doNotReplace;regex:/doNotFind/i' "
            . "--loglevel '7' --history-size '3'";

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * Mumsys_Multirename::install
     *
     * @runInSeparateProcess
     */
    public function testInstall()
    {
        $this->_object = new Mumsys_Multirename( $this->_config, $this->_oFiles, $this->_logger );
        $this->_object->install();
        $this->_object->install(); // 4 CC

        $this->assertingTrue( file_exists( $this->_config['path'] ) );

        $_SERVER['HOME'] = '/root/';
        $errBak = error_reporting();
        error_reporting( 0 );
        $this->_object = new Mumsys_Multirename( $this->_config, $this->_oFiles, $this->_logger );
        $regex = '/(Can not create dir: "\/root\/.multirename" mode: "755". Message: mkdir\(\): Permission denied)/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->install();
        error_reporting( $errBak );
    }


    /**
     * @runInSeparateProcess
     */
    public function testUpgrade()
    {
        $_SERVER['HOME'] = $this->_oldHome;

        $this->_object = new Mumsys_Multirename( $this->_config, $this->_oFiles, $this->_logger );
        $actual = $this->_object->upgrade();

        $this->assertingTrue( $actual );
    }


    /**
     * Mumsys_Multirename::getSetup
     */
    public function testGetSetup()
    {
        $actual = $this->_object->getSetup( true );
        $expected = $this->_object->getSetup( false );

        $this->assertingEquals( count( $expected ), count( $actual ) );
    }


    public function testToJson()
    {
        $value = array(1, 2, 3);
        $expected = json_encode( $value, JSON_PRETTY_PRINT );
        $actual = $this->_object->toJson( $value, JSON_PRETTY_PRINT, null );
        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @ covers Mumsys_Multirename::getVersion
     * @ covers Mumsys_Multirename::getVersions
     * @ covers Mumsys_Multirename::getVersionID
     */
    public function testAbstractClass()
    {
        $this->assertingEquals( 'Mumsys_Multirename ' . $this->_version, $this->_object->getVersion() );
        $this->assertingEquals( $this->_version, $this->_object->getVersionID() );

        $possible = $this->_object->getVersions();

        foreach ( $this->_versions as $must => $value ) {
            $this->assertingTrue( isset( $possible[$must] ) );
            $this->assertingTrue(
                ( $possible[$must] == $value ),
                'Version mismatch:' . $possible[$must] . ' - ' . $value
            );
        }
    }

}
