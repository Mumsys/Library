<?php

/**
 * Mumsys_Logger_Writer_Syslog
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2019 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 * @version     1.0.0
 * Created 2019/04
 */


/**
 * Syslog writer for the logger object.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 */
class Mumsys_Logger_Writer_Syslog
    extends Mumsys_Abstract
    implements Mumsys_Logger_Writer_Interface
{
    /**
     * Write given content to the writer
     *
     * @param string $content String to save
     *
     * @return boolean Returns true on success
     * @throws Exception on errors.
     */
    public function write( $content )
    {
        $cmd = escapeshellcmd( 'logger ' . $content );

        $data = $code = null;
        $lastLine = exec( $cmd, $data, $code );

        if ( $code > 0 ) {
            $mesg = PHP_EOL
                . 'Error code from shell execution detected.' . PHP_EOL
                . 'Cmd was: "' . $cmd . '"' . PHP_EOL
                . 'Exit code: "' . (string) $code . '"' . PHP_EOL
                . 'Cmd last line: "' . $lastLine . '"' . PHP_EOL
                . 'Contents (json): "' . json_encode( $data ) . '"' . PHP_EOL;
            throw new Mumsys_Logger_Exception( $mesg );
        }

        return true;
    }


    /**
     * Not implemented
     *
     * @throws Mumsys_Logger_Exception Dont truncate this way!
     */
    public function truncate()
    {
        $message = 'Truncate the syslog not implemented this way. Danger.';
        throw new Mumsys_Logger_Exception( $message );
    }

}
