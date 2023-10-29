<?php declare( strict_types=1 );

/**
 * Mumsys_GetOptsTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright (c) 2015 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  GetOpts
 */


/**
 * Test class for Mumsys_GetOpts.
 */
class Mumsys_GetOptsTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_GetOpts
     */
    private $_object;

    /**
     * Initial options/configuration grouped in forms of configs:
     * simple => simple,
     * simpledesc => simple with descriptions
     * simpleactions => simple with simple actions
     * simpleactionsdesc => simple with simple actions and descriptions
     *
     * @var array
     */
    private $_optionsSimple;
    private $_optionsSimpleDesc;
    private $_optionsSimpleActions;
    private $_optionsSimpleActionsDesc;

    /**
     * Input parameters to work with.
     * @var array
     */
    private $_inputSimple;
    private $_inputSimpleDesc;
    private $_inputSimpleActions;

    /**
     * Long help prefix string
     * @var string
     */
    private $_helpLong;

    /**
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
        $this->_version = '4.0.0';
        $this->_versions = array(
            'Mumsys_GetOpts' => $this->_version,
            'Mumsys_Abstract' => '3.0.3',
            'Mumsys_Php_Globals' => '2.2.0',
        );

        // simple
        $this->_optionsSimple = array(
            '-v|--verbose', // v or verbose flag
            '-i|--input:', // i or input parameter with reguired value. e.g.:
            // --input /tmp/file.txt or --input=/tmp/file.txt
            '-f:', // single short f with required input
            '--file:', // single long --file with required input
            '-h', // single short -h flag (eg: for help) not required
            '--help', // single long --help flag (seperated for testing) not required
        );
        $this->_inputSimple = array(
            'programToCall', // program to call
            '--verbose',
            '-i',
            'tmp/file.txt',
            '-f',
            'f',
            '--file',
            'file',
            '-h',
            '--help',
        );

        // simple desc
        $this->_optionsSimpleDesc = array(
            '-v|--verbose' => 'description -v|--verbose',
            '-i|--input:' => 'description -i|--input:',
            '-f:' => 'description -f:',
            '--file:' => 'description --file:',
            '-h' => 'description -h',
            '--help' => 'description --help',
        );
        $this->_inputSimpleDesc = $this->_inputSimple;

        // simple actions
        $this->_optionsSimpleActions = array_merge(
            $this->_optionsSimple,
            array(
            'action1', // without a description or params
            'action2' => array(), // with no params
            'action3' => array(
                '--long',
                '-s'
            ), // with params
            )
        );
        $this->_inputSimpleActions = array(
            'programToCall', // program to call
            '--verbose',
            '-i',
            'tmp/file.txt',
            '-f',
            'f',
            '--file',
            'file',
            '-h',
            '--help',
            'action1', // just a call
            'action2', // just a call
            'action3', // just a call
        );

        // simple actions desc
        $this->_optionsSimpleActionsDesc = $this->_optionsSimpleDesc + array(
            'action1' => 'action1 description',
            'action2' => 'action2 description',
            'action3' => array( // currently no action desc possible!
                '--long' => 'action3 description --long',
                '-s' => 'action3 description -s',
            ), // with no params
        );

        $this->_helpLong = <<<HELPLONG
Class to handle/ pipe shell arguments in php context.

Shell arguments will be parsed and an array list of key/value pairs will be
created.
When using long and shot options the long options will be used and the short
one will map to it.
Short options always have a single character. Dublicate options can't be
handled. First comes first serves will take affect (fifo).
Flags will be handled as boolean true if set.
The un-flag option take affect when: Input args begin with a "--no-" string.
E.g. --no-history. It will check if the option --history was set and will
unset it like it wasn't set in the cmd line. This is usefule when working
with different options. One from a config file and the cmd line adds or
replace some options. But this must be handled in your buissness logic. E.g. see
Mumsys_Multirename class.
The un-flag option will always disable/ remove a value.

Your options:


HELPLONG;

        $this->_object = new Mumsys_GetOpts( $this->_optionsSimple, array() );
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
     * @covers Mumsys_GetOpts::__construct
     */
    public function testConstructServerVars()
    {
        $_SERVER['argv'] = array();
        $_SERVER['argc'] = 0;

        // use server vars, not input parameters
        $object = new Mumsys_GetOpts( $this->_optionsSimple );
        $this->assertingInstanceOf( 'Mumsys_GetOpts', $object );
    }


    /**
     * @covers Mumsys_GetOpts::__construct
     */
    public function testConstructInputVars()
    {
        $_SERVER['argv'] = array();
        $_SERVER['argc'] = 0;

        // use server vars, not input parameters
        $object = new Mumsys_GetOpts( $this->_optionsSimple, $this->_inputSimple );
        $this->assertingInstanceOf( 'Mumsys_GetOpts', $object );
    }


    /**
     * @covers Mumsys_GetOpts::__construct
     */
    public function testConstructException()
    {
        $this->expectingException( 'TypeError' );
        $regex = '/(Argument #1 (.*) must be of type array, int given)/i';
        $this->expectingExceptionMessageRegex( $regex );
        new Mumsys_GetOpts( 1, 1 );
    }


    /**
     * @covers Mumsys_GetOpts::__construct
     * @covers Mumsys_GetOpts::_verifyOptions
     * @covers Mumsys_GetOpts::getOptions
     *
     */
    public function testConstructVerifyOptions()
    {
        // A
        $objectA = new Mumsys_GetOpts( $this->_optionsSimple );
        $resultA = $objectA->getOptions();
        $expectedA = array(
            '_default_' => array(
                '-v|--verbose' => 'No description',
                '-i|--input:' => 'No description',
                '-f:' => 'No description',
                '--file:' => 'No description',
                '-h' => 'No description',
                '--help' => 'No description',
            )
        );

        // B
        $objectB = new Mumsys_GetOpts( $this->_optionsSimpleDesc );
        $resultB = $objectB->getOptions();
        $expectedB = array(
            '_default_' => array(
                '-v|--verbose' => 'description -v|--verbose',
                '-i|--input:' => 'description -i|--input:',
                '-f:' => 'description -f:',
                '--file:' => 'description --file:',
                '-h' => 'description -h',
                '--help' => 'description --help',
            )
        );

        // C
        $objectC = new Mumsys_GetOpts( $this->_optionsSimpleActions );
        $resultC = $objectC->getOptions();
        $expectedC = $expectedA + array(
            'action1' => array(),
            'action2' => array(),
            'action3' => array('--long', '-s'),
        );

        // D
        $objectD = new Mumsys_GetOpts( $this->_optionsSimpleActionsDesc );
        $resultD = $objectD->getOptions();
        $expectedD = $expectedB + array(
            'action1' => 'action1 description',
            'action2' => 'action2 description',
            'action3' => array(
                '--long' => 'action3 description --long',
                '-s' => 'action3 description -s'
            ),
        );

        // compare
        $this->assertingEquals( $expectedA, $resultA );
        $this->assertingEquals( $expectedB, $resultB );
        $this->assertingEquals( $expectedC, $resultC );
        $this->assertingEquals( $expectedD, $resultD );

        // exception
        $this->expectingException( 'Mumsys_GetOpts_Exception' );
        $regex = '/(Invalid input config found for key "0", value \(json\)\: "0")/i';
        $this->expectingExceptionMessageRegex( $regex );
        $objectE = new Mumsys_GetOpts( array(0, 1) );
    }


    /**
     * @covers Mumsys_GetOpts::__construct
     * @covers Mumsys_GetOpts::_generateMappingOptions
     * @covers Mumsys_GetOpts::getMapping
     */
    public function testConstructSetMappingOptions()
    {
        // A
        $objectA = new Mumsys_GetOpts( $this->_optionsSimple, $this->_inputSimple );
        $resultA = $objectA->getMapping();
        $expectedA = array(
            '_default_' => array(
                '-v' => '--verbose',
                '--verbose' => '--verbose',
                '-i' => '--input',
                '--input' => '--input',
                '-f' => '-f',
                '--file' => '--file',
                '-h' => '-h',
                '--help' => '--help',
            ),
        );

        // B
        $objectB = new Mumsys_GetOpts( $this->_optionsSimpleDesc, $this->_inputSimpleDesc );
        $resultB = $objectB->getMapping();
        $expectedB = $expectedA;

        // C
        $objectC = new Mumsys_GetOpts( $this->_optionsSimpleActions, $this->_inputSimpleActions );
        $resultC = $objectC->getMapping();
        $expectedC = $expectedA + array(
            'action1' => array(),
            'action2' => array(),
            'action3' => array(
                '--long'=>'--long',
                '-s' => '-s',
            ),
        );

        // D
        $objectD = new Mumsys_GetOpts( $this->_optionsSimpleActionsDesc, array() );
        $resultD = $objectD->getMapping();
        $expectedD = $expectedC;

        // 4CC
        // E toggle long and short opts for action
        $optionsE = $this->_optionsSimpleActionsDesc;
        $optionsE['action3'] = array(
            '--long|-l' => 'action3 description --long|-l',
            '-s' => 'action3 description -s',
        );
        $objectE = new Mumsys_GetOpts( $optionsE, array() );
        $resultE = $objectE->getMapping();
        $expectedE = $expectedC;
        $expectedE['action3'] = array(
                '--long'=>'--long',
                '-l'=>'--long',
                '-s' => '-s',
            );

        // compare
        $this->assertingEquals( $expectedA, $resultA );
        $this->assertingEquals( $expectedB, $resultB );
        $this->assertingEquals( $expectedC, $resultC );
        $this->assertingEquals( $expectedD, $resultD );
        $this->assertingEquals( $expectedE, $resultE );
    }


    /**
     * @covers Mumsys_GetOpts::__construct
     * @covers Mumsys_GetOpts::parse
     * @covers Mumsys_GetOpts::_parseArg
     * @covers Mumsys_GetOpts::_argFindInOptions
     * @covers Mumsys_GetOpts::_argIsReqired
     */
    public function testParseSimple()
    {
        // A
        $objectA = new Mumsys_GetOpts( $this->_optionsSimple, $this->_inputSimple );
        $this->assertingInstanceOf( 'Mumsys_GetOpts', $objectA );
        $resultA = $objectA->getResult();
        $expectedA = array(
            'verbose' => true,
            'input' => 'tmp/file.txt',
            'f' => 'f',
            'file' => 'file',
            'h' => true,
            'help' => true,
        );

        // b
        $objectB = new Mumsys_GetOpts( $this->_optionsSimpleDesc, $this->_inputSimple );
        $this->assertingInstanceOf( 'Mumsys_GetOpts', $objectB );
        $resultB = $objectB->getResult();
        $expectedB = $expectedA;

        // c
        $objectC = new Mumsys_GetOpts( $this->_optionsSimpleActions, $this->_inputSimpleActions );
        $this->assertingInstanceOf( 'Mumsys_GetOpts', $objectC );
        $resultC = $objectC->getResult();
        $expectedC = $expectedA + array(
            'action1' => array(),
            'action2' => array(),
            'action3' => array(),
        );

        // d
        $objectD = new Mumsys_GetOpts( $this->_optionsSimpleActionsDesc, $this->_inputSimpleActions );
        $this->assertingInstanceOf( 'Mumsys_GetOpts', $objectD );
        $resultD = $objectD->getResult();
        $expectedD = $expectedC;

        // compare
        $this->assertingEquals( $expectedA, $resultA );
        $this->assertingEquals( $expectedB, $resultB );
        $this->assertingEquals( $expectedC, $resultC );
        $this->assertingEquals( $expectedD, $resultD );
    }

//    wont fix at php side!
//    /**
//     * negativ int as array keys and also a solution:
//     * https://github.com/php/php-src/issues/11787
//     *
//     * @covers Mumsys_GetOpts::__construct
//     * @covers Mumsys_GetOpts::parse
//     * @covers Mumsys_GetOpts::_parseArg
//     */
//    public function testParseSimpleTestNumericArg()
//    {
//        $this->markTestIncomplete('Negativ int wont fix unless php will do for array keys');
//
//        // -9 opt: Wont fix at all undtil php fix it!
//        // Use --9 works may be dont use it
//        // negativ keys works only if given as negativ int and the order must fit!
//        //  -9 then -1 wont work (php internal next key/int would be -8)
//        // If you have all numbers set in config and parameters, ok.
//        // Else: danger zone. Wont fix!
//
//        $options  = array(
//            '--2' => 'two', // -> goes int -2
//            '--1', // goes int -1
//            '-0' => 'key -0 val string', // goes "-0" ... and result parser to positive int's.
//        );
//        $input = array(
//            'program',
//            '--2',
//            '--1',
//            '-0',
//        );
//
//        $objectA = new Mumsys_GetOpts( $options, $input );
//        $this->assertingInstanceOf( 'Mumsys_GetOpts', $objectA );
//        $resultA = $objectA->getResult();
//
//        $expectedA = array(
//            '--2' => true,
//            '--1' => true,
//            '-0' => true,
//        );
//
//        // compare
//        $this->assertingTrue( $expectedA === $resultA );
//    }


    /**
     * @covers Mumsys_GetOpts::__construct
     * @covers Mumsys_GetOpts::parse
     * @covers Mumsys_GetOpts::_parseArg
     */
    public function testParseSimpleActionsRequiredDirectValueGiven()
    {
        // replace -f with -f=f, drop index 7, reindex array
        $tmp = $this->_inputSimpleActions;
        $tmp[4] = '-f=f'; // -f already set an this wont be tested 4cc
        unset( $tmp[5] );
        $input = array_values( $tmp );

        $objectA = new Mumsys_GetOpts( $this->_optionsSimpleActions, $input );
        $this->assertingInstanceOf( 'Mumsys_GetOpts', $objectA );
        $resultA = $objectA->getResult();
        $expectedA = array(
            'verbose' => true,
            'input' => 'tmp/file.txt',
            'f' => 'f',
            'file' => 'file',
            'h' => true,
            'help' => true,
            'action1' => array(),
            'action2' => array(),
            'action3' => array(),
        );

        // compare
        $this->assertingEquals( $expectedA, $resultA );
    }


    /**
     * @covers Mumsys_GetOpts::__construct
     * @covers Mumsys_GetOpts::parse
     * @covers Mumsys_GetOpts::_parseArg
     */
    public function testParseArgException1TooManyInDirectValue()
    {
        // replace -f with -f=f+f2=f3 (hack test), drop index 7, reindex array
        $tmp = $this->_inputSimpleActions;
        $tmp[6] = '-f=f+f2=f3';
        unset( $tmp[7] );
        $input = array_values( $tmp );

        $regex = '/(Arg value handling error for: "-f=f\+f2=f3")/m';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_GetOpts_Exception' );
        new Mumsys_GetOpts( $this->_optionsSimpleActions, $input );
    }


    /**
     * 4CC
     * @covers Mumsys_GetOpts::__construct
     * @covers Mumsys_GetOpts::parse
     * @covers Mumsys_GetOpts::_parseArg
     * @covers Mumsys_GetOpts::_argFindInOptions
     * @covers Mumsys_GetOpts::_argIsUntag
     */
    public function testParseArgUnTagGlobal1()
    {
        $input = $this->_inputSimple;
        $input[1] = '--no-verbose';

        $objectA = new Mumsys_GetOpts( $this->_optionsSimple, $input );
        $this->assertingInstanceOf( 'Mumsys_GetOpts', $objectA );
        $resultA = $objectA->getResult();
        $expectedA = array(
            'verbose' => false, // <-- this test 4CC
            'input' => 'tmp/file.txt',
            'f' => 'f',
            'file' => 'file',
            'h' => true,
            'help' => true,
        );

        // compare
        $this->assertingEquals( $expectedA, $resultA );
    }


    /**
     * 4CC
     * @covers Mumsys_GetOpts::__construct
     * @covers Mumsys_GetOpts::parse
     * @covers Mumsys_GetOpts::_parseArg
     * @covers Mumsys_GetOpts::_argFindInOptions
     * @covers Mumsys_GetOpts::_argIsUntag
     * @covers Mumsys_GetOpts::getResult
     *
     * parseArg:366:if ( $action !== '_default_' && isset( $this->_mapping['_default_'][$argTag] ) ) {
     * never reached! what happen?
     */
    public function testParseArgUnTagGlobalInsideAction()
    {
        $input = array(
            'programToCall', // program to call
            '--verbose',
            'action3',
            '--long',
            '-s',
            '--no-verbose', // <--- disable verbose, unTag in global
            '--no-long', // <--- disable long, unTag in action
            '--no-s', // <--- disable s, unTag in action 4CC in _argIsUntag()
        );
        $objectA = new Mumsys_GetOpts( $this->_optionsSimpleActions, $input );
        $this->assertingInstanceOf( 'Mumsys_GetOpts', $objectA );
        $resultA = $objectA->getResult();
        $expectedA = array(
            'verbose' => false,
            'action3' => array(
                'long' => false,
                's' => false,
            ),
        );

        // compare
        $this->assertingEquals( $expectedA, $resultA );
    }


    /**
     * @covers Mumsys_GetOpts::parse
     * @covers Mumsys_GetOpts::_parseArg
     */
    public function testParseArgStartWithActionsAndThenUnTagGlobal()
    {
        $options = array(
            '--help',
            'action1',
            'action2',
            'action3' => array(
                '--long' => 'action3 description --long',
                '-s' => 'action3 description -s',
            ),
        );
        $input = array(
            'program',
            'action1',
            '--help',
        );
        $objectA = new Mumsys_GetOpts( $options, $input );
        $resultA = $objectA->getResult();
        $expectedA = array(
            'help' => true,
            'action1' => array(),
        );

        // compare
        $this->assertingEquals( $expectedA, $resultA );
    }


    /**
     * @covers Mumsys_GetOpts::parse
     * @covers Mumsys_GetOpts::_parseArg
     */
    public function testParseArgWithJustActionsNoGlobals()
    {
        $options = array(
            'action1',
            'action2',
            'action3' => array(
                '--long' => 'action3 description --long',
                '-s' => 'action3 description -s',
            ),
        );
        $input = array(
            'program',
            'action2',
            'action3',
            '--long',
            'action1',
        );
        $objectA = new Mumsys_GetOpts( $options, $input );
        $resultA = $objectA->getResult();
        $expectedA = array(
            'action2' => array(),
            'action3' => array('long' => true),
            'action1' => array(),
        );

        // compare
        $this->assertingEquals( $expectedA, $resultA );
    }


    /**
     * @covers Mumsys_GetOpts::parse
     * @covers Mumsys_GetOpts::_parseArg
     * @covers Mumsys_GetOpts::getResult
     */
    public function testParseArgSingleRequiredDirectValueGiven()
    {
        $options = array('--file:' => 'description --file:');
        $input = array('programm', '--file=file');

        $objectA = new Mumsys_GetOpts( $options, $input );
        $this->assertingInstanceOf( 'Mumsys_GetOpts', $objectA );
        $resultA = $objectA->getResult();
        $expectedA = array(
            'file' => 'file',
        );

        // compare
        $this->assertingEquals( $expectedA, $resultA );
    }


    /**
     * @covers Mumsys_GetOpts::__construct
     * @covers Mumsys_GetOpts::parse
     * @covers Mumsys_GetOpts::_parseArg
     */
    public function testParseArgUnknownArgException()
    {
        $regex = '/(Option \"--iAmNotIn\" not found in option list\/configuration for action "_default_")/m';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_GetOpts_Exception' );

        $input = $this->_inputSimple;
        $input[] = '--iAmNotIn';
        new Mumsys_GetOpts( $this->_optionsSimple, $input );
    }


    /**
     * 4CC simulate invalid input
     * @covers Mumsys_GetOpts::__construct
     * @covers Mumsys_GetOpts::parse
     * @covers Mumsys_GetOpts::_parseArg
     */
    public function testParseArgInvalidRequiredException()
    {
        $options = array(
            '--file:' => 'description --file:'
        );
        $input = array(
            'programm',
            '--file',
            '--doException', // <-- invalid o text exception
            'file'
        );

        $regex = '/(Missing or invalid value for parameter "(.*)" in action ".*")/m';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_GetOpts_Exception' );

        new Mumsys_GetOpts( $options, $input );

    }


    /**
     * @covers Mumsys_GetOpts::parse
     * @covers Mumsys_GetOpts::getResult
     */
    public function testResultCheckResultCache()
    {
        $object = new Mumsys_GetOpts( $this->_optionsSimple, $this->_inputSimple );

        $actualA = $object->getResult();
        $actualB = $object->getResult();
        $expectedA = array(
            'verbose' => true,
            'input' => 'tmp/file.txt',
            'f' => 'f',
            'file' => 'file',
            'h' => true,
            'help' => true,
        );

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_GetOpts::getHelp
     * @covers Mumsys_GetOpts::getHelpLong
     * @covers Mumsys_GetOpts::_wordwrapHelp
     * @covers Mumsys_GetOpts::__toString
     */
    public function testGetHelpSimple()
    {
        $object = new Mumsys_GetOpts( $this->_optionsSimple, $this->_inputSimple );
        $actual = $object->getHelp();
        $expected = 'Global options/ information:' . PHP_EOL
             . PHP_EOL
            . '-v|--verbose' . PHP_EOL
            . '    No description' . PHP_EOL
            . '    ' . PHP_EOL
            . '-i|--input <yourValue/s>' . PHP_EOL
            . '    No description' . PHP_EOL
            . '    ' . PHP_EOL
            . '-f <yourValue/s>' . PHP_EOL
            . '    No description' . PHP_EOL
            . '    ' . PHP_EOL
            . '--file <yourValue/s>' . PHP_EOL
            . '    No description' . PHP_EOL
            . '    ' . PHP_EOL
            . '-h' . PHP_EOL
            . '    No description' . PHP_EOL
            . '    ' . PHP_EOL
            . '--help' . PHP_EOL
            . '    No description' . PHP_EOL
            . PHP_EOL
        ;

        $this->assertingEquals( $expected, $actual );
        $this->assertingEquals( $expected, $object );
        $this->assertingEquals( ( $this->_helpLong . $expected ), $object->getHelpLong() );
    }


    /**
     * @covers Mumsys_GetOpts::getHelp
     * @covers Mumsys_GetOpts::getHelpLong
     * @covers Mumsys_GetOpts::_wordwrapHelp
     * @covers Mumsys_GetOpts::__toString
     */
    public function testGetHelpSimpleActions()
    {
        $object = new Mumsys_GetOpts( $this->_optionsSimpleActions, $this->_inputSimpleActions );
        $actual = $object->getHelp();
        $expected = 'Global options/ Actions and options/ information:' . PHP_EOL
             . PHP_EOL
            . '-v|--verbose' . PHP_EOL
            . '    No description' . PHP_EOL
            . '    ' . PHP_EOL
            . '-i|--input <yourValue/s>' . PHP_EOL
            . '    No description' . PHP_EOL
            . '    ' . PHP_EOL
            . '-f <yourValue/s>' . PHP_EOL
            . '    No description' . PHP_EOL
            . '    ' . PHP_EOL
            . '--file <yourValue/s>' . PHP_EOL
            . '    No description' . PHP_EOL
            . '    ' . PHP_EOL
            . '-h' . PHP_EOL
            . '    No description' . PHP_EOL
            . '    ' . PHP_EOL
            . '--help' . PHP_EOL
            . '    No description' . PHP_EOL
            . PHP_EOL
            . 'action1' . PHP_EOL
            . '        No description' . PHP_EOL
            . PHP_EOL
            . 'action2' . PHP_EOL
            . '        No description' . PHP_EOL
            . PHP_EOL
            . 'action3' . PHP_EOL
            . '    --long' . PHP_EOL
            . '        No description' . PHP_EOL
            . '        ' . PHP_EOL
            . '    -s' . PHP_EOL
            . '        No description' . PHP_EOL
            . PHP_EOL
        ;

        $this->assertingEquals( $expected, $actual );
        $this->assertingEquals( $expected, $object );
        $this->assertingEquals( ( $this->_helpLong . $expected ), $object->getHelpLong() );
    }


    /**
     * @covers Mumsys_GetOpts::getHelp
     * @covers Mumsys_GetOpts::getHelpLong
     * @covers Mumsys_GetOpts::_wordwrapHelp
     * @covers Mumsys_GetOpts::__toString
     */
    public function testGetHelpSimpleActionsWithDesc()
    {
        $object = new Mumsys_GetOpts( $this->_optionsSimpleActionsDesc, array() );
        $actual = $object->getHelp();
        $expected = 'Global options/ Actions and options/ information:' . PHP_EOL
             . PHP_EOL
            . '-v|--verbose' . PHP_EOL
            . '    description -v|--verbose' . PHP_EOL
            . '    ' . PHP_EOL
            . '-i|--input <yourValue/s>' . PHP_EOL
            . '    description -i|--input:' . PHP_EOL
            . '    ' . PHP_EOL
            . '-f <yourValue/s>' . PHP_EOL
            . '    description -f:' . PHP_EOL
            . '    ' . PHP_EOL
            . '--file <yourValue/s>' . PHP_EOL
            . '    description --file:' . PHP_EOL
            . '    ' . PHP_EOL
            . '-h' . PHP_EOL
            . '    description -h' . PHP_EOL
            . '    ' . PHP_EOL
            . '--help' . PHP_EOL
            . '    description --help' . PHP_EOL
            . PHP_EOL
            . 'action1' . PHP_EOL
            . '    action1 description' . PHP_EOL
            . PHP_EOL
            . 'action2' . PHP_EOL
            . '    action2 description' . PHP_EOL
            . PHP_EOL
            . 'action3' . PHP_EOL
            . '    --long' . PHP_EOL
            . '        action3 description --long' . PHP_EOL
            . '        ' . PHP_EOL
            . '    -s' . PHP_EOL
            . '        action3 description -s' . PHP_EOL
            . PHP_EOL
        ;

        $this->assertingEquals( $expected, $actual );
        $this->assertingEquals( $expected, $object );
        $this->assertingEquals( ( $this->_helpLong . $expected ), $object->getHelpLong() );
    }

    /**
     * @covers Mumsys_GetOpts::getHelp
     * @covers Mumsys_GetOpts::getHelpLong
     * @covers Mumsys_GetOpts::_wordwrapHelp
     * @covers Mumsys_GetOpts::__toString
     */
    public function testGetHelpActionsWithDescNoGlobals()
    {
        $options = array(
            'action1' => 'action1 description',
            'action2' => 'action2 description',
            'action3' => array( // currently no action desc possible!
                '--long' => 'action3 description --long',
                '-s' => 'action3 description -s',
            ),
        );
        $input = array(
            'program',
        );
        $object = new Mumsys_GetOpts( $options, $input );
        $actual = $object->getHelp();
        $expected = 'Actions and options/ information:' . PHP_EOL
            . PHP_EOL
            . 'action1' . PHP_EOL
            . '    action1 description' . PHP_EOL
            . PHP_EOL
            . 'action2' . PHP_EOL
            . '    action2 description' . PHP_EOL
            . PHP_EOL
            . 'action3' . PHP_EOL
            . '    --long' . PHP_EOL
            . '        action3 description --long' . PHP_EOL
            . '        ' . PHP_EOL
            . '    -s' . PHP_EOL
            . '        action3 description -s' . PHP_EOL
            . PHP_EOL
        ;

        $this->assertingEquals( $expected, $actual );
        $this->assertingEquals( $expected, $object );
        $this->assertingEquals( ( $this->_helpLong . $expected ), $object->getHelpLong() );
    }


    /**
     * 4CC global usage
     * @covers Mumsys_GetOpts::getHelpCheckGlobalOrLocal
     */
    public function testGetHelpCheckGlobalOrLocal_global()
    {
        $options = array(
            'action3' => array( // currently no action desc possible!
                '--help|-h' => 'action3 description --help',
            ),
            '--help|-h' => 'Show the complete help'
        );

        $input = array(
            'program',
            '-h'
        );
        $object = new Mumsys_GetOpts( $options, $input );
        $helpAction = $object->getHelpCheckGlobalOrLocal();

        $actual = $object->getHelp();
        $expected = 'Global options/ Actions and options/ information:' . PHP_EOL
            . PHP_EOL
            . '--help|-h' . PHP_EOL
            . '    Show the complete help' . PHP_EOL
            . PHP_EOL
            . 'action3' . PHP_EOL
            . '    --help|-h' . PHP_EOL
            . '        action3 description --help' . PHP_EOL
            . '' . PHP_EOL
        ;

        $this->assertingNull( $helpAction );
        $this->assertingEquals( $expected, $actual );
        $this->assertingEquals( $expected, $object );
        $this->assertingEquals( $expected, $object->getHelp() );
    }


    /**
     * 4CC local/action help usage limited the that action
     * @covers Mumsys_GetOpts::getHelp
     * @covers Mumsys_GetOpts::getHelpCheckGlobalOrLocal
     */
    public function testGetHelpCheckGlobalOrLocal_local()
    {
        $options = array(
            'action3' => array( // currently no action desc possible!
                '--help|-h' => 'action3 description --help',
            ),
            '--help|-h' => 'Show the complete help',
        );

        $input = array(
            'program',
            'action3',
            '-h'
        );
        $object = new Mumsys_GetOpts( $options, $input );
        $helpAction = $object->getHelpCheckGlobalOrLocal();

        $actual = $object->getHelp( $helpAction );
        $expectedGlobal = 'Global options/ Actions and options/ information:' . PHP_EOL
            . PHP_EOL
            . '--help|-h' . PHP_EOL
            . '    Show the complete help' . PHP_EOL
            . PHP_EOL
            . 'action3' . PHP_EOL
            . '    --help|-h' . PHP_EOL
            . '        action3 description --help' . PHP_EOL
            . '' . PHP_EOL
        ;
        $expected = 'Global options/ Actions and options/ information:' . PHP_EOL
            . PHP_EOL
            . 'action3' . PHP_EOL
            . '    --help|-h' . PHP_EOL
            . '        action3 description --help' . PHP_EOL
            . '' . PHP_EOL
        ;

        $this->assertingEquals( 'action3', $helpAction );
        $this->assertingEquals( $expected, $actual );
        $this->assertingEquals( $expectedGlobal, $object );
        $this->assertingEquals( $expected, $object->getHelp( $helpAction ) );
    }

    /**
     * 4CC no help exists, shows all available
     * @covers Mumsys_GetOpts::getHelpCheckGlobalOrLocal
     */
    public function testGetHelpCheckGlobalOrLocal_noHelpSet()
    {
        $options = array(
            'action3' => array( // currently no action desc possible!
                '--long|-l' => 'action3 description --long|-l',
            ),
            '--long|-l' => 'global option --long|-l'
        );

        $input = array(
            'program',
        );
        $object = new Mumsys_GetOpts( $options, $input );
        $helpAction = $object->getHelpCheckGlobalOrLocal();

        $actual = $object->getHelp();
        $expected = 'Global options/ Actions and options/ information:' . PHP_EOL
            . PHP_EOL
            . '--long|-l' . PHP_EOL
            . '    global option --long|-l' . PHP_EOL
            . PHP_EOL
            . 'action3' . PHP_EOL
            . '    --long|-l' . PHP_EOL
            . '        action3 description --long|-l' . PHP_EOL
            . '' . PHP_EOL
        ;

        $this->assertingNull( $helpAction );
        $this->assertingEquals( $expected, $actual );
        $this->assertingEquals( $expected, $object );
        $this->assertingEquals( $expected, $object->getHelp() );
    }


    /**
     * @covers Mumsys_GetOpts::getCmd
     */
    public function testGetCmdSimple()
    {
        $objectA = new Mumsys_GetOpts( $this->_optionsSimple, $this->_inputSimple );
        $this->assertingInstanceOf( 'Mumsys_GetOpts', $objectA );
        $resultA = $objectA->getCmd();
        $expectedA = 'programToCall --verbose --input tmp/file.txt -f f --file file -h --help';

        // compare
        $this->assertingEquals( $expectedA, $resultA );
    }

    /**
     * @covers Mumsys_GetOpts::getCmd
     */
    public function testGetCmdActions()
    {
        $input = array_merge( $this->_inputSimpleActions, array('--no-help') );
        $objectA = new Mumsys_GetOpts( $this->_optionsSimpleActionsDesc, $input );
        $this->assertingInstanceOf( 'Mumsys_GetOpts', $objectA );
        $resultA = $objectA->getCmd();
        $expectedA = 'programToCall '
            . '--verbose --input tmp/file.txt -f f --file file -h --no-help action1 action2 action3';

        // compare
        $this->assertingEquals( $expectedA, $resultA );
    }

//
//    /**
//     * @covers Mumsys_GetOpts::getCmd
//     */
//    public function testGetCmd2()
//    {
//        $o = new Mumsys_GetOpts( array('-y:'), array('cmd', '-y', 'yes') );
//        $actual = $o->getCmd();
//        $expected = '-y yes';
//        $this->assertingEquals( $expected, $actual );
//
//        $this->expectingException( 'Mumsys_GetOpts_Exception' );
//        $this->expectingExceptionMessageRegex( '/(Missing value for parameter "-x")/' );
//        $o = new Mumsys_GetOpts( array('-x:'), array('cmd', '-x') );
//        $actual = $o->getCmd();
//    }
//
//
//    /**
//     * @covers Mumsys_GetOpts::getCmd
//     */
//    public function testGetCmdAdv()
//    {
//        // all in: all opts fits to an action
//        $opts = array(
//            '--verbose|-v' => true,
//            '--input:' => 'desc for input in action',
//            '--flag' => true,
//        );
//        $allInConfig = array(
//            'action1' => $opts,
//            'action2' => $opts,
//        );
//        // eg: script.php action1 --verbose --input file1.txt --flag action2 --input file2.txt
//        $input = array(
//            'script.php',
//            'action1', '--verbose', '--input', "file1.txt", '--flag',
//            'action2', '--input', 'file2.txt', '-v', '--no-v',
//        );
//
//        $object = new Mumsys_GetOpts( $allInConfig, $input );
//
//        $actual = $object->getCmd();
//        $expected = 'action1 --verbose --input file1.txt --flag action2 '
//            . '--input file2.txt --no-verbose';
//        $this->assertingEquals( $expected, $actual );
//    }
//
//
//
//
//

    /**
     * @covers Mumsys_GetOpts::getRawData
     */
    public function testGetRawData()
    {
        $object = new Mumsys_GetOpts( $this->_optionsSimple, $this->_inputSimple );
        $actual = $object->getRawData();
        $expected = array(
            '_default_' => array(
                '--verbose' => true,
                '--input' => 'tmp/file.txt',
                '-f' => 'f',
                '--file' => 'file',
                '-h' => true,
                '--help' => true,
            )
        );

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_GetOpts::getRawInput
     */
    public function testGetRawInput()
    {
        $object = new Mumsys_GetOpts( $this->_optionsSimple, $this->_inputSimple );
        $actual = $object->getRawInput();

        $this->assertingEquals( $this->_inputSimple, $actual );
    }


    /**
     * @covers Mumsys_GetOpts::resetResults
     */
    public function testResetResult()
    {
        $object = new Mumsys_GetOpts( $this->_optionsSimple, $this->_inputSimple );
        $object->resetResults();
        $this->assertingEquals( array(), $object->getMapping() );
        $this->assertingEquals( array(), $object->getResult() );
    }


    /**
     * test abstract and versions
     * @covers Mumsys_GetOpts::__construct
     */
    public function testgetVersions()
    {
        $object = new Mumsys_GetOpts( $this->_optionsSimple, $this->_inputSimple );

        $possible = $object->getVersions();

        foreach ( $this->_versions as $must => $value ) {
            $this->assertingTrue( isset( $possible[$must] ) );
            $this->assertingEquals( $possible[$must], $value );
        }
    }

}
