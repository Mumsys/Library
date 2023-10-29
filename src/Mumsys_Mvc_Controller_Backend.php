<?php

/**
 * Mumsys_Mvc_Controller_Backend
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @copyright Copyright (c) 2011 by Florian Blasel for FloWorks Company
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 * @version     1.0.0
 * Created: 2014-01-10
 */


/**
 * Mumsys base backend controller.
 *
 * Initializes by a program/module controller. Implements checks for controller,
 * config, boxes or settings of file locations and so on
 *
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Mvc
 */
class Mumsys_Mvc_Controller_Backend
    extends Mumsys_Mvc_Controller_Abstract
{
    /**
     * Version ID information
     */
    const VERSION = '1.0.0';


    /**
     * Initialize the programm controller and run its requested or default
     * action.
     *
     * @param string $programName Program to select
     * @param string $controllerName Program controller to select
     * @param string $actionName Program action to use
     *
     * @throws Mumsys_Mvc_Controller_Exception
     */
    public function initProgram( $programName, $controllerName, $actionName )
    {
        $config = $this->_configs;
        $programSettings = array();

        $program = ucfirst( $programName );
        $controller = ucfirst( $controllerName );
        $action = ucfirst( $actionName );
        /**
         * include settings file if exists for a program.
         * if not, please always create a blank file (performance!)
         */
        if ( file_exists( $config['pathPrograms'] . $program . '/config/default.php' ) ) {
            $programSettings = require_once $config['pathPrograms'] . $program . '/settings.php';

            if ( !is_array( $programSettings ) ) {
                $this->_context->getPermissions()->trackRequest(); // @phpstan-ignore-line
                $mesg = 'Warning. inclusion of settings failed or invalid type';
                $code = Mumsys_Exception::ERRCODE_DEFAULT;
                throw new Mumsys_Mvc_Controller_Exception( $mesg, $code );
            }
        }

        $programConfig = new Mumsys_Mvc_Program_Config( $this->_context, $programSettings );

        $file = $this->getControllerLocation( $program, $controller );

        if ( $file ) {
            include_once $file;

            $classname = 'Mumsys_Program_' . $program . '_' . $controller . '_Controller';
            $controllerAction = $action . 'Action';

            if ( !class_exists( $classname, false ) ) {
                $mesg = sprintf( 'Class "%1$s" not found', $classname );
                $code = Mumsys_Exception::ERRCODE_DEFAULT;
                throw new Mumsys_Mvc_Controller_Exception( $mesg, $code );
            }

            $controllerObject = new $classname( $this->_context, $programConfig );

            if ( method_exists( $controllerObject, $controllerAction ) ) {
                $this->_actionName = $action;
                $controllerObject->$controllerAction();
            } else {
                $mesg = sprintf( 'Method "%1$s" not found', $controllerAction );
                $code = Mumsys_Exception::ERRCODE_DEFAULT;
                throw new Mumsys_Mvc_Controller_Exception( $mesg, $code );
            }
        } else {
            $mesg = sprintf(
                'Abort! Unable to load program "%1$s" / "%2$s"',
                $program,
                $controller
            );
            $code = Mumsys_Exception::ERRCODE_DEFAULT;
            throw new Mumsys_Mvc_Controller_Exception( $mesg, $code );
        }
    }

}
