<?php

/*{{{*/
/**
 * Mumsys_Mvc_Display_Control_Http_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 * ----------------------------------------------------------------------------
 * @author Florian Blasel <flobee.code@gmail.com>
 * @copyright Copyright (c) 2006 by Florian Blasel for FloWorks Company
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * ----------------------------------------------------------------------------
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Mvc
 * Created: 2006-12-01
 * @filesource
 */
/*}}}*/


/**
 * Mumsys_Mvc_Display_Control_Http_Abstract is the base for the view in http
 * context eg for html output, the frontend controller
 * The templates (Mumsys_Mvc_Templates_Html_*) adding methodes to this
 * controller to have more helper methods to generate html.
 *
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Mvc
 */
abstract class Mumsys_Mvc_Display_Control_Http_Abstract
    extends Mumsys_Mvc_Display_Control_Abstract
    implements Mumsys_Mvc_Display_Control_Http_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '1.0.0';

    /**
     * headers to be set if needed for the output talking http
     * @var string
     */
    private $_headers = array(); // array to set php header()


    /**
     * Adds header string.
     *
     * @param string $s content of a Html header line
     */
    public function addHeader( $header = '' )
    {
        $this->_headers[] = $header;
    }


    /**
     * Returns headers.
     *
     * @return array List of header
     */
    public function getHeaders()
    {
        return $this->_headers;
    }


    /**
     * Output all headers which were set.
     */
    public function applyHeaders()
    {
        while (list(, $header) = each($this->_headers)) {
            header($header);
        }
    }


    /**
     * Sends given header to the output directly.
     *
     * @param string $header content of the header line e.g:
     * "Content-Type: text/html" or "Location: index.php/a/b/c"
     *
     * @throws Mumsys_Mvc_Display_Exception Throws exception if string is empty
     * or not a string
     */
    public function sendHeader( $header = '' )
    {
        if (empty($header) || !is_string($header)) {
            $message = 'Can not send header.';
            $code = Mumsys_Mvc_Display_Exception::ERROR_HTTP500;
            throw new Mumsys_Mvc_Display_Exception($message, $code);
        } else {
            header($header);
        }
    }

}
