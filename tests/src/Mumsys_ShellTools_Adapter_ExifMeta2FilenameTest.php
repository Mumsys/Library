<?php declare( strict_types=1 );

/**
 * Mumsys_ShellTools_Adapter_ExifMeta2FilenameTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright (c) 2023 by Florian Blasel
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  ShellTools
 */


/**
 * Generated 2023-08-01 at 17:15:00.
 */
class Mumsys_ShellTools_Adapter_ExifMeta2FilenameTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_ShellTools_Adapter_ExifMeta2Filename
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
        $this->_version = '1.0.0';
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
            'lineFormat' => '%5$s',
        );
        $this->_logger = new Mumsys_Logger_File( $loggerOpts );

        $this->_object = new Mumsys_ShellTools_Adapter_ExifMeta2Filename( $this->_logger );
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
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::__construct
     */
    public function test__construct()
    {
        $object = new Mumsys_ShellTools_Adapter_ExifMeta2Filename( $this->_logger );
        $this->assertingInstanceOf( 'Mumsys_ShellTools_Adapter_ExifMeta2Filename', $object );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::getCliOptions
     */
    public function testGetCliOptions()
    {
        $actualA = $this->_object->getCliOptions();
        $expectedA = array(
            'Action "exifmeta2filename"' => 'Takes one of the datetime tags in the exif metadata '
                . 'and renames the file to a fixed datetime string.' . PHP_EOL
                . 'The filename format is currently fixed to e.g: 20231231_235859.jpg ',
            'exifmeta2filename' => array(
                '--timestampFrom:' => 'Key of the timestamp from exif data. Default: DateTimeOriginal' . PHP_EOL
                    . 'Possible values: DateTimeOriginal, CreateDate or ModifyDate',
                '--location:' => 'The path or location to the file to correct your file/s',
                '--run-compare' => 'Compare the file/s and the found datetime value by hand',
                '--run-meta2filename' => 'Execute the given command. Rename the files! Make backups first!',
                '--help|-h' => 'Show the help for this action'
            ),
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::getCliOptionsDefaults
     */
    public function testGetCliOptionsDefaults()
    {
        $actualA = $this->_object->getCliOptionsDefaults();
        $expectedA = array(
            'exifmeta2filename' => array(
                'timestampFrom' => 'DateTimeOriginal',
                'location' => '/tmp/my/picture/s',
            )
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::getRequirementConfig
     */
    public function testGetRequirementConfig()
    {
        $actualA = $this->_object->getRequirementConfig();
        $expectedA = array(
            // [PHP_SAPI][strtolower( PHP_OS_FAMILY )]
            'cli' => array(
                'linux' => array(
                    'exiftool' => array('exiftool' => '') // no global params
                ),
            ),
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::validate
     */
    public function testValidateNoInput()
    {
        $inputA = array();
        $actualA = $this->_object->validate( $inputA );

        // null = not relevant because of other or not relevant input
        $this->assertingNull( $actualA );
    }


    /**
     * 4CC
     * Give all parameters (first case for all validation) to fill an be valid
     *
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::validate
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::_checkVarExistsWithDefaults
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::_checkVarExistsWithDefaultsCheckAllowListMustHave
     */
    public function testValidateWithInput()
    {
        $inputA = array(
            'exifmeta2filename' => array(
                'timestampFrom' => 'DateTimeOriginal',
                'location' => $this->_testsDir . '/testfiles/Domain/ShellTools/images',

            )
        );
        $actualA = $this->_object->validate( $inputA );

        $this->assertingTrue( $actualA ); // true = relevant
    }


    /**
     * 4CC
     * Give all parameters from defaults to fill
     *
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::validate
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::_checkVarExistsWithDefaults
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::_checkVarExistsWithDefaultsCheckAllowListMustHave
     */
    public function testValidateUseDefaultValues()
    {
        $inputA = array(
            'exifmeta2filename' => array()
        );
        $actualA = $this->_object->validate( $inputA );

        $this->assertingTrue( $actualA ); // true = relevant
    }


    /**
     * 4CC
     * Give all parameters to be invalid to fill and fail
     *
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsWithDefaultsCheckAllowListMustHave
     */
    public function testValidateCheckforErrorExceptionA()
    {
        $inputA = array(
            'exifmeta2filename' => array(
                // A
                'timestampFrom' => 'invalidValue',
                // B
                //'location' => '/should/not/exists',
            )
        );
        // logger will report all, last error in the exception
        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $this->expectingExceptionMessageRegex(
            '/Error with: exifmeta2filename --timestampFrom "invalidValue"/i'
        );
        $this->_object->validate( $inputA );
    }

    /**
     * 4CC
     * Give all parameters to be invalid to fill and fail
     *
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsWithDefaultsLocationExists
     */
    public function testValidateCheckforErrorExceptionB()
    {
        $inputB = array(
            'exifmeta2filename' => array(
                // A 'timestampFrom' => 'invalidValue',
                // B
                'location' => '/should/not/exists',
            )
        );
        // logger will report all, last error in the exception
        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $this->expectingExceptionMessageRegex(
            '/Error! Not found: exifmeta2filename --location "\/should\/not\/exists"/i'
        );
        $this->_object->validate( $inputB );
    }


    /**
     * Just run the action.
     *
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::_prepareCommand
     */
    public function testExecuteNoRealExec()
    {
        $inputA = array(
            'exifmeta2filename' => array()
        );
        $this->_object->validate( $inputA );
        $actualA = $this->_object->execute( false );

        $this->assertingTrue( $actualA );
    }


    /**
     * 4CC
     * Expect exception due to invalid defaults (location)
     *
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ExifMeta2Filename::_prepareCommand
     */
    public function testExecuteRealExecWithErrorException()
    {
        // A:
        // check _prepareExecution false first:
        $actualA = $this->_object->execute( false );
        $this->assertingFalse( $actualA );

        // B:
        $inputB = array(
            'exifmeta2filename' => array()
        );
        $this->_object->validate( $inputB );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $this->expectingExceptionMessageRegex( '/Execution error/' );
        $actualB = $this->_object->execute( true );

        $this->assertingTrue( $actualB );
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
