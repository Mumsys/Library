<?php declare(strict_types=1);

/**
 * Mumsys_Mvc_Display_Helper_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 * @version     2.0.0
 * Created: 2016-01-30
 */

/**
 * Display Helper interface for additional functionality
 */
interface Mumsys_Mvc_Display_Helper_Interface
{
    /**
     * Initialize the helper controller
     *
     * @param Mumsys_Mvc_Display_Control_Interface $display Display which inits this helper
     * @param Mumsys_Context_Interface $context Context object
     * @param array $helperOptions Options to set on construction
     */
    public function __construct( Mumsys_Mvc_Display_Control_Interface $display,
        Mumsys_Context_Interface $context,
        array $helperOptions = array() );

}
