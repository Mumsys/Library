<?php

/**
 * Mumsys_Mvc_Display_Control_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 * @version     1.0.0
 * Created: 2016-01-30
 */


/**
 * Display control interface
 *
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Mvc
 */
interface Mumsys_Mvc_Display_Control_Interface
{
    /**
     * Sets the output page title.
     *
     * @param string $title Title to be set
     */
    public function setPageTitle( $title = '' );


    /**
     * Add content to the display/output buffer.
     *
     * @param string $content Content to add to the buffer
     */
    public function add( $content = '' );


    /**
     * Output the current buffer and given content and reset the buffer.
     *
     * @param string $content Optional; Content to output after the buffer
     * contents
     */
    public function apply( $content = '' );


    /**
     * Print out the complete content.
     */
    public function show();


    /**
     * Fetch the content without any, maybe needed, headers and resets the buffer.
     * e.g.: this can be used to store the content to a file.
     *
     * @return string Returns the complete data of the requested page. e.g. The
     * hole html page. It depends on the display controller and what kind of
     * output was set.
     */
    public function fetch();
}
