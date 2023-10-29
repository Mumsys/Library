<?php

/**
 * Mumsys_Weather_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2013 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 * @verion      1.0.0
 * Created: 2013, renew 2018
 */


/**
 * Weather Interface for creating and registering weather plugins.
 *
 * @category Mumsys
 * @package Mumsys_Library
 * @subpackage Mumsys_Weather
 */
interface Mumsys_Weather_Interface
{
    /**
     * Returns a list of Mumsys_Weather_Item's for the current weather specified
     * by query parameter.
     *
     * @param mixed $query Parameter to get the current weather
     * @return array|false List of Mumsys_Weather_Item's
     */
    public function getWeather( $query = '' );

    /**
     * Returns a list of Mumsys_Weather_Item's for the weather forecast
     * specified by query parameter.
     *
     * @param mixed $query Parameter to get the weather forecast
     * @return array|false List of Mumsys_Weather_Item's
     */
    public function getWeatherForecast( $query = '' );


    /**
     * Returns raw data from weather request.
     *
     * @return string|false Raw response string from request
     */
    public function getRawWeather( $query = '' );


    /**
     * Returns the raw data from a previous request used with
     * getRawWeatherData() or getWeather().
     *
     * @return mixed Returns the data from the prevous made request.
     */
    public function getRawData();

    /**
     * Returns the request url.
     * @return string Url of the request.
     */
    public function getRequestUrl();

    /**
     * Returns a unit item.
     *
     * Please check details of the implementation. Some adaptes do not require
     * additional parameters and some does.
     *
     * @param string $type Type of the unit item to get. E.g: Temperature,
     * Direction, Speed, Default
     * @param array $parameters List of key/value pairs to initialise the item
     *
     * @return Mumsys_Weather_Item_Unit_Interface Object including key, label,
     * sign and code
     * @throws
     */
//    public function getUnitItem( $type, array $parameters = array() );
}
