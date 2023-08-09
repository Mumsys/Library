<?php

/**
 * Mumsys_Weather_Item_Unit_TemperatureTest
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
 * Mumsys_Weather_Item_Unit_Temperature Tests
 * Generated on 2018-01-21 at 20:54:06.
 */
class Mumsys_Weather_Item_Unit_TemperatureTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Weather_Item_Unit_Temperature
     */
    private $_object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $options = array('key' => 'kelvin');
        $this->_object = new Mumsys_Weather_Item_Unit_Temperature( $options );
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
     * @covers Mumsys_Weather_Item_Unit_Temperature::__construct
     * @covers Mumsys_Weather_Item_Unit_Temperature::_initInputDefaults
     */
    public function testConstruct()
    {
        $options = array('key' => 'metric');
        $this->_object->__construct( $options );

        $options = array('key' => 'imperial');
        $this->_object->__construct( $options );

        $options = array(
            'key' => 'kelvin',
            'label' => 'internal label',
            'sign' => 'sign',
            'code' => 'code'
        );
        $this->_object->__construct( $options );

        $this->assertingEquals( $options['key'], $this->_object->getKey() );
        $this->assertingEquals( $options['label'], $this->_object->getLabel() );
        $this->assertingEquals( $options['sign'], $this->_object->getSign() );
        $this->assertingEquals( $options['code'], $this->_object->getCode() );

        $this->expectingException( 'Mumsys_Weather_Item_Unit_Exception' );
        $mesg = 'Invalid "key" to get a temperature unit item: "fail"';
        $this->expectingExceptionMessage( $mesg );
        $options['key'] = 'fail';
        $this->_object->__construct( $options );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Temperature::__construct
     */
    public function testConstructException()
    {
        $this->expectingException( 'Mumsys_Weather_Item_Unit_Exception' );
        $this->expectingExceptionMessage( '"Key" must be set to initialise the object' );
        $this->_object->__construct( array() );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Temperature::convert
     */
    public function testConvert()
    {
        $actual1 = $this->_object->convert( 0, 'celsius' );
        $expected1 = -273.15;

        $actual2 = $this->_object->convert( 0, 'kelvin' );
        $expected2 = 0;

        $actual3 = $this->_object->convert( 0, 'fahrenheit' );
        $expected3 = -255.3722222222222;

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );

        $this->expectingException( 'Mumsys_Weather_Item_Unit_Exception' );
        $mesg = 'Invalid unit to convert temperature from: "kelvin" to: "xyz"';
        $this->expectingExceptionMessage( $mesg );
        $this->_object->convert( 0, 'xyz' );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Temperature::toArray
     * @covers Mumsys_Weather_Item_Unit_Temperature::toRawArray
     */
    public function testToArray()
    {
        $props = array(
            'key' => 'celsius',
            'label' => 'test key &"',
            'sign' => 'test key &"',
            'code' => 'test key &"',
        );

        $object = new Mumsys_Weather_Item_Unit_Temperature( $props );

        $actual1 = $object->toRawArray();
        $expected1 = $props;

        $actual2 = $object->toArray();
        $expected2 = array(
            'weather.item.unit.temperature.key' => 'celsius',
            'weather.item.unit.temperature.label' => 'test key &"',
            'weather.item.unit.temperature.sign' => 'test key &"',
            'weather.item.unit.temperature.code' => 'test key &"',
        );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
    }

}
