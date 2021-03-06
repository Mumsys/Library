<?php

/**
 * Mumsys_Php_Globals
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Php_Globals
 */


/**
 * Nice interface for php's GLOBAL VARIABLES.
 *
 * Wraper for $GLOBALS, $_SERVER, $_GET, $_POST, $_FILES, $_COOKIE, $_SESSION,
 * $_REQUEST and $_ENV and getenv().
 *
 * When ever using the server or env variables and your are bored about testing
 * if an array key exists and/or has a value you may find this class useful to
 * always have a default value if something NOT EXISTS. eg.: When switching to
 * shell, something is not available. This will solve some or more overhead
 * implementing things but brings more memory usage.
 * If you dont really need some of the methodes: don't use them! As long the
 * initialisation of the super globals is not needed you are in a good
 * performace way. With or without this class.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Php_Globals
 */
class Mumsys_Php_Globals
    extends Mumsys_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * Cache container for uploaded files
     * @var array
     */
    private static $_files;

    /**
     * Remote user to be detected via {@link getRemoteUser()}
     * @var string
     */
    private static $_remoteuser = null;


    /**
     * Returns an eviroment variable in this order: getenv() befor _ENV befor
     * _SERVER.
     *
     * HINT: Will be change in V >= 2.2.0 (getServerVar() for $_SERVER)
     *
     * @param string $key ID to check for
     * @param mixed $default Return value
     *
     * @return mixed Value or $default if $key is not set/null
     */
    public static function getServerVar( $key, $default = null )
    {
        return self::_getEnvVar( $key, $default );
    }


    /**
     * Returns the php $_SERVER variable if set by given key.
     *
     * HINT: Will be change in V >= 2.2.0 (getServerServerVar() for env and server vars)
     *
     * @param string $key ID to check for
     * @param mixed $default Return value
     *
     * @return mixed Value or $default if $key was not set/null
     */
    public static function getServerServerVar( $key, $default = null )
    {
        if ( isset( $_SERVER[$key] ) ) {
            $default = $_SERVER[$key];
        }

        return $default;
    }


    /**
     * Returns an eviroment variable in this order: getenv() befor _ENV befor
     * _SERVER.
     *
     * @param string $key ID to check for
     * @param mixed $default Return value
     *
     * @return mixed Value or $default if $key is not set/null
     */
    public static function getEnvVar( $key, $default = null )
    {
        return self::_getEnvVar( $key, $default );
    }


    /**
     * Returns an eviroment variable in this order: getenv() befor _ENV befor
     * _SERVER.
     *
     * @param string $key ID to check for
     * @param mixed $default Return value
     *
     * @return mixed Value or $default if $key is not set/null
     */
    private static function _getEnvVar( $key, $default = null )
    {
        $server = & $_SERVER;
        $env = & $_ENV;

        if ( isset( $server[$key] ) ) {
            $default = $server[$key];
        } elseif ( isset( $env[$key] ) ) {
            $default = $env[$key];
        } elseif ( ( $x = getenv( $key ) ) ) {
            $default = $x;
        }

        return $default;
    }


    /**
     * Returns a session variable if exists.
     *
     * If key parameter is NULL the complete session will return if $_SESSION
     * was initialised otherwise $default returns.
     *
     * @param string $key ID to check for
     * @param mixed $default Default return value if key/ID not exists
     *
     * @return mixed|false Value or $default if $key is not set/null, false if
     * session not exists (missing session_start()?)
     */
    public static function getSessionVar( $key = null, $default = null )
    {
        if ( isset( $_SESSION ) && $key === null ) {
            return $_SESSION;
        }

        if ( isset( $_SESSION[$key] ) ) {
            $default = $_SESSION[$key];
        }

        return $default;
    }


    /**
     * Returns a post variable by given key.
     * If $key is NULL it will return all posts parameters
     *
     * @param string $key ID to check for
     * @param mixed $default Default return value if key not exists
     *
     * @return array|mixed Value or $default if $key not exists
     */
    public static function getPostVar( $key = null, $default = null )
    {
        $posts = & $_POST;

        if ( isset( $posts ) && $key === null ) {
            return $posts;
        }

        if ( isset( $posts[$key] ) ) {
            $default = $posts[$key];
        }

        return $default;
    }


    /**
     * Returns a get variable by given key.
     *
     * If $key is NULL and get vars exists it will return all get parameters
     *
     * @param string $key ID to check for
     * @param mixed $default Default return value if key not exists
     *
     * @return mixed Value or $default if $key is not set/null
     */
    public static function getGetVar( $key = null, $default = null )
    {
        $gets = & $_GET;

        if ( isset( $gets ) && $key === null ) {
            return $gets;
        }

        if ( isset( $gets[$key] ) ) {
            $default = $gets[$key];
        }

        return $default;
    }


    /**
     * Returns a cookie variable by given key.
     *
     * If $key is NULL and a cookie exists it will return all cookie parameters.
     *
     * @param string $key ID to check for
     * @param mixed $default Default return value if key not exists
     *
     * @return mixed Value or $default if $key is not set/null
     */
    public static function getCookieVar( $key = null, $default = array() )
    {
        $cookies = & $_COOKIE;

        if ( isset( $cookies ) && $key === null ) {
            return $cookies;
        }

        if ( isset( $cookies[$key] ) ) {
            $default = $cookies[$key];
        }

        return $default;
    }


    /**
     * Returns a list of uploaded file variables by given key.
     *
     * @todo create list of upload item interfaces to return
     *
     * If $key is NULL it will return all file parameter BUT in a new/
     * normalised way.: E.g:
     * upload file[] and file[]
     * files[file][0][name] and files[file][1][name] are available and NOT:
     * files[file][name][0] and files[file][name][1] (PHP default style)
     *
     * @param string $key ID to check for
     * @param mixed $default Default return value if key not exists
     *
     * @return mixed Value or $default if $key is not set/null
     */
    public static function getFilesVar( $key = null, $default = null )
    {
        if ( ! isset( $_FILES ) ) {
            return $default;
        }

        if ( self::$_files === null ) {
            $newFiles = array();

            foreach ( $_FILES as $index => $file ) {

                if ( !is_array( $file['name'] ) ) {
                    $newFiles[$index][] = $file;
                    continue;
                }

                foreach ( $file['name'] as $idx => $name ) {
                    // Mumsys_Upload_Item_Default() ?
                    $newFiles[$index][$idx] = array(
                        'name' => $name,
                        'type' => $file['type'][$idx],
                        'tmp_name' => $file['tmp_name'][$idx],
                        'error' => $file['error'][$idx],
                        'size' => $file['size'][$idx]
                    );
                }
            }

            self::$_files = $newFiles;
        }

        if ( $key === null ) {
            $default = self::$_files;
        }

        if ( isset( self::$_files[$key] ) ) {
            $default = self::$_files[$key];
        }

        return $default;
    }


    /**
     * Returns a list of uploaded file variables by given key.
     *
     * @depricated since version 1.0.1 Use getFile**s**Var()
     *
     * If $key is NULL it will return all file parameter BUT in a new/
     * normalised way.: E.g:
     * upload file[] and file[]
     * files[file][0][name] and files[file][1][name] are available and NOT:
     * files[file][name][0] and files[file][name][1] (PHP default style)
     *
     * @param string $key ID to check for
     * @param mixed $default Default return value if key not exists
     *
     * @return mixed Value or $default if $key is not set/null
     */
    public static function getFileVar( $key = null, $default = null )
    {
        if ( isset( $_FILES ) && $_FILES ) {
            if ( self::$_files === null ) {
                $newFiles = array();

                foreach ( $_FILES as $index => $file ) {
                    if ( !is_array( $file['name'] ) ) {
                        $newFiles[$index][] = $file;
                        continue;
                    }

                    foreach ( $file['name'] as $idx => $name ) {
                        $newFiles[$index][$idx] = array(
                            'name' => $name,
                            'type' => $file['type'][$idx],
                            'tmp_name' => $file['tmp_name'][$idx],
                            'error' => $file['error'][$idx],
                            'size' => $file['size'][$idx]
                        );
                    }
                }

                self::$_files = $newFiles;
            }

            if ( $key === null ) {
                $default = self::$_files;
            }

            if ( isset( self::$_files[$key] ) ) {
                $default = self::$_files[$key];
            }
        }

        return $default;
    }


    /**
     * Returns a global variable if set.
     *
     * If $key is NULL it will return all global parameters
     * This does not check the super global variables like _ENV, _SERVER, _GET,
     *  _POST, _REQUEST, _COOKIE, _FILES ... but they are in as array key
     *
     * @param string $key ID to check for
     * @param mixed $default Return value
     *
     * @return mixed Value or $default if $key is not set/null
     */
    public static function getGlobalVar( $key = null, $default = null )
    {
        if ( isset( $GLOBALS ) && $key === null ) {
            return $GLOBALS;
        }
        if ( isset( $GLOBALS[$key] ) ) {
            $default = $GLOBALS[$key];
        }

        return $default;
    }


    /**
     * Returns a global value or super global value.
     *
     * Looks in the other super globals if the global variable could not be
     * found, except the _FILES in the following order:
     *      GLOBALS
     *      befor (if cli mode) argv
     *      befor getenv()
     *      befor _ENV
     *      befor _SERVER
     *      befor _SESSION
     *      before _COOKIE
     *      befor _REQUEST: (binding through gpc order in php ini)
     *
     * Dont use it until you really need to look for a global variable!
     *
     * @param string $key ID to check for
     * @param mixed $default Return value if no other can be found
     *
     * @return mixed Value or $default if $key is not set/null
     */
    public static function get( $key, $default = null )
    {
        if ( isset( $GLOBALS[$key] ) ) {
            return $GLOBALS[$key];
        } elseif ( isset( $GLOBALS['_REQUEST'][$key] ) ) {
            $return = $GLOBALS['_REQUEST'][$key];
        } elseif ( isset( $GLOBALS['_COOKIE'][$key] ) ) {
            $return = $GLOBALS['_COOKIE'][$key];
        } elseif ( isset( $GLOBALS['_SESSION'][$key] ) ) {
            $return = $GLOBALS['_SESSION'][$key];
        } elseif ( PHP_SAPI == 'cli' && isset( $_SERVER['argv'][$key] ) ) {
            $return = $_SERVER['argv'][$key];
        } else {
            $return = self::_getEnvVar( $key, $default );
        }

        return $return;
    }


    /**
     * Returns the remote user.
     *
     * This can be the http autenticated user or the remote user when using
     * apache, a logname from the envoroment or the user from a shell account.
     *
     * @return string remote username
     */
    public static function getRemoteUser()
    {
        if ( self::$_remoteuser !== null ) {
            return self::$_remoteuser;
        }

        if ( isset( $_SERVER['PHP_AUTH_USER'] ) ) {
            self::$_remoteuser = (string) $_SERVER['PHP_AUTH_USER'];
        } else if ( isset( $_SERVER['REMOTE_USER'] ) ) {
            self::$_remoteuser = (string) $_SERVER['REMOTE_USER'];
        } else if ( isset( $_SERVER['USER'] ) ) {
            self::$_remoteuser = (string) $_SERVER['USER'];
        } else if ( isset( $_SERVER['LOGNAME'] ) ) {
            self::$_remoteuser = (string) $_SERVER['LOGNAME'];
        } else {
            self::$_remoteuser = 'unknown';
        }

        return self::$_remoteuser;
    }

}
