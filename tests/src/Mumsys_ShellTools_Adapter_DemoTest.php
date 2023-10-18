<?php declare( strict_types=1 );

/**
 * Mumsys_ShellTools_Adapter_DemoTest
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
 * Generated 2023-08-01 at 12:07:24.
 */
class Mumsys_ShellTools_Adapter_DemoTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_ShellTools_Adapter_Demo
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

        $this->_object = new Mumsys_ShellTools_Adapter_Demo( $this->_logger );
    }


    /**
     * Last action of this class
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
     * @covers Mumsys_ShellTools_Adapter_Demo::__construct
     * @covers Mumsys_ShellTools_Adapter_Abstract::__construct
     */
    public function test__construct()
    {
        $object = new Mumsys_ShellTools_Adapter_Demo( $this->_logger );
        $this->assertingInstanceOf( 'Mumsys_ShellTools_Adapter_Demo', $object );
    }

    /**
     * @covers Mumsys_ShellTools_Adapter_Demo::getCliOptions
     */
    public function testGetCliOptions()
    {
        $actualA = $this->_object->getCliOptions();
        $expectedA = array(
            'demo' => 'Demo action description',
            'demoaction1' => array(
                '--help' => 'Help flag in action "demoaction1"',
                '--input:' => 'Required input. also set as default in _optionDefaults',
                '--forexec:' => 'Value for real exec test',
                '--regex:' => 'Regex test as required value',
                '--regexerror:' => 'Regexerror to test an invalid regex',
            ),
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_Demo::getCliOptionsDefaults
     */
    public function testGetCliOptionsDefaults()
    {
        $actualA = $this->_object->getCliOptionsDefaults();
        $expectedA = array(
            'demoaction1' => array(
                'input' => 'defaultValue',
            )
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_Demo::getRequirementConfig
     */
    public function testGetRequirementConfig()
    {
        $actualA = $this->_object->getRequirementConfig();
        $expectedA = array(
            'cli' => array(
                'linux' => array(
                    'testalias' => array(
                        'test' => ' -d',
                        'test',
                    ),
                ),
            ),
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_Demo::setRequirementConfig
     */
    public function testSetRequirementConfig()
    {
        $actualA = $this->_object->getRequirementConfig();
        $this->_object->setRequirementConfig( $actualA );
        $expectedA = array(
            'cli' => array(
                'linux' => array(
                    'testalias' => array(
                        'test' => ' -d',
                        'test',
                    ),
                ),
            ),
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_Demo::validate
     */
    public function testValidateEmptyInput()
    {
        $input = array();
        $actualA = $this->_object->validate( $input );

        $this->assertingNull( $actualA );
    }


    /**
     * And 4CC of abstract class.
     *
     * @covers Mumsys_ShellTools_Adapter_Demo::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_getValidationMessagesTemplate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkKeyGiven
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkValueDefault
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkValueInList
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkValueLocationExists
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkValueFileExists
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkValueDirectoryExists
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkValueRegexMatchRequired
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsWithDefaults
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsNoDefaultsNotRequired
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsNoDefaultsButRequired
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkVarExistsWithDefaultsLocationExists
     */
    public function testValidateWithAllInputs()
    {
        // makes it relevant
        //$input = array('demoaction1' =>array());
        // ...including all param checks
        $inputA = array(
            'demoaction1' => array(
                'input' => 'defaultValue',
                'help' => true,
                'file' => __FILE__,
                'regex' => 'test',
            )
        );
        // A:
        $actualA = $this->_object->validate( $inputA );

        // B: use default 'input'
        $inputB = $inputA;
        unset( $inputB['demoaction1']['input'] );
        $actualB = $this->_object->validate( $inputB );

        // compare
        $this->assertingTrue( $actualA );
        $this->assertingTrue( $actualB );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_Demo::validate
     * @covers Mumsys_ShellTools_Adapter_Demo::_checkVarExistsWithDefaultsLocationExists
     */
    public function testValidateLocationRelativValid()
    {
        // setup
        $wd = getcwd();
        chdir( '../bin' );

        // test
        $inputA = array('demoaction1' => array(
            'file' => 'testfiles/Domain/ShellTools/images/file.png',
        ));

        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );

        // re-set
        chdir( $wd );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_Demo::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkValueRegexMatchRequired
     */
    public function testValidateWrongInputExceptionRegex()
    {
        $inputA = array('demoaction1' => array(
            'help' => true,
            'regex' => 'Not t e s t value',
        ));

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $this->expectingExceptionMessageRegex(
            '/Inalid "--regex" value given: "Not t e s t value"/'
        );
        $this->_object->validate( $inputA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_Demo::validate
     * @covers Mumsys_ShellTools_Adapter_Abstract::_checkValueRegexMatchRequired
     */
    public function testValidateWrongInputExceptionRegexError()
    {
        $errReporting = error_reporting();
        error_reporting( 0 );

        $inputA = array(
            'demoaction1' => array(
                'help' => true,
                'regexerror' => 'The Regex Is In Error',
            )
        );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $this->expectingExceptionMessageRegex(
            '/Regex error for "--regexerror" "invalid regex"/'
        );
        try {
            $this->_object->validate( $inputA );

        } catch ( Exception $ex ) {
            error_reporting( $errReporting );
            throw $ex;
        }
        // stay save:
        error_reporting( $errReporting );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_Demo::validate
     */
    public function testValidateWrongInputException()
    {
        // makes it relevant
        //$input = array('demoaction1' =>array());
        // ...including all param checks
        $inputA = array('demoaction1' => array(
            'input' => 'Not defaultValue',
            'help' => true,
        ));

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $this->expectingExceptionMessageRegex( '/Value "defaultValue" not given/' );
        $this->_object->validate( $inputA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_Demo::validate
     * @covers Mumsys_ShellTools_Adapter_Demo::_checkVarExistsWithDefaultsLocationExists
     */
    public function testValidateWrongInputExceptionFileInvalid()
    {
        $inputA = array('demoaction1' => array(
            'file' => 'NotAnExistingFile',
        ));

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $this->expectingExceptionMessageRegex(
            '/Error! Not found: demoaction1 --file "NotAnExistingFile"/'
        );
        $this->_object->validate( $inputA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_Demo::validate
     * @covers Mumsys_ShellTools_Adapter_Demo::_checkVarExistsWithDefaultsCheckAllowListMustHave
     */
    public function testValidateInputInList()
    {
        $inputA = array('demoaction1' => array(
            'input' => 'defaultValue',
        ));

        $actualA = $this->_object->validate( $inputA );
        $this->assertingTrue( $actualA );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_Demo::execute
     * @covers Mumsys_ShellTools_Adapter_Demo::validate
     * @covers Mumsys_ShellTools_Adapter_Demo::_prepareCommand
     * @covers Mumsys_ShellTools_Adapter_Abstract::_getBinaryParts
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_Abstract::_execCommand
     */
    public function testExecute()
    {
        // A: no validate() before
        $actualA = $this->_object->execute( false );

        // B: with input, no real exec
        $inputB = array('demoaction1' => array()); // makes it relevant
        $this->_object->validate( $inputB );
        ob_start();
        $actualB = $this->_object->execute( false );
        $outputB = ob_get_clean();

        $expectedB = 'no real execution: test -d /tmp';

        // B: with input, with real exec
        ob_start();
        $actualC = $this->_object->execute( true );
        $outputC = ob_get_clean();
        $expectedC = 'real execution: test -d /tmp';

        // compare
        $this->assertingFalse( $actualA ); // _prepareExecution fails

        $this->assertingTrue( $actualB );
        $this->assertingEquals( $expectedB, $outputB );

        $this->assertingTrue( $actualC );
        $this->assertingEquals( $expectedC, $outputC );
    }


    /**
     * @covers Mumsys_ShellTools_Adapter_Demo::execute
     * @covers Mumsys_ShellTools_Adapter_Demo::validate
     * @covers Mumsys_ShellTools_Adapter_Demo::_prepareCommand
     * @covers Mumsys_ShellTools_Adapter_Abstract::_getBinaryParts
     * @covers Mumsys_ShellTools_Adapter_Abstract::_prepareExecution
     * @covers Mumsys_ShellTools_Adapter_Abstract::_execCommand
     */
    public function testExecuteExceptionAtExec()
    {
        $inputA = array('demoaction1' => array('forexec' => 'given')); // exec fails
        $this->_object->validate( $inputA );

        $this->expectingException( 'Mumsys_ShellTools_Adapter_Exception' );
        $this->expectingExceptionMessage( 'Execution error' );
        ob_start();
        try {
            $this->_object->execute( true );
        } catch ( Exception $ex ) {
            ob_end_clean(); // end
            throw $ex;
        }

        $test = ob_get_clean(); // if exception not thrown: clean the buffer
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
