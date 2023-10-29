<?php

/**
 * Mumsys_Mvc_Display_Control_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 * Created: 2016-01-30
 */


/**
 * Abstract display control methodes to be used in general.
 *
 * Last instance to output data to the frontend.
 * Mumsys_Mvc_Display_Control_Abstract is the base for all views. Basicly it
 * collects, applys, shows or returns given/rendered  content.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 */
abstract class Mumsys_Mvc_Display_Control_Abstract
    extends Mumsys_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '1.1.1';

    /**
     * Buffer of content to output or return.
     * @var string|mixed
     */
    private $_buffer = '';

    /**
     * Context item
     * @var Mumsys_Context_Item
     */
    protected $_context;

    /**
     * Name of the current page/output
     * @var string
     */
    protected $_pagetitle = '';

    /**
     * List of requested helper classes to buffer. The key contains the class
     * name and the value the object.
     * @var array
     */
    private $_helpers;


    /**
     * Constructor is to be implemented at the display controller which will be
     * used. e.g. in : Mumsys_Mvc_Display_Control_Http_Abstract
     */
    abstract public function __construct( Mumsys_Context $context,
        array $options = array() );


    /**
     * Returns the requested display helper class.
     *
     * @param string $extension Name of the extension to load/ get
     *
     * @return false|Mumsys_Mvc_Display_Helper_Interface E.g Mumsys_Mvc_Display_Helper_{$extension}
     * @throws Mumsys_Mvc_Display_Exception on errors init the helper class
     */
    public function getDisplayHelper( $extension )
    {
        if ( isset( $this->_helpers[$extension] ) ) {
            return $this->_helpers[$extension];
        }

        try {
            $class = 'Mumsys_Display_Helper_' . ucfirst( $extension );
            $this->_helpers[$extension] = new $class( $this->_context );
        }
        catch ( Exception $e ) {
            throw new Mumsys_Mvc_Display_Exception(
                sprintf( 'Helper class not found/ exists "%1$s"', $extension )
            );
        }

        return $this->_helpers[$extension];
    }


    /**
     * Sets the output page title.
     *
     * @param string $title Title to be set
     */
    public function setPageTitle( $title = '' )
    {
        $this->_pagetitle = (string) $title;
    }


    /**
     * Add content to the display/output buffer.
     *
     * @param string $content Content to add to the buffer
     */
    public function add( $content = '' )
    {
        $this->_buffer .= $content;
    }


    /**
     * Output given content and the current buffer and resets it.
     *
     * @param string $content Optional; Content to output after the buffer
     * contents
     */
    public function apply( $content = '' )
    {
        echo $this->_buffer . $content;
        $this->_buffer = '';
    }


    /**
     * Print out the complete content.
     */
    public function show()
    {
        echo $this->fetch();
    }


    /**
     * Fetch the content without any, maybe needed, headers and resets the buffer.
     * e.g.: this can be used to store the content to a file.
     *
     * @return string Returns the complete data of the requested page. e.g. The
     * hole html page. It depends on the display controller what kind of output
     * was set.
     */
    public function fetch()
    {
        $buffer = $this->_buffer;
        $this->_buffer = '';
        return $buffer;
    }

}
