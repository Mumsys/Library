<?php

/**
 * Mumsys_Weather_Item_DefaultTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2013 by Florian Blasel
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 * @verion      1.0.0
 * Created: 2013, renew 2018
 */


/**
 * Mumsys_Weather_Item_Default Test
 * Generated on 2013-12-06 at 17:40:19.
 */
class Mumsys_Weather_Item_DefaultTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Weather_Item_Default
     */
    private $_object;

    /**
     * Complete list of item properties
     * @var array
     */
    private $_prps;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {

        $this->_prps = array(
            'publisher' => array(
                'id' => 123,
                'name' => 'publisher name',
            ),
            'lastupdate' => 12345,
            'language' => 'de',
            'location' => array(
                'id' => 88,
                'name' => 'Hamburg',
                'country' => 'Germany',
                'countryCode' => 'DE',
                'sunrise' => 1,
                'sunset' => 100,
                'latitude' => 53.55,
                'longitude' => 10.00,
                'altitude' => 0,
                'tz_offset' => '+02:00',
            ),
            'description' => array(
                'id' => 'ISHd4',
                'key' => 'weather desc',
                'name' => 'wetter beschr.',
                'icon' => 'img.jpg',
            ),
            'temperature' => array(
                'value' => 30.0,
                'min' => 15.0,
                'max' => 45.0,
                'night' => 12.0,
                'eve' => 15.0,
                'morn' => 10.0,
                'unit' => array(
                    'key' => 'celsius',
                    'label' => 'Grad Celsius',
                    'sign' => '°C',
                    'code' => 'no code',
                ),
            ),
            'pressure' => array(
                'value' => 1006.0,
                'sea' => 996.0,
                'ground' => 1006.6,
                'unit' => array(
                    'key' => 'hectopascal',
                    'label' => 'Hektopascal',
                    'sign' => 'no sign',
                    'code' => 'hPa',
                ),
                'trend' => -1,
            ),
            'humidity' => array(
                'value' => 75,
                'unit' => array(
                    'key' => 'percent',
                    'label' => 'Prozent',
                    'sign' => '%',
                    'code' => 'no code',
                ),
            ),
            'visibility' => array(
                'value' => 0.8,
                'unit' => array(
                    'key' => 'kilometer',
                    'label' => 'Kilometer',
                    'sign' => 'no sign',
                    'code' => 'km',
                ),
            ),
            'wind' => array(
                'speed' => array(
                    'value' => 5,
                    'min' => 4,
                    'max' => 6,
                    'unit' => array(
                        'key' => 'meter per second',
                        'label' => 'Meter pro Sekunde',
                        'sign' => 'no sign',
                        'code' => 'm/s',
                    ),
                    'key' => 'strong breese',
                    'name' => 'Steife briese',
                    'gust' => 7,
                ),
                'direction' => array(
                    'value' => 300,
                    'begin' => 290,
                    'end' => 310,
                    'unit' => array(
                        'key' => 'degreese',
                        'label' => 'Grad',
                        'sign' => '°',
                        'code' => 'no code',
                    ),
                    'key' => 'Wind Name- description',
                    'name' => 'Wind- Name/ Bezeichnung',
                    'code' => 'WWS',
                ),
                'chill' => array(
                    'value' => 10,
                    'unit' => array(
                        'key' => 'celsius',
                        'label' => 'Grad celsius',
                        'sign' => '° C',
                        'code' => 'no code',
                    ),
                ),
            ),
            'clouds' => array(
                'value' => 85,
                'unit' => array(
                    'key' => 'percent',
                    'label' => 'Prozent',
                    'sign' => '%',
                    'code' => 'no code',
                ),
                'key' => 'clouds desc',
                'name' => 'Wolken beschr.',
            ),
            'precipitation' => array(
                'rain' => array(
                    'value' => 3,
                    'unit' => array(
                        'key' => 'millimetres',
                        'label' => 'Millimeter',
                        'sign' => 'no sign',
                        'code' => 'mm',
                    ),
                    'interval' => '3h',
                ),
                'snow' => array(
                    'value' => 0.5,
                    'unit' => array(
                        'key' => 'millimetres',
                        'label' => 'Millimeter',
                        'sign' => 'no sign',
                        'code' => 'mm',
                    ),
                    'interval' => '1h',
                ),
            ),
        );

        $this->_object = new Mumsys_Weather_Item_Default();
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        unset( $this->_object );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::__construct
     * @covers Mumsys_Weather_Item_Default::_getItemDefaults
     */
    public function testConstruct()
    {
        $this->_object->__construct( $this->_prps, true );
        $actual = $this->_object->toArray();
        $this->assertingTrue( ( count( $actual ) == 12 ) );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::get
     */
    public function testget()
    {
        $this->_object->__construct( $this->_prps, true );

        $actual = $this->_object->get( 'publisher', false );
        $expected = array(
            'id' => '123',
            'name' => 'publisher name'
        );

        $this->assertingEquals( $expected, $actual );
        $this->assertingFalse( $this->_object->get( 'notexists', false ) );
        $this->assertingTrue( ( is_array( ( $x = $this->_object->get() ) ) && $x ) );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setPublisher
     */
    public function testSetPublisher()
    {
        $expected = array('id' => '123', 'name' => 'Publisher name');
        $this->_object->setPublisher( $expected );
        $actual = $this->_object->toArray();
        $this->assertingEquals( $expected, $actual['publisher'] );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setLastupdate
     */
    public function testSetLastupdate()
    {
        $this->_object->setLastupdate( $this->_prps['lastupdate'] );
        $actual = $this->_object->toArray();
        $this->assertingEquals( $this->_prps['lastupdate'], $actual['lastupdate'] );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setLanguage
     */
    public function testSetLanguage()
    {
        $this->_object->setLanguage( $this->_prps['language'] );
        $actual = $this->_object->toArray();
        $this->assertingEquals( $this->_prps['language'], $actual['language'] );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setLocation
     */
    public function testSetLocation()
    {
        $this->_object->setLocation( $this->_prps['location'] );
        $actual = $this->_object->toArray();
        $this->assertingEquals( $this->_prps['location'], $actual['location'] );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setWeatherDescription
     */
    public function testSetWeatherDescription()
    {
        $this->_object->setWeatherDescription( $this->_prps['description'] );
        $actual = $this->_object->toArray();
        $this->assertingEquals( $this->_prps['description'], $actual['description'] );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setTemperature
     * @covers Mumsys_Weather_Item_Default::_createUnit
     */
    public function testSetTemperature()
    {
        $unit = $this->_prps['temperature']['unit'];
        $oUnit = new Mumsys_Weather_Item_Unit_Temperature( $unit );

        $this->_object->setTemperature( $this->_prps['temperature'] );
        $actual = $this->_object->toArray();
        $this->_prps['temperature']['unit'] = $oUnit->toArray();
        $this->assertingEquals( $this->_prps['temperature'], $actual['temperature'] );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setPressure
     * @covers Mumsys_Weather_Item_Default::_createUnit
     */
    public function testSetPressure()
    {
        $unit = $this->_prps['pressure']['unit'];
        $oUnit = new Mumsys_Weather_Item_Unit_Default( $unit );

        $this->_object->setPressure( $this->_prps['pressure'] );
        $actual = $this->_object->toArray();
        $this->_prps['pressure']['unit'] = $oUnit->toArray();
        $this->assertingEquals( $this->_prps['pressure'], $actual['pressure'] );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setHumidity
     * @covers Mumsys_Weather_Item_Default::_createUnit
     */
    public function testSetHumidity()
    {
        $unit = $this->_prps['humidity']['unit'];
        $oUnit = new Mumsys_Weather_Item_Unit_Default( $unit );

        $this->_object->setHumidity( $this->_prps['humidity'] );
        $actual = $this->_object->toArray();
        $this->_prps['humidity']['unit'] = $oUnit->toArray();
        $this->assertingEquals( $this->_prps['humidity'], $actual['humidity'] );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setVisibility
     * @covers Mumsys_Weather_Item_Default::_createUnit
     */
    public function testSetVisibility()
    {
        $unit = $this->_prps['visibility']['unit'];
        $oUnit = new Mumsys_Weather_Item_Unit_Default( $unit );

        $this->_object->setVisibility( $this->_prps['visibility'] );
        $actual = $this->_object->toArray();
        $this->_prps['visibility']['unit'] = $oUnit->toArray();
        $this->assertingEquals( $this->_prps['visibility'], $actual['visibility'] );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setWind
     */
    public function testSetWind()
    {
        $unit = $this->_prps['wind']['speed']['unit'];
        $oUnitSpeed = new Mumsys_Weather_Item_Unit_Speed( $unit );
        $unitDir = $this->_prps['wind']['direction']['unit'];
        $oUnitDirection = new Mumsys_Weather_Item_Unit_Direction( $unitDir );
        $unitChill = $this->_prps['wind']['chill']['unit'];
        $oUnitChill = new Mumsys_Weather_Item_Unit_Temperature( $unitChill );

        $this->_object->setWind( $this->_prps['wind'] );
        $actual = $this->_object->toArray();
        $this->_prps['wind']['speed']['unit'] = $oUnitSpeed->toArray();
        $this->_prps['wind']['direction']['unit'] = $oUnitDirection->toArray();
        $this->_prps['wind']['chill']['unit'] = $oUnitChill->toArray();
        $this->assertingEquals( $this->_prps['wind'], $actual['wind'] );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setWindSpeed
     * @covers Mumsys_Weather_Item_Default::_createUnit
     */
    public function testSetWindSpeed()
    {
        $unit = $this->_prps['wind']['speed']['unit'];
        $oUnitSpeed = new Mumsys_Weather_Item_Unit_Speed( $unit );

        $this->_object->setWindSpeed( $this->_prps['wind']['speed'] );
        $actual = $this->_object->toArray();
        $this->_prps['wind']['speed']['unit'] = $oUnitSpeed->toArray();
        $this->assertingEquals(
            $this->_prps['wind']['speed'], $actual['wind']['speed']
        );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setWindDirection
     * @covers Mumsys_Weather_Item_Default::_createUnit
     */
    public function testSetWindDirection()
    {
        $unitDir = $this->_prps['wind']['direction']['unit'];
        $oUnitDirection = new Mumsys_Weather_Item_Unit_Direction( $unitDir );

        $this->_object->setWindDirection( $this->_prps['wind']['direction'] );
        $actual = $this->_object->toArray();
        $this->_prps['wind']['direction']['unit'] = $oUnitDirection->toArray();
        $this->assertingEquals(
            $this->_prps['wind']['direction'],
            $actual['wind']['direction']
        );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setWindChill
     */
    public function testSetWindChill()
    {
        $unitChill = $this->_prps['wind']['chill']['unit'];
        $oUnitChill = new Mumsys_Weather_Item_Unit_Temperature( $unitChill );

        $this->_object->setWindChill( $this->_prps['wind']['chill'] );
        $actual = $this->_object->toArray();
        $this->_prps['wind']['chill']['unit'] = $oUnitChill->toArray();
        $this->assertingEquals(
            $this->_prps['wind']['chill'], $actual['wind']['chill']
        );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setClouds
     */
    public function testSetClouds()
    {
        $unit = $this->_prps['clouds']['unit'];
        $oUnit = new Mumsys_Weather_Item_Unit_Default( $unit );

        $this->_object->setClouds( $this->_prps['clouds'] );
        $actual = $this->_object->toArray();
        $this->_prps['clouds']['unit'] = $oUnit->toArray();
        $this->assertingEquals( $this->_prps['clouds'], $actual['clouds'] );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setPrecipitation
     */
    public function testSetPrecipitation()
    {
        $unitRain = $this->_prps['precipitation']['rain']['unit'];
        $oUnitRain = new Mumsys_Weather_Item_Unit_Default( $unitRain );
        $unitSnow = $this->_prps['precipitation']['snow']['unit'];
        $oUnitSnow = new Mumsys_Weather_Item_Unit_Default( $unitRain );

        $this->_object->setPrecipitation( $this->_prps['precipitation'] );
        $actual = $this->_object->toArray();
        $this->_prps['precipitation']['rain']['unit'] = $oUnitRain->toArray();
        $this->_prps['precipitation']['snow']['unit'] = $oUnitSnow->toArray();
        $this->assertingEquals(
            $this->_prps['precipitation'], $actual['precipitation']
        );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setPrecipitationRain
     */
    public function testSetPrecipitationRain()
    {
        $unitRain = $this->_prps['precipitation']['rain']['unit'];
        $oUnitRain = new Mumsys_Weather_Item_Unit_Default( $unitRain );

        $this->_object->setPrecipitationRain( $this->_prps['precipitation']['rain'] );
        $actual = $this->_object->toArray();
        $this->_prps['precipitation']['rain']['unit'] = $oUnitRain->toArray();
        $this->assertingEquals(
            $this->_prps['precipitation']['rain'],
            $actual['precipitation']['rain']
        );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::setPrecipitationSnow
     */
    public function testSetPrecipitationSnow()
    {
        $unitSnow = $this->_prps['precipitation']['snow']['unit'];
        $oUnitSnow = new Mumsys_Weather_Item_Unit_Default( $unitSnow );

        $this->_object->setPrecipitationSnow( $this->_prps['precipitation']['snow'] );
        $actual = $this->_object->toArray();
        $this->_prps['precipitation']['snow']['unit'] = $oUnitSnow->toArray();
        $this->assertingEquals(
            $this->_prps['precipitation']['snow'],
            $actual['precipitation']['snow']
        );
    }


    /**
     * @covers Mumsys_Weather_Item_Default::toArray
     */
    public function testToArray()
    {
        $actual = $this->_object->toArray();
        $this->assertingEquals( array(), $actual ); // no params in construction.
    }


    /**
     * @covers Mumsys_Weather_Item_Default::_createUnit
     */
    public function test_createUnit()
    {
        $unit = $this->_prps['temperature']['unit'];
        $oUnit = new Mumsys_Weather_Item_Unit_Temperature( $unit );

        $this->_object->setTemperature( $this->_prps['temperature'] );

        $actual = $this->_object->toArray();
        $this->_prps['temperature']['unit'] = $oUnit->toArray();
        $this->assertingEquals(
            $this->_prps['temperature'], $actual['temperature']
        );
    }

}
