<?php

/**
 * Mumsys_Request_Console
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
 * Console/ shell class to get input parameters.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Request
 */
class Mumsys_Request_Console
    extends Mumsys_Request_Abstract
    implements Mumsys_Request_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '1.1.2';


    /**
     * Initialise the request object using servers argv array.
     *
     * @param array $options Optional initial options e.g.:
     * 'programKey','controllerKey', 'actionKey',
     */
    public function __construct( array $options = array() )
    {
        parent::__construct( $options );

        $argv = $_get = Mumsys_Php_Globals::getServerVar( 'argv', array() );
        if ( $argv && is_array( $argv ) ) {
            $this->_input += $argv;
        }

        unset( $argv, $options );
    }

}
