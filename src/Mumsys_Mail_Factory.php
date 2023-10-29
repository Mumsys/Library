<?php declare (strict_types=1);

/**
 * Mumsys_Mail_Factory
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2017 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mail
 * @version 1.0.0
 * Created: 2017-05-16
 */


/**
 * Factory for the mail interface.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mail
 */
class Mumsys_Mail_Factory
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';


    /**
     * Initialise the mail object by given adapter name.
     *
     * @param string $adapter Name of the adapter to initialise (e.g> Default|
     * Memory)
     * @param array $options List of configuration parameters to initialise the
     * object
     *
     * @return Mumsys_Mail_Interface Returns the mail object
     * @throws Mumsys_Mail_Exception On errors initialising the object.
     */
    public static function getAdapter( string $adapter = 'Default',
        array $options = array() ): Mumsys_Mail_Interface
    {
        if ( ctype_alnum( $adapter ) === false ) {
            $adaptername = 'Mumsys_Mail_' . $adapter;
            $message = sprintf(
                'Invalid characters in adapter name "%1$s"', $adaptername
            );
            throw new Mumsys_Mail_Exception( $message );
        }

        $iface = 'Mumsys_Mail_Interface';
        $adaptername = 'Mumsys_Mail_' . $adapter;

        if ( class_exists( $adaptername ) === false ) {
            $message = sprintf( 'Adapter "%1$s" not available', $adaptername );
            throw new Mumsys_Mail_Exception( $message );
        }

        /** @var Mumsys_Mail_Interface $object */
        $object = new $adaptername( $options );

        if ( !( $object instanceof $iface ) ) {
            $message = sprintf(
                'Adapter "%1$s" does not implement interface "%2$s"',
                $adaptername, $iface
            );
            throw new Mumsys_Mail_Exception( $message );
        }

        return $object;
    }

}
