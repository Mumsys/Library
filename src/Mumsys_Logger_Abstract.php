<?php

/**
 * Mumsys_Logger_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2005 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 */


/**
 * Abstract class to generate log messages.
 *
 * @uses Mumsys_File Writer class
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 */
abstract class Mumsys_Logger_Abstract
    extends Mumsys_Abstract
    implements Mumsys_Logger_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '3.3.1';

    /**
     * System is unusable emerg()
     */
    const EMERG = 0;

    /**
     * Immediate action required alert()
     */
    const ALERT = 1;

    /**
     * Critical conditions crit()
     */
    const CRIT = 2;

    /**
     * Error conditions err()
     */
    const ERR = 3;

    /**
     * Warning conditions warn()
     */
    const WARN = 4;

    /**
     * Normal but significant notice()
     */
    const NOTICE = 5;

    /**
     * Informational info()
     */
    const INFO = 6;

    /**
     * Debug-level messages debug()
     */
    const DEBUG = 7;

    /**
     * Current log level to log
     *
     * Levels higher than $logLevel will be ignored; range 0-7 by default
     *
     * @var integer
     */
    protected $_logLevel = 7;  // log everything to a file

    /**
     * Available log levels.
     *
     * List of key/value pairs where the key ist this log level and the value
     * the name of the log level.
     *
     * @var array
     */
    private $_loglevels;

    /**
     * Format for the date/time string in logmessages.
     * @see http://php.net/date function for details.
     * Default: 'Y-m-d H:i:s'
     * @var string
     */
    protected $_timeFormat;

    /**
     * Format of a log message.
     * Default:
     *  1 = dateformat string (see "timeFormat" in construction)
     *  2 = username
     *  3 = name of the log level
     *  4 = id of the log level
     *  5 = the message
     *
     * @var string
     */
    protected $_logFormat = '%1$s [%2$s] [%3$s](%4$s) %5$s';

    /**
     * Flag to enable debugging or not.
     * Debug mode will print out all log messages.
     *
     * @var boolean Default: true
     */
    protected $_debug;

    /**
     * Username for each log entry
     * This can be useful if several processes will log to the same storage.
     *
     * @var string
     */
    protected $_username;

    /**
     * Linefeed sign to make a new line after a log entry (on files)
     *
     * @var string
     */
    protected $_lf = "\n";

    /**
     * Internal counter.
     *
     * @var integer
     */
    protected $_cnt = 0;


    /**
     * Initialize the logger object
     *
     * @uses Mumsys_Php_Globals Class of the mumsys library to get the remote
     * user if not set
     *
     * @param array $options Associativ array with additional params
     * - [username] optional otherwise PHP_AUTH_USER will be taken
     * - [lineFormat] optional format of log line;see $_logformat.
     * - [timeFormat] optional format of a timestamp format
     * - [logLevel] integer Optional Number of the loglevel
     *  Default: 7 (debug mode, log all)
     * - [debug] boolean Default: false
     * - [lf] string Optional Linefeed Default: \n
     */
    public function __construct( array $options = array() )
    {
        if ( !isset( $options['username'] ) ) {
            $this->_username = Mumsys_Php_Globals::getRemoteUser();
        } else {
            $this->_username = $options['username'];
        }

        if ( isset( $options['lineFormat'] ) ) {
            $this->_logFormat = (string) $options['lineFormat'];
            if ( empty( $this->_logFormat ) ) {
                throw new Mumsys_Logger_Exception( 'Log format empty' );
            }
        }

        if ( isset( $options['timeFormat'] ) ) {
            $this->_timeFormat = (string) $options['timeFormat'];
        } else {
            $this->_timeFormat = 'Y-m-d H:i:s';
        }

        if ( isset( $options['logLevel'] ) ) {
            $this->_logLevel = $options['logLevel'];
        }

        if ( isset( $options['debug'] ) ) {
            $this->_debug = $options['debug'];
        }

        if ( isset( $options['lf'] ) ) {
            $this->_lf = $options['lf'];
        }

        $this->_loglevels = array(
            self::EMERG => 'EMERG',
            self::ALERT => 'ALERT',
            self::CRIT => 'CRIT',
            self::ERR => 'ERR',
            self::WARN => 'WARN',
            self::NOTICE => 'NOTICE',
            self::INFO => 'INFO',
            self::DEBUG => 'DEBUG',
        );
    }


    /**
     * Alias wrapper to extra methode calls.
     *
     * Implements calls: emerge(), emergency(), alert(), crit(), critical(),
     * err() error(), warn(), warning(), notice(), info(), debug().
     *
     * Dont use it if you can (performace). Just compatibilty to psr.
     *
     * @param string $key Methode string to wrap to
     * @param string $values Log message value
     *
     * @return string Log message
     * @throws Mumsys_Logger_Exception if key not implemented
     */
    public function __call( $key, $values )
    {
        $level = null;

        switch ( strtolower( $key ) )
        {
            case 'emerg':
            case 'emergency':
                $level = 0;
                break;

            case 'alert':
                $level = 1;
                break;

            case 'crit':
            case 'critical':
                $level = 2;
                break;

            case 'err':
            case 'error':
                $level = 3;
                break;

            case 'warn':
            case 'warning':
                $level = 4;
                break;

            case 'notice':
                $level = 5;
                break;

            case 'info':
                $level = 6;
                break;

            case 'debug':
                $level = 7;
                break;

            default:
                $message = sprintf( 'Invalid method call: "%1$s"', $key );
                throw new Mumsys_Logger_Exception( $message );
        }

        if ( count( $values ) == 1 ) {
            $_value = $values[0];
        } else {
            $_value = $values;
        }

        return $this->log( $_value, $level );
    }


    /**
     * Sets the new message log format.
     *
     * Format of a log message.
     * Default:
     *  1 = dateformat string
     *  2 = username
     *  3 = name of the log level
     *  4 = id of the log level
     *  5 = the message
     *
     * @param string $format Log format parameters to be replaced
     */
    public function setLogFormat( $format = '%1$s [%2$s] [%3$s](%4$s) %5$s' )
    {
        $this->_logFormat = (string) $format;
    }


    /**
     * Sets the new log level to react from now on (0 - 7).
     *
     * @param integer $level Log level to set
     *
     * @throws Mumsys_Logger_Exception If level is unknown
     */
    public function setLoglevel( $level )
    {
        if ( $this->checkLevel( $level ) === false ) {
            $message = 'Log level "' . $level . '" unknown. Can not set';
            throw new Mumsys_Logger_Exception( $message );
        }

        $this->_logLevel = (int) $level;
    }


    /**
     * Checks if a loglevel is registered or not
     *
     * @param integer $level Log level to be checked
     *
     * @return boolean Returns true for OK otherwise false
     */
    public function checkLevel( $level = 0 )
    {
        if ( isset( $this->_loglevels[$level] ) ) {
            return true;
        }

        return false;
    }


    /**
     * Get the name of a loglevel.
     *
     * @param integer $level Nuber of the Log level
     * @return string Returns the string of the errorlevel
     */
    public function getLevelName( $level )
    {
        if ( !isset( $this->_loglevels[$level] ) ) {
            return 'unknown';
        }

        return $this->_loglevels[$level];
    }

}
