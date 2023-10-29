<?php declare(strict_types=1);

/**
 * Mumsys_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2011 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 *
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Abstract
 */


/**
 * Abstract class to extend mumsys classes with basic methodes and features.
 */
abstract class Mumsys_Abstract
{
    /**
     * Version ID information.
     */
    public const VERSION = '3.0.3';


    /**
     * Retuns the version ID of this program.
     *
     * @return string Returns the version ID string
     */
    public static function getVersionID()
    {
        return static::VERSION;
    }


    /**
     * Retuns the version string of called class.
     *
     * @return string Returns the version string "className versionID"
     */
    public static function getVersion()
    {
        $class = get_called_class();
        $versionString= '%1$s %2$s';

        return sprintf( $versionString, $class, $class::VERSION );
    }


    /**
     * Returns a list of class/versionID pairs which are loaded except
     * exceptions and interfaces.
     *
     * @return array Returns a list of class/versionID pairs
     */
    public static function getVersions()
    {
        $list = Mumsys_Loader::loadedClassesGet();
        $versions = array();

        foreach ( $list as $class ) {
            if ( preg_match( '/(exception|interface)/i', $class ) !== false ) {
                if ( defined( $class . '::VERSION' ) ) {
                    $versions[$class] = $class::VERSION;
                } else {
                    $versions[$class] = '0.0.0-unknown-version';
                }
            }
        }

        return $versions;
    }


    /**
     * Check given key to be a valid string.
     *
     * @param string $key Key to register (ASCII code would be good, a-Z,0-9
     * would be perfect but all will be accepted)
     *
     * @throws Mumsys_Exception Throws exception if key is not a string
     */
    protected static function _checkKey( $key )
    {
        if ( !is_string( $key ) ) {
            $message = 'Invalid initialisation key for a setter. '
                . 'A string is required!';
            throw new Mumsys_Exception( $message );
        }
    }

}
