<?php declare(strict_types=1);

/**
 * Mumsys_Serializer_Factory
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2021 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Serializer
 * @version 1.0.0
 * Created: 2021-02-06
 */


/**
 * Factory for the Serializer interface.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Serializer
 */
class Mumsys_Serializer_Factory
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';


    /**
     * Initialise the Serializer object by given name.
     *
     * @param string $adapter Name of the adapter to initialise (e.g> Default)
     *
     * @return Mumsys_Serializer_Interface Object to use un|serialize methodes.
     * @throws Mumsys_Serializer_Exception On errors initialising the object.
     */
    public static function getAdapter( string $adapter = 'Default' ): Mumsys_Serializer_Interface
    {
        if ( ctype_alnum( $adapter ) === false ) {
            $adaptername = 'Mumsys_Serializer_' . $adapter;
            $message = sprintf(
                'Invalid characters in adapter name "%1$s"', $adaptername
            );
            throw new Mumsys_Serializer_Exception( $message );
        }

        $iface = 'Mumsys_Serializer_Interface';
        $adaptername = 'Mumsys_Serializer_' . $adapter;

        if ( class_exists( $adaptername ) === false ) {
            $message = sprintf( 'Adapter "%1$s" not available', $adaptername );
            throw new Mumsys_Serializer_Exception( $message );
        }

        $object = new $adaptername();

        if ( !( $object instanceof $iface ) ) {
            $message = sprintf(
                'Adapter "%1$s" does not implement interface "%2$s"',
                $adaptername, $iface
            );
            throw new Mumsys_Serializer_Exception( $message );
        }

        return $object;
    }

}
