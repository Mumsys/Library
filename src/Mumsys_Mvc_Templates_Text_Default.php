<?php

/**
 * Mumsys_Mvc_Templates_Text_Default
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 */


/**
 * Default text templates for the display/view.
 *
 * These methodes are basicly display helpers for the text output
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 */
class Mumsys_Mvc_Templates_Text_Default
    extends Mumsys_Mvc_Templates_Text_Abstract
{
    /**
     * Version ID information
     */
    const VERSION = '1.0.0';


    /**
     * Returns a "table" by given records of data.
     *
     * @param array $rowsAndCols List of records in list of key/value pairs to
     * create the 2D structure
     */
    public function getCreateTable( $rowsAndCols )
    {
        $result = ' table for text by given array not implemented yet';
        return $result;
    }


    /**
     * Sets/Adds a "table" (in time) to the buffer of the output
     *
     * @param array $rowsAndCols List of records in list of key/value pairs to
     * create the 2D structure
     */
    public function addCreateTable( array $rowsAndCols )
    {
        $this->add( $this->getCreateTable( $rowsAndCols ) );
    }


    /**
     * Adds a message box to the output buffer.
     *
     * @param string $title Title of the message
     * @param string $content The message
     */
    public function addTitleBlock( $title = '', $content = '' )
    {
        $this->add( $this->getTitleBlock( $title, $content ) );
    }


    /**
     * Returns a message box for the output.
     *
     * @param string $title Title of the message
     * @param string $content The message
     *
     * @return string The formatted message box string
     */
    public function getTitleBlock( $title = '', $content = '' )
    {
        $data = $title . _NL . _NL;
        $data = $content . _NL . _NL . _NL;

        return $data;
    }


    /**
     * html header and footer in return to display. called in display controller.
     *
     * @param string $extras
     */
    public function getSiteHeader( $extras = '' )
    {
        $outp = 'Site Head not implemented yet. But a page title, if set is '
            . 'here "' . $this->_pagetitle . '"';
        // if ( >get('charset') ) {}

        return $outp;
    }


    /**
     * Returns the footer contents.
     *
     * @return string All content to finish the output at the end of an output
     */
    public function getSiteFooter()
    {
        $retour = _NL . _NL . '* MUMSYS POWER *' . _NL;

        return $retour;
    }


    /**
     * Send no access/ no permission information to output.
     *
     * @throws Mumsys_Mvc_Display_Exception Throws exception for controlled script
     * end or to have detailed informations (eg: debug mode)
     */
    public function noAccess()
    {
        $i18n = $this->_context->getTranslation();
        $headline = $i18n->_t( 'No access' );
        $this->title( $headline, $i18n->_t( '_CMS_NOPERMISSION_INFO' ) ); // @phpstan-ignore-line
        $this->show();

        $code = Mumsys_Mvc_Display_Exception::ERRCODE_HTTP401;
        throw new Mumsys_Mvc_Display_Exception( $headline, $code );
    }


    /**
     * Error reporting helper to add error messages.
     *
     * @param string|array $msg Message/s to output
     * @param string $line Line of the error
     * @param string $file File of the error
     * @param string|false $func Name of the function of the error
     * @param string|false $class Name of the class of the error
     */
    public function mkError( $msg, $line = null, $file = null, $func = false,
        $class = false )
    {
        $l = $this->_context->getTranslation();

        if ( is_array( $msg ) ) {
            $retour = '';
            foreach ( $msg as $key => &$val ) {
                $key++;
                $retour .= '  *' . $l->_t( '_CMS_ERROR' ) . ': ' . $key . ' - ' . $val . '' . _NL;
            }
            $retour .= _NL . _NL;
        } else {
            $retour = '* ' . $msg . '' . _NL;
        }

        if ( $line || $file || $func || $class ) {
            $retour .= '' . _NL;
        }

        if ( $line ) {
            $retour .= ' || Line: ' . $line;
        }

        if ( $file ) {
            $retour .= ' || File: ' . basename( $file );
        }

        if ( $func ) {
            $retour .= ' || Function/Methode: ' . ( $func );
        }

        if ( $class ) {
            $retour .= ' || Class: ' . ( $class );
        }

        $retour .= _NL;

        $this->setTitleBlock( $l->_t( '_CMS_ERROR' ), $retour ); // @phpstan-ignore-line
    }

}
