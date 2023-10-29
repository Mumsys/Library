<?php

/**
 * Mumsys_Registry_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2014 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Registry
 * @version     1.0.1
 */


/**
 * Mumsys registry class.
 *
 * @uses Singleton pattern
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Registry
 */
interface Mumsys_Registry_Interface
{
    /**
     * Replaces/ sets the value to the registry by given key.
     *
     * @param string $key Key to be set
     * @param mixed $value Value to be set
     * @throws Mumsys_Registry_Exception Throws exception if key is not a string
     */
    public static function replace( $key, $value );


    /**
     * Registers the value to the registry by given key.
     *
     * @param string $key Key to register
     * @param mixed $value Value to be set

     * @throws Mumsys_Registry_Exception Throws exception if key already exists
     */
    public static function register( $key, $value );


    /**
     * Sets value to the registry by given key and value.
     *
     * @todo To be removed in the future.
     *
     * @deprecated since version 1.0.0
     *
     * @throws Mumsys_Registry_Exception Throws exception
     */
    //public static function set( $key, $value );


    /**
     * Returns the value by given key.
     *
     * @param string $key Key which was set
     * @return mixed Returns the value which was set
     */
    public static function get( $key, $default = null );


    /**
     * Removes registered entry.
     *
     * @param string $key Key which was set
     *
     * @throws Mumsys_Registry_Exception Throws exception if key not exists
     */
    public static function remove( $key );
}
