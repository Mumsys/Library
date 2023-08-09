<?php

/**
 * Mumsys_Weather_Item_Unit_DefaultTest
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
 * Mumsys_Weather_Item_Unit_Default Test
 * Generated on 2018-01-21 at 17:07:55.
 */
class Mumsys_Weather_Item_Unit_DefaultTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Weather_Item_Unit_Default
     */
    private $_object;

    /**
     * @var string
     */
    private $_version;

    /**
     * @var array
     */
    private $_versions;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '1.0.0';
        $this->_versions = array(
            'Mumsys_Weather_Item_Unit_Default' => $this->_version,
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
        );

        $this->_object = new Mumsys_Weather_Item_Unit_Default();
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
     * @covers Mumsys_Weather_Item_Unit_Default::getKey
     * @covers Mumsys_Weather_Item_Unit_Default::setKey
     */
    public function testGetSetKey()
    {
        $actual1 = $this->_object->getKey();
        $expected1 = null;

        $expected2 = 'k';
        $this->_object->setKey( $expected2 );
        $this->_object->setKey( $expected2 ); //4 CC
        $actual2 = $this->_object->getKey();

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Default::getLabel
     * @covers Mumsys_Weather_Item_Unit_Default::setLabel
     */
    public function testGetSetLabel()
    {
        $actual1 = $this->_object->getLabel();
        $expected1 = null;

        $expected2 = 'l';
        $this->_object->setLabel( $expected2 );
        $this->_object->setLabel( $expected2 ); //4 CC
        $actual2 = $this->_object->getLabel();

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Default::getSign
     * @covers Mumsys_Weather_Item_Unit_Default::setSign
     */
    public function testGetSetSign()
    {
        $actual1 = $this->_object->getSign();
        $expected1 = null;

        $expected2 = 's';
        $this->_object->setSign( $expected2 );
        $this->_object->setSign( $expected2 ); //4 CC
        $actual2 = $this->_object->getSign();

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Default::getCode
     * @covers Mumsys_Weather_Item_Unit_Default::setCode
     */
    public function testGetSetCode()
    {
        $actual1 = $this->_object->getCode();
        $expected1 = null;

        $expected2 = 'c';
        $this->_object->setCode( $expected2 );
        $this->_object->setCode( $expected2 ); //4 CC
        $actual2 = $this->_object->getCode();

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Default::isModified
     * @covers Mumsys_Weather_Item_Unit_Default::setModified
     */
    public function testIsSetModified()
    {
        $actual1 = $this->_object->isModified();
        $expected1 = false;

        $expected2 = true;
        $this->_object->setModified();
        $actual2 = $this->_object->isModified();

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Default::toRawArrayHtml
     */
    public function testToRawArrayHtml()
    {
        $props = array(
            'key' => 'test key &"',
            'label' => 'test key &"',
            'sign' => 'test key &"',
            'code' => 'test key &"',
        );

        $object = new Mumsys_Weather_Item_Unit_Default( $props );

        $actual = $object->toRawArrayHtml();
        $expected = array(
            'key' => 'test key &amp;&quot;',
            'label' => 'test key &amp;&quot;',
            'sign' => 'test key &amp;&quot;',
            'code' => 'test key &amp;&quot;',
        );

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Default::toArray
     * @covers Mumsys_Weather_Item_Unit_Default::toRawArray
     */
    public function testToArray()
    {
        $props = array(
            'key' => 'test key &"',
            'label' => 'test key &"',
            'sign' => 'test key &"',
            'code' => 'test key &"',
        );

        $object = new Mumsys_Weather_Item_Unit_Default( $props );

        $actual1 = $object->toRawArray();
        $expected1 = $props;

        $actual2 = $object->toArray();
        $expected2 = array(
            'weather.item.unit.default.key' => 'test key &"',
            'weather.item.unit.default.label' => 'test key &"',
            'weather.item.unit.default.sign' => 'test key &"',
            'weather.item.unit.default.code' => 'test key &"',
        );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
    }


    /**
     * @covers Mumsys_Weather_Item_Unit_Default::getVersions
     */
    public function testVersions()
    {
        $this->assertingEquals(
            $this->_version,
            Mumsys_Weather_Item_Unit_Default::VERSION
        );

        $this->checkVersionList( $this->_object->getVersions(), $this->_versions );
    }

}
