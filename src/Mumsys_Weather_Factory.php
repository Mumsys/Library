<?php

/**
 * Mumsys_Weather_Factory
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
 * Factory to initialize weather plugin object.
 *
 * @category Mumsys
 * @package Mumsys_Library
 * @subpackage Mumsys_Weather
 */
class Mumsys_Weather_Factory
{
    /**
     * Initialize and returns weather plugin object.
     *
     * @todo only openweathermaps is implemented now.
     *
     * @param array $params Basic parameters to be set for the driver:
     * - [format] string Format of service response: json (default), xml, html
     * - [unit] string Unit can be 'metric', 'imperial', 'internal' (default:
     * metric).
     * - [language] string A language code like en, de, es, fr up to five
     * characters if a locale is needed.
     * - [apikey] string Your application key/ token/ access key your need to
     * access the data. Max 40 character
     * @param string $service Service plugin to be used. Possible
     * implementations: 'autodetect' (to find one of the following and in this
     * order (to speed up things): 'openweathermaps'
     *
     * @return Mumsys_Weather_Interface
     * @throws Exception
     */
    public static function getInstance( $service = 'auto',
        array $params = array() )
    {
        if ( $service === 'auto' || $service === 'autodetect' ) {
            $service = self::_autodetectService();
            $newService = self::_initService( $service, $params );
        } else if ( in_array( $service, array('openweathermaps') ) ) {
            $newService = self::_initService( $service, $params );
        } else {
            $newService = new Mumsys_Weather_OpenWeatherMap( $params );
        }

        return $newService;
    }


    /**
     * Find possible services to get weather data.
     *
     * @return string internal name of the available plugin service
     */
    private static function _autodetectService()
    {
        $service = 'openweathermaps';

        return $service;
    }


    /**
     * Initialize weather service object.
     *
     * @param string $service Service to initialize.
     *
     * @return Mumsys_Weather_Interface Weather service
     */
    private static function _initService( $service, array $params = array() )
    {
        switch ( $service )
        {
            case 'openweathermaps':
                $newService = new Mumsys_Weather_OpenWeatherMap( $params );
                break;

            default:
                throw new Mumsys_Weather_Exception( 'Service not implemented' );
        }

        return $newService;
    }

}
