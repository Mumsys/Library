<?php

/**
 * Mumsys_Weather_Item_Unit_Default
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2013 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 */

/**
 * Unit item factory. Loader for specific implementations.
 *
 * Current adapters: Default, Speed, Temperature, Direction, Preasure
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 */
class Mumsys_Weather_Item_Unit_Factory
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * Return new unit item by given name.
     * @param string $name Name of the required implementation; Default:
     * 'Default'
     * @param array $input List of key/values pairs for the construction of the
     * item
     *
     * @return Mumsys_Weather_Item_Unit_Interface Unit item interface
     * @throws Mumsys_Weather_Exception
     */
    public static function createItem( string $name = null,
        array $input = array() ): Mumsys_Weather_Item_Unit_Interface
    {
        if ( $name === null ) {
            $name = 'Default';
        }

        $classname = 'Mumsys_Weather_Item_Unit_' . ucfirst( $name );

        if ( ctype_alnum( $name ) === false ) {
            $mesg = sprintf(
                'Invalid characters in class name "%1$s"',
                $classname
            );
            throw new Mumsys_Weather_Exception( $mesg );
        }

        if ( !class_exists( $classname ) ) {
            $mesg = sprintf( 'Class "%1$s" not found', $classname );
            throw new Mumsys_Weather_Item_Unit_Exception( $mesg );
        }

        return new $classname( $input );
    }

}
