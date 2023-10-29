<?php

/**
 * Mumsys_Mvc_Program_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2010 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 * @version     1.0.0
 * Created: 2010-08-19
 */


/**
 * Mumsys program abstract contains methodes to be used in program controllers.
 * On construction it checks the basic access and the requested programm or can
 * create and return the default display/view object for you.
 * E.g:
 * Programs/Users/Index.php ->
 *      Mumsys_Program_User_Index_Controller extends Mumsys_Mvc_Program_Abstract
 * $view = $this->_getDisplay()
 */
abstract class Mumsys_Mvc_Program_Abstract
    extends Mumsys_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * Context item which must be available for all mumsys objects
     * @var Mumsys_Context
     */
    protected $_context;

    /**
     * Program configuration object
     * @var Mumsys_Mvc_Program_Config
     */
    protected $_programConfig;

    /**
     * Config object.
     * @var Mumsys_Config_Interface
     */
    protected $_config;


    /**
     * Initializes the program object.
     *
     * @param Mumsys_Context $context Context item
     * @param Mumsys_Mvc_Program_Config $programConfig Program config object
     * containing all configuration values which may comes from setting.php
     */
    public function __construct( Mumsys_Context $context,
        Mumsys_Mvc_Program_Config $programConfig )
    {
        $this->_programConfig = $programConfig;
        $this->_context = $context;
        $this->_config = $context->getConfig();
    }


    /**
     * Returns the display/ view.
     *
     * @param array $params Mixed parametes for the construction and to setup
     * the templates
     * @param string $outputType Type of output e.g: default|Text|Html (general
     * complexity of methodes for the output)
     * @param string $outputComplexity Complexity for the output e.g: default|
     * extended (includes more view helper which maybe useful or required)
     *
     * @return Mumsys_Mvc_Display_Control_Interface|Mumsys_Mvc_Display_Control_Http_Interface
     */
    protected function _getDisplay( array $params = array(),
        $outputType = 'default', $outputComplexity = 'default' )
    {
        $display = new Mumsys_Mvc_Display_Factory( $this->_context );
        return $display->load( $params, $outputType, $outputComplexity );
    }

}
