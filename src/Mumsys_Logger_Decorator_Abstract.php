<?php

/**
 * Mumsys_Logger_Decorator_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2005 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 */


/**
 * Decorator interface to generate log messages
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 */
abstract class Mumsys_Logger_Decorator_Abstract
    implements Mumsys_Logger_Decorator_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '3.0.0';

    /**
     * Interface to decorate.
     * @var Mumsys_Logger_Interface
     */
    protected $_object;


    /**
     * Initialize the decorated logger object
     *
     * @param Mumsys_Logger_Interface $logger Logger object to be decorated
     */
    public function __construct( Mumsys_Logger_Interface $logger )
    {
        $this->_object = $logger;
    }


    /**
     * Clones the object.
     */
    public function __clone()
    {
        $this->_object = clone $this->_object;
    }


//    public function __call( $name, $args)
//    {
//        $obj = $this->_getObject();
//        if ( method_exists( $obj, $name ) ) {
//            $obj->$name($args);
//        }
//    }


    /**
     * Get the name of a log level.
     *
     * @param integer $level Nuber of the Log level
     *
     * @return string Returns the string of the errorlevel
     */
    public function getLevelName( $level )
    {
        // @phpstan-ignore-next-line
        return $this->_object->getLevelName( $level );
    }


    /**
     * Create a log entry by a given message and log level.
     *
     * Levels are:
     * 0 EMERG    emerg()   System is unusable
     * 1 ALERT    alert()   Immediate action required
     * 2 CRIT     crit()    Critical conditions
     * 3 ERR      err()     Error conditions
     * 4 WARN     warn()    Warn conditions
     * 5 NOTICE   notice()  Normal but significant
     * 6 INFO     info()    Informational
     * 7 DEBUG    debug()   Debug-level messages
     *
     * @param string|array $input Message or list of messages to log
     * @param integer $level Level number of log priority
     *
     * @return string|void Returns the log message if needed
     */
    public function log( $input, $level = 0 )
    {
        return $this->_object->log( $input, $level );
    }


    /**
     * Checks if a loglevel is registered or not
     *
     * @param integer $level 0-7 Log level to be checked
     *
     * @return boolean Returns true for OK otherwise false
     */
    public function checkLevel( $level = 0 )
    {
        // @phpstan-ignore-next-line
        return $this->_object->checkLevel( $level );
    }


    /**
     * Returns the decorated/ the base object.
     *
     * @return Mumsys_Logger_Interface Logger object
     */
    protected function _getObject()
    {
        return $this->_object;
    }

}
