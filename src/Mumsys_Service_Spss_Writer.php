<?php

/**
 * Mumsys_Service_Spss_Writer
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2015 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 * @version     1.0.0
 * Created: 2017-11-30
 */


/**
 * Writer class for SPSS parser.
 *
 * @see https://github.com/flobee/spss
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 */
class Mumsys_Service_Spss_Writer
    extends Mumsys_Service_Spss_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '2.1.0';

    /**
     * Initialize the writer interface.
     *
     * @param \SPSS\Sav\Reader $iface Writer interface
     * @throws Mumsys_Service_Spss_Exception
     */
    public function __construct( $iface )
    {
        parent::__construct( $iface );
    }

}
