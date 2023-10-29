<?php

/**
 * Mumsys_Upload_Factory
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2018 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Upload
 * Created: 2018-12
 */

/**
 * Factory for the upload interface.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Upload
 */
class Mumsys_Upload_Factory
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';


    /**
     * Initialise the upload object.
     *
     * @param string $adapter Name of the adapter to initialise (e.g> Default|Memory)
     *
     * @return Mumsys_Upload_Interface Object to get set cookie values.
     *
     * @throws Mumsys_Upload_Exception On errors initialising the object.
     */
    public static function getAdapter( string $adapter, array $options = null ): Mumsys_Upload_Interface
    {
        if ( ctype_alnum( $adapter ) === false ) {
            $adaptername = 'Mumsys_Upload_' . $adapter;
            $message = sprintf( 'Invalid characters in class name "%1$s"', $adaptername );
            throw new Mumsys_Upload_Exception( $message );
        }

        $iface = 'Mumsys_Upload_Interface';
        $adaptername = 'Mumsys_Upload_' . $adapter;

        if ( class_exists( $adaptername ) === false ) {
            $message = sprintf( 'Class "%1$s" not available', $adaptername );
            throw new Mumsys_Upload_Exception( $message );
        }

        $object = new $adaptername( $options );

        if ( !( $object instanceof $iface ) ) {
            $message = sprintf(
                'Class "%1$s" does not implement interface "%2$s"', $adaptername, $iface
            );
            throw new Mumsys_Upload_Exception( $message );
        }

        return $object;
    }

}
