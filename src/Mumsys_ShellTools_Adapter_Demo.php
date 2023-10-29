<?php declare(strict_types=1);

/**
 * Mumsys_ShellTools_Adapter_Demo
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2023 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  ShellTools
 * Created: 2023-07-27
 */


/**
 * Demo adapter for Shell Tools implementation.
 *
 * This is mostly used as Mock for tests and static code analysis to the highest
 * level as possible.
 * And also to get an idea how things can work for own implementations/ adapter.
 * Pleqse share adapters!
 * Study the abstact class for helper to limit code or huge if else constructs.
 * The interface should have the best informations for todo's and open tasks and
 * future changes.
 *
 * @see bin/shelltools.php script or ShellTools_Default/Tests for a usage example.
 */
class Mumsys_ShellTools_Adapter_Demo
    extends Mumsys_ShellTools_Adapter_Abstract
{
    /**
     * Version ID information.
     */
    public const VERSION = '1.0.0';

    /**
     * Mixed cli tools to be required for this adapter and on the executing system/os.
     *
     * If you need several cli tools for different tasks/actions: split them to
     * simple tasks: Eg: action1 needs 'df' and action2 needs a special package:
     * then it isprobably time to split things
     *
     * @var array{cli: array{linux: array{testalias: array<string>}}}
     */
    private array $_requires = array(
        // [PHP_SAPI][strtolower( PHP_OS_FAMILY )]
        'cli' => array(
            'linux' => array(
                'testalias' => array(
                    'test', // 4 tests 4CC BUT! 'test' maps to next key. LIFO
                    'test' => ' -d', // global option -d
                ),
            // alias         cmd       cmd prams
            ),
        // demo: cross OS not implemented yet
        //'windows' => array(
        //    'test' => array('test.exe' => ' -h'),
        //),
        ),
    );

    /**
     * Options (a Mumsys_Getopts config needs) for the actions this
     * class should share, handle and use.
     * E.g:
     * <code>
     * return array(
     *      'action1' => array(... Mumsys_GetOps option config for action1)
     *      'action2' => array(... getops option config for action2)
     *      'action3' => 'Description for action3 w/o parameters'
     * </code>
     * @var array<string, scalar|array<string|int, scalar>>
     */
    private array $_options = array(
        'demo' => 'Demo action description',
        'demoaction1' => array(
            '--help' => 'Help flag in action "demoaction1"',
            '--input:' => 'Required input. also set as default in _optionDefaults',
            '--forexec:' => 'Value for real exec test',
            '--regex:' => 'Regex test as required value',
            '--regexerror:' => 'Regexerror to test an invalid regex',
        ),
    );

    /**
     * Default values to be used if options not given in cli commands.
     *
     * Warning: Make sure they are valid or can be used without any problem or
     * leave out here! A structure like Mumsys_Getopts->getResults() to return
     *
     * @var array{demoaction1: array{input: string}}
     */
    private array $_optionDefaults = array(
        'demoaction1' => array(
            'input' => 'defaultValue',
        )
    );

    /**
     * Results mem keeper from validate().
     * @ va r array<string, scalar|array<string, scalar>>
     * @var array{
     *  help: bool,
     *  input: string,
     *  forexec: string,
     * }|array<string>
     */
    private array $_results = array();

    /**
     * Flag if validation was successful.
     * @var bool
     */
    private bool $_isValid = false;

    /**
     * Initialise the adapter object.
     *
     * @param Mumsys_Logger_Interface $logger
     */
    public function __construct( Mumsys_Logger_Interface $logger )
    {
        parent::__construct(
            $logger, $this->_requires, $this->_options, $this->_optionDefaults
        );
        $this->_logger->log( __METHOD__, 7 );
    }


    /**
     * Validates all results of a Mumsys_GetOps->getResult() return.
     *
     * @uses Mumsys_Logger_Interface Logger to log or output informations
     *
     * @param array{demoaction1: string, array{help: string, input:string}} $input Results
     * from a Mumsys_GetOpts->getResult() to check to be valid as good as
     * possible in this case
     *
     * @return bool|null Returns true on success or null for not relevat for current cmd line
     * @throws Mumsys_ShellTools_Adapter_Exception Throws last detected error
     */
    public function validate( array $input ): ?bool
    {
        $this->_logger->log( __METHOD__, 7 );

        $messageTmpl = $this->_getValidationMessagesTemplate();
        $this->_results = array();
        $errorList = array();

        $action = 'demoaction1';

        if ( !isset( $input[$action] ) || !is_array( $input[$action] ) ) {
            // not of this part
            return null;
        }

        //
        // param: help , 4CC in tests
        $testHelp = $this->_checkVarExistsNoDefaultsNotRequired(
            'help', $input[$action], $action
        );
        if ( $testHelp !== null ) {
            // do something if help given
            $this->_results['help'] = true;

            // example if a default would exist, must be given if true to get/enable help
            // } else if ( $this->_checkValueDefault( $action, 'help', $this->_optionDefaults ) ) {
            // or use one of the _checkVarExists*() helper methodes to limit code
        } else {
            // ignore, not given
        }

        // 4 tests 4CC
        $testUnknownCheck4CC = $this->_checkVarExistsNoDefaultsNotRequired(
            'keyNotExistsHere', $input[$action], $action
        );

        //
        // param: input

        // 4CC, 4 tests
        $tmpInputOrFromDefault = $this->_checkVarExistsWithDefaults(
            'input', $input[$action], $action, $this->_optionDefaults
        );
        $testInput = $this->_checkVarExistsNoDefaultsNotRequired(
            'input', $input[$action], $action
        );

        if ( $testInput !== null ) {
            if ( $tmpInputOrFromDefault ) {
                // $input[$action]['input'] may not exists and comes from default
            }

            // do something (verify) if input given
            if ( $input[$action]['input'] !== 'defaultValue' ) {
                // deprecated checking errors this way
                $errorList[] = 'Value "defaultValue" not given';
            }
            $this->_results['input'] = $input[$action]['input'];

            // 4CC in tests
            if ( $this->_checkValueInList( 'abc', array('xyz', 'abc') ) ) {
                $this->_checkValueInList( 'abc', array('987', '123') ); // false 4CC
            }

        } else if ( $this->_checkValueDefault( $action, 'input', $this->_optionDefaults ) ) {
            // not given. does a default exists to be used?
            // then: do it here
            $this->_results['input'] = $this->_optionDefaults[$action]['input'];

        } else {
            // ignore, not given
        }

        // other input test as required:
        try {
            $tmpInputOrException = $this->_checkVarExistsNoDefaultsButRequired(
                'input', $input[$action], $action
            );
        }
        catch ( Exception $inpExec ) {
            // just for tests
        }

        // param: forexec (also used for tests and 4CC)
        if ( $this->_checkKeyGiven( 'forexec', $input[$action] ) ) {
            $this->_results['forexec'] = (string) $input[$action]['forexec'];
        }

        // param: file
        if ( $this->_checkKeyGiven( 'file', $input[$action] ) ) {
            if ( $this->_checkValueLocationExists( 'file', $input[$action] ) ) {
                // location (the file) exists
            }
            if ( $this->_checkValueFileExists( 'file', $input[$action] ) ) {
                // location is a file (the file) and exists
            }

            if ( ! $this->_checkValueDirectoryExists( 'file', $input[$action] ) ) {
                // not a directory
                $errorMessageAsExample = sprintf(
                    $messageTmpl, 'a', 'b', 'c', 'd'
                );
            }
        }

        //
        // param: regex 4CC
        if ( $this->_checkKeyGiven( 'regex', $input[$action] ) ) {
            //4 tests regex valid match
            $this->_checkValueRegexMatchRequired(
                $input[$action]['regex'], '/(test)/', 'regex', 'regex alt text'
            );
        }

        //
        // param: regexerror 4CC it test invalid regex
        if ( $this->_checkKeyGiven( 'regexerror', $input[$action] ) ) {
            //4 tests regex valid match
            $this->_checkValueRegexMatchRequired(
                $input[$action]['regexerror'], 'invalid regex', 'regexerror'
            );
        }

        // this is only for tests and code coverage
        //
        // other test for 'file'/_checkVarExistsWithDefaultsLocationExists
        $tmpFile = $this->_checkVarExistsWithDefaultsLocationExists(
            'file', $input[$action], $action, $this->_optionDefaults
        );
        // other test for 'input'/_checkVarExistsWithDefaultsLocationExists
        try {
            $tmpFile = $this->_checkVarExistsWithDefaultsLocationExists(
                'input', $input[$action], $action, $this->_optionDefaults
            );
            // $tmpFile can be the input or the default!!!
        } catch ( Exception $ex ) {
            // location given but not found
        }
        // end for tests

        //
        // action was called and errors exists. Stop then!
        if ( $errorList ) {
            foreach ( $errorList as $error ) {
                $this->_logger->log( $error, Mumsys_Logger_Abstract::ERR );
            }

            throw new Mumsys_ShellTools_Adapter_Exception( $error );
        }

        $this->_isValid = true;
        return true;
    }


    /**
     * Executes a command.
     *
     * Checks first if validation was made, prepares the command and executes it
     * if $realExecution is not false
     *
     * @param bool $realExecution Flag to disable real execution (false) true by default.
     *
     * @return bool True on success
     * @throws Mumsys_ShellTools_Adapter_Exception On errors
     */
    public function execute( bool $realExecution = true ): bool
    {
        $this->_logger->log( __METHOD__, 7 );

        if ( parent::_prepareExecution( $this->_isValid ) === false ) {
            return false;
        }

        //try {
        $cmdList = $this->_prepareCommand();
        foreach ( $cmdList as $cmd ) {
            // all of this disabled for demo
            if ( $realExecution === true ) {
                $results = $this->_execCommand( $cmd, true );

                echo 'real execution: ' . $cmd;
            } else {
                //$this->_logger->log( 'Test mode. No real execution', 6 );
                echo 'no real execution: ' . $cmd;
            }
        }

        return true;

        //} catch ( Throwable $thex ) {
        //    throw $thex;
        //}
    }


    /**
     * This is your place to add your features using options from command line.
     *
     * @return array<string> Command line or whatever it should do with the shell calls.
     */
    private function _prepareCommand(): array
    {
        $binParts = $this->_getBinaryParts( 'testalias' );

        $cmdList = array();
        foreach ( $binParts as $command => $globalParams ) {
            // Demo for using binary/ parts OS independent except the target path below
            //$cmd = sprintf(
            //    '%1$s%2$s %3$s', $command,
            //    $globalParams,
            //    // for tests to check real exec errors
            //    ( isset( $this->_results['forexec'] ) ? '/tmp/ShoudNotExIsTs' : '/tmp' ),
            //);

            $cmd = sprintf(
                'test -d %1$s',
                // for tests to check real exec errors
                ( isset( $this->_results['forexec'] ) ? '/tmp/ShoudNotExIsTs' : '/tmp' ),
            );

            // disabled in this demo adapter:
            //$this->_logger->log( 'Command for exiftool: ', 7 );
            //$this->_logger->log( "\t" . $cmd, 6 );

            $cmdList[] = $cmd;
        }

        return $cmdList;
    }
}
