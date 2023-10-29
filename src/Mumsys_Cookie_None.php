<?php

/**
 * Mumsys_Cookie_None
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2017 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Cookie
 * Created: 2017-05-01
 */


/**
 * None adapter for the cookie.
 *
 * This driver does nothing it returns null or $default values and settter
 * takes no effect. Note: This is a wrapper without data handling.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Cookie
 */
class Mumsys_Cookie_None
    implements Mumsys_Cookie_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';


    /**
     * Initialize the object.
     */
    public function __construct()
    {

    }


    /**
     * Returns the $default value.
     *
     * @param string $key Value of the key to return to
     * @param scalar $default Value to return if key not found
     *
     * @return mixed If set the $default or null
     */
    public function getCookie( $key = null, $default = null )
    {
        return $default;
    }


    /**
     * Sets a cookie. Just to fit the interface. It does notting.
     *
     * @param string $key Name of the cookie (a-z0-9)
     * @param string $value Value for the cookie.
     * @param integer $expire Optional to fit the interface
     * @param string $path Optional to fit the interface
     * @param string $domain Optional to fit the interface
     * @param boolean $secure Optional to fit the interface
     * @param boolean $httponly Optional to fit the interface
     *
     * @return boolean Returns always true
     */
    public function setCookie( string $key, string $value = '', int $expire = 0,
        string $path = '', string $domain = '', bool $secure = false,
        bool $httponly = false ): bool
    {
        return true;
    }


    /**
     * Sets a raw cookie. Just to fit the interface. It does notting.
     *
     * @param string $key Name of the cookie (a-z0-9)
     * @param string $value Value for the cookie.
     * @param integer $expire Optional to fit the interface
     * @param string $path Optional to fit the interface
     * @param string $domain Optional to fit the interface
     * @param boolean $secure Optional to fit the interface
     * @param boolean $httponly Optional to fit the interface
     *
     * @return boolean Returns always true
     */
    public function setRawCookie( string $key, string $value = '', int $expire = 0,
        string $path = '', string $domain = '', bool $secure = false,
        bool $httponly = false ): bool
    {
        return true;
    }


    /**
     * Removes, unsets a cookie.
     *
     * By default implementation the cookie value will be cleared to '' and after
     * the expiration time set to the past.
     *
     * @param string $key Name of the cookie (a-z 0-9)
     * @param string $path @see setCookie()
     * @param string $domain @see setCookie()
     * @param boolean $secure @see setCookie()
     * @param boolean $httponly @see setCookie()
     *
     * @return boolean Returns true on success
     */
    public function unsetCookie( string $key, string $path = '',
        string $domain = '', bool $secure = false, bool $httponly = false ): bool
    {
        return true;
    }


    /**
     * Clears and unsets all cookie values
     *
     * @return boolean Returns true
     */
    public function clear(): bool
    {
        return true;
    }

}
