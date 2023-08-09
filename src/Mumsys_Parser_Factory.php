<?php declare(strict_types=1);

/**
 * Mumsys_Parser_Factory
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2023 by Florian Blasel
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Parser
 * Created: 2023-07-31
 */

/**
 * Factory for the parser interface
 */
class Mumsys_Parser_Factory
    extends Mumsys_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '2.0.0';


    /**
     * Initialise a Parser object by given adapter name and optional options.
     *
     * @param string $adapter Name of the adapter to initialise (e.g> Default|
     * Logline)
     * @param string $format string|null $format Optional format of a string
     * @param array<string, string>|null $patterns Optional patterns to be set.
     * Otherwise default patterns of the adapter implementation will be used.
     *
     * @return Mumsys_Parser_Interface Returns the Parser adapter/object
     * @throws Mumsys_Parser_Exception On errors initialising the object
     */
    public static function getAdapter( string $adapter = 'Default',
        ?string $format = null, ?array $patterns = null ): Mumsys_Parser_Interface
    {
        if ( ctype_alnum( $adapter ) === false ) {
            $adaptername = 'Mumsys_Parser_' . $adapter;
            $mesg = sprintf(
                'Invalid characters in adapter name "%1$s"', $adaptername
            );
            throw new Mumsys_Parser_Exception( $mesg );
        }

        $iface = 'Mumsys_Parser_Interface';
        $adaptername = 'Mumsys_Parser_' . $adapter;

        if ( class_exists( $adaptername ) === false ) {
            $mesg = sprintf( 'Adapter "%1$s" not available', $adaptername );

            throw new Mumsys_Parser_Exception( $mesg );
        }

        /** @var Mumsys_Parser_Interface $object */
        $object = new $adaptername( $format, $patterns );

        if ( !( $object instanceof $iface ) ) {
            $mesg = sprintf(
                'Adapter "%1$s" does not implement interface "%2$s"',
                $adaptername, $iface
            );

            throw new Mumsys_Parser_Exception( $mesg );
        }

        return $object;
    }

}
