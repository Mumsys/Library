<?php

/**
 * Mumsys_Logger_Decorator_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2005 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 * @version     3.0.0
 */


/**
 * Decorator interface to generate log messages
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 */
interface Mumsys_Logger_Decorator_Interface
    extends Mumsys_Logger_Interface
{
    /**
     * Initialize the decorator logger object
     *
     * @param Mumsys_Logger_Interface $logger Logger object to be decorated
     */
    public function __construct( Mumsys_Logger_Interface $logger );

}
