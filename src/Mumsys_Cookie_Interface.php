<?php

/**
 * Mumsys_Cookie_Interface
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
 * Class to deal with the cookie values.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Cookie
 */
interface Mumsys_Cookie_Interface
{
    /**
     * Initialize the cookie object.
     */
    public function __construct();


    /**
     * Returns a cookie variable by given key.
     * If $key is NULL it will return all cookie parameters
     *
     * @param string|null $key Value of the key to return to
     * @param scalar $default Value to return if key not found
     *
     * @return mixed Stored value or $default if key was not set.
     */
    public function getCookie( string $key = null, $default = null );


    /**
     * Sends, sets a cookie on the client side.
     *
     * setcookie() defines a cookie to be sent along with the rest of the HTTP
     * headers. Like other headers, cookies must be sent before any output from
     * your script (this is a protocol restriction). This requires that you
     * place calls to this function prior to any output, including <html> and
     * <head> tags as well as any whitespace.
     *
     * Once the cookies have been set, they can be accessed on the next page
     * load with the $_COOKIE array. Cookie values may also exist in $_REQUEST.
     *
     * @param string $key Name of the cookie (a-z0-9)
     * @param string $value Value for the cookie. The value of the cookie. This
     * value is stored on the clients computer; do not store sensitive
     * information. Assuming the name is 'cookiename', this value is retrieved
     * through $_COOKIE['cookiename']
     * @param integer $expire unix timestamp the cookie will expire
     * @param string $path The path on the server in which the cookie will be
     * available on. If set to '/', the cookie will be available within the
     * entire domain. If set to '/foo/', the cookie will only be available
     * within the /foo/ directory and all sub-directories such as /foo/bar/ of
     * domain. The default value is the current directory that the cookie is
     * being set in.
     * @param string $domain The (sub)domain that the cookie is available to.
     * Setting this to a subdomain (such as 'www.example.com') will make the
     * cookie available to that subdomain and all other sub-domains of it (i.e.
     * w2.www.example.com). To make the cookie available to the whole domain
     * (including all subdomains of it), simply set the value to the domain
     * name ('example.com', in this case).
     * @param boolean $secure Indicates that the cookie should only be
     * transmitted over a secure HTTPS connection from the client. When set to
     * TRUE, the cookie will only be set if a secure connection exists. On the
     * server-side, it's on the programmer to send this kind of cookie only on
     * secure connection (e.g. with respect to $_SERVER["HTTPS"]).
     * @param boolean $httponly When TRUE the cookie will be made accessible
     * only through the HTTP protocol. This means that the cookie won't be
     * accessible by scripting languages, such as JavaScript. It has been
     * suggested that this setting can effectively help to reduce identity theft
     * through XSS attacks (although it is not supported by all browsers), but
     * that claim is often disputed. Added in PHP 5.2.0. TRUE or FALSE
     *
     * @return boolean True on success of false on failure.
     */
    public function setCookie( string $key, string $value = '', int $expire = 0,
        string $path = '', string $domain = '', bool $secure = false,
        bool $httponly = false ): bool;


    /**
     * Send a cookie without urlencoding the cookie value.
     *
     * setrawcookie() is exactly the same as setcookie() except that the cookie
     * value will not be automatically urlencoded when sent to the browser.
     *
     * @param string $key Name of the cookie (a-z0-9)
     * @param string $value Value for the cookie. The value of the cookie. This
     * value is stored on the clients computer; do not store sensitive
     * information. Assuming the name is 'cookiename', this value is retrieved
     * through $_COOKIE['cookiename']
     * @param integer $expire unix timestamp the cookie will expire
     * @param string $path The path on the server in which the cookie will be
     * available on. If set to '/', the cookie will be available within the
     * entire domain. If set to '/foo/', the cookie will only be available
     * within the /foo/ directory and all sub-directories such as /foo/bar/ of
     * domain. The default value is the current directory that the cookie is
     * being set in.
     * @param string $domain The (sub)domain that the cookie is available to.
     * Setting this to a subdomain (such as 'www.example.com') will make the
     * cookie available to that subdomain and all other sub-domains of it (i.e.
     * w2.www.example.com). To make the cookie available to the whole domain
     * (including all subdomains of it), simply set the value to the domain
     * name ('example.com', in this case).
     * @param boolean $secure Indicates that the cookie should only be
     * transmitted over a secure HTTPS connection from the client. When set to
     * TRUE, the cookie will only be set if a secure connection exists. On the
     * server-side, it's on the programmer to send this kind of cookie only on
     * secure connection (e.g. with respect to $_SERVER["HTTPS"]).
     * @param boolean $httponly When TRUE the cookie will be made accessible
     * only through the HTTP protocol. This means that the cookie won't be
     * accessible by scripting languages, such as JavaScript. It has been
     * suggested that this setting can effectively help to reduce identity theft
     * through XSS attacks (although it is not supported by all browsers), but
     * that claim is often disputed. Added in PHP 5.2.0. TRUE or FALSE
     *
     * @return boolean True on success of false on failure.
     */
    public function setRawCookie( string $key, string $value = '',
        int $expire = 0, string $path = '', string $domain = '',
        bool $secure = false, bool $httponly = false ): bool;


    /**
     * Removes, unsets a cookie.
     *
     * By default implementation the cookie value will be cleared to '' and
     * after the expiration time set to the past.
     * Note: Cookies can only be invalidated the same way like set with an emty
     * value and a ttl in the past. Especially when using domain, secure,
     * httponly values
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
        string $domain = '', bool $secure = false, bool $httponly = false ): bool;


    /**
     * Clears and unsets all cookie values
     *
     * @return boolean Returns true on success
     */
    public function clear(): bool;
}
