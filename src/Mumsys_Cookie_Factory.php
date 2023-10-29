<?php

/**
 * Mumsys_Cookie_Factory
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2017 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Cookie
 * @version 1.0.0
 * Created: 2017-05-01
 */


/**
 * Factory for the cookie interface.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Cookie
 */
class Mumsys_Cookie_Factory
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';


    /**
     * Initialise cookie object by given name.
     *
     * @param string $adapter Name of the adapter to initialise (e.g> Default|
     * Memory)
     *
     * @return Mumsys_Cookie_Interface Object to get set cookie values.
     *
     * @throws Mumsys_Cookie_Exception On errors initialising the object.
     */
    public static function getAdapter( string $adapter ): Mumsys_Cookie_Interface
    {
        if ( ctype_alnum( $adapter ) === false ) {
            $adaptername = 'Mumsys_Cookie_' . $adapter;
            $message = sprintf(
                'Invalid characters in adapter name "%1$s"', $adaptername
            );
            throw new Mumsys_Cookie_Exception( $message );
        }

        $iface = 'Mumsys_Cookie_Interface';
        $adaptername = 'Mumsys_Cookie_' . $adapter;

        if ( class_exists( $adaptername ) === false ) {
            $message = sprintf( 'Adapter "%1$s" not available', $adaptername );
            throw new Mumsys_Cookie_Exception( $message );
        }

        $object = new $adaptername();

        if ( !( $object instanceof $iface ) ) {
            $message = sprintf(
                'Adapter "%1$s" does not implement interface "%2$s"',
                $adaptername, $iface
            );
            throw new Mumsys_Cookie_Exception( $message );
        }

        return $object;
    }

}
