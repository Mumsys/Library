<?php

/**
 * Mumsys_Weather_FactoryTest
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
 * Mumsys_Weather_Factory Test
 * Generated 2013-12-07 at 20:07:39.
 */
class Mumsys_Weather_FactoryTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        //$this->_object = new Mumsys_Weather_Factory;
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
     * @covers Mumsys_Weather_Factory::getInstance
     */
    public function testGetInstance()
    {
        $options = array(
            'apikey' => '123',
            'format' => 'json',
            'unit' => 'metric',
            'language' => 'en',
        );
        $actual = Mumsys_Weather_Factory::getInstance( 'auto', $options );
        $expected = 'Mumsys_Weather_OpenWeatherMap';
        $this->assertingInstanceOf( $expected, $actual );
    }

}
