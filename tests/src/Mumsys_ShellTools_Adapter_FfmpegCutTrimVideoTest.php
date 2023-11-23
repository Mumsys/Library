<?php declare( strict_types=1 );

/**
 * Mumsys_ShellTools_Adapter_FfmpegCutTrimVideoTest
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
class Mumsys_ShellTools_Adapter_FfmpegCutTrimVideoTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo
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
            //'lineFormat' => '%5$s',
        );
        $this->_logger = new Mumsys_Logger_File( $loggerOpts );

        $this->_object = new Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo( $this->_logger );
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
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::__construct
     */
    public function test__construct(): void
    {
        $object = new Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo( $this->_logger );
        $this->assertingInstanceOf( 'Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo', $object );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::getCliOptions
     */
    public function testGetCliOptions(): void
    {
        $actualA = $this->_object->getCliOptions();
        $expectedA = array(
            'Action "ffmpegcuttrimvideo"' => 'Cut/ trim a video. ' . PHP_EOL
                . 'In shot: A very very limited ffmpeg wrapper to cut/trim a video with '
                . 'short examples here.' . PHP_EOL
                . '-------------------------------------------------' . PHP_EOL
                . 'WARNING: Make backups of your files before use!!!' . PHP_EOL
                . '-------------------------------------------------' . PHP_EOL
                ,
            'ffmpegcuttrimvideo' => array(
                '--location:' => 'Location to the file to cut/trim.' . PHP_EOL
                    . 'Or a directory to cut/trim all videos inside the directory '
                    . 'with the same settings. But: Only good if you have e.g: avi, '
                    . 'mpg, mp4... of the same video and want to cut them all the '
                    . 'same way. ' . PHP_EOL
                    . 'Warning: Qverwrites existing targets! Make Backups first!'
                ,
                '--targetsuffix:' => 'Default: "_cut"; Suffix filename of the '
                    . 'cutted video. E.g: video.mp4 becomes video_cut.mp4. '
                    . 'Warning: Qverwrites existing targets! Make Backups first!'
                ,
                '--timeStart:' => 'The time as start point ("HH:MM:SS.MILLISECONDS" '
                    . 'or "HH:MM:SS" )' . PHP_EOL
                    . 'Default: "00:00:00";  Optional: MILLISECONDS e.g: '
                    . '"00:00:02.459" otherwise it will be ignored when using just '
                    . 'HH:MM:SS',

                '--wayofcut:' => 'Way of the cut: ' . PHP_EOL
                    . '- "range": Default value; Cut a video from timeStart to '
                    . 'timeEnd value.' . PHP_EOL

                    . '- "duration": Cut fixed n seconds begining from --timeStart '
                    . 'value.' . PHP_EOL
                    . "\t" . 'E.g: Cut 5 min. and begin at the first minute: ' . PHP_EOL
                    . "\t" . './script --wayofcut=duration --timeStart="00:01:00" '
                    . '--timeEnd="00:05:00"' . PHP_EOL
                    . "\t" . 'A cut of minute 1-6 in result. A video of 5 minutes.'
                    . PHP_EOL

                     . '- "reverse" : Cut n seconds from the end of the video. '
                    . '--timeStart must be' . PHP_EOL
                    . "\t" . 'a negativ value. E.g: Cut the last minute from a '
                    . 'video: ' . PHP_EOL
                    . "\t" . 'Use: ./thisscript --timeStart="-00:01:00" '
                    . '--wayofcut=reverse --location...'
                ,
                '--timeEnd:' => 'The time for the cut end: "HH:MM:SS.MILLISECONDS" '
                    . 'or "HH:MM:SS". ' . PHP_EOL
                    . 'See --wayofcut if mandatory. Dont use for "reverse" wayofcut.',

                '--allowext' => 'File extensions to be allowed when using a '
                    . 'directory for "--location".' . PHP_EOL
                    . 'Default: mp4,mpg,avi,mpeg',

                '--help|-h' => 'Show the help for this action'
            ),
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::getCliOptionsDefaults
     */
    public function testGetCliOptionsDefaults(): void
    {
        $actualA = $this->_object->getCliOptionsDefaults();
        $expectedA = array(
            'ffmpegcuttrimvideo' => array(
                'location' => '/tmp/my/video/s',
                'targetsuffix' => 'cut',
                'timeStart' => '00:00:00',
                'wayofcut' => 'range',
                'allowext' => 'mp4,mpg,avi,mpeg'
            )
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::getRequirementConfig
     */
    public function testGetRequirementConfig(): void
    {
        $actualA = $this->_object->getRequirementConfig();
        $expectedA = array(
            // [PHP_SAPI][strtolower( PHP_OS_FAMILY )]
            'cli' => array(
                'linux' => array(
                    'ffmpeg' => array(
                        'ffmpeg' => ' -y -hide_banner', // force overwrite existing files
                        //'ffmpeg', // without params
                    ),
                ),
            ),
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::validate
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
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsWithDefaultsLocationExists
     */
    public function testValidateInputLocationValid(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                'location' => '/tmp',
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );
    }


    /**
     * 4CC Abstract test.
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsWithDefaultsLocationExists
     */
    public function testValidateInputLocationInalid(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                'location' => '/tmp/should/not/exists/exception',
            )
        );
        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $regex = '/Error! Not found: ffmpegcuttrimvideo --location "(.*)exception"/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->validate( $inputA );
    }


    /**
     * 4CC Abstract test.
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsWithDefaultsLocationExists
     */
    public function testValidateInputLocationUseDefault(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                //'location' => '/tmp', // not given, use default 4CC
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );
    }


    /**
     * 4CC + Abstract test.
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsWithDefaults
     */
    public function testValidateInputTargetsuffixValid(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                'targetsuffix' => 'cut', // set the same like default
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );
    }


    /**
     * 4CC + Abstract test.
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsWithDefaultsCheckAllowListMustHave
     */
    public function testValidateInputWayofcutValid(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                'wayofcut' => 'range', // set the same like default
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );
    }


    /**
     * 4CC + Abstract test.
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsWithDefaultsCheckAllowListMustHave
     */
    public function testValidateInputWayofcutInvalid(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                'wayofcut' => 'not in allow list',
            )
        );
        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $regex = '/Error with: ffmpegcuttrimvideo --wayofcut "not in allow list"/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->validate( $inputA );
    }


    /**
     * 4CC + Abstract test.
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsWithDefaultsCheckAllowListMustHave
     */
    public function testValidateInputWayofcutUseDefault(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                //'wayofcut' => use default,
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );
    }


    /**
     * 4CC + Abstract test.
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsNoDefaultsNotRequired
     */
    public function testValidateInputTimeEndValid(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                //'wayofcut' => use default,
                'timeEnd' => '00:00:12'
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );
    }


    /**
     * 4CC + Abstract test.
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsNoDefaultsNotRequired
     */
    public function testValidateInputTimeEndNotGiven(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                // just the action defaults are used
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );
    }


    /**
     * Just run the action.
     *
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::_prepareCommands
     */
    public function testExecuteWithDefaults(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                'timeEnd' => '00:00:12', // required value for success
            )
        );
        $this->_object->validate( $inputA );
        $actualA = $this->_object->execute( false );

        $this->assertingTrue( $actualA );
    }


    /**
     * Just run the action.
     *
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::_prepareCommands
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
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::_prepareCommands
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkValueRegexMatchRequired
     */
    public function testExecute_prepareCommandsExceptionA(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                'timeEnd' => '00:00:12', // required value for success in validate
                'targetsuffix' => '$%^' // excetion A in _prepareCommands
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $regex = '/Inalid "--targetsuffix" value given: "(.*)" \(Allowed: "a-Z _ - ."\)/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->execute( false );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::_prepareCommands
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkValueRegexMatchRequired
     */
    public function testExecute_prepareCommandsException4WayofcutA(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                'timeStart' => '00:00:12', // must be negativ
                'wayofcut' => 'reverse'
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $regex = '/Value for --timeStart is not a negative value: "00:00:12"/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->execute( false );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::_prepareCommands
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkValueRegexMatchRequired
     */
    public function testExecute_prepareCommandsException4WayofcutB(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                'timeStart' => '-00:00:12', // negativ not allowed except reverse
                'wayofcut' => 'range'
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $regex = '/Value for --timeStart is a negative value: "-00:00:12"/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->execute( false );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::_prepareCommands
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkValueRegexMatchRequired
     */
    public function testExecute_prepareCommandsExceptionTimeEndNotInReverse(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                'timeStart' => '-00:00:12', // must be negativ this ways
                'wayofcut' => 'reverse',
                'timeEnd' => '00:00:01', // not allowed here
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $regex = '/"timeEnd" is not allowed when using --wayofcut "reverse"/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->execute( false );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::_prepareCommands
     */
    public function testExecute_prepareCommandsExceptionTimeEndMissing(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                'timeStart' => '00:00:12', // valid
                'wayofcut' => 'range', // valid
                //'timeEnd' => '00:00:01', // missing
            )
        );
        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $regex = '/Value for --timeEnd missing/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->execute( false );
    }


    /**
     * 4CC, 'range' test success
     *
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::validate
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::_prepareCommands
     */
    public function testExecute_prepareCommandsScanDirExistingSourceA(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                'location' => $this->_testsDir . '/testfiles/Domain/ShellTools/videos/',
                'timeStart' => '00:00:00.000',
                'wayofcut' => 'range',
                'timeEnd' => '00:00:00.999',
            )
        );

        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $actualB = $this->_object->execute( false );
        $this->assertingTrue( $actualB );
    }


    /**
     * 4CC, 'duration' test success
     *
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::validate
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::_prepareCommands
     */
    public function testExecute_prepareCommandsScanDirExistingSourceB(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                'location' => $this->_testsDir . '/testfiles/Domain/ShellTools/videos/',
                'timeStart' => '00:00:00.000',
                'wayofcut' => 'duration',
                'timeEnd' => '00:00:00.999',
            )
        );

        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $actualB = $this->_object->execute( false );
        $this->assertingTrue( $actualB );
    }


    /**
     * 4CC, 'reverse' test success
     *
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::validate
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::_prepareCommands
     */
    public function testExecute_prepareCommandsScanDirExistingSourceC(): void
    {
        $inputA = array(
            'ffmpegcuttrimvideo' => array(
                'location' => $this->_testsDir . '/testfiles/Domain/ShellTools/videos/',
                'timeStart' => '-00:00:00.999',
                'wayofcut' => 'reverse',
            )
        );

        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        $actualB = $this->_object->execute( false );
        $this->assertingTrue( $actualB );
    }


    /**
     * 4CC
     * Expect exception due to invalid defaults (location)
     *
     * @covers Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo::execute
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     */
    public function testExecuteRealExecWithErrorException()
    {
        $testFile = $this->_testsDir . '/testfiles/Domain/ShellTools/videos/test_cutByTests.mp4';
        if ( file_exists( $testFile ) ) {
            $this->markTestSkipped( 'Test file from prev tests still exists.' );
        }

        // A:
        // check _prepareExecution false first:
        $actualA = $this->_object->execute( false );
        $this->assertingFalse( $actualA );

        // B:
        $inputB = array(
            'ffmpegcuttrimvideo' => array(
                'location' => $this->_testsDir . '/testfiles/Domain/ShellTools/videos/',
                'timeStart' => '00:00:00.000',
                'wayofcut' => 'range',
                'timeEnd' => '00:00:02.000',
                'targetsuffix' => '_cutByTests',
            )
        );
        $actualB = $this->_object->validate( $inputB );
        $this->assertingTrue( $actualB );

        try {
            $testResult = $this->_object->execute( true );
            $this->assertingTrue( $testResult );
        } catch ( Exception $ex ) {
            $this->markTestSkipped( 'ffmpeg bin not found or other error around excecute(true)' );
        }

        // recover, del creation
        if ( file_exists( $testFile ) ) {
            unlink( $testFile );
        }
        $this->assertingFalse( file_exists( $testFile ) );
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
