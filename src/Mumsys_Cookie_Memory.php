<?php

/**
 * Mumsys_Cookie_Memory
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2017 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Cookie
 * Created: 2017-05-01
 */


/**
 * Class to deal with the cookie values in memory.
 *
 * Note this is a wrapper. Data gets lost after the request.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Cookie
 */
class Mumsys_Cookie_Memory
    implements Mumsys_Cookie_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * Representation of the $_COOKIE on initialisation
     * @var array
     */
    private $_cookie;


    /**
     * Initialize the cookie object.
     *
     * Hold $_COOKIE values internaly to get/set/modify
     */
    public function __construct()
    {
        $this->_cookie = array();
    }


    /**
     * Returns a cookie variable by given key.
     * If $key is NULL it will return all cookie parameters
     *
     * @param string|null $key Value of the key to return to
     * @param scalar $default Value to return if key not found
     *
     * @return mixed Stored value or $default if key was not set.
     */
    public function getCookie( string $key = null, $default = null )
    {
        if ( $key === null ) {
            return $this->_cookie;
        }

        if ( isset( $this->_cookie[$key] ) ) {
            $default = $this->_cookie[$key];
        }

        return $default;
    }


    /**
     * Sets a cookie value to memory.
     *
     * @param string $key Name of the cookie (a-z0-9)
     * @param string $value Value for the cookie.
     * @param integer $expire Optional to fit the interface
     * @param string $path Optional to fit the interface
     * @param string $domain Optional to fit the interface
     * @param boolean $secure Optional to fit the interface
     * @param boolean $httponly Optional to fit the interface
     *
     * @return boolean True on success of false on failure.
     */
    public function setCookie( string $key, string $value = '', int $expire = 0,
        string $path = '', string $domain = '', bool $secure = false,
        bool $httponly = false ): bool
    {
        $this->_cookie[$key] = $value;

        return true;
    }


    /**
     * Sets a cookie value to memory.
     *
     * @param string $key Name of the cookie (a-z0-9)
     * @param string $value Value for the cookie.
     * @param integer $expire Optional to fit the interface
     * @param string $path Optional to fit the interface
     * @param string $domain Optional to fit the interface
     * @param boolean $secure Optional to fit the interface
     * @param boolean $httponly Optional to fit the interface
     *
     * @return boolean True on success of false on failure.
     */
    public function setRawCookie( string $key, string $value = '',
        int $expire = 0, string $path = '', string $domain = '',
        bool $secure = false, bool $httponly = false ): bool
    {
        $this->_cookie[$key] = $value;

        return true;
    }


    /**
     * Removes, unsets a cookie.
     *
     * By default implementation the  cookie value will be cleared to '' and
     * after the expiration time set to the past.
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
        if ( isset( $this->_cookie[$key] ) ) {
            unset( $this->_cookie[$key] );
        }

        return true;
    }


    /**
     * Clears and unsets all cookie values
     *
     * @return boolean Returns true on success
     */
    public function clear(): bool
    {
        unset( $this->_cookie );
        $this->_cookie = array();

        return true;
    }

}
