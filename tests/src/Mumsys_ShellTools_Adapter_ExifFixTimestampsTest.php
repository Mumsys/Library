<?php declare( strict_types=1 );

/**
 * Mumsys_ShellTools_Adapter_ExifFixTimestampsTest
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
 * Generated 2023-08-01 at 17:15:00.
 */
class Mumsys_ShellTools_Adapter_ExifFixTimestampsTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_ShellTools_Adapter_ExifFixTimestamps
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

        $this->_object = new Mumsys_ShellTools_Adapter_ExifFixTimestamps( $this->_logger );
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
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::__construct
     */
    public function test__construct()
    {
        $object = new Mumsys_ShellTools_Adapter_ExifFixTimestamps( $this->_logger );
        $this->assertingInstanceOf( 'Mumsys_ShellTools_Adapter_ExifFixTimestamps', $object );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::getCliOptions
     */
    public function testGetCliOptions()
    {
        $actualA = $this->_object->getCliOptions();
        $expectedA = array(
            'Action "exiffixtimestamps"' => 'Can fix the datetime value in the '
                . 'exif metadata if you have two '
                . 'values. The old date (inside a photo) and the datetime this '
                . 'photo was really made. Check the README for details.',
            'exiffixtimestamps' => array(
                '--location:' => 'The path or location to the file to correct your photo/s',

                '--datetimeValueOld:' =>
                    'A value of Y-m-d H:i:s (e.g: \'2009-01-31 00:42:54\') is reqired by default',

                '--datetimeValueNew:' =>
                    'A value of Y-m-d H:i:s (e.g: \'2023-07-17 22:56:08\') is reqired by default',

                '--fix:' => 'Default: AllDates; Possible values: AllDates, '
                    . 'DateTimeOriginal, CreateDate or ModifyDate',

                '--datetimeFormatOld:' =>
                    'Datetime format of the --datetimeValueOld to be used. Default: \'Y-m-d H:i:s\'',

                '--datetimeFormatNew:' =>
                    'Datetime format of the --datetimeValueNew to be used. Default: \'Y-m-d H:i:s\'',

                'Hint for \'DateTime\' formats' => 'See: https://www.php.net/manual/en/datetime.format.php',

                '--help|-h' => 'Show the help for this action'
            ),
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::getCliOptionsDefaults
     */
    public function testGetCliOptionsDefaults()
    {
        $actualA = $this->_object->getCliOptionsDefaults();
        $expectedA = array(
            'exiffixtimestamps' => array(
                'location' => '/tmp/my/picture/s',
                'datetimeValueOld' => '2009-01-31 00:42:54',
                'datetimeValueNew' => '2023-07-17 22:56:08',
                'fix' => 'AllDates',
                'datetimeFormatOld' => 'Y-m-d H:i:s',
                'datetimeFormatNew' => 'Y-m-d H:i:s',
            )
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::getRequirementConfig
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
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::validate
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
     * Give all parameters (first case for all validation) to fill
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::validate
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::_checkVarExistsWithDefaults
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::_checkVarExistsWithDefaultsLocationExists
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::_checkVarExistsWithDefaultsCheckAllowListMustHave
     */
    public function testValidateWithInput()
    {
        $inputA = array(
            'exiffixtimestamps' => array(
                'location' => $this->_testsDir . '/testfiles/Domain/ShellTools/images',
                'datetimeValueOld' => '2009-01-31 00:42:54',
                'datetimeValueNew' => '2023-07-17 22:56:08',
                'fix' => 'AllDates',
                'datetimeFormatOld' => 'Y-m-d H:i:s',
                'datetimeFormatNew' => 'Y-m-d H:i:s',
            )
        );
        $actualA = $this->_object->validate( $inputA );

        $this->assertingTrue( $actualA ); // true = relevant
    }


    /**
     * 4CC
     * Give all parameters from defaults to fill
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::validate
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::_checkVarExistsWithDefaults
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::_checkVarExistsWithDefaultsLocationExists
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::_checkVarExistsWithDefaultsCheckAllowListMustHave
     */
    public function testValidateUseDefaultValues()
    {
        $inputA = array(
            'exiffixtimestamps' => array()
        );
        $actualA = $this->_object->validate( $inputA );

        $this->assertingTrue( $actualA ); // true = relevant
    }


    /**
     * 4CC
     * Give all parameters (first case for all validation) to fill
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::validate
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::_checkVarExistsWithDefaultsLocationExists
     */
    public function testValidateCheckforErrorExceptionA()
    {
        $inputA = array(
            'exiffixtimestamps' => array(
                //A
                'location' => $this->_testsDir . '/tmp/should/not/exists',
                //B: 'fix' => 'NotPartOfTheList',
            )
        );
        // logger will report all, last error in the exception
        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $mesg = '/Error! Not found: exiffixtimestamps --location "(.*)\/tmp\/should\/not\/exists"/i';
        $this->expectingExceptionMessageRegex( $mesg );
        $this->_object->validate( $inputA );
    }
    /**
     * 4CC
     * Give all parameters (first case for all validation) to fill
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::validate
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::_checkVarExistsWithDefaultsCheckAllowListMustHave
     */
    public function testValidateCheckforErrorExceptionB()
    {
        $inputB = array(
            'exiffixtimestamps' => array(
                //A: 'location' => $this->_testsDir . '/tmp/should/not/exists',
                //B
                'fix' => 'NotPartOfTheList',
            )
        );
        // logger will report all, last error in the exception
        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $mesg = '/Error with: exiffixtimestamps --fix "NotPartOfTheList"/i';
        $this->expectingExceptionMessageRegex( $mesg );
        $this->_object->validate( $inputB );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::_prepareCommand
     */
    public function testExecuteNoRealExec()
    {
        $inputA = array(
            'exiffixtimestamps' => array()
        );
        $this->_object->validate( $inputA );
        $actualA = $this->_object->execute( false );

        $this->assertingTrue( $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::_prepareCommand
     */
    public function testExecuteRealExecWithErrorException()
    {
        // A:
        // check _prepareExecution false first:
        $actualA = $this->_object->execute( false );
        $this->assertingFalse( $actualA );

        // B:
        $inputB = array(
            'exiffixtimestamps' => array()
        );
        $this->_object->validate( $inputB );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $this->expectingExceptionMessageRegex( '/Execution error/' );
        $actualB = $this->_object->execute( true );

        $this->assertingTrue( $actualB );
    }


    /**
     * 4CC
     * Checks for _prepareCommand() - invalid datetimeFormatNew, datetimeValueNew
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::_prepareCommand
     */
    public function test_prepareCommandAException1()
    {
        $inputA = array(
            'exiffixtimestamps' => array(
                'datetimeFormatNew' => 'invalidFormat',
                'datetimeValueNew' => 'invalidValue',
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $this->expectingExceptionMessageRegex( '/Error in DateTimeImmutable: datetimeValueNew,datetimeFormatNew/' );
        $this->_object->execute( false );
    }


    /**
     * 4CC
     * Checks for _prepareCommand() - invalid datetimeFormatOld, datetimeValueOld
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::_prepareCommand
     */
    public function test_prepareCommandBException2()
    {
        $inputA = array(
            'exiffixtimestamps' => array(
                'datetimeFormatOld' => 'invalidFormat',
                'datetimeValueOld' => 'invalidValue',
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $this->expectingExceptionMessageRegex( '/Error in DateTimeImmutable: datetimeValueOld,datetimeFormatOld/' );
        $this->_object->execute( false );
    }


    /**
     * 4CC
     * Checks for _prepareCommand() - datetimeValueOld newer datetimeValueNew
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ExifFixTimestamps::_prepareCommand
     */
    public function test_prepareCommandCValueOldNewer3()
    {
        $inputC = array(
            'exiffixtimestamps' => array(
                'location' => $this->_testsDir . '/testfiles/Domain/ShellTools/images',
                'datetimeValueNew' => '2009-01-31 00:42:54',
                'datetimeValueOld' => '2023-07-17 22:56:08',
            )
        );
        $actualC = $this->_object->validate( $inputC );
        $this->assertingTrue( $actualC );

        $actualCA = $this->_object->execute( false );
        $this->assertingTrue( $actualCA );
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
