<?php

/**
 * Mumsys_Request_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Request
 */


/**
 * Abstract class to get input parameters.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Request
 */
abstract class Mumsys_Request_Abstract
    extends Mumsys_Abstract
    implements Mumsys_Request_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '1.0.1';

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
     * Incomming request parameters
     * @var array
     */
    protected $_input = array();

    /**
     * Incomming cookie parameters
     * @var array
     */
    protected $_inputCookie = array();

    /**
     * Request ID
     * @var string
     */
    private static $_requestId = null;


    /**
     * Initialise the request object using _GET and _POST arrays.
     *
     * After init the global arrays _POST and _GET will be reset!
     *
     * @param array $options Optional initial options e.g.:
     * 'programKey','controllerKey', 'actionKey',
     */
    public function __construct( array $options = array() )
    {
        if ( isset( $options['programKey'] ) ) {
            $this->setProgramKey( $options['programKey'] );
        }

        if ( isset( $options['controllerKey'] ) ) {
            $this->setControllerKey( $options['controllerKey'] );
        }

        if ( isset( $options['actionKey'] ) ) {
            $this->setActionKey( $options['actionKey'] );
        }

        $_cookies = Mumsys_Php_Globals::getCookieVar();
        if ( isset( $_cookies ) && is_array( $_cookies ) ) {
            $this->_inputCookie = $_cookies;
        }

        unset( $_cookies, $options );
    }


    /**
     * Returns a unique id for the current request.
     *
     * If there is already a http header (HTTP_X_REQUEST_ID) for the
     * request ID it will be used otherwise a new individual ID will be
     * generated to be set to the enviroment valiable:
     * _SERVER['HTTP_X_REQUEST_ID'].
     *
     * @return string x-http-request-id
     */
    public function getRequestId()
    {
        if ( self::$_requestId !== null ) {
            return self::$_requestId;
        }

        self::$_requestId = Mumsys_Php_Globals::getServerVar( 'HTTP_X_REQUEST_ID', false );

        if ( self::$_requestId === false ) {
            self::$_requestId = uniqid( '', true );
        } else {
            // VALID RFC 4211 COMPLIANT Universally Unique IDentifiers (UUID) check (V5)
            $regex = '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i';
            if ( !preg_match( $regex, self::$_requestId ) ) {
                self::$_requestId = uniqid( '', true );
            }
        }

        return self::$_requestId;
    }


    /**
     * Retrieve the program/module name
     *
     * @return string
     */
    public function getProgramName()
    {
        if ( $this->_programName === null ) {
            $this->_programName = $this->getParam( $this->getProgramKey() );
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
            $this->_controllerName = $this->getParam( $this->getControllerKey() );
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
            $this->_actionName = $this->getParam( $this->getActionKey() );
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
            $this->setParam( $this->getActionKey(), $value );
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


    /**
     * Returns an input parameter by given key.
     *
     * @param string $key Key name to return
     * @param mixed $default Default value to return if the key not exists
     * @return mixed Returns the requested input value of the default
     */
    public function getParam( $key, $default = null )
    {
        $key = (string) $key;
        if ( isset( $this->_input[$key] ) ) {
            $return = $this->_input[$key];
        } else {
            $return = $default;
        }

        return $return;
    }


    /**
     * Sets an input parameter
     *
     * If value is NULL it will be unset if key exists
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setParam( $key = '', $value = null )
    {
        $key = (string) $key;

        if ( ( $value === null ) && isset( $this->_input[$key] ) ) {
            unset( $this->_input[$key] );
        } elseif ( $value !== null ) {
            $this->_input[$key] = $value;
        }

        return $this;
    }


    /**
     * Returns all input parameters
     *
     * @return array incomming request valiables (_GET + _POST)
     */
    public function getParams()
    {
        return $this->_input;
    }


    /**
     * Set/ add action parameters en masse; does not overwrite or unset existing
     * keys.
     *
     * If value is NULL it will be unset if key exists
     *
     * @param array $array List of key/value pairs to set
     *
     * @return self
     */
    public function setParams( array $array )
    {
        $this->_input = $this->_input + $array;

        foreach ( $array as $key => $value ) {
            if ( $value === null ) {
                unset( $this->_input[$key] );
            }
        }

        return $this;
    }


    /**
     * Unsets/ resets all input parameters.
     *
     * @return self
     */
    public function clearParams()
    {
        $this->_input = array();

        return $this;
    }


    /**
     * Returns an cookie parameter by given key from _COOKIE.
     * If $key is empty it will return the complete _COOKIE array
     *
     * @param string $key Key name to return or null|false for all
     * @param mixed $default Default value to return if key not found
     * @return mixed Returns requested value or the default if the key does not
     * extists
     */
    public function getInputCookie( $key = null, $default = null )
    {
        if ( empty( $key ) ) {
            return $this->_inputCookie;
        }

        $key = (string) $key;
        if ( isset( $this->_inputCookie[$key] ) ) {
            $return = $this->_inputCookie[$key];
        } else {
            $return = $default;
        }

        return $return;
    }

}
