<?php

/**
 * Mumsys_Session_None
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2005 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Session
 */


/**
 * Memory based wrapper class as session interface to be used as dummy
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Session
 */
class Mumsys_Session_None
    extends Mumsys_Session_Abstract
    implements Mumsys_Session_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.1';


    /**
     * Initialize the session object.
     *
     * @param string $appKey Application domain or installation key
     */
    public function __construct( $appKey = 'mumsys' )
    {
        parent::__construct( array(), 'none', $appKey );
    }


    /**
     * Stores session informations and closes it.
     */
    public function __destruct()
    {
        $this->_records = array();
    }

}
