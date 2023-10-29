<?php

/**
 * Mumsys_Weather_Item_Default
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
 * Weather item class containing all weather informations from a weather
 * service.
 *
 * Note: This is a data container as array for the moment. To create sub objects
 * for the main properties or some units
 * does not make sence ... at the moment. if you want to have stdclass's for all
 * propertys use
 * $object = json_decode( json_encode( $this->toArray() ) ); but remind that
 * everyting is public.
 *
 * @todo check inline docs
 * @todo validation for var types
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 */
class Mumsys_Weather_Item_Default
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * The store for all data. See $_itemDefaults for all possible properties.
     * @var array
     */
    protected $_record = array();

    /**
     * Array containing the list of all possible properties.
     * @var type
     */
    private $_itemDefaults = array(
        'publisher' => array(
            'id',
            'name',
        ),
        'lastupdate', // unix timestamp in UTC
        'language', // string e.g. en|de|ru default 'en'
        'location' => array(
            'id',
            'name',
            'country',
            'countryCode',
            'sunrise',
            'sunset',
            'latitude',
            'longitude',
            'altitude',
            'tz_offset', /** @todo timezone offset: eg +1 , +0100 ? */
        ),
        /**
         * description of the weather.
         * @var array
         */
        'description' => array(
            'id',
            'key',
            'name',
            'icon',
        ),
        'temperature' => array(
            'value',
            'min',
            'max',
            'night',
            'eve',
            'morn',
            'unit'
        ),
        // 'atmosphere' => array(
        'pressure' => array(
            'value',
            'sea',
            'ground',
            'unit',
            /* trend of the barometric pressure: steady (0), rising (1), or
             * falling (-1)*/
            'trend',
        ),
        'humidity' => array(
            'value',
            'unit'
        ),
        /* visibility, on smok, fog. How far can you look */
        'visibility' => array(
             'value',
             'unit'
        ),
        'wind' => array(
            'speed' => array(
                'value',
                'min',
                'max',
                'unit',
                'key',
                'name',
                'gust'
            ),
            'direction' => array(
                'value',
                'begin',
                'end',
                'unit',
                'key',
                'name',
                'code'
            ),
            /*
             * wind chill
             * "Windkühle (gefühlte Temperatur) Die über den Windsensor
             * erfassten Windkühledaten werden mit ihren
             * aktuellen, minimalen und maximalen Werten angezeigt.
             * Die innerhalb einer bestimmten Zeitspanne gespeicherten minimalen
             * und maximalen Windkühlewerte werden
             * zusammen mit Datum und Zeitpunkt der Speicherung ebenfalls
             * angezeigt und bei Eintritt neuer Minimal-
             * oder Maximalwerte automatisch aktualisiert." */
            'chill' => array(
                'value',
                'unit'
            )

        ),
        'clouds' => array(
            'value',
            'unit',
            'key',
            'name'
        ),
        'precipitation' => array(
            'rain' => array(
                'value',
                'unit',
                'interval'
            ),
            'snow' => array(
                'value',
                'unit',
                'interval'
            ),
        ),
    );

    private $_unitDefaults = array('key', 'name', 'sign', 'code');


    /**
     * Initialize the weather item object.
     * If flag $strictlyAll is true all possible values will be set with NULL.
     * This is helpful to get a complete
     * list for all properties.
     *
     * @see set[property]() methods
     * @see _createUnit() for 'unit' property
     *
     * @param array $params List of properties to be set:
     * - 'publisher' possible properties are: 'id', 'name'
     * - 'lastupdate' unix timestamp UTC
     * - 'language' string Language code or locale string
     * - 'location' possible properties are: 'id', 'name', 'country',
     * 'countryCode', 'sunrise', 'sunset', 'latitude',
     * 'longitude', 'altitude', 'tz_offset'
     * - 'description' array Possible properties are: 'id', 'key', 'name','icon'
     * - 'temperature' array Possible properties are: 'value', 'min', 'max',
     * 'night', 'eve', 'morn', 'unit
     * - 'pressure' array Possible properties are: 'value', 'sea', 'ground',
     * 'unit', 'trend'
     * - 'humidity' array Possible properties are: 'value', 'unit'
     * - 'visibility' array Possible properties are: 'value', 'unit'
     * - 'wind' array Possible properties for the sub properties are: 'speed',
     * 'direction', 'chill'
     * - 'clouds' array Possible properties are: 'value', 'unit'
     * - 'precipitation' array Possible properties for the sub properties are:
     * 'rain', 'snow'
     * @param boolean $strict Flag to set all possible values or just the
     * data you have.
     */
    public function __construct( array $params = array(), bool $strict = false )
    {
        if ( $strict ) {
            $this->_record = $this->_getItemDefaults( $this->_itemDefaults );
        }

        if ( $params ) {
            if ( isset( $params['publisher'] ) ) {
                $this->setPublisher( $params['publisher'] );
            }
            if ( isset( $params['lastupdate'] ) ) {
                $this->_record['lastupdate'] = (string) $params['lastupdate'];
            }
            if ( isset( $params['language'] ) ) {
                $this->_record['language'] = (string) $params['language'];
            }
            if ( isset( $params['location'] ) ) {
                $this->setLocation( $params['location'] );
            }
            if ( isset( $params['description'] ) ) {
                $this->setWeatherDescription( $params['description'] );
            }
            if ( isset( $params['temperature'] ) ) {
                $this->setTemperature( $params['temperature'] );
            }
            if ( isset( $params['pressure'] ) ) {
                $this->setPressure( $params['pressure'] );
            }
            if ( isset( $params['humidity'] ) ) {
                $this->setHumidity( $params['humidity'] );
            }
            if ( isset( $params['visibility'] ) ) {
                $this->setVisibility( $params['visibility'] );
            }
            if ( isset( $params['wind'] ) ) {
                $this->setWind( $params['wind'] );
            }
            if ( isset( $params['clouds'] ) ) {
                $this->setClouds( $params['clouds'] );
            }
            if ( isset( $params['precipitation'] ) ) {
                $this->setPrecipitation( $params['precipitation'] );
            }
        }
    }


    /**
     * Generic getter to return item properties.
     *
     * @param string $key Key of the properties to return @see $_itemDefaults
     * @param mixed $default Reurn value if $key not exists. Default: null
     *
     * @return mixed Returns the value for the given $key or $default
     * @throws Mumsys_Exception If key is not a string
     */
    public function get( string $key = null, $default = null )
    {
        if ( $key === null ) {
            return $this->_record;
        }

        if ( isset( $this->_record[$key] ) ) {
            return $this->_record[$key];
        }

        return $default;
    }


    /**
     * Sets the publisher informations
     *
     * @param array $publ Properties for the publisher:
     * - 'id' string Unique ID for the publisher
     * - 'name' string Name of the publisher
     */
    public function setPublisher( array $publ = array() )
    {
        if ( isset( $publ['id'] ) ) {
            $this->_record['publisher']['id'] = (string) $publ['id'];
        }

        if ( isset( $publ['name'] ) ) {
            $this->_record['publisher']['name'] = (string) $publ['name'];
        }
    }


    /**
     * Sets the last update time this record was created/ published.
     *
     * @param integer $utcLastUpdate Unix timestamp UTC
     */
    public function setLastupdate( $utcLastUpdate )
    {
        $this->_record['lastupdate'] = (int) $utcLastUpdate;
    }


    /**
     * Sets the language.
     *
     * @param string $lang Language code or locale all text data belongs to in
     * description or 'name' value
     */
    public function setLanguage( $lang )
    {
        $this->_record['language'] = (string) $lang;
    }


    /**
     * Sets the location informations.
     *
     * @param array $location Properties for the location:
     * - 'id' string Value which belongs to the publisher. The unique id for
     * this location at their end
     * - 'name' string Name of the location
     * - 'country' string Full name of the country
     * - 'countryCode' string Country code by ISO 3166. E.g.: DE, US, FR, AT,
     * CH, RU, UA
     * - 'sunrise' int Unix timestamp of the timezone this location belong to
     * - 'sunset' int Unix timestamp of the timezone this location belong to
     * - 'latitude' float Latitude value of this location
     * - 'longitude' float Longitude value of this location
     * - 'altitude' float Altitude value of this location
     * - 'tz_offset' string Offset of the timezone e.g.: -0700, +0100
     */
    public function setLocation( array $location = array() )
    {
        if ( isset( $location['id'] ) ) {
            $this->_record['location']['id'] = (string) $location['id'];
        }
        if ( isset( $location['name'] ) ) {
            $this->_record['location']['name'] = (string) $location['name'];
        }
        if ( isset( $location['country'] ) ) {
            $this->_record['location']['country'] = (string) $location['country'];
        }
        if ( isset( $location['countryCode'] ) ) {
            $this->_record['location']['countryCode'] = (string) $location['countryCode'];
        }
        if ( isset( $location['sunrise'] ) ) {
            $this->_record['location']['sunrise'] = (int) $location['sunrise'];
        }
        if ( isset( $location['sunset'] ) ) {
            $this->_record['location']['sunset'] = (int) $location['sunset'];
        }
        if ( isset( $location['latitude'] ) ) {
            $this->_record['location']['latitude'] = (float) $location['latitude'];
        }
        if ( isset( $location['longitude'] ) ) {
            $this->_record['location']['longitude'] = (float) $location['longitude'];
        }
        if ( ( isset( $location['altitude'] ) ) ) {
            $this->_record['location']['altitude'] = (float) $location['altitude'];
        }
        if ( ( isset( $location['tz_offset'] ) ) ) {
            $this->_record['location']['tz_offset'] = (string) $location['tz_offset'];
        }
    }


    /**
     * Sets the weather description.
     *
     * @param array $desc Properties for the the weather description:
     * - 'id' string Unique identifier
     * - 'key' string Name for the weather description as key, nativ, internal
     * name. E.g.: Mostly Cloudy
     * - 'name' string Translated name for the weather description key
     * - 'icon' string Code or name for an image/ icon
     */
    public function setWeatherDescription( array $desc = array() )
    {
        if ( isset( $desc['id'] ) ) {
            $item['id'] = (string) $desc['id'];
        }
        if ( isset( $desc['key'] ) ) {
            $item['key'] = (string) $desc['key'];
        }
        if ( isset( $desc['name'] ) ) {
            $item['name'] = htmlspecialchars( $desc['name'], ENT_QUOTES, 'UTF-8', false );
        }
        if ( isset( $desc['icon'] ) ) {
            $item['icon'] = (string) $desc['icon'];
        }

        if ( isset( $item ) ) {
            $this->_record['description'] = $item;
        }
    }


    /**
     * Sets the temperature.
     *
     * @see _createUnit() for 'unit' property
     *
     * @param array $temp Properties for temperature:
     * - 'value' float Value which belongs to the unit
     * - 'min' float Min value which belongs to the unit
     * - 'max' float Max value which belongs to the unit
     * - 'night' float Value which belongs to the unit
     * - 'eve' float Value which belongs to the unit for evening
     * - 'morn' float Value which belongs to the unit for moring
     * - 'unit' array containing 'key', 'name', 'code' and 'sign' keys for the
     * number of the values. This will be
     * probably in hectopascal or millibar. E.g.: 1015 hPa or 1015mbar
     */
    public function setTemperature( array $temp = array() )
    {
        if ( isset( $temp['value'] ) ) {
            $this->_record['temperature']['value'] = (float) $temp['value'];
        }
        if ( isset( $temp['min'] ) ) {
            $this->_record['temperature']['min'] = (float) $temp['min'];
        }
        if ( isset( $temp['max'] ) ) {
            $this->_record['temperature']['max'] = (float) $temp['max'];
        }
        if ( isset( $temp['night'] ) ) {
            $this->_record['temperature']['night'] = (float) $temp['night'];
        }
        if ( isset( $temp['eve'] ) ) {
            $this->_record['temperature']['eve'] = (float) $temp['eve'];
        }
        if ( isset( $temp['morn'] ) ) {
            $this->_record['temperature']['morn'] = (float) $temp['morn'];
        }
        if ( isset( $temp['unit'] ) ) {
            if ( ( $item = $this->_createUnit( 'Temperature', $temp['unit'] ) ) ) {
                $this->_record['temperature']['unit'] = $item;
            }
        }
    }


    /**
     * Sets the atmosphere pressure.
     *
     * @see _createUnit() for 'unit' properties
     *
     * @param array $press Properties for pressure:
     * - 'value' float value which belongs to the unit
     * - 'sea' float value which belongs to the unit on sea level
     * - 'ground' float value which belongs to the unit on ground level
     * - 'unit' array containing 'key', 'name', 'code' and 'sign' keys for the
     * number of the value. This will be
     * probably hectopascal or millibar. E.g.: 1015 hPa or 1015mbar
     * - 'trend' int Trend of the barometric pressure: steady (0), rising (1)
     * or falling (-1). (integer: -1, 0, 1)
     */
    public function setPressure( array $press = array() )
    {
        if ( isset( $press['value'] ) ) {
            $this->_record['pressure']['value'] = (float) $press['value'];
        }
        if ( isset( $press['sea'] ) ) {
            $this->_record['pressure']['sea'] = (float) $press['sea'];
        }
        if ( isset( $press['ground'] ) ) {
            $this->_record['pressure']['ground'] = (float) $press['ground'];
        }
        if ( isset( $press['unit'] ) ) {
            if ( ( $item = $this->_createUnit( 'Default', $press['unit'] ) ) ) {
                $this->_record['pressure']['unit'] = $item;
            }
        }
        if ( isset( $press['trend'] ) ) {
            $this->_record['pressure']['trend'] = (int) $press['trend'];
        }
    }


    /**
     * Sets the humidity.
     *
     * @see _createUnit() for 'unit' properties
     *
     * @param array $humidity Properties for
     * - 'value' float value which belongs to the unit
     * - 'unit' array containing 'key', 'name', 'code' and 'sign' keys for the
     * number of the value. This will be probably in percent. E.g.: 85%
     */
    public function setHumidity( array $humidity = array() )
    {
        if ( isset( $humidity['value'] ) ) {
            $this->_record['humidity']['value'] = (float) $humidity['value'];
        }
        if ( isset( $humidity['unit'] ) ) {
            if ( ( $item = $this->_createUnit( 'Default', $humidity['unit'] ) ) ) {
                $this->_record['humidity']['unit'] = $item;
            }
        }
    }


    /**
     * Sets the visibility e.g. on smok, fog. How far can you look.
     *
     * @see _createUnit() for 'unit' properties
     *
     * @param array $visibility Properties for
     * - 'value' float value which belongs to the unit
     * - 'unit' array containing 'key', 'name', 'code' and 'sign' keys for the
     * number of the value eg. metres, kilo metres,
     * miles
     */
    public function setVisibility( array $visibility = array() )
    {
        if ( isset( $visibility['value'] ) ) {
            $this->_record['visibility']['value'] = (float) $visibility['value'];
        }
        if ( isset( $visibility['unit'] ) ) {
            if ( ( $item = $this->_createUnit( 'Default', $visibility['unit'] ) ) ) {
                $this->_record['visibility']['unit'] = $item;
            }
        }
    }


    /**
     * Sets the wind properties for wind speed, wind direction or wind chill if
     * available.
     * Alias method for setWindSpeed() setWindDirection() setWindChill().
     *
     * @see setWindSpeed()
     * @see setWindDirection()
     * @see setWindChill()
     *
     * @param array $wind Properties for 'speed', 'direction' and 'chill' to be
     * set.
     */
    public function setWind( array $wind = array() )
    {
        if ( isset( $wind['speed'] ) ) {
            $this->setWindSpeed( $wind['speed'] );
        }

        if ( isset( $wind['direction'] ) ) {
            $this->setWindDirection( $wind['direction'] );
        }

        if ( isset( $wind['chill'] ) ) {
            $this->setWindChill( $wind['chill'] );
        }
    }


    /**
     * Sets the wind speed properties.
     *
     * @see _createUnit() for 'unit' properties
     *
     * @param array $speed Properties to be set are:
     * - 'value' float number for the wind speed
     * - 'min' float Min number for the wind speed
     * - 'max' float Max number for the wind speed
     * - 'gust' float Gust number for the wind speed of gusts
     * - 'unit' containing 'key', 'name', 'code' and 'sign' keys for the wind
     * speed (value,min,max,gust)
     * This is probably for 'm/s', 'km/h', 'mph' e.g 30 m/s, 9 km/h or 2 mps
     * - 'key' string Name for the wind speed as key, nativ, internal name.
     * E.g.: Gentle Breeze
     * - 'name' string Translated name for the wind speed key
     */
    public function setWindSpeed( array $speed = array() )
    {
        if ( isset( $speed['value'] ) ) {
            $this->_record['wind']['speed']['value'] = (float) $speed['value'];
        }
        if ( isset( $speed['min'] ) ) {
            $this->_record['wind']['speed']['min'] = (float) $speed['min'];
        }
        if ( isset( $speed['max'] ) ) {
            $this->_record['wind']['speed']['max'] = (float) $speed['max'];
        }
        if ( isset( $speed['gust'] ) ) {
            $this->_record['wind']['speed']['gust'] = (float) $speed['gust'];
        }
        if ( isset( $speed['unit'] ) ) {
            if ( ( $item = $this->_createUnit( 'Speed', $speed['unit'] ) ) ) {
                $this->_record['wind']['speed']['unit'] = $item;
            }
        }
        if ( isset( $speed['key'] ) ) {
            $this->_record['wind']['speed']['key'] =
                htmlspecialchars( $speed['key'], ENT_QUOTES, 'UTF-8', false );
        }
        if ( isset( $speed['name'] ) ) {
            $this->_record['wind']['speed']['name'] =
                htmlspecialchars( $speed['name'], ENT_QUOTES, 'UTF-8', false );
        }
    }


    /**
     * Sets the wind direction properties.
     *
     * @see _createUnit() for 'unit' properties
     *
     * @param array $direct Properties to be set are:
     * - 'value' float number for the temperature
     * - 'begin' float number wind direction begin
     * - 'end' float number wind direction end
     * - 'unit' containing 'key', 'name', 'code' and 'sign' keys for the number
     * of the wind directions (value,begin,end)
     * This is probably for "degrees" e.g 30°C, 60°F or 273,15 K
     * - 'key' string Name for the wind direction as key, nativ, internal name.
     * E.g.: West-southwest
     * - 'name' string Translated name for the wind direction key
     * - 'code' string code for the wind direction e.g.: 'WSW' for
     * West-southwest
     */
    public function setWindDirection( array $direct = array() )
    {
        if ( isset( $direct['value'] ) ) {
            $this->_record['wind']['direction']['value'] = (float) $direct['value'];
        }
        if ( isset( $direct['begin'] ) ) {
            $this->_record['wind']['direction']['begin'] = (float) $direct['begin'];
        }
        if ( isset( $direct['end'] ) ) {
            $this->_record['wind']['direction']['end'] = (float) $direct['end'];
        }
        if ( isset( $direct['unit'] ) ) {
            if ( ( $item = $this->_createUnit( 'Direction', $direct['unit'] ) ) ) {
                $this->_record['wind']['direction']['unit'] = $item;
            }
        }
        if ( isset( $direct['key'] ) ) {
            $this->_record['wind']['direction']['key'] =
                htmlspecialchars( $direct['key'], ENT_QUOTES, 'UTF-8', false );
        }
        if ( isset( $direct['name'] ) ) {
            $this->_record['wind']['direction']['name'] =
                htmlspecialchars( $direct['name'], ENT_QUOTES, 'UTF-8', false );
        }
        if ( isset( $direct['code'] ) ) {
            $this->_record['wind']['direction']['code'] = (string) $direct['code'];
        }
    }


    /**
     * Sets the wind chill: Temperature by wind speed or "how do you feed the
     * temperature"
     *
     * @see _createUnit() for 'unit' properties
     *
     * @param array $chill Properties to be set are:
     * - 'value' float number for the temperature
     * - 'unit' containing 'key', 'name', 'code' and 'sign' keys for the number
     * of the temperature (value)
     */
    public function setWindChill( array $chill = array() )
    {
        if ( isset( $chill['value'] ) ) {
            $this->_record['wind']['chill']['value'] = (float) $chill['value'];
        }

        if ( isset( $chill['unit'] ) ) {
            $item = $this->_createUnit( 'Temperature', $chill['unit'] );
            $this->_record['wind']['chill']['unit'] = $item;
        }
    }


    /**
     * Sets the value for clouds in percent.
     *
     * @see _createUnit() for 'unit' properties
     *
     * @param array $clouds Properties to be set are:
     * - 'value' float number for percent
     * - 'unit' containing 'key', 'name', 'code' and 'sign' keys for the number
     * of percent (value)
     * - 'key' string Name for the clouds situation. E.g.: overcast clouds
     */
    public function setClouds( array $clouds = array() )
    {
        if ( isset( $clouds['value'] ) ) {
            $this->_record['clouds']['value'] = (float) $clouds['value'];
        }

        if ( isset( $clouds['unit'] ) ) {
            $item = $this->_createUnit( 'Default', $clouds['unit'] );
            $this->_record['clouds']['unit'] = $item;
        }

        if ( isset( $clouds['key'] ) ) {
            $this->_record['clouds']['key'] = (string) $clouds['key'];
        }

        if ( isset( $clouds['name'] ) ) {
            $this->_record['clouds']['name'] = (string) $clouds['name'];
        }
    }


    /**
     * Sets the precipitation for rain and snow if available.
     * Alias method for setPrecipitationRain() setPrecipitationSnow().
     *
     * @see setPrecipitationRain()
     * @see setPrecipitationSnow()
     *
     * @param array $cond Properties for 'rain' and 'snow' to be set
     */
    public function setPrecipitation( array $cond = array() )
    {
        if ( isset( $cond['rain'] ) ) {
            $this->setPrecipitationRain( $cond['rain'] );
        }

        if ( isset( $cond['snow'] ) ) {
            $this->setPrecipitationSnow( $cond['snow'] );
        }
    }


    /**
     * Sets the properties for rain precipitation.
     *
     * @see _createUnit() for 'unit' properties
     *
     * @param array $rain Properties to be set are:
     * - 'value' number for the quantity of the rain,
     * - 'unit' containing 'name', 'code' and 'sign' keys for the number of the
     * rain (value),
     * - 'interval' duration for this kind of forecast / value
     */
    public function setPrecipitationRain( array $rain = array() )
    {
        if ( isset( $rain['value'] ) ) {
            $this->_record['precipitation']['rain']['value'] = (string) $rain['value'];
        }
        if ( isset( $rain['unit'] ) ) {
            if ( ( $item = $this->_createUnit( 'Default', $rain['unit'] ) ) ) {
                $this->_record['precipitation']['rain']['unit'] = $item;
            }
        }
        if ( isset( $rain['interval'] ) ) {
            $this->_record['precipitation']['rain']['interval'] =
                (string) $rain['interval'];
        }
    }


    /**
     * Sets the properties for snow precipitation.
     *
     * @see _createUnit() for 'unit' properties
     *
     * @param array $rain Properties to be set are:
     * - 'value' number for the quantity of the snow,
     * - 'unit' containing 'name', 'code' and 'sign' keys for the number of the
     * snow (value),
     * - 'interval' duration for this kind of forecast / value
     */
    public function setPrecipitationSnow( array $snow = array() )
    {
        if ( isset( $snow['value'] ) ) {
            $this->_record['precipitation']['snow']['value'] =
                (string) $snow['value'];
        }

        if ( isset( $snow['unit'] ) ) {
            $item = $this->_createUnit( 'Default', $snow['unit'] );
            $this->_record['precipitation']['snow']['unit'] = $item;
        }

        if ( isset( $snow['interval'] ) ) {
            $this->_record['precipitation']['snow']['interval'] =
                (string) $snow['interval'];
        }
    }


    /**
     * Returns all data in a homogeneous structure as array.
     *
     * @see $_itemDefaults for property list and descriptions
     *
     * @return array Returns the record
     */
    public function toArray()
    {
        $array = $this->_record;
        if ( isset( $this->_record['temperature']['unit'] ) ) {
            $array['temperature']['unit'] =
                $this->_record['temperature']['unit']->toArray();
        }
        if ( isset( $this->_record['pressure']['unit'] ) ) {
            $array['pressure']['unit'] =
                $this->_record['pressure']['unit']->toArray();
        }
        if ( isset( $this->_record['humidity']['unit'] ) ) {
            $array['humidity']['unit'] =
                $this->_record['humidity']['unit']->toArray();
        }
        if ( isset( $this->_record['visibility']['unit'] ) ) {
            $array['visibility']['unit'] =
                $this->_record['visibility']['unit']->toArray();
        }
        if ( isset( $this->_record['wind']['speed']['unit'] ) ) {
            $array['wind']['speed']['unit'] =
                $this->_record['wind']['speed']['unit']->toArray();
        }
        if ( isset( $this->_record['wind']['direction']['unit'] ) ) {
            $array['wind']['direction']['unit'] =
                $this->_record['wind']['direction']['unit']->toArray();
        }
        if ( isset( $this->_record['wind']['chill']['unit'] ) ) {
            $array['wind']['chill']['unit'] =
                $this->_record['wind']['chill']['unit']->toArray();
        }
        if ( isset( $this->_record['clouds']['unit'] ) ) {
            $array['clouds']['unit'] =
                $this->_record['clouds']['unit']->toArray();
        }
        if ( isset( $this->_record['precipitation']['snow']['unit'] ) ) {
            $array['precipitation']['snow']['unit'] =
                $this->_record['precipitation']['snow']['unit']->toArray();
        }
        if ( isset( $this->_record['precipitation']['rain']['unit'] ) ) {
            $array['precipitation']['rain']['unit'] =
                $this->_record['precipitation']['rain']['unit']->toArray();
        }

        return $array;
    }


    /**
     * Returns array containing the unit properties key, name, sign and code.
     *
     * @param string Type of the Unit e.g. 'temperature', 'speed' or 'Default'
     * @param array $input Unit properties for a value. Allowed array keys:
     * - 'key' string Required if not 'Default' Internal name and key
     * - 'name' string Translated key name
     * - 'sign' string A sign or code for a sign
     * - 'code' string Code, short univeral key
     *
     * @return Mumsys_Weather_Item_Unit_Interface
     */
    private function _createUnit( $type = 'Default', array $input = array() )
    {
        return Mumsys_Weather_Item_Unit_Factory::createItem( $type, $input );
    }


    /**
     * Returns the hole defaults properties from given array.
     *
     * @param array $params Properties config as array values to initialize as key.
     * @return array Returns list of key/value pairs where the value ist set to be NULL
     */
    private function _getItemDefaults( array $params = array() )
    {
        $item = array();
        foreach ( $params as $key => $value ) {
            if ( is_array( $value ) ) {
                $x = $this->_getItemDefaults( $value );
                if ( $x ) {
                    $item[$key] = $x;
                }
            } else {
                if ( $value == 'unit' ) {
                    $item['unit'] = $this->_createUnit();
                } else {
                    $item[$value] = null;
                }
            }
        }

        return $item;
    }

}
