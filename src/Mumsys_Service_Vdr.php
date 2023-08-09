<?php

/**
 * {{{ Mumsys_Service_Vdr
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2015 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 * @version     1.0.0
 * 0.1 Created: 2015-10-08
 }}} */


/**
 * {{{ Wrapper class to deal with svdrpsend command from vdr project (Simple VDR Protocol).
 *
 * SVDRP give you the possibility to run the svdrpsend commands in php context.
 * So it is easy now to work with vdr in php. E.g: Dump the EPG to
 * the mysql database, List channels, recording, epg details and so on.
 *
 * Check also the svdrp.php from vdr-tools project to be used as demo and to run
 * as an extra shell script which will be more easy to understand svdrpsend
 * commands.
 *
 * Example:
 * <code>
 * </code>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 *
 * @uses Mumsys_Logger Logger obejct in context item
 }}} */
class Mumsys_Service_Vdr
    extends Mumsys_Service_Vdr_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * List of channel items. Item keys are:
     * 'vdr_id', 'name', 'bouquet', 'frequency', 'parameter', 'source', 'symbolrate',
     * 'VPID', 'APID', 'TPID', 'CAID', 'SID', 'NID', 'TID', 'RID'
     * @link http://vdr-wiki.de/wiki/index.php/Channels.conf VDR Specs
     * @ v ar array
     */
    //private $_channels = array(); disabled 4SCA


    /**
     * Return the list of available channels.
     * Example:<pre>
     * id title;[semikolon]bouquet/transp:
     * phpcs:disable
     * id Name ;bouquet :Frequenz :Parameter          :Signalquelle:Symbolrate:VPID :APID     :TPID:CAID:SID  :NID :TID :RID
     * 30 NDR 2;ZDFmobil:674000000:B8C23D12G4M16S0T8Y0:R           :0         :673=2:674=deu@3:679 :0   :16426:8468:4097:0</pre>
     * phpcs:enable
     *
     * @return array|false Returns list of key/value pair where the key is the
     * channels ID and the value the channels name
     */
    public function channelsGet()
    {
        return $this->_channelsGet();
    }


    /**
     * Returns the channel item by given id.
     *
     * @param integer $id Channel ID to return
     *
     * @return array Channel item
     */
    public function channelGet( $id = null )
    {
        $id = (int) $id;
        $channel = $this->_channelsGet( $id );

        return reset( $channel );
    }


    /**
     * Returns list of channels.
     *
     * @param string|integer $key Keyword to search for or integer for a specific channel ID
     *
     * @return array List of channel items
     *
     * @throws Mumsys_Service_Exception
     */
    public function channelSearch( $key = null )
    {
        return $this->_channelsGet( $key );
    }


    /**
     * Adds a new channel to the vdr.
     *
     * @param string $name Chanel name
     * @param string $transponder
     * @param string $frequency
     * @param string $parameter
     * @param string $source
     * @param string $symbolrate
     * @param string $VPID
     * @param string $APID
     * @param string $TPID
     * @param string $CAID
     * @param string $SID
     * @param string $NID
     * @param string $TID
     * @param string $RID
     * @param integer|null $channelID Internal channel ID
     *
     * @return array Channel item containing the new ID
     *
     * @throws Mumsys_Service_Exception If execution fails (E.g.: Existing, not existing record)
     */
    public function channelAdd( $name, $transponder, $frequency, $parameter,
        $source, $symbolrate, $VPID, $APID, $TPID, $CAID, $SID, $NID, $TID,
        $RID, $channelID = null )
    {
        try
        {
            $channelString = $this->_channelStringGet(
                null, $name, $transponder, $frequency, $parameter, $source,
                $symbolrate, $VPID, $APID, $TPID, $CAID, $SID, $NID, $TID, $RID
            );

            $response = $this->execute( 'NEWC', $channelString );
            $result = reset( $response );
            $item = $this->_channelString2ItemGet( $result );
        }
        catch ( Exception $ex ) {
            throw new Mumsys_Service_Exception( $ex->getMessage(), $ex->getCode() );
        }

        return $item;
    }


    /**
     * Removes given channel from vdr.
     *
     * @param integer $channelID Internal ID of the channel
     *
     * @return boolean True on success
     *
     * @throws Mumsys_Service_Exception If channel ID <= 0
     */
    public function channelDelete( $channelID = null )
    {
        if ( !(int) $channelID ) {
            throw new Mumsys_Service_Exception( 'Invalid channel ID' );
        }

        $response = $this->execute( 'DELC', $channelID );
        $result = reset( $response );

        return true;
    }


    /**
     * Returns a channel item.
     *
     * @param integer $channelID
     * @param string $name
     * @param string $transponder
     * @param string $frequency
     * @param string $parameter
     * @param string $source
     * @param string $symbolrate
     * @param string $VPID
     * @param string $APID
     * @param string $TPID
     * @param string $CAID
     * @param string $SID
     * @param string $NID
     * @param string $TID
     * @param string $RID
     *
     * @return array List of key/value pairs of a channel item.
     */
    public function channelItemCreate( $channelID, string $name, $transponder,
        $frequency, $parameter, $source, $symbolrate, $VPID, $APID, $TPID,
        $CAID, $SID, $NID, $TID, $RID )
    {
        $item = array(
            'channel_id' => $channelID,
            'name' => (string) $name,
            'bouquet' => $transponder,
            'frequency' => $frequency,
            'parameter' => trim( $parameter ),
            'source' => trim( $source ),
            'symbolrate' => trim( $symbolrate ),
            'VPID' => trim( $VPID ),
            'APID' => trim( $APID ),
            'TPID' => trim( $TPID ),
            'CAID' => trim( $CAID ),
            'SID' => trim( $SID ),
            'NID' => trim( $NID ),
            'TID' => trim( $TID ),
            'RID' => trim( $RID ),
        );

        return $item;
    }


    /**
     * Returns a list of channels.
     *
     * @param string|integer $key String to search for channels or integer for
     * a specific channel id to return.
     *
     * @return array List of channel items found. Item keys are:
     *      'vdr_id', 'name', 'bouquet', 'frequency', 'parameter', 'source', 'symbolrate',
     *      'VPID', 'APID', 'TPID', 'CAID', 'SID', 'NID', 'TID', 'RID'
     */
    private function _channelsGet( $key = null )
    {
        if ( $key === null ) {
            $search = null;
        } else if ( is_string( $key ) ) {
            $search = trim( $key );
        } else if ( is_numeric( $key ) && (int) $key > 0 ) {
            $search = (int) $key;
        } else {
            throw new Mumsys_Service_Exception( 'Invalid channel parameter' );
        }

        $records = $this->execute( 'LSTC', $search );
        $channelList = array();

        foreach ( $records as $idx => $line ) {
            $parts = explode( ':', $line );
            $posStart = strpos( $parts[0], ' ' );
            //$posEnd = strpos($parts[0], ';');

            $channelID = substr( $parts[0], 0, $posStart );
            $names = explode( ';', ( substr( $parts[0], $posStart + 1 ) ) );
            $recordName = str_replace( '|', ':', $names[0] );

            $channelList[$channelID] = $this->channelItemCreate(
                $channelID,
                $recordName,
                ( isset( $names[1] ) ? $names[1] : $names[0] ),
                $parts[1],
                $parts[2],
                $parts[3],
                $parts[4],
                $parts[5],
                $parts[6],
                $parts[7],
                $parts[8],
                $parts[9],
                $parts[10],
                $parts[11],
                $parts[12]
            );

        }

        return $channelList;
    }


    /**
     * Returns the channel string to execute.
     *
     * @param integer|null $channelID
     * @param string $name
     * @param string $transponder
     * @param string $frequency
     * @param string $parameter
     * @param string $source
     * @param string $symbolrate
     * @param string $VPID
     * @param string $APID
     * @param string $TPID
     * @param string $CAID
     * @param string $SID
     * @param string $NID
     * @param string $TID
     * @param string $RID
     *
     * @return string
     */
    private function _channelStringGet( $channelID, $name, $transponder,
        $frequency, $parameter, $source, $symbolrate, $VPID, $APID, $TPID,
        $CAID, $SID, $NID, $TID, $RID )
    {
        if ( $channelID === null ) {
            $template = '%1$s;%2$s:%3$s:%4$s:%5$s:%6$s:%7$s:%8$s:%9$s:%10$s:%11$s:%12$s:%13$s:%14$s';
        } else {
            $template = '%15$s %1$s;%2$s:%3$s:%4$s:%5$s:%6$s:%7$s:%8$s:%9$s:%10$s:%11$s:%12$s:%13$s:%14$s';
        }

        $timerString = sprintf(
            $template,
            $name,
            $transponder,
            $frequency,
            $parameter,
            $source,
            $symbolrate,
            $VPID,
            $APID,
            $TPID,
            $CAID,
            $SID,
            $NID,
            $TID,
            $RID,
            $channelID
        );

        return $timerString;
    }


    private function _channelString2ItemGet( $line )
    {
        $parts = explode( ':', $line );
        $posStart = strpos( $parts[0], ' ' );
        $posEnd = strpos( $parts[0], ';' );

        $channelID = substr( $parts[0], 0, $posStart );
        $names = explode( ';', ( substr( $parts[0], $posStart + 1 ) ) );
        $recordName = str_replace( '|', ':', $names[0] );

        $item = $this->channelItemCreate(
            $channelID, $recordName, $names[1], $parts[1], $parts[2], $parts[3],
            $parts[4], $parts[5], $parts[6], $parts[7], $parts[8], $parts[9],
            $parts[10], $parts[11], $parts[12]
        );

        return $item;
    }

    // --- recordings ------------------------------------------------------------------------------


    /**
     * Returns recording details. An epg item.
     *
     * @param integer $recordingID Recording ID to get
     * @param boolean $path Optional; Set to true to get the path of the specified recording
     *
     * @return array|string Recording item or path
     *
     * @throws Mumsys_Session_Exception If ID is 0 or lower
     */
    public function recordingGet( $recordingID = 0, $path = false )
    {
        $recording = null;
        $recordingID = (int) $recordingID;

        if ( (int) $recordingID <= 0 ) {
            throw new Mumsys_Service_Exception( 'Invalid recording ID' );
        }

        if ( $path ) {
            $tmp = $this->_recordingGetPath( $recordingID );
            $recording = $tmp;
        } else {
            $tmp = $this->execute( 'LSTR', $recordingID );
            $recording = reset( $tmp );
        }

        return $recording;
    }


    /**
     * Returns the list of recordings.
     *
     * @return array Returns the list of key/value pairs where the key is the
     * internal recording ID and the value is a list of key/value pairs of
     * recording properties
     */
    public function recordingsGet()
    {
        $recordings = array();

        $records = $this->execute( 'LSTR' );
        foreach ( $records as $line ) {
            $line = trim( $line );

            $partA = explode( ' ', substr( $line, 0, 23 ) );
            $partB = substr( $line, 23, 1 );

            $title = str_replace( '|', ':', substr( $line, 25 ) );
            $date = explode( '.', $partA[1] );
            $dateString = sprintf( '20%1$s-%2$s-%3$s', $date[2], $date[1], $date[0] );

            $id = (int) $partA[0];
            $options = array(
                'id' => $id,
                'date' => $dateString,
                'time_start' => $partA[2],
                'duration' => substr( $partA[3], 0, 5 ),
                'new' => ( ( $partB == '*' ) ? 1 : 0 ),
                'title' => $title,
            );

            $recordings[$id] = $options;
        }

        unset( $records, $id, $options, $partA, $partB, $line );

        return $recordings;
    }


    /**
     * Returns the path of a specified recording.
     *
     * @param integer $recordingID Recording ID to get
     *
     * @return string Path of the recording
     */
    public function _recordingGetPath( $recordingID = null )
    {
        $result = '';

        if ( $recordingID !== null && (int) $recordingID <= 0 ) {
            throw new Mumsys_Session_Exception( 'Invalid recording id' );
        } else {
            $records = $this->execute( 'LSTR', $recordingID . ' path' );
            $result = reset( $records );
        }

        return $result;
    }


    /**
     * Return the timer by given timer ID.
     *
     * @param integer $timerID Optional timer ID to get only this timer details
     *
     * @return array|false Returns list of key/value pair where the key is the
     * timer ID and the value is a key/value pair of timer properties
     */
    public function timerGet( $timerID = null )
    {
        return reset( $this->_timersGet( $timerID ) );
    }


    /**
     * Return the list of timers.
     *
     * @return array|false Returns list of key/value pair where the key is the
     * timer ID and the value is a key/value pair of timer properties
     */
    public function timersGet()
    {
        return $this->_timersGet();
    }


    /**
     *
     * @param integer|null $timerID Timer ID to get ro 0 or null for all timers
     *
     * @return array Timer item or list of timer items wher array key contants theitem ID
     *
     * @throws Mumsys_Session_Exception If $timerID given and is 0 (zero)
     */
    private function _timersGet( $timerID = null )
    {
        if ( $timerID !== null && (int) $timerID <= 0 ) {
            throw new Mumsys_Session_Exception( 'Invalid timer id' );
        }

        $id = (int) $timerID;
        $timers = array();

        $records = $this->execute( 'LSTT', $id );

        foreach ( $records as $line ) {
            $options = $this->_timerString2ItemGet( $line );
            $timers[$options['id']] = $options;
        }

        return $timers;
    }


    /**
     * Adds a timer to the vdr.
     *
     * @param integer $activ Aktiv (1) or inactiv (0)
     * @param integer $channelID Channel ID
     * @param integer $dayOfMonth Day of the month 1-31
     * @param integer $timeStart Number of the start time eg.: 2015
     * @param integer $timeEnd Number of the ending of the recording eg.: 2231
     * @param integer $priority Recording priority 0-99
     * @param integer $lifetime Lifetime 0-99
     * @param string $title Title/ name of the timer
     * @param string $notes Additional Informations, optional
     * @param integer $id ID of the timer. Null for a new one or id to modify/update
     *
     * @return array|false Return the timer item.
     */
    public function timerAdd( $activ, $channelID, $dayOfMonth, $timeStart,
        $timeEnd, $priority, $lifetime, $title, $notes, $id = null )
    {
        $timerString = $this->_timerStringGet(
            $id, $activ, $channelID, $dayOfMonth, $timeStart, $timeEnd, $priority,
            $lifetime, $title, $notes
        );

        $response = $this->execute( 'NEWT', $timerString );
        $result = $this->_timerString2ItemGet( $response );

        return $result;
    }


    /**
     * Sets the specified timer to activ or inactiv.
     *
     * @param integer $timerID Timer ID to enable/ disable
     * @param boolean $active Flag to enable (true) or disable (false, default) the timer
     *
     * @return array Return the timer item on success
     *
     * @throws Mumsys_Session_Exception
     */
    public function timerSetActive( $timerID, $active = false )
    {
        $id = (int) $timerID;

        if ( $id <= 0 ) {
            throw new Mumsys_Session_Exception( 'Invalid timer id' );
        }

        $status = 'off';
        if ( $active ) {
            $status = 'on';
        }

        $param = $id . ' ' . $status;
        $response = $this->execute( 'MODT', $timerID );

        return $this->_timerString2ItemGet( $response );
    }


    /**
     * Returns key/value pair of a timer record.
     *
     * @param integer $activ Aktiv (1) or inactiv (0)
     * @param integer $channelid Channel ID
     * @param integer $day Day of the month
     * @param integer $timeStart Number of the start time eg.: 2015
     * @param integer $timeEnd Number of the ending of the recording eg.: 2231
     * @param integer $priority Recording priority 0-99
     * @param integer $lifetime Lifetime 0-99
     * @param string $title Title/ name of the timer
     * @param string $notes Additional Informations, optional
     * @param integer $id ID of the timer. Null for a new one or id to modify/update
     *
     * @return array Timer item
     */
    public function timerItemCreate( $activ, $channelid, $day, $timeStart,
        $timeEnd, $priority, $lifetime, $title, $notes, $id = null )
    {
        $record = array(
            'activ' => $activ,
            'channelid' => $channelid,
            'day' => $day,
            'time_start' => $timeStart,
            'time_end' => $timeEnd,
            'priority' => $priority,
            'lifetime' => $lifetime,
            'title' => str_replace( '|', ':', $title ),
            'notes' => $notes,
        );

        if ( !is_null( $id ) ) {
            $record['id'] = $id;
        }

        return $record;
    }


    /**
     * Returns a timer record from given timer string format.
     *
     * @param string $timerString The timer string from svdrp program
     * @return array Returns the timer record or false if record ID is missing.
     */
    private function _timerString2ItemGet( $timerString = null )
    {
        $line = trim( $timerString );

        $parts = explode( ':', $line );

        $posStart = strpos( $parts[0], ' ' );

        $recordId = substr( $parts[0], 0, ( $posStart ) );

        $record = $this->timerItemCreate(
            //$record = $this->timerRecordGet(
            substr( $parts[0], ( $posStart + 1 ) ),
            $parts[1],
            $parts[2],
            $parts[3],
            $parts[4],
            $parts[5],
            $parts[6],
            $parts[7],
            $parts[8],
            $recordId
        );

        return $record;
    }


    /**
     * Returns a timer string format from given timer record to set/ update the timer.
     *
     * @param array $record The timer record
     * @return string Returns the timer string.
     */
    public function timerItem2StringGet( array $record = array() )
    {
        if ( empty( $record['id'] ) ) {
            $record['id'] = null;
        }

        $timerString = $this->_timerStringGet(
            $record['activ'],
            $record['channelid'],
            $record['day'],
            $record['time_start'],
            $record['time_end'],
            $record['priority'],
            $record['lifetime'],
            $record['title'],
            $record['notes'],
            $record['id']
        );

        return $timerString;
    }


    /**
     * Returns the timer string to be used to set or update a timer.
     *
     * Api note: [nummer] aktiv:Kanalnummer:Tag_des_Monats:Startzeit:Endzeit:Priorität:Dauerhaftigkeit:Titel:
     *
     * @param integer|null $id ID of the timer. Null for a new one or id to modify/update
     * @param integer $activ Aktiv (1) or inactiv (0)
     * @param integer $channelID Channel ID
     * @param integer $dayOfMonth Day of the month
     * @param integer $timeStart Number of the start time eg.: 2015
     * @param integer $timeEnd Number of the ending of the recording eg.: 2231
     * @param integer $priority Recording priority 0-99
     * @param integer $lifetime Lifetime 0-99
     * @param string $title Title/ name of the timer
     * @param string $notes Additional Informations, optional
     *
     * @return string Timer string to be used to add or update a timer
     */
    private function _timerStringGet( $id, $activ, $channelID, $dayOfMonth,
        $timeStart, $timeEnd, $priority, $lifetime, $title, $notes )
    {
        if ( isset( $id ) ) {
            $template = '%1$s:%2$s:%3$s:%4$s:%5$s:%6$s:%7$s:%8$s:%9$s';
        } else {
            $template = '%10$s %1$s:%2$s:%3$s:%4$s:%5$s:%6$s:%7$s:%8$s:%9$s';
        }

        $timerString = sprintf(
            $template,
            $activ,
            $channelID,
            $dayOfMonth,
            $timeStart,
            $timeEnd,
            $priority,
            $lifetime,
            str_replace( ':', '|', $title ),
            $notes,
            $id
        );

        return $timerString;
    }

}
