<?php declare(strict_types=1);

/**
 * Mumsys_ShellTools_Adapter_Abstract
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
 * Abstract ShellTools implementation for common/ shared methodes.
 */
abstract class Mumsys_ShellTools_Adapter_Abstract
    extends Mumsys_Abstract
    implements Mumsys_ShellTools_Adapter_Interface
{
    /**
     * Version ID information.
     */
    public const VERSION = '1.0.0';

    /**
     * Logger obeject to log or output informations.
     *
     * @var Mumsys_Logger_Interface
     */
    protected Mumsys_Logger_Interface $_logger;

    /**
     * Requirement config from your adapter.
     *
     * If you need several cli tools for different tasks/actions: split them to
     * simple tasks: Eg: action1 needs 'commandA' and action2 needs a special
     * package: Then it is probably time to split things.
     *
     * E.g: array{cli: array{linux: array{testalias: array{cmd, global cmd opts}}}}
     *
     * @var array<string, array<string, array<string, array<string>>>>
     */
    private array $_requires;

    /**
     * Options config from your adapter.
     *
     * A Mumsys_Getopts config needs) for the actions this class should share,
     * handle and use.
     * E.g:
     * <code>
     * return array(
     *      'action1' => array(... Mumsys_GetOps option config for action1)
     *      'action2' => array(... getops option config for action2)
     *      'action3' => 'Description for action3 w/o parameters'
     * </code>
     * @var array<string, scalar|array<string|int, scalar>>
     */
    private array $_options;

    /**
     * Default values to be used if options not given in cli commands.
     *
     * Warning: Make sure they are valid or can be used without any problem or
     * leave out here! A structure like Mumsys_Getopts->getResults() to return
     *
     * Eg: array{demoaction1: array{input: string}}
     *
     * @var array<string, scalar|array<string|int, scalar>>
     */
    private array $_optionDefaults;

    /**
     * Validation message template string for sprintf():
     *      1: status: 'Error:' or 'Using:'
     *      2: default or given param: 'default ' or '' (current)
     *      3: param: '--param'
     *      4: value: the value in ""
     * @var string
     */
    private string $_validationMessageTemplate = '%1$s %2$s%3$s "%4$s"';

    /**
     * Binary parts of a requires config for current sapi and os family
     * @see getBinaryParts()
     *
     * To be used in your adapter for cross OS commands. Current adapters not
     * cross OS compatible, yet
     *
     * @var array<string,string>
     */
    private $_binaryParts;


    /**
     * Initialise the adapter object.
     *
     * @param Mumsys_Logger_Interface $logger
     * @param array<string, array<string, array<string, array<string>>>> $requires
     * List of key/value pairs of the _requires config
     * @param array<string,scalar|array<string|int,scalar>> $options Cli options
     * @param array<string,scalar|array<string|int,scalar>> $optionDefaults Cli default options
     */
    public function __construct( Mumsys_Logger_Interface $logger,
        array $requires, array $options, array $optionDefaults )
    {
        $this->_logger = $logger;
        $this->_requires = $requires;
        $this->_options = $options;

        /**
         * global options, eg: ?
         * sourcePath
         * sourceFile
         * targetPath
         * targetFile
         */
        $this->_optionDefaults = $optionDefaults;
        //$this->_logger->log( get_called_class() . '::__construct()', 7 );
    }


    /**
     * Retruns the _requires config.
     *
     * @return array<string, array<string, array<string, array<string>>>> List of
     * key/value pairs of the _requires config
     */
    public function getRequirementConfig(): array
    {
        return $this->_requires;
    }


    /**
     * Sets the requirement config.
     *
     * [PHP_SAPI][strtolower( PHP_OS_FAMILY )][ programID ] [ key => mixed ] pairs
     *
     * @param array<string, array<string, array<string, array<string>>>> $config List of
     * key/value pairs of the _requires config
     */
    public function setRequirementConfig( array $config = array() ): void
    {
        $this->_requires = $config;
    }


    /**
     * Returns the config (a Mumsys_Getopts config needs) for the actions this
     * program should share to be used.
     *
     * E.g:
     * <code>
     * return array(
     *      'action1' => array(... Mumsys_GetOps option config for action1)
     *      'action2' => array(... getops option config for action2)
     *      'action3' => 'Description for action3 w/o parameters'
     * </code>
     * @return array<string, scalar|array<string|int, scalar>> Cli options
     */
    public function getCliOptions(): array
    {
        return $this->_options;
    }


    /**
     * Returns option default values to be available to outer world.
     *
     * Default options can be used to pipe that values to the adapters validation
     * to be used if no other value is given e.g. by shell command to limit parameters.
     *
     * @return array<string, scalar|array<string|int, scalar>> optional option defaults
     */
    public function getCliOptionsDefaults(): array
    {
        return $this->_optionDefaults;
    }


    /**
     * Simple global checks for all concrete implementations.
     *
     * Currently just a check if validate() already succeed.
     *
     * @todo here _requires could take affect to check for the cli tool if they are available on OS's
     *
     * @param bool $isValid Validation status from concrete adapter
     *
     * @return bool True for valid else false
     */
    protected function _prepareExecution( bool $isValid ): bool
    {
        // $this->_logger->log( __METHOD__, Mumsys_Logger_Abstract::DEBUG );

        if ( $isValid ) {
            return true;
        } else {
            $this->_logger->log( 'Validation not done or not for this action', 6 );
            return false;
        }

        // your business logic in your implementation/ adapter...
    }


    /**
     * Executes a shell command.
     *
     * @todo improve using proc_open() ? https://stackoverflow.com/questions/2320608/php-stderr-after-exec
     *
     * @param string $command Command to be executed
     * @param bool $hideStdErr Flag to hide stderr on shell execution
     *
     * @return array<string, scalar|array<mixed>>
     * @throws Mumsys_ShellTools_Adapter_Exception If exit code not 0
     */
    protected function _execCommand( string $command, $hideStdErr = false ): array
    {
        $data = $code = null;
        $_cmd = escapeshellcmd( $command ); // too many excapes? see meta2filename
        if ( $hideStdErr ) {
            $_cmd .= ' 2>/dev/null';
        }

        $this->_logger->log( 'Run command now: "' . $_cmd . '"', 7 );
        ob_start();
        $lastLine = exec( $_cmd, $data, $code );
        $cliOutput = ob_get_clean();

        if ( $code > 0 ) {
            $this->_logger->log( 'Warning! Error from shell execution detected:', 0 );
            $this->_logger->log( 'cmd was : "' . $_cmd . '"', 0 );
            $this->_logger->log( 'cmd code: ' . $code, 0 );
            $this->_logger->log( 'cmd output:', 0 );
            $this->_logger->log( $data, 0 );

            throw new Mumsys_ShellTools_Adapter_Exception( 'Execution error' );
        }

        $result = array(
            'message' => $lastLine,
            'code' => $code,
            'content' => $data
        );

        return $result;
    }


    /**
     * Returns a standard template string to fromat the validate() parameter checks.
     *
     * @return string Template string for sprintf():
     *      1: status: 'Error:' or 'Using:'
     *      2: default or given param: 'default ' or '' (current)
     *      3: param: '--param'
     *      4: value: the value in ""
     */
    protected function _getValidationMessagesTemplate()
    {
        return $this->_validationMessageTemplate;
    }


    /**
     * Returns the binary for given alias based on sapi and OS family.
     *
     * The returning array can contain more then one key/value pair.
     *
     * @param string $alias Alias of the command from $_requires config
     *
     * @return array<string, string> Key(command)/value(global params)
     * pairs or key(int index)/command pairs or a mix of it
     */
    protected function _getBinaryParts( string $alias ): array
    {
        // just check once
        if ( $this->_binaryParts !== null ) {
            return $this->_binaryParts;
        }

        $config = $this->getRequirementConfig();

        if ( isset( $config[ PHP_SAPI ][ strtolower( PHP_OS_FAMILY ) ][$alias] ) ) {
            $record = $config[ PHP_SAPI ][ strtolower( PHP_OS_FAMILY ) ][$alias];
            foreach ( $record as $key => $value ) {
                if ( is_int( $key ) ) {
                    $this->_binaryParts[$value] = '';
                } else {
                    $this->_binaryParts[$key] = $value;
                }
            }

            return $this->_binaryParts;

        }
        // @codeCoverageIgnoreStart
        // OS dependent
        return array($alias => '');
        // @codeCoverageIgnoreEnd
    }


    //
    // helper
    //


    /**
     * Check if key is set in values. (Simple wrapperto avoid many if isset else constructs)
     *
     * @param string $key Key
     * @param array<scalar> $values List of key/value pairs
     *
     * @return bool True for is set
     */
    protected function _checkKeyGiven( string $key, array $values )
    {
        return isset( $values[$key] );
    }


    /**
     * Check if a default value exists.
     *
     * @param string $keyAction Action key
     * @param string $keyParam Parameter of the action
     * @param array<string, array<string, scalar>> $values List of key/value
     * pairs (_optionDefaults subpart probably given)
     *
     * @return bool True for is set
     */
    protected function _checkValueDefault( string $keyAction, string $keyParam,
        array $values )
    {
        return isset( $values[$keyAction][$keyParam] );
    }


    /**
     * Checks if a value exists and is the same in a given list.
     *
     * @param string $value Valus to look for in list
     * @param array<scalar> $list List of value may match to the value
     *
     * @return bool True for found
     */
    protected function _checkValueInList( string $value, array $list ): bool
    {
        foreach ( $list as $option ) {
            if ( $value === $option ) {
                return true;
            }
        }
        return false;
    }


    /**
     * Check if a value (file or directory exists on the local filesystem.
     *
     * @param string $key Key of list to check
     * @param array<string, string> $list List of key/value pairs (option
     * defaults or inputs)
     *
     * @return bool True for found
     */
    protected function _checkValueLocationExists( string $key, array $list )
    {
        return file_exists( $list[$key] );
        //return Mumsys_Php::file_exists( "file://" . $list[$key] );
    }


    /**
     * Checks a file is a file or link an is found.
     *
     * @param string $key Key of list to check
     * @param array<string, string> $list List of key/value pairs (option
     * defaults or inputs)
     *
     * @return bool True for found.
     */
    protected function _checkValueFileExists( string $key, array $list )
    {
        return ( is_file( $list[$key] ) || is_link( $list[$key] ) );
    }


    /**
     * Checks a directory exists and is a directory.
     *
     * @param string $key Key of list to check
     * @param array<string, string> $list List of key/value pairs (option
     * defaults or inputs)
     *
     * @return bool
     */
    protected function _checkValueDirectoryExists( string $key, array $list )
    {
        return ( file_exists( $list[$key] ) && is_dir( $list[$key] . DIRECTORY_SEPARATOR ) );
    }


    /**
     * Checks if a value matches a regular expression.
     *
     * The key is reqired for reporting and to limit code.
     *
     * @param string $value Value to be checked
     * @param string $regex Regular expression
     * @param string $key Valiable name (key)
     * @param string $regexAltTxt Alternativ text instead of regex in exception
     *
     * @return true True for success or exception will be thrown
     * @throws Mumsys_ShellTools_Adapter_Exception
     */
    protected function _checkValueRegexMatchRequired( string $value,
        string $regex, string $key, string $regexAltTxt = '' ): bool
    {
        $status = preg_match( $regex, $value );
        if ( $status === 1 ) {
            return true;
        } else if ( $status === false ) {
            $mesg = sprintf( 'Regex error for "--%1$s" "%2$s"', $key, $regex );
            throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
        } else {
            $regexText = ' (Allowed: "' . $regex . '")';
            if ( $regexAltTxt ) {
                $regexText = ' (Allowed: "' . $regexAltTxt . '")';
            }
            $mesg = sprintf(
                'Inalid "--%1$s" value given: "%2$s"%3$s',
                $key, $value, $regexText
            );
            throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
        }
    }


    //
    // concrete cases as alias to avoid long if else constructs
    //


    /**
     * Checks if variable given or default will be used and returns it.
     *
     * Dont use this if you dont have a default values.
     *
     * @param string $key Key of the value list to check if set
     * @param array<string> $valueList List of key/value pairs
     * @param string $action Current action
     * @param array<string, array<string>> $optionDefaults All default options
     * to be used as default if value for the key not given
     *
     * @return string|null
     */
    protected function _checkVarExistsWithDefaults( string $key,
        array $valueList, string $action, array $optionDefaults )
    {
        if ( isset( $valueList[$key] ) ) {
            $result = $valueList[$key];

        } else if ( isset( $optionDefaults[$action][$key] ) ) {
            $result = $optionDefaults[$action][$key];

            $message = sprintf(
                $this->_validationMessageTemplate,
                'Using: ' . $action,
                'default ',
                '--' . $key,
                $optionDefaults[$action][$key]
            );
            $this->_logger->log( $message, 6 );
        } else {
            // @codeCoverageIgnoreStart
            // ignore, not given. if the default would not exists anymore, only
            // then, this can happen
            return null;
            // @codeCoverageIgnoreEnd
        }

        return $result;
    }


    /**
     * Checks if a required variable given or throw exception.
     *
     * Dont use this if you dont have a default values.
     *
     * @param string $key Key of the value list to check if set
     * @param array<string> $valueList List of key/value pairs
     * @param string $action Current action
     *
     * @return string|null
     */
    protected function _checkVarExistsNoDefaultsButRequired(string $key,
        array $valueList, string $action)
    {
        if ( isset( $valueList[$key] ) ) {
            $result = $valueList[$key];

            $message = sprintf(
                $this->_validationMessageTemplate,
                'Using: ' . $action,
                '',
                '--' . $key,
                $valueList[$key]
            );
            $this->_logger->log( $message, 6 );

        } else {
            // not given, here required: Error!
            $mesg = sprintf(
                $this->_validationMessageTemplate,
                'Required value missing (for action: "' . $action . '")',
                '',
                '--' . $key,
                ''
            );
            $this->_logger->log( $mesg, 6 );
            throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
        }

        return $result;
    }


    /**
     * Checks if variable given.
     *
     * @param string $key Key of the value list to check if set
     * @param array<string> $valueList List of key/value pairs
     * @param string $action Current action
     *
     * @return string|null The value if set or null for not found
     */
    protected function _checkVarExistsNoDefaultsNotRequired( string $key,
        array $valueList, string $action )
    {
        if ( isset( $valueList[$key] ) ) {

            $result = $valueList[$key];

            $message = sprintf(
                $this->_validationMessageTemplate,
                'Using: ' . $action,
                '',
                '--' . $key,
                $valueList[$key]
            );
            $this->_logger->log( $message, 6 );

        } else {
            // ignore, not given
            return null;
        }

        return $result;
    }


    /**
     * Checks if variable given or takes the default value.
     *
     * Note: if abs. path did not succeed it checks for relativ location and
     * returns the value if a match was found.
     *
     * Dont use this if you dont have a default value and if the value is not
     * for a test for file or dir exists check.
     *
     * @param string $key Key of the value list to check if set
     * @param array<string> $valueList List of key/value pairs
     * @param string $action Current action
     * @param array<string, array<string>> $optionDefaults All default options
     * to be used as default if value for the key not given
     *
     * @return string|null
     * @throws Mumsys_ShellTools_Adapter_Exception
     */
    protected function _checkVarExistsWithDefaultsLocationExists( string $key,
        array $valueList, string $action, array $optionDefaults )
    {
        $result = null;
        // // param: location: path | file
        if ( isset( $valueList[$key] ) ) {

            if ( $this->_checkValueLocationExists( $key, $valueList ) ) {
                // path or file:
                $result = $valueList[$key];

                $message = sprintf(
                    $this->_validationMessageTemplate,
                    'Using: ' . $action,
                    '',
                    '--' . $key,
                    $valueList[$key]
                );
                $this->_logger->log( $message, 6 );

            } else {
                if ( isset( $valueList[$key] ) && realpath( $valueList[$key] ) === false ) {
                    //can be relativ
                    $wd = $_SERVER['PWD'] ?? '';
                    $loc = $wd . DIRECTORY_SEPARATOR . $valueList[$key];
                    if ( file_exists( $loc ) ) {
                        $result = $loc;
                    }
                }

                if ( empty( $result ) ) {
                    $mesg = sprintf(
                        $this->_validationMessageTemplate,
                        'Error! Not found: ' . $action, '', '--' . $key,
                        $valueList[$key]
                    );
                    $this->_logger->log( $mesg, 6 );
                    throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
                }
            }

        } else if ( isset( $optionDefaults[$action][$key] ) ) {
            // use default location
            $result = $optionDefaults[$action][$key];

            $message = sprintf(
                $this->_validationMessageTemplate,
                'Using: ' . $action,
                'default ',
                '--' . $key,
                $optionDefaults[$action][$key]
            );
            $this->_logger->log( $message, 6 );

        } else {
            // @codeCoverageIgnoreStart
            // ignore, not given. if the default would not exists anymore, only
            // then, this can happen
            return null;
            // @codeCoverageIgnoreEnd
        }

        return $result;
    }


    /**
     * Checks if variable given and if the value matches to one of a allow list.
     *
     * Or default will be used.
     *
     * @param string $key Key of the value list to check if set
     * @param array<string> $valueList List of key/value pairs
     * @param string $action Current action
     * @param array<string, array<string>> $optionDefaults All default options
     * to be used as default if value for the key not given
     * @param array<string> $allowList List of values wich are allowed
     *
     * @return string|null
     * @throws Mumsys_ShellTools_Adapter_Exception
     */
    protected function _checkVarExistsWithDefaultsCheckAllowListMustHave( string $key,
        array $valueList, string $action, array $optionDefaults,
        array $allowList )
    {
        if ( isset( $valueList[$key] ) ) {

            if ( $this->_checkValueInList( $valueList[$key], $allowList ) ) {

                $result = $valueList[$key];

                $message = sprintf(
                    $this->_validationMessageTemplate,
                    'Using: ' . $action,
                    '',
                    '--' . $key,
                    $valueList[$key]
                );
                $this->_logger->log( $message, 6 );

            } else {
                $mesg = sprintf(
                    $this->_validationMessageTemplate,
                    'Error with: ' . $action,
                    '',
                    '--' . $key,
                    $valueList[$key]
                );

                $this->_logger->log( $mesg, 6 );
                throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
            }

        } else if ( isset( $optionDefaults[$action][$key] ) ) {
            // not given. but if a default was set. take it.
            $result = $optionDefaults[$action][$key];

            $message = sprintf(
                $this->_validationMessageTemplate,
                'Using: ' . $action,
                'default ',
                '--' . $key,
                $optionDefaults[$action][$key]
            );
            $this->_logger->log( $message, 6 );

        } else {
            // @codeCoverageIgnoreStart
            // ignore, not given. if the default would not exists anymore, only
            // then, this can happen
            return null;
            // @codeCoverageIgnoreEnd
        }

        return $result;
    }

}
