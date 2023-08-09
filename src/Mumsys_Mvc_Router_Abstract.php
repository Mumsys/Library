<?php

/**
 * Mumsys_Mvc_Router_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 */


/**
 * Abstract router class to get input parameters.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 */
abstract class Mumsys_Mvc_Router_Abstract
    extends Mumsys_Abstract
    implements Mumsys_Mvc_Router_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '1.0.0';

    /**
     * The current programm name
     * @var string
     */
    protected $_programName;

    /**
     * Default program key name for retrieving the program parameter
     * @var string
     */
    protected $_programNameKey = 'program';

    /**
     * The current controller name
     * @var string
     */
    protected $_controllerName;

    /**
     * Default controller key name for retrieving the controller parameter
     * @var string
     */
    protected $_controllerNameKey = 'controller';

    /**
     * The current action name
     * @var string
     */
    protected $_actionName;

    /**
     * Default action key name for retrieving the "action" parameter
     * @var string
     */
    protected $_actionNameKey = 'action';

    /**
     * Request interface
     * @var Mumsys_Request_Interface
     */
    protected $_request;

    /**
     * The route. Possible options: rewrite|http|html|js|json|console|shell|id|hash
     * Most of them only decide the format to output or return. @see setRoute()
     * @var string
     */
    protected $_route;


    /**
     * Initialise the router object.
     *
     * @param string $route Route to set. @see setRoute()
     * @param Mumsys_Request_Interface $request Request interface
     * @param array $options Optional initial options e.g.: 'programKey',
     * 'controllerKey', 'actionKey' mappings to initialize the object
     */
    public function __construct( Mumsys_Request_Interface $request,
        $route = 'default', array $options = array() )
    {
        $this->_request = $request;
        $this->setRoute( $route );

        if ( isset( $options['programKey'] ) ) {
            $this->setProgramKey( $options['programKey'] );
        }

        if ( isset( $options['controllerKey'] ) ) {
            $this->setControllerKey( $options['controllerKey'] );
        }

        if ( isset( $options['actionKey'] ) ) {
            $this->setActionKey( $options['actionKey'] );
        }
    }


    /**
     * Sets/ replaces the route.
     *
     * Note: Not all route options are possible from scratch.
     * E.g. "hash" need a translator from db or config file to map the hash to
     * program/controller/action
     *
     * @param string $route Route to be set:
     * default|html = Html wrapped using ampersand "&amp;": program=Admin&ampcontroller=Index&amp;action=show
     * rewrite = Rewrite rule; program/controller/action
     * http|js|json = Http or js/json/console using "&" as delimiter
     * console|shell = Using key=value pairs
     * id = Dot seperated values. Like id=program.controller.action
     * hash = md5 of program controller action
     *
     * @throws Mumsys_Mvc_Router_Exception
     */
    public function setRoute( $route )
    {
        $routes = array('default', 'rewrite', 'http', 'html', 'js', 'json', 'console', 'shell', 'id', 'hash');
        if ( !in_array( $route, $routes ) ) {
            $message = sprintf(
                'Invalid route "%1$s" to set. Possible routes: "%2$s"', $route,
                implode( '|', $routes )
            );
            throw new Mumsys_Mvc_Router_Exception( $message );
        }

        $this->_route = (string) $route;
    }


    /**
     * Retrieve the module name
     *
     * @return string
     */
    public function getProgramName()
    {
        if ( $this->_programName === null ) {
            $this->_programName = $this->_request->getParam( $this->getProgramKey() );
        }

        return $this->_programName;
    }


    /**
     * Sets/ replaces the program name.
     *
     * @param string $value Name of the program
     * @return self
     */
    public function setProgramName( $value = null )
    {
        $this->_programName = ucwords( (string) $value );
        return $this;
    }


    /**
     * Returns the controller name.
     *
     * @return string Name of the controller
     */
    public function getControllerName()
    {
        if ( $this->_controllerName === null ) {
            $this->_controllerName = $this->_request->getParam( $this->getControllerKey() );
        }

        return $this->_controllerName;
    }


    /**
     * Sets/ replaces the controller name.
     *
     * @param string $value Name of the controller
     * @return self
     */
    public function setControllerName( $value = null )
    {
        $this->_controllerName = ucwords( (string) $value );

        return $this;
    }


    /**
     * Retrieve the action name.
     *
     * @return string Name of the action
     */
    public function getActionName()
    {
        if ( $this->_actionName === null ) {
            $this->_actionName = $this->_request->getParam( $this->getActionKey() );
        }

        return $this->_actionName;
    }


    /**
     * Sets/ replaces the action name
     *
     * @param string $value Name of the action
     * @return self
     */
    public function setActionName( $value = null )
    {
        $this->_actionName = strtolower( (string) $value );
        if ( $value === null ) {
            $this->_request->setParam( $this->getActionKey(), $value );
        }

        return $this;
    }


    /**
     * Returns the program name.
     *
     * @return string Name of the program
     */
    public function getProgramKey()
    {
        return $this->_programNameKey;
    }


    /**
     * Sets/ replaces the program key.
     *
     * @param string $key Key of the program to idenify from a request
     * @return self
     */
    public function setProgramKey( $key = 'program' )
    {
        $this->_programNameKey = (string) $key;

        return $this;
    }


    /**
     * Retuns the name of the controller key
     *
     * @return string Key name of the controller key to idenify from a request
     */
    public function getControllerKey()
    {
        return $this->_controllerNameKey;
    }


    /**
     * Sets/ replaces the controller key name
     *
     * @param string $key Key name of the controller
     * @return self
     */
    public function setControllerKey( $key = 'controller' )
    {
        $this->_controllerNameKey = (string) $key;

        return $this;
    }


    /**
     * Returns the action key name
     *
     * @return string
     */
    public function getActionKey()
    {
        return $this->_actionNameKey;
    }


    /**
     * Sets/ replaces the action key name.
     *
     * @param string $key Name of the action
     * @return self
     */
    public function setActionKey( $key = 'action' )
    {
        $this->_actionNameKey = (string) $key;

        return $this;
    }

}
