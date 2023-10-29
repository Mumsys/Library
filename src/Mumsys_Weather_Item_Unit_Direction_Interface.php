<?php

/**
 * Mumsys_Weather_Item_Unit_Direction_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2013 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 * @verion      1.0.0
 * Created: 2013, renew 2018
 */


/**
 * Interface for the weather direction unit items.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 */
interface Mumsys_Weather_Item_Unit_Direction_Interface
{
    /**
     * Returns an alternativ representation value by given degrees value.
     *
     * Implemented code's (codeTo):
     *  - 'clock' E.g. 0 degrees = 12 o'clock
     *
     * @param integer|float $value Value in degrees
     * @param string $codeTo Code to convert to. e.g: 'clock'
     */
    public function convert( $value, string $codeTo );


    /**
     * Returns the code of a direction (e.g: Wind direction) to be represented
     * as value.
     *
     * E.g. SW = South west, SSW = South south west.
     * Hint: (45°) on publication, (22,5°) on collection
     *
     * @param integer $deg Direction in degrees
     * @param integer $precision Precision of the wind direction to return.
     * 0 = 45° steps (8 steps) (default),
     * 1 = 22.5° steps (16 steps)
     *
     * @return string Code of the wind direction
     * @throws Mumsys_Weather_Exception
     */
    public function getDirectionCode( $deg = 0, $precision = 0 );
}
