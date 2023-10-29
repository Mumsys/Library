<?php

/**
 * Mumsys_Logger_File
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
/* }}} */


/**
 * Class to generate log messages to a logfile.
 *
 * @uses Mumsys_File As writer interface if none given on construction.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 */
class Mumsys_Logger_File
    extends Mumsys_Logger_Abstract
    implements Mumsys_Logger_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '3.0.4';

    /**
     * path and filename to the log file.
     * @var string
     */
    protected $_logfile;

    /**
     * Type of ways to log a message. Default is a fopen "a" (add/ append).
     *
     * @var string
     */
    protected $_logway;

    /**
     * Number of bytes for a logfile befor the contents it will be cleaned.
     * If zero (0): no limit.
     * If $_debug is enabled $_maxfilesize will not take affect.
     *
     * @var integer
     */
    protected $_maxfilesize = 0;

    /**
     * Component to write log messages to e.g. a file or database.
     *
     * @var Mumsys_Logger_Writer_Interface
     */
    protected $_writer;

    /**
     * Initialize the logger file object
     *
     * @param array $options Associativ array with additional params
     * - [logfile] string Location of the logfile; optional, if not set
     *      logs will be stored to /tmp/ ! Make sure you have access to it.
     * - [way] string Default: fopen "a"
     * - [username] optional otherwise PHP_AUTH_USER will be taken
     * - [lineFormat] optional format of log line;see $_logformat.
     * - [timeFormat] optional format of a timestamp format
     * - [logLevel] integer Optional Number of the loglevel Default: 7 (debug
     * mode, log all)
     * - [debug] boolean Default: false
     * - [lf] string Optional Linefeed Default: \n
     * - [maxfilesize] integer Optional Number of Bytes for the logfile Default:
     *   0 (no limit)
     * @param Mumsys_Logger_Writer_Interface $writer Writer interface
     *
     * @uses Mumsys_File Uses Mumsys_File object for file logging if $writer not set
     */
    public function __construct( array $options = array(),
        Mumsys_Logger_Writer_Interface $writer = null )
    {
        if ( empty( $options['logfile'] ) ) {
            $this->_logfile = '/tmp/'
                . basename( __FILE__ ) . '_'
                . date( 'Y-m-d', time() );
        } else {
            $this->_logfile = $options['logfile'];
        }

        if ( empty( $options['way'] ) ) {
            $this->_logway = $options['way'] = 'a';
        } else {
            $this->_logway = (string) $options['way'];
        }

        if ( isset( $options['maxfilesize'] ) ) {
            $this->_maxfilesize = $options['maxfilesize'];
        }

        if ( $writer === null ) {
            $fileOptions = array(
                'file' => $this->_logfile,
                'way' => $this->_logway
            );
            $this->_writer = new Mumsys_File( $fileOptions );
        } else {
            $this->_writer = $writer;
        }

        parent::__construct( $options );

        if ( ( $message = $this->checkMaxFilesize() ) !== '' ) {
            $this->log( $message, Mumsys_Logger_Abstract::INFO );
        }
    }


    /**
     * Create a log entry by a given log level.
     *
     * 0 EMERG    emerg()   System is unusable
     * 1 ALERT    alert()   Immediate action required
     * 2 CRIT     crit()    Critical conditions
     * 3 ERR      err()     Error conditions
     * 4 WARN     warn()    Warn conditions
     * 5 NOTICE   notice()  Normal but significant
     * 6 INFO     info()    Informational
     * 7 DEBUG    debug()   Debug-level messages
     *
     * @param string|array $input Message or list of messages to be logged
     * @param integer $level Level number of log priority
     *
     * @return string Returns the formated log message string
     */
    public function log( $input, $level = 0 )
    {
        try
        {
            $datesting = date( $this->_timeFormat, time() );
            $levelName = $this->getLevelName( $level );

            if ( !is_scalar( $input ) ) {
                $input = json_encode( $input );
            }

            $message = sprintf(
                $this->_logFormat,
                $datesting,
                $this->_username,
                $levelName,
                $level,
                $input
            );

            $message .= $this->_lf;

            if ( $level <= $this->_logLevel || $this->_debug ) {
                $this->_writer->write( $message );
            }
        }
        catch ( Exception $e ) {
            throw $e;
        }

        return $message;
    }


    /**
     * Return the logfile property, the location of the logfile.
     *
     * @return string Location of the logfile
     */
    public function getLogFile()
    {
        return $this->_logfile;
    }


    /**
     * Checks if the max filesize reached and drops the logfile.
     *
     * If debug mode is enabled this methode will return '' if
     * maxfilesize <= 0.
     *
     * @return string Returns string with information that the log was
     * purged or empty string.
     */
    public function checkMaxFilesize()
    {
        $message = '';

        if ( $this->_maxfilesize <= 0 || $this->_debug ) {
            return $message;
        }

        if ( ( $fsize = filesize( $this->_logfile ) ) > $this->_maxfilesize ) {
            $this->_writer->truncate();
            $message = sprintf(
                'Max filesize (%1$s Bytes) reached. Log purged now',
                $this->_maxfilesize
            );
        }

        return $message;
    }

}
