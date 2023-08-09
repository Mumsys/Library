<?php

/**
 * Mumsys_Mvc_Program_Config
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @author Florian Blasel <flobee.code@gmail.com>
 * @copyright Copyright (c) 2011 by Florian Blasel for FloWorks Company
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc_Program
 */


/**
 * Mumsys program configuration object.
 *
 * Program/module configuration to be avalable as e.g.:
 * $configs['program'] => array(
 *      'system' => array(
 *          'phpini' => array(
 *              // php.ini setting which are required in here
 *          ),
 *      ),
 *      'program' => array(
 *          'controller' => array(
 *              'custom' => array(
 *                  'your config1' => 'your value2',
 *                  'your config2' => 'your value2',
 *              'database' => array(
 *                  // credentials for a new db connection in here like in
 *                  // main config
 *              ),
 */
class Mumsys_Mvc_Program_Config
    extends Mumsys_Config_File
{
    /**
     * Version ID information
     */
    const VERSION = '1.0.0';

//    /**
//     * Program configuration vars in an array container.
//     * @var array
//     *
//     * private $_config;
//     */
//
//    /**
//     * Context item which must be available for all mumsys objects
//     * @var Mumsys_Context
//     *
//     * private $_context;
//     */


    /**
     * Initialize the program config object.
     *
     * @param Mumsys_Context $context Context ojects for depenencies.
     * Note: This may be including a different DB driver, so take care and clone
     * or create a new Context if needed
     * @param array $config Config parameter to be set.
     * @param array $paths List of locations for config files
     */
    public function __construct( Mumsys_Context $context,
        array $config = array(), array $paths = array() )
    {
        unset( $context ); // currently unused here
        parent::__construct( $config, $paths );
    }

}
