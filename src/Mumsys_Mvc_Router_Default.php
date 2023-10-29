<?php

/**
 * Mumsys_Mvc_Router_Default
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
 * Default router class to map incoming data to internal functionality.
 *
 * In detail: Incomming parameters like program/module, controller/subprogram
 * action/subcalls are the parameters the router will working on in the mvc
 * concept.
 * These parameters will be taken and route to that endpoit. The simplest way
 * E.g:
 * index.php?program=User&controller=User&action=myaccount will repipe on the
 * filesystem to: src/Programs/User/Controllers/UserController.php
 *  -> function myaccountAction()
 *
 * The default is to use standard parameters to map and call the requested
 * program parts. E.g: program=User&controller=Index=action=show
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 */
class Mumsys_Mvc_Router_Default
    extends Mumsys_Mvc_Router_Abstract
    implements Mumsys_Mvc_Router_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * @var Mumsys_Request_Interface
     */
    protected $_request;


    /**
     * Initialise the router object.
     *
     * @todo use the request opject not the options, also in abtract class
     *
     * @param Mumsys_Request_Interface $request Request interface
     * @param array $options Optional initial options
     */
    public function __construct( Mumsys_Request_Interface $request,
        array $options = array() )
    {
        $this->_request = $request;

        if ( isset( $options['programKey'] ) ) {
            $this->setProgramKey( $options['programKey'] );
        }

        if ( isset( $options['controllerKey'] ) ) {
            $this->setControllerKey( $options['controllerKey'] );
        }

        if ( isset( $options['actionKey'] ) ) {
            $this->setActionKey( $options['actionKey'] );
        }
    }

}
