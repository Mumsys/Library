<?php declare( strict_types=1 );

/**
 * Mumsys_ShellTools_DefaultTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright (c) 2023 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  ShellTools
 */


/**
 * Generated 2023-07-31 at 20:41:24.
 */
class Mumsys_ShellTools_DefaultTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_ShellTools_Default
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
     * @var Mumsys_Logger_Interface
     */
    private $_logger;

    /**
     * @var Mumsys_Config_Interface
     */
    private $_config;

    /**
     * @var array
     */
    private $_adapterList;

    /**
     * Version string of current _object
     * @var string
     */
    private $_version;

    /**
     * List of objects this _objects needs
     * @var array
     */
    private $_versions;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '1.0.0';
        $this->_versions = array(
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
            'Mumsys_Logger_File' => '3.0.4',
            'Mumsys_Config_Default' => '3.0.0',
        );

        $this->_testsDir = realpath( dirname( __FILE__ ) . '/../' );
        $this->_logfile = $this->_testsDir . '/tmp/' . basename( __FILE__ ) . '.log';

        $loggerOpts = array(
            'logfile' => $this->_logfile,
            'way' => 'w',
            'logLevel' => 7,
            'lineFormat' => '%5$s',
        );
        $this->_logger = new Mumsys_Logger_File( $loggerOpts );

        $this->_adapterList = array(
            new Mumsys_ShellTools_Adapter_Demo( $this->_logger ),
        );
        $this->_config = new Mumsys_Config_Default();

        $this->_object = new Mumsys_ShellTools_Default( $this->_adapterList, $this->_logger, $this->_config );
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        if ( file_exists( $this->_logfile ) ) {
            unlink( $this->_logfile );
        }
    }


    /**
     * @covers Mumsys_ShellTools_Default::__construct
     */
    public function test__construct()
    {
        $config = new Mumsys_Config_Default();
        $object = new Mumsys_ShellTools_Default( $this->_adapterList, $this->_logger, $config );
        $this->assertingInstanceOf( 'Mumsys_ShellTools_Default', $object );
    }


    /**
     * @covers Mumsys_ShellTools_Default::validate
     * @covers Mumsys_ShellTools_Adapter_Demo::validate
     */
    public function testValidate()
    {
        $cliResults = array();
        $this->_object->validate( $cliResults ); // not of this part!
        $this->assertingTrue( true ); // 'runs until here'? Good!

        // 4CC
        // run this action. defaults will be used
        $cliResults['demoaction1'] = array();
        $this->_object->validate( $cliResults );
        // action with input's
        $cliResults['demoaction1']['input'] = 'defaultValue';
        $cliResults['demoaction1']['help'] = true;
        $this->_object->validate( $cliResults );

        $this->assertingTrue( true ); // 'runs until here'? Good!

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $this->expectingExceptionMessageRegex( '/Value "defaultValue" not given/' );
        $cliResults['demoaction1']['input'] = 'given';
        $this->_object->validate( $cliResults );
    }


    /**
     * @covers Mumsys_ShellTools_Default::execute
     * @covers Mumsys_ShellTools_Adapter_Demo::execute
     */
    public function testExecute()
    {
        // pre: call the action of the adapter
        $cliResults['demoaction1'] = array();
        $cliResults['demoaction1']['input'] = 'defaultValue';
        $this->_object->validate( $cliResults );

        // test:
        ob_start();
        $this->_object->execute( false );
        $output = ob_get_clean();
        $this->assertingEquals( 'no real execution: test -d /tmp', $output );

        $this->assertingTrue( true ); // 'runs until here'? Good!
    }


    /**
     * @covers Mumsys_ShellTools_Default::addAdapter
     */
    public function testAddAdapter()
    {
        $adapter = new Mumsys_ShellTools_Adapter_ExifFilename2Meta( $this->_logger );
        $this->_object->addAdapter( $adapter );
        $this->assertingTrue( true ); // runs until here? Good!
    }


    /**
     * 4CC - Adapter already set
     *
     * @covers Mumsys_ShellTools_Default::addAdapter
     */
    public function testAddAdapterExceptionA()
    {
        $this->expectingException( 'Mumsys_ShellTools_Exception' );
        $this->expectingExceptionMessageRegex( '/Adapter already set/' );

        $adapter = new Mumsys_ShellTools_Adapter_Demo( $this->_logger );
        $this->_object->addAdapter( $adapter );
    }


    /**
     * 4CC - Invalid adapter interface
     *
     * @covers Mumsys_ShellTools_Default::addAdapter
     */
    public function testAddAdapterTypeErrorException()
    {
        $this->expectingException( 'TypeError' );

        $adapter = new stdClass();
        $this->_object->addAdapter( $adapter );
    }

    /**
     * Version checks
     */
    public function testVersions()
    {
        $this->assertingEquals( $this->_version, $this->_object::VERSION );
        $this->checkVersionList( $this->_object->getVersions(), $this->_versions );
    }

}
