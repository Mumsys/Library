<?php declare( strict_types=1 );

/**
 * Mumsys_ShellTools_Adapter_ResizeImagesTest
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
 * Generated 2023-08-11 at 17:15:00.
 */
class Mumsys_ShellTools_Adapter_ResizeImagesTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_ShellTools_Adapter_ResizeImages
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
     * Important!
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        $testsDir = realpath( dirname( __FILE__ ) . '/../' );
        $logfile = $testsDir . '/tmp/' . basename( __FILE__ ) . '.log';

        // clean up prev. logs
        if ( file_exists( $logfile ) ) {
            unlink( $logfile );
        }
    }


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '2.0.0';
        $this->_versions = array(
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
            'Mumsys_Logger_File' => '3.0.4',
        );

        $this->_testsDir = realpath( dirname( __FILE__ ) . '/../' );
        $this->_logfile = $this->_testsDir . '/tmp/' . basename( __FILE__ ) . '.log';

        $loggerOpts = array(
            'logfile' => $this->_logfile,
            'way' => 'a',
            'logLevel' => 7,
            //'lineFormat' => '%5$s',
        );
        $this->_logger = new Mumsys_Logger_File( $loggerOpts );

        $this->_object = new Mumsys_ShellTools_Adapter_ResizeImages( $this->_logger );
    }


    /**
     * Last action of this class! static method!!!
     */
    public static function tearDownAfterClass(): void
    {
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::__construct
     */
    public function test__construct(): void
    {
        $object = new Mumsys_ShellTools_Adapter_ResizeImages( $this->_logger );
        $this->assertingInstanceOf( 'Mumsys_ShellTools_Adapter_ResizeImages', $object );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::getCliOptions
     */
    public function testGetCliOptions(): void
    {
        $actualA = $this->_object->getCliOptions();
        $expectedA = array(
            'Action "resizeimages"' => 'Resize images and keep ratio (dimensions) '
                . 'using imagemagick "convert" command.' . PHP_EOL
                . 'Suffix and size will be merged.' . PHP_EOL
                . 'Eg: suffix: _x, size: 1600 will create a file from source to '
                . '[filename]_x1600[.ext] => photo_x1600.jpg' . PHP_EOL
                . 'If (optional) --target (path!) given that path would be used to '
                . 'store resized images. Default: source path = target path.' . PHP_EOL
                . PHP_EOL
                . 'Currently limitations: Problems with network drives found '
                . 'combined with utf8 characters in the location string.'
                ,
            'resizeimages' => array(
                '--source:' => 'The directory or location to the file to use',
                '--size:' => 'Size in pixel. Default 1600',
                '--suffix:' => 'Default: "_x". Suffix for resized files. ',
                '--target:' => 'Optional; Target path to store resized images. By '
                    . 'default it would use the path from --source',
                '--help|-h' => 'Show the help for this action',
            ),
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::getCliOptionsDefaults
     */
    public function testGetCliOptionsDefaults(): void
    {
        $actualA = $this->_object->getCliOptionsDefaults();
        $expectedA = array(
            'resizeimages' => array(
                'size' => '1600',
                'suffix' => '_x',
            )
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::getRequirementConfig
     */
    public function testGetRequirementConfig(): void
    {
        $actualA = $this->_object->getRequirementConfig();
        $expectedA = array(
            // [PHP_SAPI][strtolower( PHP_OS_FAMILY )]
            'cli' => array(
                'linux' => array(
                    'imagemagick:convert' => array('convert' => '') // no global params
                ),
            ),
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::validate
     */
    public function testValidateNoInput(): void
    {
        $inputA = array();
        $actualA = $this->_object->validate( $inputA );

        // null = not relevant because of other or not relevant input
        $this->assertingNull( $actualA );
    }


    /**
     * 4CC + Abstract test.
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsWithDefaultsLocationExists
     */
    public function testValidateInputSourceValid(): void
    {
        $inputA = array(
            'resizeimages' => array(
                'source' => $this->_testsDir,
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );
    }


    /**
     * 4CC Abstract test.
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsWithDefaultsLocationExists
     */
    public function testValidateInputSourceInalid(): void
    {
        $inputA = array(
            'resizeimages' => array(
                'source' => '/tmp/should/not/exists/exception',
            )
        );
        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $regex = '/Error! Not found: resizeimages --source "(.*)exception"/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->validate( $inputA );
    }


    /**
     * 4CC Abstract test.
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsWithDefaultsLocationExists
     */
    public function testValidateInputTargetUseDefault(): void
    {
        $inputA = array(
            'resizeimages' => array(
                //'target' => '/tmp', // not given, use default 4CC
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );
    }


    /**
     * 4CC + Abstract test.
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsNoDefaultsNotRequired
     */
    public function testValidateInputTargetValid(): void
    {
        $inputA = array(
            'resizeimages' => array(
                'target' => '/tmp',
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );
    }


    /**
     * Just run the action.
     *
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::_prepareCommands
     */
    public function testExecuteWithDefaults(): void
    {
        $inputA = array(
            'resizeimages' => array(
                'source' => '/tmp',
            )
        );
        $this->_object->validate( $inputA );
        $actualA = $this->_object->execute( false );

        $this->assertingTrue( $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::_prepareCommands
     */
    public function testExecute_prepareCommandsSourceAsFile(): void
    {
        $inputA = array(
            'resizeimages' => array(
                'source' => $this->_testsDir
                    . '/testfiles/Domain/ShellTools/images/file.png',
            )
        );
        $this->_object->validate( $inputA );
        $actualA = $this->_object->execute( false );

        $this->assertingTrue( $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::_prepareCommands
     */
    public function testExecute_prepareExecutionFalse(): void
    {
        $inputA = array(
            'wrong action' => array()
        );
        $this->_object->validate( $inputA );
        $actualA = $this->_object->execute( false );

        $this->assertingFalse( $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::_prepareCommands
     */
    public function testExecute_prepareCommandsTargetNotExists(): void
    {
        $inputA = array(
            'resizeimages' => array(
                'source' => '/tmp',
                'target' => '/should/not/exists',
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $regex = '/Target dir not exists "\/should\/not\/exists"/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->execute( false );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::_prepareCommands
     */
    public function testExecute_prepareCommandsSizeInvalid(): void
    {
        $inputA = array(
            'resizeimages' => array(
                'source' => '/tmp',
                'size' => 'notANumber',
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $regex = '/Invalid value for --size "notANumber" given. Not a number \(0-9\)/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->execute( false );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::_prepareCommands
     */
    public function testExecute_prepareCommandsNoFilesFoundException(): void
    {
        $inputA = array(
            'resizeimages' => array(
                'source' => $this->_testsDir . '/testfiles',
                'size' => '1234',
                'target' => $this->_testsDir . '/tmp',
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $regex = '/No files found to handle in --source "(.+)"/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->execute( false );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ResizeImages::_prepareCommands
     */
    public function testExecuteRealExecution(): void
    {
        $inputA = array(
            'resizeimages' => array(
                'source' => $this->_testsDir . '/testfiles/Domain/ShellTools/images/file.png',
                'size' => '1234',
                'suffix' => '_resizeimages_x',
                'target' => $this->_testsDir . '/tmp',
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $actualB = $this->_object->execute( true );
        $this->assertingTrue( $actualB );

        // cleanup
        $testfile = $this->_testsDir . '/tmp/file_resizeimages_x1234.png';
        if ( file_exists( $testfile ) ) {
            unlink( $testfile );
        }
    }


    /**
     * Version checks
     */
    public function testVersions(): void
    {
        $this->assertingEquals( $this->_version, $this->_object::VERSION );
        $this->checkVersionList( $this->_object->getVersions(), $this->_versions );
    }

}
