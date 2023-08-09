<?php

/**
 * Mumsys_Logger_Decorator_None
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2017 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 */


/**
 * None decorator for the logger.
 * This one extends the logger to none extras, eg useful for tests.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 */
class Mumsys_Logger_Decorator_None
    extends Mumsys_Logger_Decorator_Abstract
    implements Mumsys_Logger_Decorator_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';


    /**
     * Initialize the decorator messages logger object
     *
     * @param Mumsys_Logger_Interface $logger Logger object to be decorated
     * @param array $options List of options to be set on construction; Optional
     */
    public function __construct( Mumsys_Logger_Interface $logger,
        array $options = array() )
    {
        unset( $options ); // currently unused here

        parent::__construct( $logger );
    }


    /**
     * Alias wrapper to none extra methode calls.
     *
     * @param string $key Methode string to wrap to
     * @param string $value Log message value
     *
     * @return void
     */
    public function __call( $key, $value )
    {
        return;
    }

}
