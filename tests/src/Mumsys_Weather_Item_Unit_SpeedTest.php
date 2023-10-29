<?php

/**
 * Mumsys_Weather_Item_Unit_SpeedTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2018 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 * @verion      1.0.0
 * Created: 2013, renew 2018
 */


/**
 * Mumsys_Weather_Item_Unit_Speed Tests
 * Generated on 2018-01-21 at 20:54:06.
 */
class Mumsys_Weather_Item_Unit_SpeedTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Weather_Item_Unit_Speed
     */
    private $_object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $options = array('code' => 'm/s');
        $this->_object = new Mumsys_Weather_Item_Unit_Speed( $options );
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
     * @covers Mumsys_Weather_Item_Unit_Speed::__construct
     * @covers Mumsys_Weather_Item_Unit_Speed::_initInputDefaults
     */
    public function testConstruct()
    {
        $options = array('m/s', 'mps', 'mph', 'km/h', 'kn', 'nmiph', 'bf');
        foreach ( $options as $code ) {
            $this->_object->__construct( array('code' => $code) );

            $this->assertingEquals( $code, $this->_object->getCode() );
        }

        $options = array(
            'key' => 'key',
            'label' => 'internal label',
            'sign' => 'sign',
            'code' => 'm/s'
        );
        $this->_object->__construct( $options );

        $this->assertingEquals( $options['key'], $this->_object->getKey() );
        $this->assertingEquals( $options['label'], $this->_object->getLabel() );
        $this->assertingEquals( $options['sign'], $this->_object->getSign() );
        $this->assertingEquals( $options['code'], $this->_object->getCode() );

        $this->expectingException( 'Mumsys_Weather_Exception' );
        $mesg = 'Invalid "code" to get a speed unit item: "failure"';
        $this->expectingExceptionMessage( $mesg );
        $options['code'] = 'failure';
        $this->_object->__construct( $options );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Speed::__construct
     */
    public function testConstructException()
    {
        $this->expectingException( 'Mumsys_Weather_Exception' );
        $this->expectingExceptionMessage( '"Code" must be set to initialise the object' );
        $this->_object->__construct( array() );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Speed::convert
     */
    public function testConvert()
    {
        $testsTo = array(
            'm/s' => array(
                'm/s' => 1,
                'mps' => Mumsys_Weather_Item_Unit_Speed::MS_TO_MPS,
                'mph' => Mumsys_Weather_Item_Unit_Speed::MS_TO_MPH,
                'km/h' => Mumsys_Weather_Item_Unit_Speed::MS_TO_KMPH,
                'kn' => Mumsys_Weather_Item_Unit_Speed::MS_TO_KNOTS,
                'nmiph' => Mumsys_Weather_Item_Unit_Speed::MS_TO_NMIPH,
                'bf' => Mumsys_Weather_Item_Unit_Speed::MS_TO_BF,
            ),
            'km/h' => array(
                'm/s' => Mumsys_Weather_Item_Unit_Speed::KMPH_TO_MS,
                'mph' => Mumsys_Weather_Item_Unit_Speed::KMPH_TO_MPH,
            ),
            'mph' => array(
                'm/s' => Mumsys_Weather_Item_Unit_Speed::MPH_TO_MS,
                'mps' => Mumsys_Weather_Item_Unit_Speed::MPH_TO_MPS,
                'km/h' => Mumsys_Weather_Item_Unit_Speed::MPH_TO_KMPH,
                'kn' => Mumsys_Weather_Item_Unit_Speed::MPH_TO_KNOTS,
                'nmiph' => Mumsys_Weather_Item_Unit_Speed::MPH_TO_NMIPH,
                'bf' => Mumsys_Weather_Item_Unit_Speed::MPH_TO_BF
            )
        );

        foreach ( $testsTo as $codeFrom => $testList ) {
            $this->_object->__construct( array('code' => $codeFrom) );

            foreach ( $testList as $codeTo => $expected ) {
                $mesg = 'codeFrom: "' . $codeFrom . '", codeTo: "' . $codeTo . '" failed';
                if ( $codeTo ==='bf' ) {
                    $actual = $this->_object->convert( 1, $codeTo );
                    $this->assertingEquals( (int)$expected, $actual, $mesg );
                } else {
                    $actual = $this->_object->convert( 1, $codeTo );
                    $this->assertingEquals( $expected, $actual, $mesg );
                }
            }
        }

        $this->expectingException( 'Mumsys_Weather_Item_Unit_Exception' );
        $mesg = 'Speed conversion not implemented yet for "mph" to "xyz"';
        $this->expectingExceptionMessage( $mesg );
        $this->_object->convert( 0, 'xyz' );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Speed::toArray
     * @covers Mumsys_Weather_Item_Unit_Speed::toRawArray
     */
    public function testToArray()
    {
        $props = array(
            'code' => 'mph',
            'label' => 'test key &"',
            'sign' => 'test key &"',
            'key' => 'test key &"',
        );

        $object = new Mumsys_Weather_Item_Unit_Speed( $props );

        $actual1 = $object->toRawArray();
        $expected1 = $props;

        $actual2 = $object->toArray();
        $expected2 = array(
            'weather.item.unit.speed.code' => 'mph',
            'weather.item.unit.speed.label' => 'test key &"',
            'weather.item.unit.speed.sign' => 'test key &"',
            'weather.item.unit.speed.key' => 'test key &"',
        );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
    }

}
