<?php

/**
 * Mumsys_Request_Default
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
 * Default request class to get input parameters $_GET, $POST, $_COOKIE.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Request
 */
class Mumsys_Request_Default
    extends Mumsys_Request_Abstract
    implements Mumsys_Request_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '1.1.0';

    /**
     * Incomming get parameters
     * @var array
     */
    protected $_inputGet = array();

    /**
     * Incomming post parameters
     * @var array
     */
    protected $_inputPost = array();


    /**
     * Initialise the request object using _GET and _POST arrays.
     *
     * @param array $options Optional initial options e.g.:
     * 'programKey','controllerKey', 'actionKey',
     */
    public function __construct( array $options = array() )
    {
        parent::__construct( $options );

        $_get = Mumsys_Php_Globals::getGetVar();
        if ( isset( $_get ) && is_array( $_get ) ) {
            $this->_inputGet = $_get;
            $this->_input += $_get;
        }

        $_post = Mumsys_Php_Globals::getPostVar();
        if ( isset( $_post ) && is_array( $_post ) ) {
            $this->_inputPost = $_post;
            $this->_input += $_post;
        }

        unset( $_get, $_post, $options );
    }


    /**
     * Returns a post parameter by requested key.
     *
     * If $key is empty it will return all incoming posts parameters
     *
     * @param string $key Key to get
     * @param mixed $default Value to return if key not exists
     * @return mixed
     */
    public function getInputPost( $key = null, $default = null )
    {
        if ( empty( $key ) ) {
            return $this->_inputPost;
        }

        if ( isset( $this->_inputPost[$key] ) ) {
            return $this->_inputPost[$key];
        } else {
            return $default;
        }
    }


    /**
     * Sets an input post and standard input parameter.
     *
     * If value is NULL it will be unset if key exists
     *
     * @param string $key Parameter key to set
     * @param mixed $value Parameter value to set
     * @return self
     */
    public function setInputPost( $key, $value )
    {
        $key = (string) $key;

        if ( ( null === $value ) && isset( $this->_inputPost[$key] ) ) {
            unset( $this->_inputPost[$key] );
            unset( $this->_input[$key] );
        } elseif ( null !== $value ) {
            $this->_inputPost[$key] = $value;
            $this->_input[$key] = $value;
        }

        return $this;
    }


    /**
     * Returns a get parameter by requested key.
     *
     * If $key is empty it will return all incomming get parameters
     *
     * @param string $key Array key to get
     * @param mixed $default Value to return if key not exists
     *
     * @return mixed
     */
    public function getInputGet( $key = null, $default = null )
    {
        if ( empty( $key ) ) {
            return $this->_inputGet;
        }
        if ( isset( $this->_inputGet[$key] ) ) {
            return $this->_inputGet[$key];
        } else {
            return $default;
        }
    }


    /**
     * Sets an input get and standard input parameter.
     *
     * If value is NULL it will be unset if key exists
     *
     * @param string $key Parameter key to set
     * @param mixed $value Parameter value to set
     * @return self
     */
    public function setInputGet( $key, $value )
    {
        $key = (string) $key;

        if ( ( null === $value ) && isset( $this->_inputGet[$key] ) ) {
            unset( $this->_inputGet[$key] );
            unset( $this->_input[$key] );
        } elseif ( null !== $value ) {
            $this->_inputGet[$key] = $value;
            $this->_input[$key] = $value;
        }

        return $this;
    }

}
