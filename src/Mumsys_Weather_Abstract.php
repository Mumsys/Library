<?php

/**
 * Mumsys_Weather_Abstract
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
 * Abstract class for weather service plugins
 *
 * @category Mumsys
 * @package Mumsys_Library
 * @subpackage Mumsys_Weather
 */
abstract class Mumsys_Weather_Abstract
{
    const VERSION = '1.0.0';
    /**
     * Application access key. Max length 40 character
     * @var string
     */
    protected $_apiKey = '';


    /**
     * Initialize the object.
     *
     * @param array $params Basic parameters to be set for the driver:
     * - [format] string Format of service response: json (default), xml, html
     * - [unit] string Unit can be 'metric', 'imperial', 'internal' (default:
     *  metric).
     * - [language] string A language code like en, de, es, fr up to five
     * characters if a locale is needed.
     * - [apikey] string Your application key/ token/ access key your need to
     * access the data. Max 40 character
     */
    public function __construct( array $params = array() )
    {
        $unit = isset( $params['unit'] );
        if ( $unit && in_array( $params['unit'], $this->_units ) ) {
            $this->_unit = $params['unit'];
        }

        $lang = isset( $params['language'] );
        if ( $lang && in_array( $params['language'], $this->_languages ) ) {
            $this->_language = substr( (string) $params['language'], 0, 5 );
        }

        $format = isset( $params['format'] );
        if ( $format && in_array( $params['format'], $this->_formats ) ) {
            $this->_format = $params['format'];
        }

        if ( isset( $params['apikey'] ) ) {
            $this->_apiKey = substr( (string) $params['apikey'], 0, 40 );
        }
    }


    /**
     * Returns universal unit by given code.
     *
     * @todo Future: Bring to a unit object with methodes like
     * $unit->getFormated()
     *
     * @param string $code Code to return the unit item. Possble values are:
     * 'percent', 'millimetre', 'metre'
     * @param number $value Value for plural forms of translation
     *
     * @return array List of key/value pairs containing array keys: 'key',
     * 'name', 'sign', 'code'
     * @throws Mumsys_Weather_Exception
     */
    public function getUnitUniversal( $code = '', $value = false )
    {
        $result = null;

        if ( $value !== false ) {
            $value = (float) $value;
        }

        switch ( $code )
        {
            case 'percent':
                $result = array(
                    'key' => 'percent',
                    'name' => _( 'Percent' ),
                    'sign' => '%',
                    'code' => null, // _('pct.') //  engl, p de_DE
                );
                break;

            case 'millimetre': // sing.
            case 'millimetres': // pl.
                $result = array(
                    'key' => 'millimetre',
                    'name' => _( 'Millimetre' ), // for plural translation
                    'sign' => null,
                    'code' => 'mm'
                );
                break;

            case 'metre': // sing.
            case 'metres': // pl.
                $result = array(
                    'key' => 'metre',
                    'name' => _( 'Metre' ),
                    'sign' => null,
                    'code' => 'm'
                );
                break;

            case 'mile': // sing.
            case 'miles': // pl.
                $result = array(
                    'key' => 'mile',
                    'name' => _( 'mile' ),
                    'sign' => null,
                    'code' => 'mi'
                );
                break;

            default:
                $mesg = 'Invalid code to return units';
                throw new Mumsys_Weather_Exception( $mesg );
                break;
        }

        return $result;
    }


    /**
     * Returns pressure units for hectopascals (hPa) or millibars (mbar).
     *
     * @todo Future: Bring to a unit object with methodes like
     * $unit->getFormated()
     *
     * @param string $unit Unit to return. Possible values: 'hectopascals',
     * 'pascals', 'millibars'
     *
     * @return array Returns pressure units
     * @throws Mumsys_Weather_Exception Throws exception if given $unit was
     * invalid
     */
    public function getUnitPressure( $unit = 'hectopascal' )
    {
        // 1 hPa = 100 Pa
        // 101325 Pa = 1013,25 hPa = 101,325 kPa  (Hektopascal = Millibar) = 1
        // atm (standard atmosphere)
        switch ( $unit )
        {
            case 'pascal':  // pl.
            case 'pascals': // sing.
            case 'Pa':
                $result = array(
                    'key' => 'pascals', // = millibars
                    'name' => 'Pascals',
                    'sign' => null,
                    'code' => 'Pa',
                );
                break;

            case 'hectopascal':
            case 'hectopascals':
            case 'hPa';
                $result = array(
                    'key' => 'hectopascals', // = millibars
                    'name' => _( 'Hectopascals' ),
                    'sign' => null,
                    'code' => 'hPa',
                );
                break;

            case 'millibars':
            case 'mbar':
                $result = array(
                    'key' => 'millibars', // = millibars
                    'name' => 'Millibar',
                    'sign' => null,
                    'code' => 'mbar',
                );
                break;

            default:
                $mesg = 'Invalid unit get to pressure units: "' . $unit . '"';
                throw new Mumsys_Weather_Exception( $mesg );
                break;
        }

        return $result;
    }



    /**
     * Returns weather data from requested url.
     *
     * @param string $url Url or host to fetch data from
     *
     * @return mixed|false Returns the content from given url
     * @throws Mumsys_Weather_Exception If no request can be made
     */
    protected function fetch( $url )
    {
        $data = '';
        if ( function_exists( 'curl_init' ) ) {
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_URL, $url );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
            $version = 'Mumsys_Weather_Interface ' . self::VERSION;
            curl_setopt( $curl, CURLOPT_USERAGENT, $version );
            $data = curl_exec( $curl );
            curl_close( $curl );
        } else if ( ini_get( 'allow_url_fopen' ) ) {
            $data = file_get_contents( $url );
        } else {
            $message = sprintf(
                'Can not request the service for "%1$s"',
                $url
            );
            throw new Mumsys_Weather_Exception( $message );
        }

        if ( empty( $data ) ) {
            $data = false;
        }

        return $data;
    }

}
