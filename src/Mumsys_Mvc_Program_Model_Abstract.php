<?php

/**
 * Mumsys_Mvc_Program_Model_Abstract
 * for MUMSYS (Multi User Management System)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc_Program
 */


/**
 * Mumsys program model abstract contains methodes to be used in the program
 * model.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc_Program
 */
abstract class Mumsys_Mvc_Program_Model_Abstract
    extends Mumsys_Abstract
    implements Mumsys_Mvc_Program_Model_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '1.0.0';

    /**
     * Context item which must be available for all mumsys objects
     * @var Mumsys_Context_Interface
     */
    protected $_context;


    /**
     * Initializes the program model object.
     *
     * @param Mumsys_Context_Interface $context Context item
     */
    public function __construct( Mumsys_Context_Interface $context )
    {
        $this->_context = $context;
    }

}
