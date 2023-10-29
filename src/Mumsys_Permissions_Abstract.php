<?php

/**
 * Mumsys_Permissions_Abstract
 * for MUMSYS (Multi User Management System)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Permissions
 */


/**
 * Class to deal with the permissions (acl)
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Permissions
 */
abstract class Mumsys_Permissions_Abstract
    extends Mumsys_Abstract
    implements Mumsys_Permissions_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '3.1.0';


    /**
     * Tracks the current request.
     * old alias function track_onlineuser
     *
     * @throws Mumsys_Permissions_Exception Throws exception on errors
     */
    public function trackRequest()
    {
        throw new Mumsys_Permissions_Exception( __METHOD__ . ' Not implemented yet' );
    }


    /**
     * get/set language (get_language)
     * load basic language and program language
     *
     * @param string|false $language Languge to load otherwise the default
     * language will be loaded
     *
     * @return boolean true on success
     * @throws Mumsys_Permissions_Exception Throws exception on errors
     */
    public function languageLoad( $language = false )
    {
        throw new Mumsys_Permissions_Exception( __METHOD__ . ' Not implemented yet' );
    }

}
