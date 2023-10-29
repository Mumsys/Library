<?php

/**
 * Mumsys_Mvc_Program_Model_Interface
 * for MUMSYS (Multi User Management System)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc_Program
 * @version     1.0.0
 * Created: 2016-03-16
 */


/**
 * Mumsys program model abstract contains methodes to be used in the program
 * model.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc_Program
 */
interface Mumsys_Mvc_Program_Model_Interface
{
    /**
     * Initializes the program model object.
     *
     * @param Mumsys_Context_Interface $context Context item
     */
    public function __construct( Mumsys_Context_Interface $context );

}
