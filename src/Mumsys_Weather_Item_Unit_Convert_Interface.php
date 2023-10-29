<?php

/**
 * Mumsys_Weather_Item_Unit_Convert_Interface
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
 * Interface for the weather unit items extending a convert() method eg for
 * temperature, speed, directions..
 *
 * Check the concrete implementation for the $to value.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 */
interface Mumsys_Weather_Item_Unit_Convert_Interface
{
    /**
     * Adds conversion/calculation possibility to target implementation.
     *
     * @param mixed $value Value to be converted
     * @param string $to Target key/code to convert to.
     */
    public function convert( $value, string $to );

}
