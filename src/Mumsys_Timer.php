<?php declare( strict_types=1 );

/**
 * Mumsys_Timer
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright (c) 2006 by Florian Blasel
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Timer
 * V1 - Created 2006-01-12
 */


/**
 * Track the duration between a start and end time.
 *
 * Example 1:
 * <code>
 *  $timer = new Mumsys_Timer(true);
 *  // do some stuff
 *  echo $timer; // print out the duration time
 * </code>
 *  Example 2:
 * <code>
 *  $timer = new Mumsys_Timer($_SERVER['REQUEST_TIME_FLOAT']);
 *  // do some stuff
 *  echo $timer; // print out the duration time
 * </code>
 *  Example 3 (manual):
 * <code>
 *  $timer = new Mumsys_Timer();
 *  // do some stuff
 *  $timer->start();             // start recording the time
 *  // do some more stuff
 *  $duration = $timer->stop();  // return the duration OR:
 *  echo $timer                  // print out the duration time
 * </code>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Timer
 */
class Mumsys_Timer
    extends Mumsys_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '3.2.1';

    /**
     * Float start time in seconds or microtime format
     * @var float
     */
    private $_start = 0;

    /**
     * Float stop time in microtime format
     * @var float
     */
    private $_stop = 0;

    /**
     * The number of seconds, micro seconds
     * @var float
     */
    private $_elapsed = 0.0;


    /**
     * Initialize the object.
     *
     * Two different options to automatically start the timer:
     * 1. When true the current time (microtime) will be used
     * 2. If the $start value contains a float value it will be used as start
     * time.
     *
     * @param boolean|float $start If true enable "start now"
     * function otherwise given float value will be used as starttime in
     * mictotime format; If true time recording starts now
     */
    public function __construct( $start = false )
    {
        if ( $start === true ) {
            $this->start();
        }

        if ( is_float( $start ) ) {
            $this->startTimeSet( $start );
        }
    }


    /**
     * Start the timer
     */
    public function start()
    {
        $this->_start = $this->_microtimeGet();
    }


    /**
     * Stop the timer and calculate the time between start and stop time.
     *
     * @return float Returns the number of seconds, micro seconds
     */
    public function stop()
    {
        $this->_stop = $this->_microtimeGet();
        $this->_elapsed = $this->_stop - $this->_start;

        return $this->_elapsed;
    }


    /**
     * Returns the start time.
     *
     * @return float|0 Returns start timestamp in microtime format.
     */
    public function startTimeGet()
    {
        return $this->_start;
    }


    /**
     * Sets the start time.
     *
     * @param float $timestamp Start time as float value to be set
     */
    public function startTimeSet( $timestamp )
    {
        $this->_start = (float) $timestamp;
    }


    /**
     * Returns the stop time.
     *
     * @return float|0 Returns stop timestamp in microtime format.
     */
    public function stopTimeGet()
    {
        return $this->_stop;
    }


    /**
     * Returns the elapsed time.
     *
     * @return float|0 Returns elapsed timestamp in microtime format.
     */
    public function elapsedTimeGet()
    {
        return $this->_elapsed;
    }


    /**
     * Get microtime.
     *
     * @return float Returns the current timestamp in microtime format.
     */
    protected function _microtimeGet()
    {
        return microtime( true );
    }


    /**
     * Stops the timer and returns the duration as string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->stop();
    }

}
