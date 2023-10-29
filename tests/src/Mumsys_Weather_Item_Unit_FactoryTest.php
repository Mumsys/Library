<?php

/**
 * Mumsys_Weather_Item_Unit_FactoryTest
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
 * Mumsys_Weather_Item_Unit_Factory Test
 * Generated on 2018-01-21 at 16:22:08.
 */
class Mumsys_Weather_Item_Unit_FactoryTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Weather_Item_Unit_Factory
     */
    private $_object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_object = new Mumsys_Weather_Item_Unit_Factory;
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
     * @covers Mumsys_Weather_Item_Unit_Factory::createItem
     */
    public function testCreateItem()
    {
        $actual = $this->_object->createItem();

        $this->assertingInstanceOf( 'Mumsys_Weather_Item_Unit_Interface', $actual );
        $this->assertingInstanceOf( 'Mumsys_Weather_Item_Unit_Default', $actual );

        $this->expectingException( 'Mumsys_Weather_Exception' );
        $mesg = 'Invalid characters in class name "Mumsys_Weather_Item_Unit_$$$"';
        $this->expectingExceptionMessage( $mesg );
        $this->_object->createItem( '$$$' );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Factory::createItem
     */
    public function testCreateItemException()
    {
        $this->expectingException( 'Mumsys_Weather_Exception' );
        $mesg = 'Class "Mumsys_Weather_Item_Unit_DriverNotIn" not found';
        $this->expectingExceptionMessage( $mesg );
        $actual = $this->_object->createItem( 'DriverNotIn' );
    }

}
