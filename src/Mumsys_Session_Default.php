<?php

/**
 * Mumsys_Session
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2005 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Session
 * Created: 2005-01-01
 */


/**
 * Class to deal with the php session
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Session
 */
class Mumsys_Session_Default
    extends Mumsys_Session_Abstract
    implements Mumsys_Session_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '1.1.0';


    /**
     * Initialize the session object.
     *
     * @param string $appkey Application key the session belongs to.
     */
    public function __construct( $appkey = 'mumsys' )
    {
        /**
         * session_cache_limiter('private');
         * http://de2.php.net/manual/en/function.session-cache-limiter.php
         * session_cache_expire(180);
         * echo $cache_expire = session_cache_expire();
         */
        if ( session_status() == PHP_SESSION_NONE && !headers_sent() ) {
            session_start();
        }

        parent::__construct(
            Mumsys_Php_Globals::getSessionVar( null, array() ),
            session_id(),
            $appkey
        );
    }


    /**
     * Stores session informations managed by this object.
     */
    public function __destruct()
    {
        $_SESSION[$this->_id] = parent::getCurrent();
        session_write_close();
    }


    /**
     * Clears and unsets the current session at all.
     */
    public function clear()
    {
        parent::clear();
        session_unset();
    }

}
