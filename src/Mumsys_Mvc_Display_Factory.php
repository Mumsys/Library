<?php

/**
 * Mumsys_Mvc_Display_Factory
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2006 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 * Created: 2006-12-01 (svn)
 */


/**
 * Mumsys display/ view factory.
 *
 * Make your view controller and templates available based on given
 * propertys.
 *
 * Structure:
 *
 *  - Mumsys_Mvc_Display_Control_Factory
 *
 *      - Mumsys_Mvc_Display_Control_Abstract
 *
 *          Frontend Controller / View:
 *          eg: Mumsys_Mvc_Display_Control_{type} Http|Stdout
 *          (Default=Stdout: eg: for shell|text output)
 *
 *          - Mumsys_Mvc_Display_Control_Http_Abstract (basicly for headers)
 *          - Mumsys_Mvc_Display_Control_Http_Default
 *              View helper defaults:
 *              - Mumsys_Mvc_Templates_Html_Abstract            <- custom
 *                  - Mumsys_Mvc_Templates_Html_Default         <- custom
 *                      - Mumsys_Mvc_Templates_Html_Extended    <- custom/optional
 *
 *
 *          - Mumsys_Mvc_Display_Control_Stdout_Default
 *              View helper defaults:
 *              - Mumsys_Mvc_Templates_Text_Abstract            <- custom
 *                  - Mumsys_Mvc_Templates_Text_Default         <- custom
 *                      - Mumsys_Mvc_Templates_Text_Extended    <- optional
 *
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 */
class Mumsys_Mvc_Display_Factory
{
    /**
     * Version ID information
     */
    const VERSION = '3.0.0';

    /**
     * Mumsys_Context object.
     * @var Mumsys_Context_Interface
     */
    private $_context;

    /**
     * Optional parameters for the target display controller
     * @var array
     */
    private $_options;

    /**
     * Controller type to be loaded, "default" for text or "Html" for xml, html,
     * xhtml, html5 or other outputs which are to be implemented. default: default
     * @var string
     */
    private $_outputType = 'default';

    /**
     * Extension to be loaded: default or extended version with extras methodes.
     * @var string
     */
    private $_outputComplexity = 'default';


    /**
     * Initialize the factory.
     *
     * @param Mumsys_Context_Interface $context Context item
     */
    public function __construct( Mumsys_Context_Interface $context )
    {
        $this->_context = $context;
    }


    /**
     * Factory methode to get a display/ view with our without view helper
     * implementations.
     *
     * When calling the methode without the 2nd and 3rd parameter the type and
     * driver will be take from the config: frontend/controller/[type|driver]
     * will be used.
     * Otherwise Text Default is the standard driver for the output.
     *
     * @param array $options Parameters to initialise the display/view
     * controller.
     * @param string $outputType Frontend/ View controller to initialize:
     * default | text | html
     * @param string $outputComplexity Complexity to load on initialisation
     * default | extended | custom
     *
     * @return Mumsys_Mvc_Display_Control_Interface Returns a display object which
     * can be Mumsys_Mvc_Templates_*_* or own implementations.
     *
     * @throws Mumsys_Mvc_Display_Exception Throws exception on errors
     */
    public function load( array $options = array(), $outputType = 'default',
        $outputComplexity = 'default' )
    {
        $this->_options = $options;

        $outputType = ucwords( $outputType );
        $outputComplexity = ucwords( $outputComplexity );

        if ( empty( $outputType ) ) {
            $outputType = $this->_context->getConfig()->get( 'frontend/controller/type', 'Text' );
        }

        if ( empty( $outputComplexity ) ) {
            $outputType = $this->_context->getConfig()->get( 'frontend/controller/driver', 'Default' );
        }

        $this->_outputType = $outputType;
        $this->_outputComplexity = $outputComplexity;

        /**
         * If X-Moz set to prefetch, exit
         */
        if ( !empty( $_SERVER['HTTP_X_MOZ'] ) && strcasecmp( $_SERVER['HTTP_X_MOZ'], 'prefetch' ) == 0 ) {
            header( 'HTTP/1.0 403 Forbidden' );
            $message = 'HTTP_X_MOZ prefetch is disabled';
            $code = Mumsys_Mvc_Display_Exception::ERRCODE_DEFAULT;

            throw new Mumsys_Mvc_Display_Exception( $message, $code );
        }

        $templateDriver = sprintf(
            'Mumsys_Mvc_Templates_%1$s_%2$s',
            $this->_outputType,
            $this->_outputComplexity
        );

        if ( class_exists( $templateDriver, false ) ) {
            $display = new $templateDriver( $this->_context, $this->_options );
        } else {
            $message = sprintf(
                'Driver for the display "%1$s" %2$s" not found',
                $outputType,
                $outputComplexity
            );
            $code = Mumsys_Mvc_Display_Exception::ERRCODE_DEFAULT;

            throw new Mumsys_Mvc_Display_Exception( $message, $code );
        }

        $this->_context->replaceDisplay( $display );

        return $display;
    }

}
