<?php

/**
 * Mumsys_GetterSetter_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  GetterSetter
 * @version     1.0.0
 */


/**
 * Mumsys getter/setter interface.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  GetterSetter
 */
interface Mumsys_GetterSetter_Interface
{
    /**
     * Replaces/ sets the value for a given key.
     *
     * @param string $key Key to be set
     * @param mixed $value Value to be set
     */
    public function replace( $key, $value = null );


    /**
     * Registers the value by given key.
     *
     * @param string $key Key to register
     * @param mixed $value Value to be set
     *
     * @throws Mumsys_Exception If key exists
     */
    public function register( $key, $value = null );


    /**
     * Returns the value by given key.
     *
     * @param string $key Key which was set
     *
     * @return mixed Returns the value which was set or the default will return
     */
    public function get( $key, $default = null );


    /**
     * Removes registered entry.
     *
     * @param string $key Key which was set
     *
     * @return boolean True on success or false if the key wasn't found
     */
    public function remove( $key );
}
