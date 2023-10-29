<?php

/**
 * Mumsys_Context_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2015 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Context
 * @version     3.0.0
 */


/**
 * Mumsys context interface.
 *
 * Component container to place in needed constructs like MVC or other
 * application structures. This one will be used for the mumsys library and
 * holds only the basic parts of/for it. Like: session, config, database,
 * logger, translation (I18n) and request objects.
 * It can be also used for other projects by extending it and implement your
 * individual components to hold eg: In mvc context you may have getter/setter
 * for the view/controller and models. This would be e.g. in the
 * Cms_Context_* object and extends this class.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Context
 */
interface Mumsys_Context_Interface
{
    /**
     * Returns the config object.
     *
     * @return Mumsys_Config_Interface
     *
     * @throws Mumsys_Exception if config was not set
     */
    public function getConfig();


    /**
     * Register the default config object.
     *
     * @param Mumsys_Config_Interface $config
     *
     * @throws Mumsys_Exception if config was already set
     */
    public function registerConfig( Mumsys_Config_Interface $config );


    /**
     * Returns the session object.
     *
     * @return Mumsys_Session_Interface Returns the Mumsys_Session object
     *
     * @throws Mumsys_Exception Throws exception if object was not set
     */
    public function getSession();


    /**
     * Register the default session object.
     *
     * @param Mumsys_Session_Interface $session Session object
     *
     * @throws Mumsys_Exception Throws exception if object was already set
     */
    public function registerSession( Mumsys_Session_Interface $session );


    /**
     * Returns the database object.
     *
     * @return Mumsys_Db_Driver_Interface Returns the database object
     * @throws Mumsys_Exception If database was not set
     */
    public function getDatabase();


    /**
     * Sets the default database object.
     *
     * @param Mumsys_Db_Driver_Interface $db Database object
     * @throws Mumsys_Exception Throws exception if object was already set
     */
    public function registerDatabase( Mumsys_Db_Driver_Interface $db );


    /**
     * Replace the database object.
     *
     * @param Mumsys_Db_Driver_Interface $db Database object
     */
    public function replaceDatabase( Mumsys_Db_Driver_Interface $db );


    /**
     * Returns the translation object.
     *
     * @return Mumsys_I18n_Interface Returns translation object
     *
     * @throws Mumsys_Exception If object was not set
     */
    public function getTranslation();


    /**
     * Sets the translation object.
     *
     * @param Mumsys_I18n_Interface $translate Translation object
     *
     * @throws Mumsys_Exception Throws exception if object was already set
     */
    public function registerTranslation( Mumsys_I18n_Interface $translate );


    /**
     * Returns the logger object.
     *
     * @return Mumsys_Logger_Interface Returns the logger object
     *
     * @throws Mumsys_Exception If class was not set
     */
    public function getLogger();


    /**
     * Sets the logger object.
     *
     * @param Mumsys_Logger_Interface $logger Logger object
     * @throws Mumsys_Exception Throws exception if the object was already set
     */
    public function registerLogger( Mumsys_Logger_Interface $logger );


    /**
     * Returns the display/ frontend controller object.
     *
     * @return Mumsys_Mvc_Display_Control_Interface
     * @throws Mumsys_Mvc_Display_Exception Throws exception if the object was not set
     */
    public function getDisplay();


    /**
     * Replaces the display/ frontend controller object.
     *
     * @param Mumsys_Mvc_Display_Control_Interface $display Display controller to set
     */
    public function replaceDisplay( Mumsys_Mvc_Display_Control_Interface $display );


    /**
     * Returns the controller request object.
     *
     * @return Mumsys_Request_Interface Returns the request object
     */
    public function getRequest(): Mumsys_Request_Interface;


    /**
     * Reqisters the request object.
     *
     * @param Mumsys_Request_Interface $request Request object
     *
     * @throws Mumsys_Exception Throws exception if object was already set
     */
    public function registerRequest( Mumsys_Request_Interface $request );


    /**
     * Replace the request object.
     *
     * @param Mumsys_Request_Interface $request Request object
     */
    public function replaceRequest( Mumsys_Request_Interface $request );


    /**
     * Returns a generic, already registered, interface/ object.
     *
     * @param string $interface Name of the Interface the object implements
     * @param mixed $default Default value to return if interface not exists
     *
     * @return object Retuns the requested interface/object
     * @throws Mumsys_Exception Throws exception if the object was not set before and if default was not set
     */
    public function getGeneric( $interface, $default = null );


    /**
     * Registers a new interface/ object.
     *
     * @param string $interface Name of the Interface the object implements
     * @param object $value Object to register
     *
     * @throws Mumsys_Exception Throws exception if the object exists or if
     * incoming type is invalid
     */
    public function registerGeneric( $interface, $value );
}
