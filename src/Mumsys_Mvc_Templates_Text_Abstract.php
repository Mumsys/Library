<?php

/**
 * Mumsys_Mvc_Templates_Text_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @author Florian Blasel <flobee.code@gmail.com>
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 * Created: 2016-02-04
 */


/**
 * Default abstract class for stdout output e.g. text for the shell output
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 */
abstract class Mumsys_Mvc_Templates_Text_Abstract
    extends Mumsys_Mvc_Display_Control_Stdout_Abstract
{
    /**
     * Version ID information
     */
    const VERSION = '1.0.0';

    /**
     * Context item which must be available for all mumsys objects
     * @var Mumsys_Context
     */
    protected $_context;

    /**
     * Page title for the output
     * @var string
     */
    protected $_pagetitle = '';


    /**
     * Initialize the display text object.
     *
     * @param Mumsys_Context $context Context object
     * @param array $options Optional options to setup the frontend controller
     */
    public function __construct( Mumsys_Context_Interface $context,
        array $options = array() )
    {
        $this->_context = $context;

        if ( isset( $options['pageTitle'] ) ) {
            $this->_pagetitle = (string) $options['pageTitle'];
        }
    }
}
