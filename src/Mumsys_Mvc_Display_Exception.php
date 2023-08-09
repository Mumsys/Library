<?php

/**
 * Mumsys_Mvc_Display_Exception
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 * @version     1.0.0
 * Created: 2016-01-30
 */
/* }}} */


/**
 * Generic Exception class.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 */
class Mumsys_Mvc_Display_Exception
    extends Mumsys_Mvc_Exception
{
    /**
     * Technical error. General error code if no other code is possible.
     */
    const ERRCODE_DEFAULT = 1;

    /**
     * HTTP 401 error code
     */
    const ERRCODE_HTTP401 = 401;

    /**
     * HTTP 403 error code
     */
    const ERRCODE_HTTP403 = 403;

    /**
     * HTTP 500 error code, server error
     */
    const ERRCODE_HTTP500 = 500;

    /**
     * HTTP 503 error code
     */
    const ERRCODE_HTTP503 = 503;

}
