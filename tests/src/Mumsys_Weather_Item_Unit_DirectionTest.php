<?php

/**
 * Mumsys_Weather_Item_Unit_DirectionTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2018 by Florian Blasel
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 * @verion      1.0.0
 * Created: 2013, renew 2018
 */


/**
 * Mumsys_Weather_Item_Unit_Direction Tests
 * Generated on 2018-01-21 at 20:54:06.
 */
class Mumsys_Weather_Item_Unit_DirectionTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Weather_Item_Unit_Direction
     */
    private $_object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $options = array();
        $this->_object = new Mumsys_Weather_Item_Unit_Direction( $options );
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
     * @covers Mumsys_Weather_Item_Unit_Direction::__construct
     */
    public function testConstruct()
    {
        $options = array(
            'key' => 'key',
            'label' => 'some degrees',
            'sign' => 'a sign',
            'code' => 'a code'
        );
        $this->_object->__construct( $options );

        $this->assertingEquals( 'degrees', $this->_object->getKey() );
        $this->assertingEquals( $options['label'], $this->_object->getLabel() );
        $this->assertingEquals( $options['sign'], $this->_object->getSign() );
        $this->assertingEquals( $options['code'], $this->_object->getCode() );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Direction::getDirectionCode
     */
    public function testGetDirectionCode()
    {
        $n =0;
        $codes = array(
            0 => array(
                'N' => 0,
                'NO' => 45,
                'O' => 90,
                'SO' => 135,
                'S' => 180,
                'SW' => 225,
                'W' => 270,
                'NW' => 315,
                //'N' => 360,
            ),
            1 => array(
                'N' => $n,
                'NNO'   => ( ++$n * 22.5 ),
                'NO'    => ( ++$n * 22.5 ),
                'ONO'   => ( ++$n * 22.5 ),
                'O'     => ( ++$n * 22.5 ),
                'OOS'   => ( ++$n * 22.5 ),
                'SO'    => ( ++$n * 22.5 ),
                'SSO'   => ( ++$n * 22.5 ),
                'S'     => ( ++$n * 22.5 ),
                'SSW'   => ( ++$n * 22.5 ),
                'SW'    => ( ++$n * 22.5 ),
                'WSW'   => ( ++$n * 22.5 ),
                'W'     => ( ++$n * 22.5 ),
                'WWN'   => ( ++$n * 22.5 ),
                'NW'    => ( ++$n * 22.5 ),
                'NNW'   => ( ++$n * 22.5 ),
            ),
        );

        foreach ( $codes as $precision => $testList ) {
            foreach ( $testList as $expected => $degrees ) {
                $actual = $this->_object->getDirectionCode( $degrees, $precision );

                $mesg = 'Presision: ' . (string) $precision . '; Expected: '
                    . $expected . '; Value: ' . $degrees;
                $this->assertingEquals( $expected, $actual, $mesg );
            }
        }

        $this->expectingException( 'Mumsys_Weather_Item_Unit_Exception' );
        $mesg = 'Invalid value for degrees: "999"';
        $this->expectingExceptionMessage( $mesg );
        $this->_object->getDirectionCode( 999, 1 );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Direction::convert
     */
    public function testConvert()
    {
        $testList = array(
            0 => 12,
            1 => 12,
            45 => 2,
            90 => 3,
            180 => 6,
            270 => 9,
            360 => 12
        );

        foreach ( $testList as $degrees => $expected ) {
            $actual = $this->_object->convert( $degrees, 'clock' );

            $mesg = 'degrees: "' . $degrees . '", clock: "' . $expected
                . '", res: ' . $actual . ' failed';
            $this->assertingEquals( $expected, $actual, $mesg );
        }

        $this->assertingEquals( 45, $this->_object->convert( 45, 'degrees' ) );

        $this->expectingException( 'Mumsys_Weather_Item_Unit_Exception' );
        $mesg = 'Direction conversion to "xyz" not implemented';
        $this->expectingExceptionMessage( $mesg );
        $this->_object->convert( 0, 'xyz' );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Direction::convert
     */
    public function testConvertException()
    {
        $this->expectingException( 'Mumsys_Weather_Item_Unit_Exception' );
        $mesg = 'Invalid value for degrees: "999"';
        $this->expectingExceptionMessage( $mesg );
        $this->_object->convert( 999, 'clock' );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Direction::toArray
     * @covers Mumsys_Weather_Item_Unit_Direction::toRawArray
     */
    public function testToArray()
    {
        $props = array(
            'key' => 'degrees',
            'label' => 'test key &"',
            'sign' => 'test key &"',
            'code' => 'mph',
        );

        $object = new Mumsys_Weather_Item_Unit_Direction( $props );

        $actual1 = $object->toRawArray();
        $expected1 = $props;

        $actual2 = $object->toArray();
        $expected2 = array(
            'weather.item.unit.direction.key' => 'degrees',
            'weather.item.unit.direction.code' => 'mph',
            'weather.item.unit.direction.label' => 'test key &"',
            'weather.item.unit.direction.sign' => 'test key &"',
        );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
    }

}
