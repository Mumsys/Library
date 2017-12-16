<?php

/**
 * Mumsys_Cookie_Mock
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
 * Mock adapter for the cookie interface for testing purpose.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Cookie
 */
class Mumsys_Cookie_Mock
    implements Mumsys_Cookie_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * Optional: Location for the local cookie file.
     * Default: /tmp/toolboxCookieMock.{USERNAME}.tmp
     * @var string
     */
    private $_cookieFile;


    /**
     * Initialize the cookie object.
     *
     * Hold cookie values internaly to get/set/modify
     *
     * @param string $cookieFile Optional cookie file location.
     */
    public function __construct( string $cookieFile = '' )
    {
        if ( $cookieFile && ( is_dir(dirname($cookieFile) . DIRECTORY_SEPARATOR) ) ) {
            $this->_cookieFile = $cookieFile;
        } else {
            $this->_cookieFile = '/tmp/MumsysCookieMock.'
                . Mumsys_Php_Globals::getRemoteUser() . '.tmp';
        }

        $this->_loadCookieData();
    }


    /**
     * Remove cookie on destruction.
     */
    public function __destruct()
    {
        if ( file_exists($this->_cookieFile) ) {
            unlink($this->_cookieFile);
        }
    }


    /**
     * Returns a cookie variable by given key.
     *
     * If $key is NULL it will return all cookie parameters
     *
     * @param string $key Value of the key to return to
     * @param scalar $default Value to return if key not found
     *
     * @return mixed Stored value or $default if key was not set.
     */
    public function getCookie( $key = null, $default = null )
    {
        $cookie = $this->_loadCookieData();

        if ( isset($cookie) && $key === null ) {
            return $cookie;
        }

        if ( isset($cookie[$key]) ) {
            $default = $cookie[$key];
        }

        return $default;
    }


    /**
     * Sets a cookie value to the mock.
     *
     * @param string $key Name of the cookie (a-z0-9)
     * @param string $value Value for the cookie.
     * @param integer $expire Optional, Just to fit the interface
     * @param string $path Optional, Just to fit the interface
     * @param string $domain Optional, Just to fit the interface
     * @param boolean $secure Optional, Just to fit the interface
     * @param boolean $httponly Optional, Just to fit the interface
     *
     * @return boolean True on success of false on failure.
     */
    public function setCookie( string $key, string $value = '', int $expire = 0,
        string $path = '', string $domain = '', bool $secure = false,
        bool $httponly = false ): bool
    {
        $cookie = $this->_loadCookieData();

        $cookie[$key] = $value;

        return file_put_contents($this->_cookieFile, json_encode($cookie));
    }


    /**
     * Sets a raw cookie value to the mock.
     *
     * @param string $key Name of the cookie (a-z0-9)
     * @param string $value Value for the cookie.
     * @param integer $expire Optional, Just to fit the interface
     * @param string $path Optional, Just to fit the interface
     * @param string $domain Optional, Just to fit the interface
     * @param boolean $secure Optional, Just to fit the interface
     * @param boolean $httponly Optional, Just to fit the interface
     *
     * @return boolean True on success of false on failure.
     */
    public function setRawCookie( string $key, string $value = '', int $expire = 0,
        string $path = '', string $domain = '', bool $secure = false,
        bool $httponly = false ): bool
    {
        $cookie = $this->_loadCookieData();

        $cookie[$key] = $value;

        return file_put_contents($this->_cookieFile, json_encode($cookie));
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
        $cookie = $this->_loadCookieData();

        if ( isset($cookie[$key]) ) {
            unset($cookie[$key]);

            return file_put_contents($this->_cookieFile, json_encode($cookie));
        }

        return true;
    }


    /**
     * Clears and unsets all cookie values
     */
    public function clear(): bool
    {
        $cookie = $this->_loadCookieData();

        unset($cookie);

        $this->_cookie = array();

        file_put_contents($this->_cookieFile, json_encode($this->_cookie));
    }


    /**
     * Loads cookie file from local storage.
     *
     * @return array Returns cookie data as list of key/value pairs
     */
    private function _loadCookieData()
    {
        if ( file_exists($this->_cookieFile) ) {
            $content = file_get_contents($this->_cookieFile);
            $this->_cookie = json_decode($content, true);
        } else {
            $this->_cookie = array();
        }

        return $this->_cookie;
    }

}