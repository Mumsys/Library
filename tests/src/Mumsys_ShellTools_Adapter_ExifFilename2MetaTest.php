<?php declare( strict_types=1 );

/**
 * Mumsys_ShellTools_Adapter_ExifFilename2MetaTest
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
class Mumsys_ShellTools_Adapter_ExifFilename2MetaTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_ShellTools_Adapter_ExifFilename2Meta
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

        $this->_object = new Mumsys_ShellTools_Adapter_ExifFilename2Meta( $this->_logger );
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
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::__construct
     */
    public function test__construct()
    {
        $object = new Mumsys_ShellTools_Adapter_ExifFilename2Meta( $this->_logger );
        $this->assertingInstanceOf( 'Mumsys_ShellTools_Adapter_ExifFilename2Meta', $object );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::getCliOptions
     */
    public function testGetCliOptions()
    {
        $actualA = $this->_object->getCliOptions();
        $expectedA = array(
            'Action "exiffilename2meta"' => 'Takes datetime values of a filename and set it to'
                . ' the exif metadata of images or other supported files ("man 1 exiftool"). '
                . 'The format of the filename can be set to take the file, grap the datetime '
                . 'values and sets them to the exif metadata. A lot of old digital cameras '
                . 'don\'t have exif metadata but store the datetime as the filename it was created. '
                . 'Why doing it?: If you rename the file you can still get datetime infomations.' . PHP_EOL
                . 'Example: ' . PHP_EOL
                . 'A: format  : \'%pr%Y-%m-%d_%T%rp\' ' . PHP_EOL
                . 'A: filename: \'IMG-2023-07-31_192919_gopro.jpg\'' . PHP_EOL
                . 'B: format  : \'%pr%D_%T%rp\' ' . PHP_EOL
                . 'B: filename: \'IMG-20230802_192919_gopro.jpg\'' . PHP_EOL
                . 'Check the README for details.'
                ,
            'exiffilename2meta' => array(
                '--location:' => 'The directory or location to the file to use',
                '--locationFormat:' => 'Format of the location. ' . PHP_EOL
                    . 'If a directory is given: ALL FILES MUST BE of the same format!!!' . PHP_EOL
                    . 'Example for a file: "2023-07-31_192919_gopro.jpg"' . PHP_EOL
                    . 'The format is "%Y-%m-%d_%T%rp"' . PHP_EOL
                    . 'Format aliases available:' . PHP_EOL
                    . '    %D = for date e.g: 20230216' . PHP_EOL
                    . '    %T = for time in 24h format! e.g: 235859' . PHP_EOL
                    . '    %Y = for year e.g: 2023' . PHP_EOL
                    . '    %m = for month e.g: 12 (December)' . PHP_EOL
                    . '    %d = for day e.g: 01 (January)' . PHP_EOL
                    . '    %H = for hour (24h) e.g: 23' . PHP_EOL
                    . '    %i = for minute e.g: 02' . PHP_EOL
                    . '    %s = for seconds e.g: 01' . PHP_EOL
                    . '    %pr = for prefix e.g: \'IMG-\' mixed chars' . PHP_EOL
                    . '    %rp = for suffix e.g: \'_gopro.jpg\' mixed chars' . PHP_EOL
                    . '    Hint: %pr = prefix, reverse %pr = %rp'
                    ,
                '--set' => 'Default "AllDates". Key of the metadata to set: Possible values: '
                    . 'AllDates, DateTimeOriginal, CreateDate or ModifyDate',
                '--locationFilter:' => 'Allow only this extension/s. E.g: "jpg". Default: "jpg,png",'
                    . ' All supported extensions by exiftool: "*"',
                '--help|-h' => 'Show the help for this action'
            ),
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::getCliOptionsDefaults
     */
    public function testGetCliOptionsDefaults()
    {
        $actualA = $this->_object->getCliOptionsDefaults();
        $expectedA = array(
            'exiffilename2meta' => array(
                'location' => '/tmp/my/picture/s',
                'set' => 'AllDates',
                // list of extension to allow, comma seperated
                'locationFilter' => 'jpg,png',
            )
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::getRequirementConfig
     */
    public function testGetRequirementConfig()
    {
        $actualA = $this->_object->getRequirementConfig();
        $expectedA = array(
            'cli' => array(
                'linux' => array(
                    'exiftool' => array('exiftool' => '') // no global params
                ),
            ),
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * Just run validate()
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::validate
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
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::validate
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_checkVarExistsWithDefaults
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_checkVarExistsNoDefaultsButRequired
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_checkVarExistsWithDefaultsLocationExists
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_checkVarExistsNoDefaultsButRequired
     */
    public function testValidateWithInput()
    {
        $inputA = array(
            'exiffilename2meta' => array(
                'location' => $this->_testsDir . '/testfiles/Domain/ShellTools/images',
                'locationFormat' => '%pr%D_%T%rp',
                'set' => 'AllDates',
                'locationFilter' => 'jpg,png',
            )
        );
        $actualA = $this->_object->validate( $inputA );

        $this->assertingTrue( $actualA ); // true = relevant
    }


    /**
     * 4CC
     * Give all parameters from defaults to fill
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::validate
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_checkVarExistsWithDefaults
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_checkVarExistsNoDefaultsButRequired
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_checkVarExistsWithDefaultsLocationExists
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_checkVarExistsWithDefaultsCheckAllowListMustHave
     */
    public function testValidateUseDefaultValues()
    {
        $inputA = array(
            'exiffilename2meta' => array(
                'locationFormat' => '%pr%D_%T%rp' // must be given
            )
        );
        $actualA = $this->_object->validate( $inputA );

        $this->assertingTrue( $actualA ); // true = relevant
    }


    /**
     * 4CC
     * Give all parameters from defaults to fill
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::validate
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_checkVarExistsWithDefaultsLocationExists
     */
    public function testValidateLocationNotFoundException()
    {
        $inputA = array(
            'exiffilename2meta' => array(
                'location' => '/should/not/exists' // must be given
            )
        );
        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $regex = '/Error! Not found: exiffilename2meta --location "\/should\/not\/exists"/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->validate( $inputA );
    }


    /**
     * 4CC
     * Give all parameters from defaults to fill
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::validate
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_checkVarExistsWithDefaults
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_checkVarExistsNoDefaultsButRequired
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_checkVarExistsWithDefaultsLocationExists
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_checkVarExistsWithDefaultsCheckAllowListMustHave
     */
    public function testValidateLocationFormatNotGivenException()
    {
        $inputA = array(
            'exiffilename2meta' => array(
                //'locationFormat' => '%pr%D_%T%rp' // must be given
            )
        );
        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $regex = '/Required value missing \(for action: "exiffilename2meta"\) --locationFormat ""/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->validate( $inputA );
    }


    /**
     * 4CC
     * Give all parameters with/for errors
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::validate
     */
    public function testValidateCheckforErrorException()
    {
        $inputA = array(
            'exiffilename2meta' => array(
                'location' => $this->_testsDir . '/tmp/should/not/exists',
            )
        );
        // logger will report all, last error in the exception
        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $mesg = '/Error! Not found: exiffilename2meta --location "(.*)\/tmp\/should\/not\/exists"/i';
        $this->expectingExceptionMessageRegex( $mesg );
        $this->_object->validate( $inputA );
    }


    /**
     * 4CC
     * Give invalid set parameter.
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::validate
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_checkVarExistsWithDefaultsCheckAllowListMustHave
     */
    public function testValidateErrorForSetException()
    {
        $inputA = array(
            'exiffilename2meta' => array(
                'locationFormat' => '%pr%D_%T%rp', // valid
                'set' => 'invalidValue', // throw error
            )
        );
        // logger will report all, last error in the exception
        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $mesg = '/Error with: exiffilename2meta --set "invalidValue"/i';
        $this->expectingExceptionMessageRegex( $mesg );
        $this->_object->validate( $inputA );
    }


    /**
     * Just run execute() to be valid
     * Invalid default location exception
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_prepareCommands
     */
    public function testExecuteNoRealExecExceptionA()
    {
        $inputA = array(
            'exiffilename2meta' => array(
                'location' => $this->_testsDir . '/testfiles/Domain/ShellTools/images',
                'locationFormat' => '%pr%D_%T%rp', // must be given
                'locationFilter' => 'jpg', // only a valid jpg exists in test files
            )
        );
        $this->_object->validate( $inputA );

        $actualA = $this->_object->execute( false );
        $this->assertingTrue( $actualA );
    }


    /**
     * Just run execute() with _prepareCommands() exception
     * Invalid default location exception
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_prepareCommands
     */
    public function testExecuteNoRealExecExceptionB()
    {
        $inputA = array(
            'exiffilename2meta' => array(
                'locationFormat' => '%pr%D_%T%rp' // must be given
            )
        );
        $this->_object->validate( $inputA );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $this->expectingExceptionMessageRegex( '/No files found to handle/' );
        $this->_object->execute( false );
    }


    /**
     * Just run with default an valid location.
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_prepareCommands
     */
    public function testExecuteRealExec()
    {
        // A:
        // check _prepareExecution false first:
        $actualA = $this->_object->execute( false );
        $this->assertingFalse( $actualA );

        // B:
        $inputB = array(
            'exiffilename2meta' => array(
                'location' => $this->_testsDir . '/testfiles/Domain/ShellTools/images',
                'locationFormat' => '%pr%D_%T%rp', // must be given
                'locationFilter' => 'jpg', // only a valid jpg exists in test files
            )
        );
        $this->_object->validate( $inputB );

        $actualB = $this->_object->execute( true );
        $this->assertingTrue( $actualB );

        // cleanup
        $testfileBak = $this->_testsDir
            . '/testfiles/Domain/ShellTools/images/filename2meta_20230729_235859.jpg_original';
        if ( file_exists( $testfileBak ) ) {
            unlink( $testfileBak );
        }
        $this->assertingTrue( true ); // run until here, good
    }


    /**
     * Just run with default switch individual  locationFormat in _prepareCommands.
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_prepareCommands
     */
    public function testExecuteRealExecB()
    {
        // A:
        // check _prepareExecution false first:
        $actualA = $this->_object->execute( false );
        $this->assertingFalse( $actualA );

        // B:
        $inputB = array(
            'exiffilename2meta' => array(
                'location' => $this->_testsDir . '/testfiles/Domain/ShellTools/images',
                'locationFormat' => '%pr%Y%m%d_%H%i%s%rp', // must be given
                'locationFilter' => 'jpg', // only a valid jpg exists in test files
            )
        );
        $this->_object->validate( $inputB );

        $actualB = $this->_object->execute( true );
        $this->assertingTrue( $actualB );
    }


    /**
     * 4CC
     * Checks for _prepareCommand() - individual datetime parser checks
     *
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_ExifFilename2Meta::_prepareCommands
     */
    public function test_prepareCommandAException1()
    {
        $inputA = array(
            'exiffilename2meta' => array(
                'location' => $this->_testsDir . '/testfiles/Domain/ShellTools/images',
                'locationFormat' => '%pr%Y%m%d_%H%i%s%rp', // individual parser checks
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $this->expectingExceptionMessageRegex( '/String of filename seems invalid "file.png"/' );
        $this->_object->execute( false );
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
