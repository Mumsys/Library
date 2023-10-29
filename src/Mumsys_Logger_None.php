<?php

/**
 * Mumsys_Logger_None
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
/* }}} */


/**
 * Class to generate log messages without a concrete implementation.
 *
 * This driver hold the functionality but does whether e.g.: writing to a file
 * nor output messages.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 */
class Mumsys_Logger_None
    extends Mumsys_Logger_Abstract
    implements Mumsys_Logger_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '3.0.1';


    /**
     * Create a log entry by a given log level.
     *
     * 0 EMERG    emerg()   System is unusable
     * 1 ALERT    alert()   Immediate action required
     * 2 CRIT     crit()    Critical conditions
     * 3 ERR      err()     Error conditions
     * 4 WARN     warn()    Warn conditions
     * 5 NOTICE   notice()  Normal but significant
     * 6 INFO     info()    Informational
     * 7 DEBUG    debug()   Debug-level messages
     *
     * @param string|array $input Message or list of messages to be logged
     * @param integer $level Level number of log priority
     *
     * @return string Log message
     */
    public function log( $input, $level = 0 )
    {
        $datesting = date( $this->_timeFormat, time() );
        $levelName = $this->getLevelName( $level );

        if ( !is_scalar( $input ) ) {
            $input = json_encode( $input );
        }

        $message = sprintf(
            $this->_logFormat,
            $datesting,
            $this->_username,
            $levelName,
            $level,
            $input
        );

        $message .= $this->_lf;

        return $message;
    }

}
