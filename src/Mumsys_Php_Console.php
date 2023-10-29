<?php declare(strict_types=1);

/**
 * Mumsys_Php_Console
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2018 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Php
 * Created on 2018-01-14
 */


/**
 * Class for php improvements using cli.
 *
 * This class extends or adds php features which are for the cli.
 *
 * All methodes should be called staticly.
 *
 * Example:
 * <code>
 * <?php
 * $value = Mumsys_Php::float('123');
 * ?>
 * </code>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Php
 */
class Mumsys_Php_Console
    extends Mumsys_Php
{
    /**
     * Version ID information.
     */
    const VERSION = '3.0.1';


    /**
     * Check free disk space (df -a)
     *
     * Basicly this function is usefule when working with large files which will
     * be created or copied. This function can help you to break operations befor
     * disk-full errors will occour. The check will work on percentage (%) of
     * free disk space (df -a)
     *
     * Note: This methode only works if the process will run as long the job is
     * done. Eg coping big data from a to b and every few seconds you can call
     * this methode to check if you are save with disk size.
     *
     * @staticvar array $paths Memory for check paths
     * @staticvar array $sizes Memory for checked disksizes
     * @staticvar array $times Memory for time of last check
     *
     * @param string $path Location to call a df -a command it should be the root
     * path of a mountpoit at least or the path e.g: where to store some data
     * @param integer $secCmp Number of seconds a compare-time will be OK during
     * a process befor df -a command will be called again (limit the number if
     * you have huge file movements (and good network or disk write speed) to
     * beware crashes or "disk-full"- errors)
     * @param integer $maxSizeCmp Number in percent. Max size when a disk-full
     * event should be thrown (max allowed/free size compare Value) Default: 92%
     * @param Mumsys_Logger_Interface $logger Logger object which needs at least
     * the log method
     * @param string $inputCmd 'df' command to be executed. Maybe different on
     * windows e.g: 'c:/cygwin/bin/df.exe -a [path]; Parameter %1$s is the
     * placeholder for the $path.
     *
     * @return boolean Returns true if disk size exceed the limit or false
     */
    public static function check_disk_free_space( $path = '', $secCmp = 60,
        $maxSizeCmp = 92, Mumsys_Logger_Interface $logger = null,
        $inputCmd = 'df -a %1$s' )
    {
        static $paths = null;
        static $sizes = null;
        static $times = null;

        if ( !( $logger instanceof Mumsys_Logger_Interface ) ) {
            throw new Mumsys_Php_Exception( 'Invalid logger interface' );
        }

        // key of exec result in $cmd
        $resultKey = 4;

        if ( !is_dir( $path . DIRECTORY_SEPARATOR ) ) {
            $message = __METHOD__ . ': path do not exists: "' . $path . '"';
            $logger->log( $message, 3 );
            return true;
        }

        $cmd = sprintf( $inputCmd, $path );

        $now = time();
        $_v = array();

        if ( $paths === null ) {
            $paths = array();
            $sizes = array();
            $times = array();
        }

        $logger->log( __METHOD__ . ': using path: ' . $path, 6 );

        // cached data check
        $i = array_search( $path, $paths );
        if ( $i !== false && isset( $times[$i] ) && ( $now - $times[$i] ) <= $secCmp ) {
            if ( $sizes[$i] >= $maxSizeCmp ) {
                $message = __METHOD__ . ': disc space overflow: ' . $sizes[$i]
                    . ' (' . $maxSizeCmp . ' is max limit!)';
                $logger->log( $message, 3 );
                return true;
            } else {
                $message = __METHOD__ . 'disc space OK: ' . $sizes[$i]
                    . '% (' . $maxSizeCmp . ' is max limit!)';
                $logger->log( $message, 6 );
                return false;
            }
        }

        try {
            $tmp = '';
            $data = null;
            $return = null;

            $result = exec( $cmd, $data, $return );

            if ( !$result || $return !== 0 ) {
                $mesg = __METHOD__ . ': cmd error: "' . $cmd . '"';
                throw new Mumsys_Php_Exception( $mesg, 1 );
            }

            $logger->log( __METHOD__ . ': cmd: "' . $cmd . '"', 7 );

            $r = explode( ' ', $data[1] );
            foreach ( $r as $a => $b ) {
                $b = trim( $b );
                if ( $b != '' ) {
                    $_v[] = $b;
                }
            }
            $logger->log( $_v, 7 );

            $size = (int) $_v[$resultKey];

            $paths[] = $path;
            $sizes[] = $size;
            $times[] = time();

            if ( $size >= $maxSizeCmp ) {
                $logger->log(
                    sprintf(
                        __METHOD__ . ': disc space overflow: size: "%1$s" (max'
                        . ' limit: "%2$s") for path: "%3$s"',
                        $sizes[$i], $maxSizeCmp, $path
                    ), 3
                );
                return true;
            } else {
                $logger->log(
                    sprintf(
                        __METHOD__ . 'disc space OK: size "%1$s" (max limit: '
                        . '"%2$s") for path: "%3$s"', $sizes[$i],
                        $maxSizeCmp, $path
                    ), 6
                );

                return false;
            }
        } catch ( Exception $e ) {
            $logger->log(
                __METHOD__ . ': Catchable exception. Message: "'
                . $e->getMessage() . '"', 0
            );
            return true;
        }
    }

}
