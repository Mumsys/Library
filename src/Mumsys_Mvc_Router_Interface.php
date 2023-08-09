<?php

/**
 * Mumsys_Mvc_Router_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 * @version     1.0.0
 */


/**
 * Abstract request class to get input parameters.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 */
interface Mumsys_Mvc_Router_Interface
{
    /**
     * Initialise the router object.
     *
     * @param Mumsys_Request_Interface $request Request interface
     * @param array $options Optional initial options e.g.: 'programKey',
     * 'controllerKey', 'actionKey' mappings to initialize the object
     */
    public function __construct( Mumsys_Request_Interface $request,
        array $options = array() );


    /**
     * Retrieve the module name
     *
     * @return string
     */
    public function getProgramName();


    /**
     * Sets/ replaces the program name.
     *
     * @param string $value Name of the program
     * @return self
     */
    public function setProgramName( $value = null );


    /**
     * Returns the controller name.
     *
     * @return string Name of the controller
     */
    public function getControllerName();


    /**
     * Sets/ replaces the controller name.
     *
     * @param string $value Name of the controller
     * @return self
     */
    public function setControllerName( $value = null );


    /**
     * Retrieve the action name.
     *
     * @return string Name of the action
     */
    public function getActionName();


    /**
     * Sets/ replaces the action name
     *
     * @param string $value Name of the action
     * @return self
     */
    public function setActionName( $value = null );


    /**
     * Returns the program name.
     *
     * @return string Name of the program
     */
    public function getProgramKey();


    /**
     * Sets/ replaces the program key.
     *
     * @param string $key Key of the program to idenify from a request
     * @return self
     */
    public function setProgramKey( $key = 'program' );


    /**
     * Retuns the name of the controller key
     *
     * @return string Key name of the controller key to idenify from a request
     */
    public function getControllerKey();


    /**
     * Sets/ replaces the controller key name
     *
     * @param string $key Key name of the controller
     * @return self
     */
    public function setControllerKey( $key = 'controller' );


    /**
     * Returns the action key name
     *
     * @return string
     */
    public function getActionKey();


    /**
     * Sets/ replaces the action key name.
     *
     * @param string $key Name of the action
     * @return self
     */
    public function setActionKey( $key = 'action' );

}
