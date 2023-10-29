<?php

/**
 * {{{ Mumsys_Service_Vdr_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2015 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 * @version     1.0.0
 * 0.1 Created: 2015-10-08
 }}} */


/**
 * {{{ Abstract class to deal with svdrpsend command from vdr project (Simple
 * VDR Protocol).
 *
 * SVDRP give you the possibility to run the svdrpsend commands in php context.
 * So it is easy now to work with vdr in php. E.g: Dump the EPG to
 * the mysql database, List channels, recording, epg details and so on.
 *
 * This class implements the basics to connect, disconnect and execute commands
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 *
 * @uses Mumsys_Logger Logger mobejct in context item
 }}} */
abstract class Mumsys_Service_Vdr_Abstract
    extends Mumsys_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.1';

    /**
     * Context item for dependency injection.
     * @var Mumsys_Context_Item
     */
    protected $_context;

    /**
     * Logger object.
     * @var Mumsys_Logger_Interface
     */
    protected $_logger;

    /**
     * Hostname / IP to connect to (default: localhost)
     * @var string
     */
    private $_host;

    /**
     * Port to connect to (default: 6419)
     * @var integer
     */
    private $_port;

    /**
     * Timeout in seconds (default: 5)
     * @var integer
     */
    private $_timeout;

    /**
     * Flag about the connection status
     * @var resource|false
     */
    private $_connection;

    /**
     * List of channel items. Item keys are:
     * 'vdr_id', 'name', 'bouquet', 'frequency', 'parameter', 'source', 'symbolrate',
     * 'VPID', 'APID', 'TPID', 'CAID', 'SID', 'NID', 'TID', 'RID'
     * @link http://vdr-wiki.de/wiki/index.php/Channels.conf VDR Specs
     * @ var array
     */
    //private $_channels = array(); disabled 4SCA

    /**
     * List of times
     * @ var array
     */
    // private $_timers = array(); disabled 4SCA

    /**
     * List of recordings
     * @ var array
     */
    // private $_recordings = array(); disabled 4SCA


    /**
     * Initialise the object.
     *
     * @param Mumsys_Context $context Context object to get the Logger object
     * @param string $host Host or ip to the server. Default: localhost
     * @param integer $port Port to connect to
     * @param integer $timeout
     */
    public function __construct( Mumsys_Context_Item $context,
        $host = 'localhost', $port = 6419, $timeout = 5 )
    {
        $this->_context = $context;

        $this->_logger = $context->getLogger();

        $this->_host = (string) $host;
        $this->_port = (int) $port;
        $this->_timeout = (int) $timeout;
        $this->_connection = false;
        /*
          $config = array(
          'commands' => array(
          //'getchannels' => 'svdrpsend -d ' . $globalConfig['hostname']
              . ' lstc | /usr/bin/cut -f1 -d';' | tr -d \'\r\n\'',
          'getchannels' => 'svdrpsend -d ' . $hostname . ' lstc',
          'gettimers' => 'svdrpsend -d ' . $hostname . ' lstt',
          )

          );
         */

        if ( $this->_host > '' && $this->_port > '' && $this->_timeout > 0 && is_object( $context ) ) {
            $this->connect();
        }
    }


    /**
     * Destuction. Disconnect and reset connection status.
     *
     * @return bool Status of disconnect()
     */
    public function __destruct()
    {
        $this->disconnect();
    }


    /**
     * Connect to the vdr service.
     *
     * @return boolean Returns true on success or connection already exists
     *
     * @throws Mumsys_Service_Exception If connection fails
     */
    public function connect()
    {
        try
        {
            if ( $this->isOpen() ) {
                return true;
            }

            $errno = 0;
            $errstr = '';
            $this->_connection = fsockopen(
                $this->_host, $this->_port, $errno, $errstr, $this->_timeout
            );

            if ( $this->_connection === false ) {
                $message = 'Connection to server "' . $this->_host
                    . '" failt: ' . $errstr . ' (' . $errno . ')';
                $this->_logger->log( $message, 3 );

                throw new Mumsys_Service_Exception( $message, Mumsys_Exception::ERRCODE_500 );
            }

            $this->_logger->log( 'Connection to vdr server: ' . $this->_host, 7 );

            $result = fgets( $this->_connection, 128 );

            if ( empty( $result ) || $result == "timeout\n" || !preg_match( "/^220 /", $result ) ) {
                $message = 'Connection failure. Expected code 220; Result was "' . $result . '"';
                $this->_logger->log( $message, 3 );
                $this->disconnect();

                throw new Mumsys_Service_Exception( $message, 1 );
            }
        }
        catch ( Exception $e ) {
            throw new Mumsys_Service_Exception( $e->getMessage(), 1 );
        }

        return true;
    }


    /**
     * Disconnect and reset reset connection status.
     *
     * @return boolean Returns the status of the close command. True on success
     */
    public function disconnect()
    {
        if ( !$this->isOpen() ) {
            return true;
        }

        $this->execute( 'QUIT' );
        $return = fclose( $this->_connection );
        $this->_connection = false;
        $this->_logger->log( 'Connection close: ' . $this->_host, 7 );

        return $return;
    }


    /**
     * Execute given command.
     *
     * @param string $command Command to be executed
     * @param string $parameters Optional parameters to pipe to the command
     *
     * @return array|false Returns a list of records in raw format or false if
     * the connection is missing
     * @thows Mumsys_Service_Exception If a result code do not match any spec
     */
    public function execute( $command, $parameters = '' )
    {
        $message = __METHOD__ . ' command: "' . $command . '", params: "'
            . $parameters . '"';
        $this->_logger->log( $message, 7 );

        if ( !$this->isOpen() ) {
            throw new Mumsys_Service_Exception( 'Not connected' );
        }

        /** @todo check cmd in the future. speedup things */
        $cmdlist = array(
            'default' => array(
                'CHAN', 'CLRE', 'DELC', 'DELR', 'DELT',
                'EDIT', 'GRAB', 'HELP', 'HITK', 'LSTC',
                'LSTE', 'LSTR', 'LSTT', 'MESG', 'MODC',
                'MODT', 'MOVC', 'NEWC', 'NEWT', 'NEXT',
                'PLAY', 'PLUG', 'PUTE', 'REMO', 'SCAN',
                'STAT', 'UPDT', 'UPDR', 'VOLU', 'QUIT',
            ),
            // e.g: 'PLUG EPGSEARCH LSTS' ?
            'EPGSEARCH' => array(
                'LSTS', 'NEWS', 'DELS', 'EDIS', 'MODS',
                'UPDS', 'UPDD', 'SETS', 'FIND', 'QRYS',
                'QRYF', 'LSRD', 'LSTC', 'NEWC', 'EDIC',
                'DELC', 'RENC', 'LSTB', 'NEWB', 'DELB',
                'EDIB', 'LSTE', 'SETP', 'LSTT', 'NEWT',
                'DELT', 'EDIT', 'DEFT', 'LSCC', 'MENU',
                'UPDT',
                ),
        );

        $command = strtoupper( $command );

        if ( !in_array( $command, $cmdlist['default'] ) ) {
            throw new Mumsys_Service_Exception( 'Command unknown or not implemented yet. Exiting' );
        }

        $cmd = $command;
        if ( $parameters > '' ) {
            $cmd = $cmd . ' ' . stripslashes( $parameters );
        }

        fputs( $this->_connection, $cmd . "\n" );

        $records = $record = $channel = array();

        while ( $raw = fgets( $this->_connection, 2048 ) ) {
            if ( !preg_match( '/^(\d{3})( |-)(.*)$/i', $raw, $data ) ) {
                continue;
            }

            $this->_logger->log( 'data: "' . print_r( $raw, true ) . '"', 7 );

            /*
              214 Hilfetext
              215 EPG Eintrag
              216 Image grab data (base 64)
              220 VDR-Service bereit
              221 VDR-Service schließt Sende-Kanal
              250 Angeforderte Aktion okay, beendet
              354 Start senden von EPG-Daten
              451 Angeforderte Aktion abgebrochen: lokaler Fehler bei der Bearbeitung
              500 Syntax-Fehler, unbekannter Befehl
              501 Syntax-Fehler in Parameter oder Argument
              502 Befehl nicht implementiert
              504 Befehls-Parameter nicht implementiert
              550 Angeforderte Aktion nicht ausgeführt
              554 Transaktion fehlgeschlagen
             */
            switch ( trim( $data[1] ) ) {
                case '250':
                    $records[] = trim( $data[3] );
                    break;

                // EPG record, LSTE
                case '215':
                    // $records[] = trim($data[3]);
                    $line = trim( $data[3] );
                    $tmp = $this->_parseEPGLine( $line );

                    if ( $tmp === true ) {
                        $records[] = $record;
                        $record = array();
                    } else {
                        if ( $tmp ) {
                            if ( key( $tmp ) == 'extras' ) {
                                $record['extras'][] = $tmp['extras'];
                            } else {
                                $record += $tmp;
                            }
                        }
                    }

                    break; // end epg list

                case '220':
                case '221':
                    break;

                case '501':
                    // also for "Not found"
                    // also for "channel not unique"
                    // trim($data[3]);
                    throw new Mumsys_Service_Exception( $data[3], $data[1] );
                    //break;

                default:
                    $message = 'None catchable exception. '
                        . 'Invalid result or not implemented (yet): '
                        . $data[3];
                    throw new Mumsys_Service_Exception( $message );
                    //break;
            }

            // the last record, break the loop
            if ( trim( $data[2] ) != '-' ) {
                break;
            }

            //$this->_logger->log('$records: "' . print_r($records, true) . '"', 7);
        }
        unset( $raw, $data );

        return $records;
    }


    /**
     * Test if connect() was called successfully.
     *
     * @return boolean Returns true if connection was opend successfully or
     * false on failure or the connection was closed
     */
    public function isOpen()
    {
        if ( is_resource( $this->_connection ) ) {
            return true;
        }

        return false;
    }


    // Returns parts of an epg entry or parts of a recorded show (nearly like a epg entry).
    private function _parseEPGLine( $line = '' )
    {
        static $extras = null;
        $record = array();

        switch ( ( $line[0] ) )
        {
            case 'c':   // end of a channel
                break;

            case 'C':   // begin of a channel
                // eg: C T-8468-514-514 ZDF (T) (T)
                $record = array('channel_key' => '', 'channel_name' => '');
                if ( preg_match( '/^C ([^ ]+) *(.*)/', $line, $channels ) ) {
                    $record['channel_key'] = $channels[1];
                    $record['channel_name'] = $channels[2];
                }
                break;

            case 'D':
                $record['description'] = substr( $line, 2 );
                break;

            case 'e':   // end of a record
                return true;
                //break;

            case 'E':   // begin of a record/event
                if ( $line[0] . $line[1] . $line[2] == 'End' ) {
                    return true;
                }

                if ( preg_match( '/^E (.*?) (.*?) (.*?) (.*?) (.*)/', $line, $event ) ) {
                    $record['event_id'] = $event[1];
                    $record['timestamp'] = $event[2];
                    $record['duration'] = $event[3];
                    $record['e_tableid'] = @$event[4];
                    $record['e_version'] = @$event[5];
                }

                break;

            case 'G':
                $record['genre'] = substr( $line, 2 ); // raw format
                break;

            case 'R': // age check, advisory
                $record['advisory'] = substr( $line, 2 );
                break;

            case 'S':
                $record['subtitle'] = substr( $line, 2 );
                break;

            case 'T':
                $record['title'] = substr( $line, 2 );
                break;

            case 'V':
                $record['vps'] = substr( $line, 2 );
                break;

            case 'X':
                if ( preg_match( '/^X (.*?) (.*?) (.*?) (.*)/', $line, $extras ) ) {
                    $item['stream_kind'] = $extras[1];
                    $item['stream_type'] = $extras[2];
                    $item['stream_lang'] = $extras[3];
                    $item['stream_desc'] = $extras[4];
                    $record['extras'] = $item;
                }
                break;

            // in recordings:
            case 'F':
                $record['framerate'] = substr( $line, 2 );
                break;

            case 'P':
                $record['priority'] = substr( $line, 2 );
                break;

            case 'L':
                $record['lifetime'] = substr( $line, 2 );
                break;

            case '@':
                $record['notes'] = substr( $line, 2 );
                break;

            default:
                $mesg = 'Input error. If you see this, something went wrong. Input was: "' . $line . '"';
                throw new Mumsys_Service_Exception( $mesg );
                //break;
        }

        return $record;
    }

}
