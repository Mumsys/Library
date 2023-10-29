<?php

/**
 * Mumsys_Config
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2009 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Config
 */


/**
 * Mumsys configuration.
 * @deprecated since version 1.0.1 Use Mumsys_Config_File or
 * Mumsys_Config_Default from now on
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Config
 */
class Mumsys_Config
    extends Mumsys_Config_File
    implements Mumsys_Config_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '1.0.1';

}
