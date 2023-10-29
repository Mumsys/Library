<?php

/**
 * Mumsys_Weather_OpenWeatherMap
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
 * Open Weather API version 2.5 from openweathermaps.com
 *
 * @category Mumsys
 * @package Mumsys_Library
 * @subpackage Mumsys_Weather
 */
class Mumsys_Weather_OpenWeatherMap
    extends Mumsys_Weather_Abstract
    implements Mumsys_Weather_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '2.5.0';

    /**
     * Format of the service to be used.
     * @var string
     */
    protected $_format = 'json';

    /**
     * List of possible formats which can be used
     * @var array
     */
    protected $_formats = array('json', 'xml');

    /**
     * Unit system to be used
     * @var string
     */
    protected $_unit = 'metric';

    /**
     * List of possible units to be used
     * @var array
     */
    protected $_units = array('internal', 'metric', 'imperial');

    /**
     * Language or locale string to be used
     * @var string
     */
    protected $_language = 'en';

    /**
     * List of possible languages to be used.
     * Available languages (07/2013): English - en, Russian - ru, Italian - it, Spanish - sp, Ukrainian - ua,
     * German - de, Portuguese - pt, Romanian - ro, Polish - pl, Finnish - fi, Dutch - nl, French - fr, Bulgarian - bg,
     * Swedish - se, Chinese Traditional - zh_tw, Chinese Simplified - zh_cn, Turkish - tr
     */
    protected $_languages = array(
        'en', 'ru', 'it', 'sp', 'ua', 'de', 'pt', 'ro', 'pl', 'fi', 'nl', 'fr',
        'bg', 'se', 'zh_tw', 'zh_cn', 'tr'
    );

    /**
     * Base api url for the requests.
     * @var string
     */
    private $_apiBaseUrl = 'http://api.openweathermap.org/data/2.5/';

    /**
     * Action string to create the request url.
     * @var string
     */
    private $_actionUrl = '';

    /**
     * Compiled and completed url with parameters to do a request to the service.
     * @var string
     */
    private $_requestUrl;

    /**
     * Raw data from a request json or xml string.
     * @var string
     */
    private $_rawData;


    /**
     * Initialize the openweathermap.org service interface.
     *
     * @param array $params Basic parameters to be set for this driver:
     *  - [format] string required Format of service response: json (default), xml, html
     *  - [unit] string required Unit can be 'metric', 'imperial', 'internal' (default: metric).
     *  - [language] string required A language code like en, de, es, fr up to
     * five characters if a locale is needed.
     *  - [apikey] string required Your application key/ token/ access key your
     * need to access the data. Max 40 character
     *
     * @throws Mumsys_Weather_Exception
     */
    public function __construct( array $params = array() )
    {
        /** @todo to be check with other services */
        $message = false;

        if ( !isset( $params['unit'] ) ) {
            $message = 'Missing reqired "unit" parameter';
        }

        if ( !isset( $params['language'] ) ) {
            $message = 'Missing reqired "language" parameter';
        }

        if ( !isset( $params['format'] ) ) {
            $message = 'Missing reqired "format" parameter';
        }

        if ( !isset( $params['apikey'] ) ) {
            $message = 'Missing reqired "apikey" parameter';
        }

        if ( $message ) {
            throw new Mumsys_Weather_Exception( $message );
        }

        parent::__construct( $params );
    }


    /**
     * Returns a list of Mumsys_Weater_OpenWeatherMap_Item's
     *
     * @param string $action action to perform: <br />
     * - "weather" Get the current weather. If a comma seperated list of city IDs is given in "query" this action will
     * switch to "group" internally<br/>
     * - "group" Get list of current weather for given list of city IDs. Note: can be fetched only by json format<br />
     * - "forecast" or "forecast/hourly" (get list of forecasts in 3 hours interval for 40 records (~ 5 days + the rest
     * of the current day)<br/>
     * - "forecast/daily" (get list of forecasts up to 14 days, default: 7 days<br/>
     * - "forecast/daily/14" to fetch 14 days (max) or less.
     * Api Note: The 3 hours forecast is available for 5 days. Daily forecast is available for 14 days.<br/>
     * - "find" | "find/accurate[/numResults]" get list exactly equivalent to your searching word of city. Use integer
     * for numResults to reduce the result.<br/>
     * - "find/like[/numResults]" get list of citys searching by substring. Use integer for numResults to reduce
     * the result.
     * @param string|array $query [[Name of the city],[country code]], array containing "lon" and "lat" keys for
     * getting a weather by coordinate or a city IDs (in "weather" action a comma seperated list of city IDs is possible
     * '524901,2172797,703448,2643743') E.g.: 'London,uk' or array('lon'=>139, 'lat'=>35) or
     * @param string $lang A language code. Available languages (07/2013):
     * English - en, Russian - ru, Italian - it, Spanish - sp, Ukrainian - ua, German - de, Portuguese - pt,
     * Romanian - ro, Polish - pl, Finnish - fi, Dutch - nl, French - fr, Bulgarian - bg, Swedish - se,
     * Chinese Traditional - zh_tw, Chinese Simplified - zh_cn, Turkish - tr
     * @param string $units Units can be 'metric', 'imperial', 'internal' (default: metric).
     * E.g: Celsius, Fahrenheit, kelvin
     * @param string $format Format of service response: json (default), xml, html
     * @param type $apiKey Your application key/ token/ access key your need to access the data
     *
     * @return array|false List of Mumsys_Weather_Item's
     */
    private function _getWeather( $action, $query = '' )
    {
        $this->_requestUrl = $this->_buildUrl( $action, $query );

        return $this->_getContent( $this->_requestUrl );
    }


    /**
     * Returns a list of Mumsys_Weather_Item's for the current weather.
     *
     * @param string|array $query [[Name of the city],[country code]], array containing "lon" and "lat" keys for
     * getting a weather by coordinate or city IDs (a comma seperated list of city IDs) is possible e.g:
     * '524901,2172797,703448,2643743') 'London,uk' or array('lon'=>139, 'lat'=>35)
     *
     * @return array|false List of Mumsys_Weather_Item's
     */
    public function getWeather( $query = '' )
    {
        return $this->_getWeather( 'weather', $query );
    }


    /**
     * Returns a list of Mumsys_Weather_Item's of forecasts in 3 hours interval for 40 records (~ 5 days + the rest of
     * the day) specified by query parameter.
     * Api Note: The 3 hours forecast is available for 5 days. Daily forecast is available for 14 days.
     *
     * @todo query options to be tested! beschreibung überarbeiten beschreibung passend finalisieren!
     *
     * @param string|array $query [[Name of the city],[country code]], array containing "lon" and "lat" keys for
     * getting a weather by coordinate or city IDs (a comma seperated list of city IDs) is possible e.g:
     * '524901,2172797,703448,2643743') 'London,uk' or array('lon'=>139, 'lat'=>35)
     *
     * @return array|false List of Mumsys_Weather_Item's
     */
    public function getWeatherForecast( $query = '' )
    {
        return $this->_getWeather( 'forecast', $query );
    }


    /**
     * Returns a list of Mumsys_Weather_Item's of forecasts in 3 hours interval for up to 14 days specified by days and
     * query parameter.
     * Api Note: The 3 hours forecast is available for 5 days. Daily forecast is available for 14 days.
     *
     * @todo query options to be tested! beschreibung überarbeiten beschreibung passend finalisieren!
     *
     * @param string|array $query [[Name of the city],[country code]], array containing "lon" and "lat" keys for
     * getting a weather by coordinate or city IDs (a comma seperated list of city IDs) is possible e.g:
     * '524901,2172797,703448,2643743') 'London,uk' or array('lon'=>139, 'lat'=>35)
     * @param integer $numDays Number of days in 3 hour interval to return. Default is 7, max 14 days
     *
     * @return array|false List of Mumsys_Weather_Item's
     */
    public function getWeatherForecastDaily( $query = '', $numDays = 7 )
    {
        $days = (int) $numDays;
        if ( $days > 14 || $days < 1 ) {
            $days = 7;
        }

        $action = 'forecast/daily/' . $days;

        return $this->_getWeather( $action, $query );
    }


    /**
     * Returns a list of Mumsys_Weather_Item's searching exactly to your searching word of city parameter.
     *
     * @param string $city Exact name of the city to search for
     * @param integer $numResults Number of results to return. Default is 5
     *
     * @return array|false List of Mumsys_Weather_Item's
     */
    public function getWeatherFindAccurate( $city = '', int $numResults = 5 )
    {
        $num = (int) $numResults;
        $action = 'find/accurate/' . $num;

        return $this->_getWeather( $action, $city );
    }


    /**
     * Returns a list of Mumsys_Weather_Item's searching with sounds like to your searching word of city paramerter
     *
     * @param string $city Name of the city to search for
     * @param integer $numResults Number of results to return. Default is 5
     *
     * @return array|false List of Mumsys_Weather_Item's
     */
    public function getWeatherFindLike( $city = '', $numResults = 5 )
    {
        $num = (int) $numResults;
        $action = 'find/like/' . $num;

        return $this->_getWeather( $action, $city );
    }


    /**
     * Returns raw data from weather request.
     *
     * @see OpenWeatherMap::getWeather()
     * @return string|false Raw response string in json or xml format
     */
    public function getRawWeather( $query = '' )
    {
        $this->_requestUrl = $this->_buildUrl( 'weather', $query );

        return $this->fetch( $this->_requestUrl );
    }


    /**
     * Returns the raw data from a previous request used with get[Weather|Forecast]() action.
     *
     * @return mixed
     */
    public function getRawData()
    {
        return $this->_rawData;
    }


    /**
     * Returns the request url.
     * @return string Url of the request.
     */
    public function getRequestUrl()
    {
        return $this->_requestUrl;
    }


    /**
     * Returns a newly created weather item.
     *
     * @return Mumsys_Weather_Item_Default
     */
    public function createItem()
    {
        $init = array(
            'publisher' => array(
                'id' => 2,
                'name' => 'openweathermaps.com',
            ),
        );
        return $this->_createItem( $init );
    }


    public function fetch( $url )
    {
        $data = parent::fetch( $url );

        $oCache = new Mumsys_Cache( 'owm', $url );
        $oCache->setPath( './tmp/' );
        if ( $oCache->isCached() ) {
            return $oCache->read();
        }

        if ( $data !== false ) {
            // every 20min - 3h new data will be available!
            // not more than every 10min a request should be made! at all!
            // at this end we cache re-requested data for 15min.
            $oCache->write( $ttl = ( 15 * 60 ), $data );

            // --- just tracking new records ---
            $tmp = $url . PHP_EOL . print_r( json_decode( $data ), true );
            file_put_contents( './tmp/weather.' . date( 'Ymd-Hi', time() ) . '.tmp', $tmp );
        }

        return $data;
    }


    /**
     * Builds and returns the request url.
     *
     * @param string $action Action perform.
     * @param string|array $query Search parameter
     * @return string Returns the request url
     * @throws Mumsys_Weather_Exception
     */
    private function _buildUrl( $action, $query )
    {
        $actions = explode( '/', $action );
        $cntActions = count( $actions );
        $parameters = array();

        switch ( $actions[0] )
        {
            case 'weather':
            case 'group':
                $this->_actionUrl = $actions[0];
                break;

            case 'forecast': // 'forecast/daily', 'forecast/daily/14'
                if ( $cntActions >= 2 ) {
                    if ( !in_array( $actions[1], array('daily', 'hourly') ) ) {
                        $mesg = 'Invalid action parameter for the request: "' . $actions[1] . '"';
                        throw new Mumsys_Weather_Exception( $mesg );
                    }

                    $this->_actionUrl = 'forecast/' . $actions[1];
                    if ( $cntActions == 3 ) {
                        $parameters[] = 'cnt=' . (int) $actions[2];
                    }
                } else {
                    $this->_actionUrl = 'forecast';
                }
                break;

            case 'find': // 'find/accurate' , 'find/like'
                $this->_actionUrl = 'find';

                if ( $cntActions >= 2 ) {
                    if ( !in_array( $actions[1], array('accurate', 'like') ) ) {
                        $mesg = 'Invalid action parameter for the request: "' . $actions[1] . '"';
                        throw new Mumsys_Weather_Exception( $mesg );
                    } else {
                        $parameters[] = 'type=' . $actions[1];
                    }

                    if ( $cntActions == 3 ) {
                        $parameters[] = 'cnt=' . (int) $actions[2];
                    }
                }
                break;

            default:
                throw new Exception( 'Invalid action parameter for the request' );
                break;
        }

        /** note: needs to be set/checked before _format will be set to parameters! $_format can change here! */
        if ( ( $result = $this->_buildSearch( $query ) ) ) {
            foreach ( $result as $value ) {
                $parameters[] = $value;
            }
        }

        if ( $this->_language ) {
            $parameters[] = 'lang=' . (string) $this->_language;
        }

        if ( $this->_unit ) {
            $parameters[] = 'units=' . (string) $this->_unit;
        }

        if ( $this->_format ) {
            $parameters[] = 'mode=' . (string) $this->_format;
        }

        if ( $this->_apiKey ) {
            $parameters[] = 'APPID=' . (string) $this->_apiKey;
        }

        $queryStr = implode( '&', $parameters );

        return $this->_apiBaseUrl . $this->_actionUrl . '?' . $queryStr;
    }


    /**
     * Pre prosessor for the query parameter.
     * For details see getWeather() method.
     *
     * @param string $query Query to fetch data
     * @return array|false Returns list of parameters for the request url
     * @throws Mumsys_Weather_Exception Throws exception on invalid or missing parameters
     */
    private function _buildSearch( $query = '' )
    {
        $parameters = false;

        if ( $query ) {
            if ( is_array( $query ) ) {
                if ( !is_numeric( $query['lat'] ) || !is_numeric( $query['lon'] ) ) {
                    throw new Exception( 'Invalid query parts detected: "' . $query . '"' );
                }

                $parameters[] = 'lat=' . (string) $query['lat'];
                $parameters[] = 'lon=' . (string) $query['lon'];
            } else {
                $queryParts = explode( ',', $query );
                $numeric = $string = false;

                if ( count( $queryParts ) ) {

                    foreach ( $queryParts as $part ) {
                        if ( is_numeric( $part ) ) {
                            $numeric[] = $part;
                        } else if ( !empty( $part ) && is_string( $part ) ) {
                            $string = true;
                        }
                    }

                    if ( $numeric && !$string ) {
                        $parameters[] = 'id=' . implode( ',', $numeric );
                        if ( $this->_actionUrl == 'weather' && count( $numeric ) > 1 ) {
                            $this->_actionUrl = 'group';
                            $this->_format = 'json';
                        }
                    } else {
                        $parameters[] = 'q=' . (string) $query;
                    }
                } else {
                    $mesg = 'Invalid query parts detected: "' . $queryParts . '"';
                    throw new Mumsys_Weather_Exception( $mesg );
                }
            }
        } else {
            throw new Mumsys_Weather_Exception( 'Missing query parameter for the request' );
        }

        return $parameters;
    }


    /**
     * Receives the requested content and parses the response to return a homogeneous structure.
     *
     * @param string $url Full api url to request
     * @return array|false
     */
    private function _getContent( $url )
    {
        $this->_rawData = $this->fetch( $url );
        if ( $this->_rawData === false ) {
            return false;
        }

        $result = false;
        switch ( $this->_format )
        {
            case 'json':
                $result = $this->_parseJson( $this->_rawData );
                break;

            case 'xml':
                $result = $this->_parseXml( $this->_rawData );
                break;
        }

        return $result;
    }


    /**
     * Returns a homogeneous structure from given api request.
     *
     * @todo only for weather, group action at the moment. forecast missing
     *
     * @param string $response Json response from api request
     *
     * @return array Returns list of Mumsys_Weather_Item_Default's for futher actions
     * @throws Mumsys_Weather_Exception
     */
    private function _parseJson( $response )
    {
        $objects = array();
        $data = json_decode( $response );

        if ( ( isset( $data->cod ) && $data->cod == 404 ) ) {
            return false;
        }

        // single return
        if ( !empty( $data->cod ) && $data->cod == 200 ) {
            $objects = array($data);
        }
        // multi result return
        if ( !empty( $data->cnt ) && !empty( $data->list ) ) {
            $objects = $data->list;
        }

        $result = false;
        foreach ( $objects as $i => $item ) {
            $weatherItem = $this->createItem();

            $weatherItem->setLastupdate( $item->dt );
            // isset($item->base)?$item->base:'openweathermap';
            $weatherItem->setLanguage( $this->_language );

            $location = array(
                'name' => $item->name,
                'country' => '',
                'countryCode' => $item->sys->country,
                'id' => $item->id,
                'sunrise' => $item->sys->sunrise,
                'sunset' => $item->sys->sunset,
                'latitude' => $item->coord->lat,
                'longitude' => $item->coord->lon,
                'altitude' => @$item->coord->alt,
            );
            $weatherItem->setLocation( $location );

            if ( isset( $item->weather[0] ) ) { // foreach ( $item->weather as $j => $weather ) {
                $desc = array(
                    'id' => $item->weather[0]->id,
                    'key' => $item->weather[0]->main,
                    'name' => $item->weather[0]->description,
                    'icon' => $item->weather[0]->icon,
                );
                $weatherItem->setWeatherDescription( $desc );
            }

            $temp = array(
                'value' => $item->main->temp,
                'min' => $item->main->temp_min,
                'max' => $item->main->temp_max,
                'night' => @$item->main->night, // @todo not always present
                'eve' => @$item->main->eve, // @todo not always present
                'morn' => @$item->main->morn, // @todo not always present
                'unit' => Mumsys_Weather_Item_Unit_Factory::createItem( 'Temperature', $this->_unit ),
            );
            $weatherItem->setTemperature( $temp );

            $press = array(
                'value' => $item->main->pressure,
                'unit' => $this->getUnitPressure(),
                'sea' => @$item->main->sea_level, // @todo not always present
                'ground' => @$item->main->grnd_level, // @todo not always present
                //'rising' => -1, 0, 1 // not exits in api
            );
            $weatherItem->setPressure( $press );

            $humidity = array(
                'value' => $item->main->humidity,
                'unit' => $this->getUnitUniversal( 'percent' )
            );
            $weatherItem->setHumidity( $humidity );

            // not exits in api
            //smok, fog, visibility is value = 50m
            //$weatherItem->setVisibility( array('value'=> $value, 'unit'=> $this->getUnitUniversal('metre', $value)) );

            $targetWindCode = 'm/s';
            $windSpeed = array(
                'value' => $this->convertWindSpeed( 'm/s', $item->wind->speed, $targetWindCode ),
                'min' => null,
                'max' => null,
                'unit' => $this->getUnitWindSpeed( $targetWindCode ),
                'key' => '', // name="Gentle Breeze"
                'name' => '', // translated name="Gentle Breeze"
                'gust' => @$item->wind->gust // @todo windboen not always present
            );
            $weatherItem->setWindSpeed( $windSpeed );

            $windDirect = array(
                'value' => $item->wind->deg,
                'unit' => $this->getUnitWindDirection(),
                'begin' => @$item->wind->var_beg, // @todo not always present
                'end' => @$item->wind->var_end, // @todo not always present
                'key' => '', // name="West-southwest"
                'name' => '', // translation of name="West-southwest"
                'code' => $this->getCodeWindDirection( $item->wind->deg, 1 ) // code="WSW"
            );
            $weatherItem->setWindDirection( $windDirect );

            // Temperature by wind speed or how do you feed the Temperature
            // $weatherItem->setWindChill( array('value'=>$xx, 'unit'=>$this->getUnitTemperature($this->_unit));

            $clouds = array(
                'value' => $item->clouds->all,
                'unit' => $this->getUnitUniversal( 'percent' ),
            );
            $weatherItem->setClouds( $clouds );

            $rain = array();
            if ( isset( $item->rain ) ) {
                if ( isset( $item->rain->{'3h'} ) ) {
                    $rainValue = $item->rain->{'3h'};
                    $rainInterval = '3h';
                } else if ( isset( $item->rain->{'1h'} ) ) {
                    $rainValue = $item->rain->{'1h'};
                    $rainInterval = '1h';
                } else {
                    $rainValue = 0;
                    $rainInterval = 'err';
                }

                $rain['value'] = $rainValue;
                $rain['unit'] = $this->getUnitUniversal( 'millimetre' );
                $rain['interval'] = $rainInterval;
            }
            $weatherItem->setPrecipitationRain( $rain );

            $snow = array();
            if ( isset( $item->snow ) ) {
                if ( isset( $item->snow->{'3h'} ) ) {
                    $snowValue = $item->snow->{'3h'};
                    $snowInterval = '3h';
                } else if ( isset( $item->snow->{'1h'} ) ) {
                    $snowValue = $item->snow->{'1h'};
                    $snowInterval = '1h';
                } else {
                    $snowValue = 0;
                    $snowInterval = 'err';
                }
                $snow['value'] = $snowValue;
                $snow['unit'] = $this->getUnitUniversal( 'millimetre' );
                $snow['interval'] = $snowInterval;
            }
            $weatherItem->setPrecipitationSnow( $snow );

            /* useful for tests
          $result[$i]['lastupdate'] = $item->dt;
          $result[$i]['base'] = @$item->base; // @todo not always present
          $result[$i]['language'] = $this->_language;

          $result[$i]['location'] = array();
          $result[$i]['location']['name'] = $item->name;
          $result[$i]['location']['country'] = $item->sys->country;
          $result[$i]['location']['id'] = $item->id;
          $result[$i]['location']['sunrise'] = $item->sys->sunrise;
          $result[$i]['location']['sunset'] = $item->sys->sunset;
          $result[$i]['location']['latitude'] = $item->coord->lat;
          $result[$i]['location']['longitude'] = $item->coord->lon;
          $result[$i]['location']['altitude'] = @$item->coord->alt;

          $result[$i]['descriptions'] = array();

          foreach ( $item->weather as $j => $weather ) {
          $result[$i]['descriptions'][$j] = array();
          $result[$i]['descriptions'][$j]['id'] = $weather->id;
          $result[$i]['descriptions'][$j]['key'] = $weather->main;
          $result[$i]['descriptions'][$j]['value'] = $weather->description;
          $result[$i]['descriptions'][$j]['icon'] = $weather->icon;
          }

          $result[$i]['temperature'] = array();
          $result[$i]['temperature']['value'] = $item->main->temp;
          $result[$i]['temperature']['min'] = $item->main->temp_min;
          $result[$i]['temperature']['max'] = $item->main->temp_max;
          //[night] => 6.53    [eve] => 7.52    [morn] => 8.42
          $result[$i]['temperature']['unit'] = $this->getUnitTemperature($this->_unit);

          $result[$i]['pressure'] = array();
          $result[$i]['pressure']['value'] = $item->main->pressure;
          $result[$i]['pressure']['unit'] = $this->getUnitPressure();
          $result[$i]['pressure']['sea'] = @$item->main->sea_level;
          $result[$i]['pressure']['ground'] = @$item->main->grnd_level;

          $result[$i]['humidity'] = array();
          $result[$i]['humidity']['value'] = $item->main->humidity;
          $result[$i]['humidity']['unit'] = $this->getUnitUniversal('percent');

          $targetWindCode = 'm/s';
          $result[$i]['wind'] = array();
          $result[$i]['wind']['speed'] = array();
          $result[$i]['wind']['speed']['value'] = $this->convertWindSpeed('m/s', $item->wind->speed, $targetWindCode);
          $result[$i]['wind']['speed']['unit'] = $this->getUnitWindSpeed($targetWindCode);
          $result[$i]['wind']['speed']['key'] = ''; // name="Gentle Breeze"
          $result[$i]['wind']['speed']['name'] = ''; // translated name="Gentle Breeze"
          $result[$i]['wind']['speed']['gust'] = @$item->wind->gust; // @todo windboen not always present

          $result[$i]['wind']['direction'] = array();
          $result[$i]['wind']['direction']['value'] = $item->wind->deg;
          $result[$i]['wind']['direction']['unit'] = $this->getUnitWindDirection();
          $result[$i]['wind']['direction']['begin'] = @$item->wind->var_beg; // @todo not always present
          $result[$i]['wind']['direction']['end'] = @$item->wind->var_end; // @todo not always present
          $result[$i]['wind']['direction']['key'] = ''; // name="West-southwest"
          $result[$i]['wind']['direction']['name'] = ''; // translation of name="West-southwest"
          $result[$i]['wind']['direction']['code'] = $this->getCodeWindDirection( $item->wind->deg ); // code="WSW"

          $result[$i]['clouds'] = array();
          $result[$i]['clouds']['value'] = $item->clouds->all;
          $result[$i]['clouds']['unit'] = $this->getUnitUniversal('percent');

          $result[$i]['precipitation'] = array();
          $result[$i]['precipitation']['rain']['value'] = @$item->rain->{'3h'}; // @todo not always present
          $result[$i]['precipitation']['rain']['interval'] = '3h';
          $result[$i]['precipitation']['rain']['unit'] = $this->getUnitUniversal('millimetre');
          ['gust']
          $result[$i]['precipitation']['snow']['value'] = @$item->snow->{'3h'}; // @todo not always present
          $result[$i]['precipitation']['snow']['interval'] = '3h';
          $result[$i]['precipitation']['snow']['unit'] = $this->getUnitUniversal('millimetre');
         */

            $result[$i] = $weatherItem;
        }

        return $result;
    }


    /**
     * Returns a homogeneous structure from given api request.
     *
     * @see _parseJson implementation which contains different, more complete data except the descriptions.
     *
     * @param string $response Xml response from api request
     * @return array homogeneous structure for futher actions
     */
    private function _parseXml( $response )
    {
        if ( $response === false
            //|| ( isset( $data->cod ) && $data->cod == 404 )
        ) {
            return false;
        }

        libxml_use_internal_errors( true );
        libxml_clear_errors();

        $data = new SimpleXMLElement( $response );

        $objects = array();

        // single return
        if ( isset( $data->city ) || isset( $data->city->coord ) ) {
            $objects = array($data);
        }

        // multi return
        if ( isset( $data->forecast ) || isset( $data->location ) ) {
            // $objects = $data -> list;
            throw new Exception( 'Multi return not implemented yet' );
        }

        $result = false;
        foreach ( $objects as $i => $item ) {
            $weatherItem = $this->createItem();

            if ( isset( $item->lastupdate['value'] ) ) {
                $strTime = (string) $item->lastupdate['value'];
                $lastupdate = mktime(
                    substr( $strTime, 11, 2 ), substr( $strTime, 14, 2 ),
                    substr( $strTime, 17, 2 ), substr( $strTime, 5, 2 ),
                    substr( $strTime, 8, 2 ), substr( $strTime, 0, 4 )
                );

                $weatherItem->setLastupdate( $lastupdate );
            }

            $weatherItem->setLanguage( $this->_language );

            $location = array();
            if ( isset( $item->city['id'] ) ) {
                $location['id'] = (string) $item->city['id'];
            }
            if ( isset( $item->city['name'] ) ) {
                $location['name'] = (string) $item->city['name'];
            }
            if ( isset( $item->city->country ) ) {
                $location['country'] = '';
                $location['countryCode'] = (string) $item->city->country;
            }
            if ( isset( $item->city->sun['rise'] ) || isset( $item->city->sun['set'] ) ) {
                $strTime = (string) $item->city->sun['rise'];
                $sunrise = mktime(
                    substr( $strTime, 11, 2 ), substr( $strTime, 14, 2 ),
                    substr( $strTime, 17, 2 ), substr( $strTime, 5, 2 ),
                    substr( $strTime, 8, 2 ), substr( $strTime, 0, 4 )
                );
                $strTime = (string) $item->city->sun['set'];
                $sunset = mktime(
                    substr( $strTime, 11, 2 ), substr( $strTime, 14, 2 ),
                    substr( $strTime, 17, 2 ), substr( $strTime, 5, 2 ),
                    substr( $strTime, 8, 2 ), substr( $strTime, 0, 4 )
                );
                $location['sunrise'] = $sunrise;
                $location['sunset'] = $sunset;
            }

            if ( isset( $item->city->coord['lon'] ) && isset( $item->city->coord['lat'] ) ) {
                $location['latitude'] = (float) $item->city->coord['lat'];
                $location['longitude'] = (float) $item->city->coord['lon'];

                if ( isset( $item->city->coord['alt'] ) ) {
                    $location['altitude'] = (float) $item->city->coord['alt'];
                } else {
                    $location['altitude'] = null;
                }
            }

            $weatherItem->setLocation( $location );

            if ( isset( $item->weather['number'] ) ) {
                $desc = array(
                    'id' => (string) $item->weather['number'],
                    'key' => (string) @$item->weather['main'], // not in in xml ?
                    'name' => (string) $item->weather['value'],
                    'icon' => (string) $item->weather['icon'],
                );
                $weatherItem->setWeatherDescription( $desc );
            }

            $temp = array();
            if ( isset( $item->temperature['unit'] ) ) {
                $unitCur = (string) $item->temperature['unit'];
                $temp['unit'] = $this->getUnitTemperature( $this->_unit );
                $unitTarget = $temp['unit']['key'];
                $list = array('value', 'min', 'max', 'night', 'eve', 'morn');
                foreach ( $list as $key ) {
                    if ( isset( $item->temperature[$key] ) ) {
                        $tempValue = (float) $item->temperature[$key];
                        $value = $this->convertTemperature(
                            $unitCur, $tempValue, $unitTarget
                        );
                        $temp[$key] = $value;
                    }
                }
                $weatherItem->setTemperature( $temp );
                unset( $list, $temp, $value );
            }

            $press = array();
            if ( isset( $item->pressure['unit'] ) ) {
                $press = array(
                    'value' => (float) $item->pressure['value'],
                    'unit' => $this->getUnitPressure( (string) $item->pressure['unit'] ),
                    //'rising' => -1, 0, 1 // not exits in api
                );
                if ( isset( $item->pressure['sea'] ) ) {
                    $press['sea'] = (float) $item->pressure['sea']; // @todo not always present
                }
                if ( isset( $item->pressure['ground'] ) ) {
                    $press['ground'] = (float) $item->pressure['ground']; // @todo not always present
                }
                $weatherItem->setPressure( $press );
            }

            if ( isset( $item->humidity['unit'] ) ) {
                $humidity = array(
                    'value' => (float) $item->humidity['value'],
                    'unit' => $this->getUnitUniversal( 'percent' ),
                );
                $weatherItem->setHumidity( $humidity );
            }

            // not exits in api
            //smok, fog, visibility is value = 50m
            //$weatherItem->setVisibility( array('value'=> $value, 'unit'=> $this->getUnitUniversal('metre', $value)) );

            $targetWindCode = 'm/s';
            $windSpeed = array(
                'value' => $this->convertWindSpeed(
                    'm/s', (string) $item->wind->speed['value'], $targetWindCode
                ),
                'unit' => $this->getUnitWindSpeed( $targetWindCode ),
                'key' => (string) $item->wind->speed['name'], // name="Gentle Breeze"
                'name' => _( (string) $item->wind->speed['name'] ), // translated name="Gentle Breeze"
            );

            if ( isset( $item->wind->speed['min'] ) ) {
                $windSpeed['min'] = $item->wind->speed['min']; // @todo not always present
            }
            if ( isset( $item->wind->speed['max'] ) ) {
                $windSpeed['max'] = $item->wind->speed['max']; // @todo not always present
            }
            if ( isset( $item->wind->speed['gust'] ) ) {
                $windSpeed['gust'] = $item->wind->speed['gust']; // @todo not always present
            }
            $weatherItem->setWindSpeed( $windSpeed );

            $windDirect = array();
            if ( isset( $item->wind->direction['value'] ) ) {
                $windDirect['unit'] = $this->getUnitWindDirection();
                if ( isset( $item->wind->direction['value'] ) ) {
                    $windDirect['value'] = $item->wind->direction['value']; // @todo not always present
                    // code="WSW"
                    $windDirect['code'] = $this->getCodeWindDirection(
                        (int) $item->wind->direction['value'], 1
                    );
                }

                if ( isset( $item->wind->direction['name'] ) ) {
                    $windDirect['key'] = (string) $item->wind->direction['name'];
                    $windDirect['name'] = _( (string) $item->wind->direction['name'] );
                }

                if ( isset( $item->wind->direction['begin'] ) ) {
                    $windDirect['begin'] = $item->wind->direction['begin']; // @todo not always present
                }

                if ( isset( $item->wind->direction['end'] ) ) {
                    $windDirect['end'] = $item->wind->direction['end']; // @todo not always present
                }

                if ( isset( $item->wind->direction['code'] ) ) {
                    $windDirect['key'] = $item->wind->direction['name'];
                }

                $weatherItem->setWindDirection( $windDirect );
            }

            // Temperature by wind speed or how do you feed the Temperature
            // $weatherItem->setWindChill( array('value'=>$xx, 'unit'=>$this->getUnitTemperature($this->_unit));

            if ( isset( $item->clouds['value'] ) ) {
                $clouds = array(
                    'value' => (float) $item->clouds['value'],
                    'unit' => $this->getUnitUniversal( 'percent' ),
                );
                if ( isset( $item->clouds['name'] ) ) {
                    $clouds['key'] = $item->clouds['name'];
                    $clouds['name'] = _( $item->clouds['name'] );
                }
                $weatherItem->setClouds( $clouds );
            }

            $rainOrSnow = array();

            $mode = (string) @$item->precipitation['mode'];
            if ( isset( $item->precipitation['mode'] ) && $mode != 'no' ) {
                if ( in_array( $mode, array('rain', 'snow') ) ) {
                    $rainOrSnow[$mode]['value'] = $item->precipitation['value'];
                    $rainOrSnow[$mode]['unit'] = $this->getUnitUniversal( 'millimetre' );
                    $rainOrSnow[$mode]['interval'] = $item->precipitation['unit'];
                } else {
                    $msg = sprintf(
                        'Invalivor unknown mode "%1$s" to create precipitation records',
                        $mode
                    );
                    throw new Exception( $msg );
                }

                if ( $mode == 'rain' ) {
                    $weatherItem->setPrecipitationRain( $rainOrSnow );
                } else if ( $mode == 'snow' ) {
                    $weatherItem->setPrecipitationSnow( $rainOrSnow );
                }
            }

            $result[$i] = $weatherItem;
        }

        return $result;
    }


    /**
     * Returns a newly created weather item.
     *
     * @param array $params Parameters to initialise
     * @return Mumsys_Weather_Item_Default
     */
    private function _createItem( array $params )
    {
        return new Mumsys_Weather_Item_Default( $params, false );
    }

}
