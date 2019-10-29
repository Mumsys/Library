<?php

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


/**
 * Class to handle/ pipe shell arguments in php context.
 *
 * Shell arguments will be parsed and an array list of key/value pairs will be
 * created.
 * When using long and shot options the long options will be used and the short
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
 * @todo global config parameters like "help", "version" or "cron" ?
 * @todo Actions groups must be validated, extend whitelist configuration
 * @todo 2019-04-30 eg a --power flag is set:
 *      'power' => true
 * invalidate:
 *      --power --no-power:
 *      'power' => false
 * not set:
 *      [] not set
 * power is boolean but not a mix of bool or unset!?
 *
 * @todo 2019-05-14 Several actions without options not implemented! ? run.php act1 act2 ...
 *
 * Example:
 * <code>
 * // Simple usage:
 * // Parameter options: A list of options or a list of key value pairs where the
 * // value contains help/ usage informations.
 * // The colon at the end of an option shows that an input must
 * // follow. Otherwise is will be handled as flag (boolean set or not set) and
 * // if this do not match an error will be thrown.
 * $paramerterOptions = array(
 *  '--cmd:',        // A required value; No help message or:
 *  '--program:' => 'optional: your help/ usage information as value',
 *  '--pathstart:', => 'Path where your files are'
 *  '--delsource'   // optional
 * );
 * // Advanced usage (including several actions like):
 * // e.g.: action1 --param1 val1 --param2 val2 action2 --param1 val1 ...
 * $paramerterOptions = array(
 *    'action1' => array(
 *        '--param1:', // No help message or:
 *        '--param2:' => 'optional: your usage information as array value',
 *    'action2' => array(
 *        '--param1:', => 'Path where your files are',
 *        // ...
 * );
 *
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
 * //      'program'=> 'programvalue',
 * //      'pathstart'=>'pathstartvalue',
 * //      'delsource' => true // as boolean true
 * // In advanced setup it will return something like this:
 * // $programOptions = array(
 * //      'action1' => array(
 * //           'program'=> 'programvalue',
 * //           'pathstart'=>'pathstartvalue',
 * //      'action2' => array( ...
 * </code>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  GetOpts
 */
class Mumsys_GetOpts
    extends Mumsys_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '3.6.1';

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
     * @var array
     */
    private $_options;

    /**
     * Mapping for short and long options.
     * @var array
     */
    private $_mapping;

    /**
     * List (key value pairs) of all parameter which are in the whitelist.
     * @var array
     */
    private $_result;

    /**
     * List (key value pairs) of all parameter without - and -- parameter
     * prefixes
     * @var array
     */
    private $_resultCache;

    /**
     * List of argument values (argv)
     * @var array
     */
    private $_argv;

    /**
     * Argument count.
     * @var integer
     */
    private $_argc;

    /**
     * Internal flag to deside if action handling will be activated or not.
     * @var boolean
     */
    private $_hasActions;

    /**
     * Internal flag to deside if data has changed so that the results must be
     * created again.
     * @var boolean
     */
    private $_isModified;


    /**
     * Initialise the object and parse incoming parameters.
     *
     * @todo a value can contain a "-" or '--' or -9
     * @todo Some parameters can be required in combination
     *
     * @param array $configOptions List of configuration parameters to look for
     * @param array $input List of input arguments. Optional, uses default input
     * handling then
     *
     * @throws Mumsys_GetOpts_Exception On error initialsing the object
     */
    public function __construct( array $configOptions = array(),
        array $input = null )
    {
        if ( empty( $configOptions ) ) {
            $msg = 'Empty options detected. Can not parse shell arguments';
            throw new Mumsys_GetOpts_Exception( $msg );
        }

        if ( empty( $input ) ) {
            $this->_argv = Mumsys_Php_Globals::getServerServerVar( 'argv', array() );
            $this->_argc = Mumsys_Php_Globals::getServerServerVar( 'argc', 0 );
        } else {
            $this->_argv = $input;
            $this->_argc = count( $input );
        }

        $this->_options = $this->verifyOptions( $configOptions );
        $this->setMappingOptions( $this->_options );

        $this->parse();
    }


    /**
     * Parse parameters to create the result.
     *
     * @throws Mumsys_GetOpts_Exception
     */
    public function parse()
    {
        $argPos = 1; // zero is the calling program
        $argv = & $this->_argv;
        $var = null;
        $return = array();
        $errorMsg = '';
        $errorNotice = '';
        $unflag = array();

        foreach ( $this->_options as $action => $params ) {
            while ( $argPos < $this->_argc ) {
                $arg = $argv[$argPos];

                // new action detect
                if ( isset( $this->_options[$arg] ) && $arg !== $action ) {
                    break;
                }

                // skip values as they are expected in argPos + 1, if any
                if ( isset( $arg[0] ) && $arg[0] == '-' ) {
                    if ( $arg[1] == '-' ) {
                        $argTag = '--' . substr( $arg, 2, strlen( $arg ) );
                    } else {
                        $argTag = '-' . $arg[1]; // take the short flag
                    }

                    if ( isset( $this->_mapping[$action][$argTag] ) ) {
                        $var = $this->_mapping[$action][$argTag];
                    } else {
                        // a --no-FLAG' to unset?
                        $test = substr( $argTag, 5, strlen( $argTag ) );
                        if ( strlen( $test ) == 1 ) {
                            $unTag = '-' . $test;
                            if ( isset( $this->_mapping[$action][$unTag] ) ) {
                                // use the long opt, the short one maps to
                                $unTag = $this->_mapping[$action][$unTag];
                            }
                        } else {
                            $unTag = '--' . $test;
                        }

                        if ( isset( $this->_mapping[$action][$unTag] ) ) {
                            $unflag[$action][] = $unTag;
                        } else {
                            $errorMsg .= sprintf(
                                'Option "%1$s" not found in option list/configuration '
                                . 'for action "%2$s"%3$s',
                                $argTag, $action, PHP_EOL
                            );
                            $argPos++;
                            continue;
                        }
                    }

                    // whitelist check
                    foreach ( $this->_options[$action] as $_opk => $_opv ) {
                        if ( is_string( $_opk ) ) {
                            $_opv = $_opk;
                        }

                        if ( !isset( $return[$action][$var] ) ) {
                            if ( strpos( $_opv, $arg ) !== false ) {
                                if ( strpos( $_opv, ':' ) !== false ) {
                                    if ( isset( $argv[$argPos + 1] )
                                        && isset( $argv[$argPos + 1][0] )
                                        && $argv[$argPos + 1][0] != '-'
                                    ) {
                                        $return[$action][$var] = $argv[++$argPos];
                                    } else {
                                        /* @todo value[1] is a "-" ... missing parameter or is it the value ? */
                                        $errorMsg .= sprintf(
                                            'Missing value for parameter "%1$s" '
                                            . 'in action "%2$s"%3$s',
                                            $var,
                                            $action,
                                            PHP_EOL
                                        );
                                    }
                                } else {
                                    $return[$action][$var] = true;
                                }

                                //unset($this->_options[$_opk]);
                            } else {
                                // ???
                            }
                        } else {
                            // we got it already: was it req and had a value?
                            //echo PHP_EOL . 'xx: ';print_r($argv[$argPos]); print_r($argv[++$argPos]) ;
                            //$argPos+=2;
                            //break;
                        }
                    }
                } else {
                    // action / sub program call or flag detected!
                    //$action = $arg;
//                    $return[$action] = array(); // 2019-05-14: enable shows (some) actions
//                    without options but act in a weired order if order not match the config
                    //throw new Mumsys_GetOpts_Exception('action / sub program call or flag detected' . $arg);
                }

                $argPos++;
            }
        }

        if ( $errorMsg ) {
            //$errorMsg .= PHP_EOL . 'Help: ' . PHP_EOL . $this->getHelp() . PHP_EOL;
            $message = 'Invalid input parameters detected!' . PHP_EOL . $errorMsg;
            throw new Mumsys_GetOpts_Exception( $message );
        }

        if ( $unflag ) {
            foreach ( $unflag as $action => $values ) {
                foreach ( $values as $key => $unTag ) {
                    if ( isset( $return[$action][$unTag] ) ) {
                        $return[$action][$unTag] = false;
                    }
                }
            }
        }

        if ( count( $return ) == 1 ) {
            $this->_hasActions = false;
        } else {
            $this->_hasActions = true;
        }

        $this->_result = $return;
    }


    /**
     * Checks and verfiy incomming options for he parser.
     *
     * @param array $config Configuration to check for actions and validity
     *
     * @return array action/options list to work with internally
     * @throws Mumsys_GetOpts_Exception On errors with the input
     */
    public function verifyOptions( array $config )
    {
        $key = key( $config );

        if ( ( isset( $config[$key] ) && isset( $config[$key][0] ) && $config[$key][0] === '-' )
            || ( $key[0] === '-' && ( is_string( $config[$key] ) || is_bool( $config[$key] ) ) )
        ) {
            $return = array('_default_' => $config);
        } else if ( isset( $config[$key] ) && is_integer( $config[$key] ) ) {
            $message = sprintf(
                'Invalid input config found for key: "%1$s", value: "%2$s"',
                $key,
                $config[$key]
            );
            throw new Mumsys_GetOpts_Exception( $message );
        } else {
            $keys = array_keys( $config );
            if ( is_string( $keys[0] ) && $keys[0][0] != '-' ) {
                $return = $config;
            } else {
                $message = 'Invalid input config found';
                throw new Mumsys_GetOpts_Exception( $message );
            }
        }

        return $return;
    }


    /**
     * Returns the list of key/value pairs of the input parameters without
     * "-" and "--" from the cmd line.
     *
     * @return array List of key/value pair from incoming cmd line.
     */
    public function getResult()
    {
        if ( $this->_resultCache && !$this->_isModified ) {
            return $this->_resultCache;
        } else {
            $result = array();
            foreach ( $this->_result as $action => $params ) {
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

                    $result[$action][substr( $key, $num )] = $value;
                }
            }

            if ( $this->_hasActions ) {
                $this->_resultCache = $result;
            } else {
                if ( isset( $result['_default_'] ) ) {
                    $this->_resultCache = $result['_default_'];
                } else {
                    $this->_resultCache = $result;
                }
                $this->_hasActions = false;
            }

            return $this->_resultCache;
        }
    }


    /**
     * Returns the validated string of incoming arguments.
     *
     * @todo add script to cmd line
     *
     * @return string Argument string
     */
    public function getCmd()
    {
        $parts = '';
        $cmd = '';
        foreach ( $this->_result as $action => $values ) {
            if ( $action != '_default_' ) {
                $parts .= $action . ' ';
            }

            foreach ( $values as $k => $v ) {
//                if ($k === 0) {
//                    continue;
//                }

                if ( $v === false || $v === true ) {
                    foreach ( $this->_options[$action] as $opk => $opv ) {
                        if ( is_string( $opk ) ) {
                            $opv = $opk;
                        }

                        if ( preg_match( '/(' . $k . ')/', $opv ) ) {
                            if ( $v === false ) {
                                $parts .= '--no'
                                    . str_replace( '--', '-', $this->_mapping[$action][$k] )
                                    . ' '
                                ;
                            } else {
                                $parts .= $k . ' ';
                            }
                        }
                    }
                } else {
                    $parts .= sprintf( '%1$s %2$s ', $k, $v );
                }
            }
        }

        $this->_cmd = $cmd . '' . trim( $parts );

        return $this->_cmd;
    }


    /**
     * Returns help/ parameter informations by given options on initialisation.
     *
     * @return string Help informations
     */
    public function getHelp()
    {
        $str = '';
        $tab = '';

        if ( !$this->_hasActions ) {
            $str .= 'Actions/ options/ information:' . PHP_EOL;
        }

        foreach ( $this->_options as $action => $values ) {
            if ( $action !== '_default_' ) {
                $str .= "" . $action . '' . PHP_EOL;
                $tab = "    "; // as 4 spaces
            }

            foreach ( $values as $k => $v ) {
                if ( is_string( $k ) ) {
                    $option = $k;

                    if ( is_bool( $v ) ) {
                        $desc = '';
                    } else {
                        $desc = $v;
                    }
                } else {
                    $option = $v;
                    $desc = '';
                }

                $needvalue = strpos( $option, ':' );
                $option = str_replace( ':', '', $option );

                if ( $needvalue ) {
                    $option .= ' <yourValue/s>';
                }

                if ( $desc ) {
                    $desc = PHP_EOL . $tab . "    "
                        . wordwrap( $desc, 76, PHP_EOL . "    " )
                        . PHP_EOL;
                }

                $str .= $tab . $option . $desc . '' . PHP_EOL;
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
    public function getHelpLong()
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
     * Return the list of actions and list of key/value pairs right after the
     * parser process at construction time.
     *
     * @return array List key/value pais of the incoming parameters
     */
    public function getRawData()
    {
        return $this->_result;
    }


    /**
     * Returns the raw input.
     *
     * @return array Returns the given input array or _SERVER['argv'] array
     */
    public function getRawInput()
    {
        return $this->_argv;
    }


    /**
     * Free/ cleanup collected, calculated results to generate them new. Given
     * default/ setup data will be still available.
     */
    public function resetResults()
    {
        $this->_resultCache = array();
        $this->_mapping = array();
        $this->_result = array();
    }


    /**
     * Returns the mapping of short and long options.
     *
     * @return array List of key value pairs where the key is the option and the
     * value the target to map to it.
     */
    public function getMapping()
    {
        return $this->_mapping;
    }


    /**
     * Sets and returns the mapping of options if several short and long options exists.
     *
     * @param array $options List of incoming options
     *
     * @return array List of key value pair which is the mapping of options
     */
    public function setMappingOptions( array $options = array() )
    {
        $mapping = array();

        foreach ( $options as $action => $values ) {
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

        $this->_mapping = $mapping;
    }


    /**
     * Prints the help message.
     */
    public function __toString()
    {
        return $this->getHelp();
    }

}
