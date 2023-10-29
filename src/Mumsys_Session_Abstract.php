<?php

/**
 * Mumsys_Session_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2005 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Session
 * Created: 2005-01-01, new in 2016-05-17
 */


/**
 * Abstact session class to handle the session data.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Session
 */
abstract class Mumsys_Session_Abstract
    extends Mumsys_Abstract
    implements Mumsys_Session_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * Representation of the session for this use
     * @var array
     */
    protected $_records = array();

    /**
     * ID of the session of this request.
     * @var string
     */
    protected $_id;


    /**
     * Initialize the session object base records manager.
     *
     * @param array $records All session records available
     * @param string $sessionId ID of the current session
     * @param string $appKey Application key/ Installation key
     */
    public function __construct( array $records, $sessionId, $appKey )
    {
        $this->_records = $records;
        $this->_id = $appKey . '_' . $sessionId;
    }


    /**
     * Stores session informations and close it
     */
    abstract public function __destruct();


    /**
     * Returns the value of given key.
     *
     * @param string $key value of the key to return to
     * @param scalar $default
     * @return mixed Stored value or $default if key was not set.
     */
    public function get( $key, $default = null )
    {
        if ( isset( $this->_records[$this->_id][$key] ) ) {
            return $this->_records[$this->_id][$key];
        }

        return $default;
    }


    /**
     * Returns the current session data based on the current session id.
     *
     * Note: This is befor it will be available in $_SESSION.
     *
     * @return mixed Stored value or empty array
     */
    public function getCurrent()
    {
        if ( isset( $this->_records[$this->_id] ) ) {
            return $this->_records[$this->_id];
        }

        return array();
    }


    /**
     * Returns the complete active session data.
     *
     * Note: This is befor it will be available in $_SESSION. Existing records
     * in $_SESSION after initialisation of this class are not listed!
     *
     * @return array Stored value
     */
    public function getAll()
    {
        return $this->_records;
    }


    /**
     * Register a new session key.
     *
     * Its like register a domain for the application. This is the failsave
     * setter to add new values to the session.
     *
     * @param string $key Name of the key to register.
     * @param mixed $value optional; Value to be stored
     * @throws Mumsys_Session_Exception Throws exception if key already exists
     */
    public function register( $key, $value = null )
    {
        $this->_checkKey( $key );

        if ( array_key_exists( $key, $this->_records[$this->_id] ) ) {
            $mesg = sprintf( 'Session key "%1$s" exists', $key );
            throw new Mumsys_Session_Exception( $mesg );
        }

        $this->_records[$this->_id][$key] = $value;
    }


    /**
     * Replace/sets the value for the given key.
     *
     * @param string $key Identifier/ session key to set
     * @param mixed $value Value to be stored
     */
    public function replace( $key, $value = null )
    {
        $this->_records[$this->_id][$key] = $value;
    }


    /**
     * Removes registered entry.
     *
     * @param string $key Key which was set
     *
     * @return boolean True on success or false if the key wasn't set before or is null
     */
    public function remove( $key )
    {
        if ( isset( $this->_records[$this->_id][$key] ) ) {
            unset( $this->_records[$this->_id][$key] );

            return true;
        }

        return false;
    }


    /**
     * Returns the session ID.
     *
     * @return string
     */
    public function getID()
    {
        return $this->_id;
    }


    /**
     * Clears and unsets the current session
     */
    public function clear()
    {
        $this->_records = array();
    }

}
