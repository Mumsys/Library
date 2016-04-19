<?php


/* {{{ */
/**
 * Mumsys_Request_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 * ----------------------------------------------------------------------------
 * @author Florian Blasel <flobee.code@gmail.com>
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * ----------------------------------------------------------------------------
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Request
 * @filesource
 */
/* }}} */


/**
 * Request inferface
 *
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Request
 */
interface Mumsys_Request_Interface
{


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
    public function setProgramName( $value );


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
    public function setControllerName( $value );


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
    public function setActionName( $value );


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
     * @return Zend_Controller_Request_Abstract
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


    /**
     * Returns an input parameter by given key.
     *
     * @param string $key Key name to return
     * @param mixed $default Default value to return if the key not exists
     * @return mixed Returns the requested input value of the default
     */
    public function getParam( $key, $default = null );


    /**
     * Sets an input parameter
     *
     * If value is NULL it will be unset if key exists
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setParam( $key, $value );


    /**
     * Returns all input parameters
     *
     * @return array incomming request valiables (_GET + _POST)
     */
    public function getParams();


    /**
     * Set action parameters en masse; does not overwrite
     *
     * If value is NULL it will be unset if key exists
     *
     * @param array $array List of key/value pairs to set
     * @return self
     */
    public function setParams( array $array );


    /**
     * Unset input parameters
     *
     * @return self
     */
    public function clearParams();


    /**
     * Returns an cookie parameter by given key from _COOKIE.
     *
     * If $key is NULL it returns the complete _COOKIE array
     *
     * @param string $key Key name to return or null for all
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns requested value or the default if the key does not
     * extists
     */
    public function getCookie( $key = null, $default = null );

}