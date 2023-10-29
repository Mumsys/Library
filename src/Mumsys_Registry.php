<?php

/**
 * Mumsys_Registry
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2014 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Registry
 * @version     1.0.0
 * Created: 2014-01-07
 */


/**
 * Mumsys registry class implementing singleton pattern.
 *
 * @uses Singleton ANTI pattern
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Registry
 */
abstract class Mumsys_Registry
    extends Mumsys_Abstract
    implements Mumsys_Registry_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.1.0';

    /**
     * List of properties to register
     * @var array
     */
    private static $_registry = array();


    /**
     * Replaces/ sets the value to the registry by given key.
     *
     * @param string $key Key to be set
     * @param mixed $value Value to be set
     *
     * @throws Mumsys_Registry_Exception Throws exception if key is not a string
     */
    public static function replace( $key, $value )
    {
        parent::_checkKey( $key );
        self::$_registry[$key] = $value;
    }


    /**
     * Registers the value to the registry by given key.
     *
     * @param string $key Key to register
     * @param mixed $value Value to be set

     * @throws Mumsys_Registry_Exception Throws exception if key already exists
     */
    public static function register( $key, $value )
    {
        parent::_checkKey( $key );

        if ( array_key_exists( $key, self::$_registry ) ) {
            $message = sprintf( 'Registry key "%1$s" exists', $key );
            throw new Mumsys_Registry_Exception( $message );
        }

        self::$_registry[$key] = $value;
    }


    /**
     * @deprecated since version 1.0.0
     * @todo To be removed in the future.
     *
     * Sets value to the registry by given key and value.
     *
     * @throws Mumsys_Registry_Exception Throws exception
     */
    public static function set( $key, $value )
    {
        $message = 'Unknown meaning for set(). Use register() or replace() '
            . 'methodes';
        throw new Mumsys_Registry_Exception( $message );
    }


    /**
     * Returns the value by given key.
     *
     * @param string $key Key which was set
     *
     * @return mixed Returns the value which was set
     */
    public static function get( $key, $default = null )
    {
        if ( isset( self::$_registry[$key] ) ) {
            return self::$_registry[$key];
        }

        return $default;
    }


    /**
     * Removes registered entry.
     *
     * @param string $key Key which was set
     */
    public static function remove( $key )
    {
        if ( isset( self::$_registry[$key] ) ) {
            unset( self::$_registry[$key] );
        }
    }

}
