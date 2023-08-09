<?php

/**
 * Mumsys_Mvc_Controller_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2014 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Mvc
 * @version     1.0.0
 * Created: 2014-01-10
 */


/**
 * Mumsys abstract mvc base controller
 *
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Mvc
 */
abstract class Mumsys_Mvc_Controller_Abstract
{
    /**
     * Version ID information
     */
    const VERSION = '1.0.1';

    /**
     * Mumsys context
     * @var Mumsys_Context_Interface
     */
    protected $_context;

    /**
     * All config values from mumsys config
     * @var array
     */
    protected $_configs;

    /**
     * Name of the current program
     * @var string
     */
    protected $_programName;

    /**
     * Name of the current controller
     * @var string
     */
    protected $_controllerName;

    /**
     * Name of the current action
     * @var string
     */
    protected $_actionName;


    /**
     * Initialise Mvc controller
     *
     * @param Mumsys_Context $context
     */
    public function __construct( Mumsys_Context_Interface $context )
    {
        $this->_context = $context;
        /** @todo Timing problem? when are all configs loaded? */
        $this->_configs = $context->getConfig()->getAll();
    }


    /**
     * Returns the controller location by given program and controller
     * variables. If the controller can not by found the default controller
     * will be returned!
     *
     * @param string $program Name of the program
     * @param string $controller Name of the controller
     *
     * @return string Returns the location of the controller file
     */
    public function getControllerLocation( $program = null, $controller = null )
    {
        $this->_programName = preg_replace( '/ +/i', '_', ucwords( $program ) );
        $this->_controllerName = $controller;

        if ( !$this->checkControllerLocation( $program, $controller ) ) {
            $this->_programName = $this->_configs['defaultProgram'];
            $this->_controllerName = $this->_configs['defaultController'];
        }

        return $this->_configs['pathPrograms'] . $this->_programName . '/'
            . $this->_controllerName . 'Controller.php';
    }


    /**
     * Checks if a controller location exists.
     *
     * @param string $program Name of the program
     * @param string $controller Name of the controller (filename without
     * extension)
     * @return boolean Returns true on success of false if the location not exists
     */
    public function checkControllerLocation( $program = null, $controller = null )
    {
        if ( preg_match( MUMSYS_REGEX_AZ09X, $program )
            && preg_match( MUMSYS_REGEX_AZ09X, $controller )
        ) {
            $file = $this->_configs['pathPrograms'] . $program . '/'
                . $controller . 'Controller.php';

            if ( file_exists( $file ) ) {
                return true;
            }
        }

        return false;
    }


    /**
     * Returns the name ot the current program.
     *
     * @return string Name of the current program
     */
    public function getProgramName()
    {
        return $this->_programName;
    }


    /**
     * Returns the name ot the current controller
     *
     * @return string Name of the current controller
     */
    public function getControllerName()
    {
        return $this->_controllerName;
    }


    /**
     * Returns the name ot the current action.
     *
     * @return string Name of the current action
     */
    public function getActionName()
    {
        return $this->_actionName;
    }

}
