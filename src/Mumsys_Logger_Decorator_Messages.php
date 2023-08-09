<?php

/**
 * Mumsys_Logger_Decorator_Messages
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2005 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 */


/**
 * Messages decorator for the logger.
 *
 * This one extends the logger to also output messages to stdout.
 *
 * You may also use the colors for the shell output.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 */
class Mumsys_Logger_Decorator_Messages
    extends Mumsys_Logger_Decorator_Abstract
    implements Mumsys_Logger_Decorator_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '3.0.0';

    /**
     * Username for each log entry
     * This can be useful if several processes will log to the same storage.
     *
     * @var string
     */
    private $_username;

    /**
     * Level to output log messages
     *
     * @var integer
     */
    private $_msgLogLevel = 6;

    /**
     * Format of a log message for the output. (When printing log messages
     * directly to stdout).
     *
     * Default:
     *  1 = dateformat string
     *  2 = username
     *  3 = name of the log level
     *  4 = id of the log level
     *  5 = the message
     *
     * E.g: "[%3$s] %5$s"
     *
     * @var string
     */
    private $_logFormatMsg = '%1$s [%2$s] [%3$s](%4$s) %5$s';

    /**
     * Format for the date/time string in logmessages.
     * @see http://php.net/date function for details.
     *
     * @var string
     */
    private $_timeFormat = 'Y-m-d H:i:s';

    /**
     * Flag to enable console colors for the output string or not. default: false
     * @var boolean
     */
    private $_msgColors = false;

    /**
     * Color setup for level 0-7, default color
     * @var array
     */
    private $_colors = array(
        0 => '[41m', //Red background
        1 => '[41m',
        2 => '[41m',
        3 => '[41m',
        4 => '[43m', // Yellow or orange background (term related)
        5 => '[43m',
        6 => '[42m', // Green background
        7 => '[44m', // Blue background
        -1 => '[7m', // invert white bg, black text
    );

    /**
     * Flag to enable debugging or not.
     * Debug mode will print out all log messages.
     *
     * @var boolean Default: true
     */
    private $_debug;

    /**
     * Linefeed sign to make a new line after a log entry (on files)
     *
     * @var string
     */
    private $_lf = "\n";


    /**
     * Initialize the decorator messages logger object
     *
     * @param Mumsys_Logger_Interface $logger Logger object to be decorated
     * @param array $options List of options to be set on construction:
     *  - [username] optional otherwise PHP_AUTH_USER will be taken
     *  - [msgDatetimeFormat] optional format of a timestamp format
     *  - [msglogLevel] integer Optional Message log level for messages which
     *      should be printed (if msgEcho=true)
     *  - [msgLineFormat] optional Output format which should be printed (if
     *    msgEcho=true)
     *  - [msgColors] optional, Enable console colors for the "messages" only
     *    (not the hole string)
     *  - [debug] boolean Default: false
     *  - [lf] string Optional Linefeed Default: \n
     */
    public function __construct( Mumsys_Logger_Interface $logger,
        array $options = array() )
    {
        parent::__construct( $logger );

        if ( empty( $options['username'] ) ) {
            $this->_username = Mumsys_Php_Globals::getRemoteUser();
        } else {
            $this->_username = $options['username'];
        }

        if ( isset( $options['msglogLevel'] ) ) {
            $this->_msgLogLevel = $options['msglogLevel'];
        }

        if ( isset( $options['msgLineFormat'] ) ) {
            $this->_logFormatMsg = (string) $options['msgLineFormat'];
        }

        if ( isset( $options['msgDatetimeFormat'] ) ) {
            $this->_timeFormat = (string) $options['msgDatetimeFormat'];
        }

        if ( isset( $options['msgColors'] ) ) {
            $this->_msgColors = (bool) $options['msgColors'];
        }

        if ( isset( $options['debug'] ) ) {
            $this->_debug = (bool) $options['debug'];
        }

        if ( isset( $options['lf'] ) ) {
            $this->_lf = $options['lf'];
        }
    }


    /**
     * Clones the object.
     */
    public function __clone()
    {
        $this->_object = clone $this->_object;
    }


    /**
     * Create a log entry by a given message and log level.
     *
     * Levels are:
     * 0 EMERG    emerg()   System is unusable
     * 1 ALERT    alert()   Immediate action required
     * 2 CRIT     crit()    Critical conditions
     * 3 ERR      err()     Error conditions
     * 4 WARN     warn()    Warn conditions
     * 5 NOTICE   notice()  Normal but significant
     * 6 INFO     info()    Informational
     * 7 DEBUG    debug()   Debug-level messages
     *
     * @param string|array $input Message or list of messages to log
     * @param integer $level Level number of log priority
     *
     * @return string Returns the log message of the base class
     */
    public function log( $input, $level = 0 )
    {
        if ( $level <= $this->_msgLogLevel || $this->_debug ) {
            if ( !is_scalar( $input ) ) {
                $input = json_encode( $input );
            }

            $datesting = '';
            if ( !empty( $this->_timeFormat ) ) {
                $datesting = date( $this->_timeFormat, time() );
            }

            $msgOut = sprintf(
                $this->_logFormatMsg,
                $datesting,
                $this->_username,
                $this->getLevelName( $level ),
                $level,
                $input
            );

            if ( $this->_msgColors ) {
                $msgOut = $this->getMessageColored( $msgOut, $level );
            }

            $msgOut .= $this->_lf;

            echo $msgOut;
        }

        return parent::log( $input, $level );
    }


    /**
     * Returns a colorised message string for the shell output.
     *
     * @param string $message The log message
     * @param integer $level Level number of log priority
     *
     * @return string String including characters for the shell output which
     * makes text colored
     */
    public function getMessageColored( $message, $level = 0 )
    {
        $chr27 = chr( 27 ); // escape sequence

        switch ( $level )
        {
            case Mumsys_Logger_Abstract::EMERG:
                $color = $this->_colors[0];
                break;

            case Mumsys_Logger_Abstract::ALERT:
                $color = $this->_colors[1];
                break;

            case Mumsys_Logger_Abstract::ERR:
                $color = $this->_colors[2];
                break;

            case Mumsys_Logger_Abstract::CRIT:
                $color = $this->_colors[3];
                break;

            case Mumsys_Logger_Abstract::WARN:
                $color = $this->_colors[4];
                break;

            case Mumsys_Logger_Abstract::NOTICE:
                $color = $this->_colors[5];
                break;

            case Mumsys_Logger_Abstract::INFO:
                $color = $this->_colors[6];
                break;

            case Mumsys_Logger_Abstract::DEBUG:
                $color = $this->_colors[7];
                break;

            default:
                if ( isset( $this->_colors[$level] ) ) {
                    $color = $this->_colors[$level];
                } else {
                    $color = $this->_colors[-1];
                }
                break;
        }

        return sprintf( '%1$s%2$s%3$s%1$s[0m', $chr27, $color, $message );
    }


    /**
     * Sets the message log format.
     *
     * Default:
     *  1 = dateformat string
     *  2 = username
     *  3 = name of the log level
     *  4 = id of the log level
     *  5 = the message
     *
     * E.g: "[%3$s] %5$s"
     *
     * @param string $format Substitution parameters for the log message
     */
    public function setMessageLogFormat( $format = '%1$s [%2$s] [%3$s](%4$s) %5$s' )
    {
        $this->_logFormatMsg = (string) $format;
    }


    /**
     * Sets the new message log level to react from now on (0 - 7).
     *
     * @param integer $level Log level to set
     * @throws Mumsys_Logger_Exception If level is unknown
     */
    public function setMessageLoglevel( $level )
    {
        // @phpstan-ignore-next-line
        if ( $this->_getObject()->checkLevel( $level ) === false ) {
            $message = 'Level "' . $level . '" unknown to set the message log level';
            throw new Mumsys_Logger_Exception( $message );
        }

        $this->_msgLogLevel = (int) $level;
    }


    /**
     * Returns the list of loglevel/shellcolor confgurations.
     *
     * @return array List of console colors for each existing level
     */
    public function getColors()
    {
        return $this->_colors;
    }


    /**
     * Sets/ replaces the list of loglevel/console colors.
     *
     * @param array $colors List of loglevel/console color pairs
     */
    public function setColors( array $colors )
    {
        $this->_colors = $colors;
    }

}
