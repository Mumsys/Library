<?php declare( strict_types=1 );

/**
 * Mumsys_GetOpts
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2011 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  GetOpts
 * Created: 2011-04-11
 */

// test unflag overwrites flag eg: --help --no-help

/**
 * Class to handle/ pipe shell arguments in php context.
 *
 * Shell arguments will be parsed and an array list of key/value pairs will be
 * created.
 * When using long and short options the long options will be used and the short
 * one will map to it.
 * Short options always have a single character. Dublicate options can't be
 * handled. First comes first serves will take affect (fifo).
 *
 * Flags will be handled as boolean true if set.
 * The un-flag option take affect when: Input args begin with a "--no-" string.
 * E.g. --no-history. It will check if the option --history was set and will
 * unset it like it wasn't set in the cmd line. This is usefule when working
 * with different options. One from a config file and the cmd line adds or
 * replace some options. But this must be handled in your buissness logic.
 * E.g. see Mumsys_Multirename class.
 * The un-flag option will always disable/ remove a value.
 *
 * Example:
 * <code>
 * // Simple usage:
 * // Parameter options: A list of options or a list of key value pairs where
 * // the value contains help/ usage informations.
 * // The colon at the end of an option shows that an input must
 * // follow. Otherwise is will be handled as a flag (boolean set or not set)
 * // and if this do not match an error will be thrown.
 *
 * $paramerterOptions = array(
 *  // the value a option:
 *  '--cmd:',        // A required value; No help message or:
 * // required value in input
 *  '--program:' => 'your help/ usage information as value',
 * // required value in input
 *  '--pathstart:', => 'Path where your files are'
 *  '--delsource'   // flag. if given set to true in result
 * );
 *
 * // Advanced usage (setup including several actions like):
 * // e.g.: action1 --param1 val1 --param2 val2 action2 --param1 val1 ...
 * $paramerterOptions = array(
 *    'action1' => array(
 *        '--param1:', // just as array value No help message or:
 *        '--param2:' => 'your usage information as array value',
 *    'action2' => array(
 *        '--param1:', => 'Path where your files are',
 *        // ...
 * );
 *  *
 * // input is optional, when not using it the $_SERVER['argv'] will be used.
 * $input = null;
 * // or:
 * $input = array(
 *      '--program:',
 *      'programvalue',
 *      '--pathstart:',
 *      'pathstartvalue,
 *      '--delsource'
 * );
 *
 * $getOpts = new Mumsys_GetOpts($paramerterOptions, $input);
 * $programOptions = $getOpts->getResult();
 * // it will return like:
 * // $programOptions = array(
 * //      'program' => 'programvalue',
 * //      'pathstart' => 'pathstartvalue',
 * //      'delsource' => true // as boolean true
 * // In advanced setup (setup with actions) it will return something like this:
 * // $programOptions = array(
 * //      'action1' => array(
 * //           'program' => 'programvalue',
 * //           'pathstart' =>'pathstartvalue',
 * //      'action2' => array( ...
 * </code>
 *
 * Limitations: Only one action of the same name can be requested per request.
 * Also see some todo's tags at some methodes with are still open!
 * E.g: Numeric values which are negativ.:
 *      Dont work:          Works:
 *      --input "-0.123"    --input="-0.123"
 *      --input -0.123      --input=-0.123
 */
class Mumsys_GetOpts
    extends Mumsys_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '4.0.0';

    /**
     * Cmd line.
     * @var string
     */
    private $_cmd;

    /**
     * List, whitelist of argument parameters to look for.
     * Note: When using several keys e.g.: -a|--append: the longer one will be
     * used, the short will map to it and: first come, first serves.
     * E.g: /program -a "X" --append "Y" -> --append will be "X", "Y" ignored!
     * Output: array("append" => "X")
     *
     * After _verifyOptions()
     *
     * @var array<string, string|array<string|int, string>>
     */
    private $_options;

    /**
     * Mapping for short and long options.
     * @var array<string, array<string,string>>
     */
    private $_mapping;

    /**
     * Internal list (key value pairs) of all parameter which are in the whitelist.
     * @var array<string, array<string, scalar>>
     */
    private $_rawResult;

    /**
     * List (key value pairs) of all parameter without - and -- parameter
     * prefixes
     * @var array<string, array<string, scalar>|scalar>
     */
    private $_resultCache;

    /**
     * List of argument values (argv)
     * @var array<string>
     */
    private $_argv;

    /**
     * Argument count.
     * @var integer
     */
    private $_argc;

    /**
     * Internal flag if action are given in options or not.
     * @var boolean
     */
    private $_hasActionsInOpts;

    /**
     * Internal flag to deside if data has changed so that the results must be
     * created again.
     * @var boolean
     */
    private $_isModified;


    /**
     * Initialise the object and verify and parse incoming options/ config.
     *
     * @param array<int|string, string|array<string|int,string>> $configOptions
     * List of configuration parameters to look for
     * @param array<string> $input List of input arguments. Optional else uses
     *  default input (_argv) handling then
     *
     * @throws Mumsys_GetOpts_Exception On error initialsing the object
     */
    public function __construct( array $configOptions, array $input = null )
    {
        if ( $input === null ) {
            $this->_argv = Mumsys_Php_Globals::getServerVar( 'argv', array() );
            $this->_argc = Mumsys_Php_Globals::getServerVar( 'argc', 0 );
        } else {
            $this->_argv = $input;
            $this->_argc = count( $input );
        }

        $this->_verifyOptions( $configOptions ); // gen: $this->_options
        $this->_generateMappingOptions( $this->_options ); // gen: $this->_mapping

//print_r($this->_mapping);

        $this->_rawResult = $this->parse();
    }


    /**
     * Parse parameters to create the result.
     *
     * @todo trim() values? eg using auto-complete in shell like: --file= /tmp<TAB><TAB>
     *
     * @return array<string, array<string, scalar>> Result to be used
     * @throws Mumsys_GetOpts_Exception
     */
    public function parse(): array
    {
        $argPos = 1; // zero is the calling program
        $argv = & $this->_argv;
        $return = array();
        $inAction = '_default_';

        // Task:
        //
        // arg or action? detect '-'
        //
        // if arg:
        //      arg: long or short?: '-'
        //
        //      test argv for '=' (--key=value) so this value can be taken
        //          if so: defin arg long/short and value
        //
        //      arg in mapping?
        //      or
        //      a --no-FLAG in mapping?
        //          yes: invalidate in result
        //
        //      a reqired value?
        //          test for a ':' in options
        //              if so, next position must be the value
        //
        // if action:
        //      test if action exist
        //
        //      has args? same tests like for arg
        // E.g: $argv: Array(
        //    [0] => runner.php
        //    [1] => --help
        //    [2] => fixtimestamps
        //    [3] => --location=/tmp (in action fixtimestamps)

        while ( $argPos < $this->_argc ) {
            $argValue = $argv[$argPos];

            if ( isset( $argValue[0] ) && $argValue[0] == '-' ) {
                // its an arg. parseArg() with the last state of action or _default_
                $return = $this->_parseArg( $argValue, $inAction, $return, $argPos );

            } else {
                if ( isset( $this->_mapping[$argValue] ) ) {
                    $inAction = $argValue;
                    // it has options!
                    // next interation will do handle args if given
                    if ( !isset( $return[$argValue] ) ) {
                        $return[$argValue] = array();
                    }
                }
            }
            $argPos++;
        }

        return $return;
    }


    /**
     * Parse current argment tag and collect results.
     *
     * @todo a value can contain e.g: "-00:00..." works this way: --key="-00" not that way: --key "-00"
     * @todo benchmark opts given -f=f vs -f "f", whats faster? for the docs!
     *
     * @param string $argValue Current argument value
     * @param string $action Current action to test the argTag for
     * @param array<string, array<string, scalar>> $results Current results of
     * the parsing process
     * @param int $argPos Current position/ sequence of the argument
     *
     * @return array<string, array<string, scalar>> Previous results incl.
     * results found in the current sequence
     * @throws Mumsys_GetOpts_Exception On errors handling the argument
     */
    private function _parseArg( string $argValue, string $action,
        array $results, &$argPos ): array
    {
        if ( !isset( $results['_default_'] ) && isset( $this->_mapping['_default_'] ) ) {
            $results['_default_'] = array();
        }

        if ( $argValue[1] == '-' ) {
            $argTag = '--' . substr( $argValue, 2, strlen( $argValue ) );
        } else {
            $argTag = '-' . $argValue[1]; // take the short flag
        }

        // value directly given?
        $resultValue = null;
        if ( strpos( $argValue, '=' ) !== false ) {
            // tag and a value given. split to go on
            $valParts = explode( '=', $argValue );
            if ( count( $valParts ) != 2 ) {
                $mesg = sprintf(
                    'Arg value handling error for: "%1$s"', $argValue
                );
                throw new Mumsys_GetOpts_Exception( $mesg );
            }
            $argTag = trim( $valParts[0] );
            $resultValue = trim( $valParts[1] );
        }

        // 4SCA
        // @codeCoverageIgnoreStart
        if ( ( isset( $this->_mapping['_default_'] )
            && !is_array( $this->_mapping['_default_'] ) )
            || !is_array( $this->_mapping[$action] ) ) {
            throw new Mumsys_GetOpts_Exception( 'Unexpected error' );
        }
        // @codeCoverageIgnoreEnd

        // is a global tag? not in mapping?:
        // is a --no-FLAG to disable?
        // or an unknown tag?
        if ( ! isset( $this->_mapping[$action][$argTag] ) ) {

            if ( $action !== '_default_' && isset( $this->_mapping['_default_'][$argTag] ) ) {
                //start with action and option and call global arg

                // a global match found
                $internalAction = '_default_';
                // force using the long opt as tag
                $argTag = $this->_mapping[$internalAction][$argTag];

            } else {
                // can be an action untag or a global untag

                if ( ( $unTag = $this->_argIsUntag( $action, $argTag ) ) !== false ) {
                    // use the long opt, the short one maps to
                    $unTag = $this->_mapping[$action][$unTag];
                    // force beeing false if set later again (--no-f vs. -f)
                    $results[$action][$unTag] = false;
                    // argTag done here
                    return $results;

                } else if ( ( $unTag = $this->_argIsUntag( '_default_', $argTag ) ) !== false ) {
                    // a global arg!
                    // use the long opt, the short one maps to
                    $unTag = $this->_mapping['_default_'][$unTag];
                    // force beeing set and false if set later again (--no-f vs. -f)
                    $results['_default_'][$unTag] = false;
                    // argTag done here
                    return $results;

                } else {
                    // not in mapping found!
                    /** @todo/feat: ignore unknown tags or report as error? verbose mode? */
                    $mesg = sprintf(
                        'Option "%1$s" not found in option list/configuration for action "%2$s"',
                        $argTag, $action
                    );
                    throw new Mumsys_GetOpts_Exception( $mesg );
                }
            }

        } else {
            $internalAction = $action;
            // force using the long opt as tag
            $argTag = $this->_mapping[$internalAction][$argTag];
        }

        // the config/option may look like this: --file|-f:
        $optionsTag = $this->_argFindInOptions( $internalAction, $argTag, $action );

        // Action argTag already handeld?
        // - untag finished before and set if found
        // - fifo: if not already done check required flag/arg now
        if ( ! isset( $results[$internalAction][$argTag] ) ) {
            // test for a '-f:' or '--file|-f:' in options for required tag
            $argRequired = $this->_argIsReqired( $optionsTag );

            if ( $argRequired ) {
                // $resultValue  (from -x=y) if set or next $argValue must be
                if ( isset( $resultValue ) ) {
                    $results[$internalAction][$argTag] = $resultValue;

                } else {
                    // next argValue must be

                    if ( isset( $this->_argv[$argPos + 1] )
                        && isset( $this->_argv[$argPos + 1][0] )
/** @todo a value can contain e.g: "-00:00..." work this was: --key="-00" not that way: --key "-00" */
                        && $this->_argv[$argPos + 1][0] != '-'
                    ) {
                        // count up for next usage? no the sequence ends. next loop...
                        $results[$internalAction][$argTag] = $this->_argv[$argPos + 1];
                    } else {
                        $mesg = sprintf(
                            'Missing or invalid value for parameter "%1$s" in action "%2$s"',
                            $optionsTag, $action
                        );
                        throw new Mumsys_GetOpts_Exception( $mesg );
                    }
                }
            } else {
                $results[$internalAction][$argTag] = true; // a flag
            }
        } // end Action argTag already handeld

        return $results;
    }


    /**
     * Returns the tag to disable it or false if the tag was not found.
     *
     * @param string $action Current action
     * @param string $argTag Argument tag
     *
     * @return string|false argTag of option including short or long prefix otherwise false
     */
    private function _argIsUntag( string $action, string $argTag ): string|false
    {
        $test = substr( $argTag, 5, strlen( $argTag ) );
        if ( strlen( $test ) === 1 ) {
            $unTag = '-' . $test;
        } else {
            $unTag = '--' . $test;
        }

        if ( isset( $this->_mapping[$action][$unTag] ) ) {
            return $unTag;
        }

        return false;
    }


    /**
     * Checks if optionTag (e.g: argument combination -f|--file:) contains the
     * required flag (:).
     *
     * @todo benchmark strpos( $optionsTag, ':' ) vs last char or known position of a : ?
     *
     * @param string $optionsTag Options tag to test for
     *
     * @return bool True if is required or fals if not
     */
    private function _argIsReqired( string $optionsTag ): bool
    {
        if ( strpos( $optionsTag, ':' ) !== false ) {
            // has a colon next argv should have the value
            return true;
        }

        return false;
    }


    /**
     * Returns the first match of argTag in the options/ configuration.
     *
     * If using also actions and the same global and action option exists,
     * e.g --help, the actions --help will be used first.
     * Action help used: progCall.php action1 --help
     * Global help used: progCall.php --help action1 --help
     *
     * @param string $action Current action of the parse in use
     * @param string $argTag Current argument tag
     * @param string $realAction internal action the argTag was found (a
     * _default_ arg inside an action given)
     *
     * @return string The key of the options array to work with for the action
     * @throws Mumsys_GetOpts_Exception If option key could not be found
     */
    private function _argFindInOptions( string $action, string $argTag,
        string $realAction ): string
    {
        foreach ( (array)$this->_options[$action] as $optKey => &$optVal ) {
        //foreach ( $this->_options[$action] as $optKey => &$optVal ) {

            if ( is_int( $optKey ) !== false && is_string( $optVal ) ) {
                $optKey = $optVal;
            }
            /** @var string $optKey 4SCA */

            // replace required flag for fast exact match
            $optKeyReplaced = str_replace( ':', '', $optKey );
            if ( $optKeyReplaced === $argTag ) {
                // single tag but required
                return $optKey;
            }

            if ( strpos( $optKeyReplaced, '|' ) !== false ) {
                // aliased options

                $list = explode( '|', $optKeyReplaced );
                foreach ( $list as $_optTag ) {
                    if ( $_optTag === $argTag ) {
                        return $optKey;
                    }
                }
            }

            // next option to check...
        }

        // @codeCoverageIgnoreStart
        //
        // This can come up with negativ array keys in options and mapping and
        // given args which are casted to int's internally by php parser
        // Not tested because wont work but will throw the exception here!

        $optionalAction = '';
        if ( $realAction !== $action ) {
            $optionalAction = ' (action: "' . $realAction . '")';
        }
        $mesg = sprintf(
            'IF YOU SEE THIS PLEASE SEND config/options AND input to have '
            . ' tests. ' . PHP_EOL .
            'Keys with negativ int\'s in options already known. wont fix! '
            . PHP_EOL .
            'Tag in mapper found for action: "%1$s" but not the option for '
            . 'argTag: "%2$s"%3$s',
            $action, $argTag, $optionalAction
        );
        throw new Mumsys_GetOpts_Exception( $mesg );
        // @codeCoverageIgnoreEnd
    }


    /**
     * Checks, verfiy and build the structure from incomming options for the parser.
     *
     * @param array<int|string, string|array<string|int,string>> $config Configuration
     * to check for actions and validity
     *
     * @throws Mumsys_GetOpts_Exception On errors with the input
     */
    private function _verifyOptions( array $config ): void
    {
        // arguments to be checked as global options
        // all the following as: action [action options]...
        $options = array();
        // -9 opt wont work. use --9 opt to be string and valid for options and mapping
        foreach ( $config as $key => $value ) {
            if ( is_int( $key )
                && is_string( $value )
                && isset( $value[0] )
                && $value[0] === '-' ) {

                // 4SCA
                if ( !isset( $options['_default_'] ) || !is_array( $options['_default_'] ) ) {
                    $options['_default_'] = array();
                }
                // 0 => --file
                $options['_default_'][$value] = 'No description';

            } else if ( is_int( $key )
                && is_string( $value )
                && isset( $value[0] )
                && $value[0] !== '-' ) {

                // 0 => action1
                $options[$value] = array(); // arg action

            } else if ( is_string( $key )
                && isset( $key[0] )
                && $key[0] === '-'
                && is_string( $value ) ) {

                // 4SCA
                if ( !isset( $options['_default_'] ) || !is_array( $options['_default_'] ) ) {
                    $options['_default_'] = array();
                }
                // --file => description
                $options['_default_'][$key] = $value; // arg key/desc pair

            } else if ( is_string( $key )
                && isset( $key[0] )
                && $key[0] !== '-'
                && ( is_string( $value ) || is_array( $value ) ) ) {

                // action1 => description or action1 => array(...)
                $options[$key] = $value;

            } else {
                // Other invalid
                $mesg = sprintf(
                    'Invalid input config found for key "%1$s", value (json): "%2$s"',
                    $key, json_encode( $value )
                );
                throw new Mumsys_GetOpts_Exception( $mesg );
            }
        }

        // finalise: _default_/ globals to the top to be handeld first!
        if ( isset( $options['_default_'] ) && count( $options ) > 1 ) {
            $tmp = array();
            $tmp['_default_'] = $options['_default_'];
            unset( $options['_default_'] );
            $return = array_merge( $tmp, $options );
        } else {
            $return = & $options;
        }

        $this->_options = $return;
    }


    /**
     * Returns the list of key/value pairs of the input parameters without
     * "-" and "--" from the cmd line.
     *
     * @return array<string, array<string, scalar>|scalar> List of key/value pair from
     * incoming cmd line.
     */
    public function getResult(): array
    {
        if ( $this->_resultCache && $this->_isModified === false ) {
            return $this->_resultCache;
        } else {
            $result = array();
            foreach ( $this->_rawResult as $action => $params ) {
                // prepare action maybe with empty action
                if ( $action != '_default_' ) {
                    $result[$action] = array();
                }
                foreach ( $params as $key => $value ) {
                    // drop - and -- from keys
                    if ( isset( $key[1] ) && $key[1] == '-' ) {
                        $num = 2;
                    } else {
                        $num = 1;
                    }
                    if ( $action === '_default_' ) {
                        $result[substr( $key, $num )] = $value;
                    } else {
                        /** @var array<string, array<string, scalar>> $result 4SCA */
                        $result[$action][substr( $key, $num )] = $value;
                    }
                }
            }

            $this->_resultCache = $result;
            $this->_isModified = false;

            return $result;
        }
    }


    /**
     * Returns help/ parameter informations by given options on initialisation.
     *
     * @return string Help informations
     */
    public function getHelp(): string
    {
        $str = '';
        $tab = '';

        if ( isset( $this->_options['_default_'] ) && $this->_hasActionsInOpts === false ) {
            $str .= 'Global options/ information:' . PHP_EOL . PHP_EOL;
        } else if ( isset( $this->_options['_default_'] ) && $this->_hasActionsInOpts ) {
            $str .= 'Global options/ Actions and options/ information:' . PHP_EOL . PHP_EOL;
        } else {
            $str .= 'Actions and options/ information:' . PHP_EOL . PHP_EOL;
        }

        foreach ( $this->_options as $action => $values ) {
            if ( $action !== '_default_' ) {
                $str .= "" . $action . '' . PHP_EOL;
                $tab = "    "; // as 4 spaces
            }

            if ( is_string( $values ) ) {
                // a action desc only given?
                $str .= $tab . $this->_wordwrapHelp( $values, 76, PHP_EOL . $tab ) . PHP_EOL;

            } else if ( is_array( $values ) && empty( $values ) ) {
                // empty action desc: 'No description'
                $str .= $tab . "    " . 'No description'; // pre set if empty

            } else {
                foreach ( $values as $k => $v ) {
                    if ( is_string( $k ) ) {
                        $option = $k;
                        // std desc
                        $desc = $v;
                    } else {
                        $option = $v;
                        $desc = 'No description';
                    }

                    $needvalue = strpos( $option, ':' );
                    $option = str_replace( ':', '', $option );

                    if ( $needvalue ) {
                        $option .= ' <yourValue/s>';
                    }

                    if ( $desc ) {
                        $desc = PHP_EOL . $tab . "    "
                            . $this->_wordwrapHelp( $desc, 76, PHP_EOL . "    " . $tab );
                    }

                    $str .= $tab . $option . $desc . '' . PHP_EOL;
                }
            }
            $str = trim( $str ) . PHP_EOL . PHP_EOL;
        }

        return $str;
    }


    /**
     * Returns help/ parameter informations by given options on initialisation
     * including usage informations.
     *
     * @return string Long help informations
     */
    public function getHelpLong(): string
    {
        $string = <<<TEXT
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


TEXT;
        return $string . $this->getHelp();
    }


    /**
     * Returns the validated string of incoming arguments.
     *
     * @return string Argument string
     */
    public function getCmd(): string
    {
        $parts = '';
        $cmd = sprintf( '%1$s ', $this->_argv[0] );

        foreach ( $this->_rawResult as $action => $values ) {
            if ( $action != '_default_' ) {
                $parts .= $action . ' ';
            }

            foreach ( $values as $k => $v ) {
                if ( is_bool( $v ) ) {
                    if ( $v === false ) {
                        $parts .= '--no'
                            . str_replace( '--', '-', $this->_mapping[$action][$k] )
                            . ' '
                        ;
                    } else {
                        $parts .= $k . ' ';
                    }
                } else {
                    $parts .= sprintf( '%1$s %2$s ', $k, $v );
                }
            }
        }

        $this->_cmd = $cmd . trim( $parts );

        return $this->_cmd;
    }


    /**
     * Return the list of actions and list of key/value pairs the parser accepted.
     *
     * @return array<string, array<string, scalar>> List of key/value pairs of
     * the incoming parameters
     */
    public function getRawData(): array
    {
        return $this->_rawResult;
    }


    /**
     * Returns the raw input.
     *
     * @return array<string> Returns the given input array or _SERVER['argv'] array
     */
    public function getRawInput(): array
    {
        return $this->_argv;
    }


    /**
     * Free/ cleanup collected, calculated results to generate them new. Given
     * default/ setup data will be still available.
     */
    public function resetResults(): void
    {
        $this->_isModified = false;
        $this->_resultCache = array();
        $this->_mapping = array();
        $this->_rawResult = array();
    }


    /**
     * Returns the internal options after verifyOptions().
     *
     * @return array<string, string|array<string|int, string>> The internal
     * options after _verifyOptions()
     */
    public function getOptions(): array
    {
        return $this->_options;
    }


    /**
     * Returns the mapping of short and long options.
     *
     * @return array<string, string|array<string>> List of key value pairs where
     * the key is the option and the value the target to map to it.
     */
    public function getMapping(): array
    {
        return $this->_mapping;
    }


    /**
     * Sets the mapping of options if several short and long options exists.
     *
     * @param array<string, string|array<string|int, string>> $options List of
     * incoming options
     */
    private function _generateMappingOptions( array $options = array() ): void
    {
        $mapping = array();

        foreach ( $options as $action => $values ) {
            if ( is_string( $values ) || empty( $values ) ) {
                $mapping[$action] = array(); //ign desc here, no params, no action mapping
                continue;
            }

            foreach ( $values as $opkey => $opValue ) {
                if ( is_string( $opkey ) ) {
                    $opValue = $opkey;
                }

                $opValue = str_replace( ':', '', $opValue );

                $parts = explode( '|', $opValue );

                if ( isset( $parts[1] ) ) {
                    if ( strlen( $parts[0] ) > strlen( $parts[1] ) ) {
                        $_key = 0;
                    } else {
                        $_key = 1;
                    }

                    $mapping[$action][$parts[0]] = $parts[$_key];
                    $mapping[$action][$parts[1]] = $parts[$_key];
                } else {
                    $mapping[$action][$parts[0]] = $parts[0];
                }
            }
        }

        if ( count( $mapping ) > 1 ) {
            $this->_hasActionsInOpts = true;
        } else {
            $this->_hasActionsInOpts = false;
        }

        $this->_mapping = $mapping;
    }


    /**
     * Wordwrap with lineending checks to stay in format of the original text.
     *
     * @param string $text The text to wrap
     * @param int $with With/ count columns
     * @param string $eol Lineending e.g: PHP_EOL
     *
     * @return string The wraped string
     */
    private function _wordwrapHelp( string $text, int $with, string $eol ): string
    {
        $list = explode( PHP_EOL, $text );
        $result = '';
        foreach ( $list as $value ) {
            $result .= wordwrap( $value, $with, $eol ) . $eol;
        }

        return $result;
    }


    /**
     * Prints the help message.
     */
    public function __toString(): string
    {
        return $this->getHelp();
    }

}
