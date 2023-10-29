<?php

/**
 * Mumsys_Context_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2014 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Context
 */


/**
 * Context item abstract does the real job for collecting components in an item.
 *
 * Note: This implementation is NOT type save! But the item implementations are!
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Context
 */
abstract class Mumsys_Context_Abstract
    extends Mumsys_Abstract
    implements Mumsys_Context_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '3.0.0';

    /**
     * Configuration as array container.
     * @var array
     */
    private $_config = array();


    /**
     * Cleans up the stored resources
     */
    public function __destruct()
    {
        foreach ( $this->_config as $key => & $value ) {
            $this->_config[ $key ] = null;
        }
    }


    /**
     * Clones internal objects of the context item.
     */
    public function __clone()
    {
        foreach ( $this->_config as $key => & $value ) {
            $this->_config[ $key ] = clone $this->_config[ $key ];
        }
    }


    /**
     * Returns the object by given key.
     *
     * @param string $key Name of the class to reqister
     *
     * @return object Returns the object by given key if it was set
     *
     * @throws Mumsys_Context_Exception Throws exception if the object was not set
     */
    protected function _get( $key )
    {
        if ( !isset( $this->_config[ $key ] ) ) {
            $mesg = sprintf( '"%1$s" not set', $key );
            throw new Mumsys_Context_Exception( $mesg );
        }

        return $this->_config[ $key ];
    }


    /**
     * Register/ set initially the object by given key.
     *
     * @param string $key Name of the object to register
     * @param object $value The object to be register
     *
     * @throws Mumsys_Context_Exception Throws exception if object already set
     */
    protected function _register( $key, $value )
    {
        if ( isset( $this->_config[ $key ] ) ) {
            $mesg = sprintf( '"%1$s" already set', $key );
            throw new Mumsys_Context_Exception( $mesg );
        }

        $this->_config[ $key ] = $value;
    }


    /**
     * Replaces the object by given key.
     *
     * @param string $key Name of the object to set
     * @param object $value The object to be register
     *
     * @throws Mumsys_Context_Exception Throws exception if object was already set
     */
    protected function _replace( $key, $value )
    {
        $this->_config[ $key ] = $value;
    }

}
