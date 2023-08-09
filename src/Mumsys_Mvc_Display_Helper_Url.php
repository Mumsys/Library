<?php

/**
 * Mumsys_Mvc_Display_Helper_Url
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
 * Created: 2016-01-30
 */


/**
 * Abstract display control methodes to be used in general.
 *
 * Last instance to output data to the frontend.
 * Mumsys_Mvc_Display_Control_Abstract is the base for all views. Basicly it
 * collects, applys, shows or returns given content.
 */
abstract class Mumsys_Mvc_Display_Helper_Url
{
    /**
     * Version ID information
     */
    const VERSION = '1.0.0';

    /**
     * Mumsys_Context object.
     * @var Mumsys_Context_Interface
     */
    private $_context;

    /**
     * Misc options
     * @var array
     */
    private $_options; // @phpstan-ignore-line


    /**
     * Initialaze the url helper.
     *
     * @param Mumsys_Context_Interface $context Context item
     * @param array $options Optional options
     */
    public function __construct( Mumsys_Context_Interface $context,
        array $options = array() )
    {
        $this->_context = $context;
        $this->_options = $options;
    }


    /**
     * config:
     *  default: controller=XXX program=XXX action=XXX
     *  rewrite: program/controller/action/
     *  id (id=hash of programm controller action)
     *  regex: ??
     *
     * format: html, js (javascript), console/shell
     */
    public function url( $path, $params, $format = 'default' )
    {
        $parts = explode( '/', $path );
        switch ( count( $parts ) )
        {
            // program/controller/action
            case 3:
                $result = $parts;
                break;

            // same program: controller/action will be used
            case 2:
                $result = array($this->getProgramName(), $parts[0], $parts[1]);
                break;

            // same program AND controller: action will be used
            case 1:
                $result = array($this->getProgramName(), $this->getControllerName(), $parts[0]);
                break;

            default:
                throw new Mumsys_Mvc_Router_Exception( 'Invalid parameters given to url()' );
        }
        $result = $this->_urlFormat( $result, $config );
    }


    protected function _urlFormat( $parts, $config )
    {
        $template = null;
        switch ( strtolower( $config ) )
        {
            case 'default':
            case 'html':
                $result = array(
                    $this->getProgramKey() . '=' . $parts[0],
                    $this->getControllerKey() . '=' . $parts[1],
                    $this->getActionKey() . '=' . $parts[2],
                );
                $template = true;
                break;

            case 'rewrite':
                $result = $parts[0] . '/' . $parts[1] . '/' . $parts[2];
                break;

            case 'id':
                $result = 'id=' . $parts[0] . '.' . $parts[1] . '.' . $parts[2];
                break;

            case 'hash':
                $result = 'hash=' . md5( $parts[0] . $parts[1] . $parts[2] );
                break;

            default:
                throw new Mumsys_Mvc_Router_Exception( sprintf( 'Invalid config "%1$s"', $config ) );
        }

        if ( $template ) {
            switch ( strtolower( $this->_route ) )
            {
                case 'default':
                case 'html':
                    $template = '%1$s&amp;%2$s&amp;%3$s';
                    break;

                case 'js':
                    $template = '%1$s&%2$s&%3$s';
                    break;

                case 'console':
                case 'shell':
                case 'terminal':
                case 'term':
                case 'xterm':
                    $template = '%1$s %2$s %3$s';
                    break;

                default:
                    throw new Mumsys_Mvc_Router_Exception( sprintf( 'Invalid route "%1$s"', $this->_route ) );
            }

            $result = sprintf( $template, $parts[0], $parts[1], $parts[2] );
        }

        return $result;
    }

}
