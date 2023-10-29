<?php

/**
 * Mumsys_Mvc_Display_Control_Http_Interface
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
 * Created: 2016-12-01
 */


/**
 * Http interface for the display controller.
 * Adds methodes to set and send e.g. headers to the e.g. Webserver
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 */
interface Mumsys_Mvc_Display_Control_Http_Interface
    extends Mumsys_Mvc_Display_Control_Interface
{


    /**
     * Adds header to be send on output.
     * @param string $header content of a Html header line
     */
    public function addHeader( $header = '' );


    /**
     * Returns headers.
     *
     * @return array List of header
     */
    public function getHeaders();


    /**
     * Output all headers which were set.
     */
    public function applyHeaders();


    /**
     * Sends given header to the output directly.
     *
     * @param string $header content of the header line e.g:
     * "Content-Type: text/html" or "Location: index.php/a/b/c"
     *
     * @throws Mumsys_Mvc_Display_Exception Throws exception if string is empty
     * or not a string
     */
    public function sendHeader( $header = '' );
}
