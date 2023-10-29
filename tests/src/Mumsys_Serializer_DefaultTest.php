<?php declare(strict_types=1);

/**
 * Mumsys_Serializer_DefaultTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2021 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Serializer
 */

/**
 * Mumsys_Serializer_Default Tests
 * Generated on 2021-02-06.
 */
class Mumsys_Serializer_DefaultTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Serializer_Default
     */
    private $_object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_object = new Mumsys_Serializer_Default();
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
     * @covers Mumsys_Serializer_Default::serialize
     */
    public function testSerialize()
    {
        $actualA = $this->_object->serialize( 'abc' );
        $actualB = $this->_object->serialize( null );
        $actualC = $this->_object->serialize( true );
        $actualD = $this->_object->serialize( false );

        $this->assertingEquals( 's:3:"abc";', $actualA );
        $this->assertingEquals( 'N;', $actualB );
        $this->assertingEquals( 'b:1;', $actualC );
        $this->assertingEquals( 'b:0;', $actualD );
    }


    /**
     * @covers Mumsys_Serializer_Default::unserialize
     */
    public function testUnserialize()
    {
        $actualA = $this->_object->unserialize( 's:3:"abc";' );
        $actualB = $this->_object->unserialize( 'N;' );
        $actualC = $this->_object->unserialize( 'b:1;' );
        $actualD = $this->_object->unserialize( 'b:0;' );

        $this->assertingEquals( 'abc', $actualA );
        $this->assertingTrue( ( $actualB === null ) );
        $this->assertingTrue( $actualC );
        $this->assertingFalse( $actualD );
    }


    /**
     * @covers Mumsys_Serializer_Default::unserialize
     */
    public function testUnserializeExceptionObject()
    {
        $this->expectingException( 'Mumsys_Serializer_Exception' );
        $mesg = 'Serialized value must be a php serialized string. Value: "Mumsys_Serializer_Default"';
        $this->expectingExceptionMessage( $mesg );
        $this->_object->unserialize( $this->_object );
    }


    /**
     * @covers Mumsys_Serializer_Default::unserialize
     */
    public function testUnserializeExceptionBool()
    {
        $this->expectingException( 'Mumsys_Serializer_Exception' );
        $mesg = 'Serialized value must be a php serialized string. Value: "boolean"';
        $this->expectingExceptionMessage( $mesg );
        $this->_object->unserialize( true );
    }
}
