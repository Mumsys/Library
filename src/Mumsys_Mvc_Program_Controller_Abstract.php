<?php

/**
 * Mumsys_Mvc_Program_Controller_Abstract
 * for MUMSYS (Multi User Management System)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2010 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc_Program
 * Created: 2010-08-19 (svn)
 */


/**
 * Mumsys program abstract contains methodes to be used in program controllers.
 * On construction it checks the basic access and the requested programm or can
 * create and return the default display/view object for you.
 * E.g:
 * Programs/Users/Index.php ->
 *      Mumsys_Program_User_Index_Controller extends Mumsys_Mvc_Program_Abstract
 * $view = $this->_getDisplay()
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc_Program
 */
abstract class Mumsys_Mvc_Program_Controller_Abstract
    extends Mumsys_Abstract
{
    /**
     * Version ID information
     */
    const VERSION = '1.0.1';

    /**
     * Context item which must be available for all mumsys objects
     * @var Mumsys_Context_Interface
     */
    protected $_context;

    /**
     * Program configuration object which comes basicly from settings.php file
     * which can exists.
     * @var Mumsys_Mvc_Program_Config
     */
    protected $_programConfig;


    /**
     * Initializes the program object.
     *
     * @param Mumsys_Context_Interface $context Context item
     * @param Mumsys_Mvc_Program_Config $programConfig Program config object
     * containing all configuration values which may comes from setting.php
     */
    public function __construct( Mumsys_Context_Interface $context,
        Mumsys_Mvc_Program_Config $programConfig )
    {
        $this->_programConfig = $programConfig;
        $this->_context = $context;
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
     * @return Mumsys_Mvc_Display_Control_Interface at last instance
     */
    public function getDisplay( array $params = array(),
        $outputType = 'default', $outputComplexity = 'default' )
    {
        $display = new Mumsys_Mvc_Display_Factory( $this->_context );

        return $display->load( $outputType, $outputComplexity, $params );
    }


    /**
     * Initialize a model.
     *
     * @param string $program Name of the program to load
     * @param string $model Name of the model to load
     *
     * @return Mumsys_Mvc_Program_Model_Interface
     */
    protected function loadModel( $program, $model )
    {
        $className = sprintf( 'Mumsys_Program_%1$s_%2$s_Model', $program, $model );
        if ( class_exists( $className ) ) {
            return new $className( $this->_context );
        }

        $message = sprintf( 'Unable to load the model: "%1$s"', $className );
        $code = Mumsys_Exception::ERRCODE_404;
        throw new Mumsys_Mvc_Program_Exception( $message, $code );
    }

}
