<?php

/**
 * Mumsys_Session_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2005 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Session
 * @version     1.1.0
 */


/**
 * Session interface
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Session
 */
interface Mumsys_Session_Interface extends Mumsys_GetterSetter_Interface
{
    /**
     * Returns the value of given key.
     *
     * @param string $key value of the key to return to
     * @param scalar $default
     *
     * @return mixed Stored value or $default if key was not set.
     */
    public function get( $key, $default = null );

    /**
     * Returns the current session data based on the current session id.
     *
     * Note: This is befor it will be available in $_SESSION.
     *
     * @return mixed Stored value
     */
    public function getCurrent();

    /**
     * Returns the complete active session data.
     *
     * Note: This is befor it will be available in $_SESSION. Existing records
     * in $_SESSION after initialisation of this class are not listed!
     *
     * @return mixed Stored value
     */
    public function getAll();

    /**
     * Register a new session key.
     *
     * Its like register a domain for the application. This is the failsave
     * setter to add new values to the session.
     *
     * @param string $key Name of the key to register.
     * @param mixed $value optional; Value to be stored
     *
     * @throws Mumsys_Session_Exception Throws exception if key already exists
     */
    public function register( $key, $value = null );

    /**
     * Replace/sets the value for the given key.
     *
     * @param string $key Identifier/ session key to set
     * @param mixed $value Value to be stored
     */
    public function replace( $key, $value = null );

    /**
     * Removes registered entry.
     *
     * @param string $key Key which was set
     *
     * @return boolean True on success or false if the key wasn't set before
     */
    public function remove( $key );

    /**
     * Returns the session ID.
     *
     * @return string
     */
    public function getID();

    /**
     * Clears and unsets the current session
     */
    public function clear();
}
