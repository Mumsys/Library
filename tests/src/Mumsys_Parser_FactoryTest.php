<?php declare(strict_types=1);

/**
 * Mumsys_Parser_FactoryTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2023 by Florian Blasel
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Parser
 * Created: 2023-07-31
 */


/**
 * Test class/ mock for the interface checks, 4CC
 */
class Mumsys_Parser_FactoryTestClassNoInterface
{
    const VERSION = '0.0.0';
}


/**
 * Factory tests for the parser interface
 */
class Mumsys_Parser_FactoryTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Parser_Factory
     */
    private $_object;

    /**
     * @var string
     */
    private $_version;

    /**
     * List of objects this _objects needs
     * @var array
     */
    private $_versions;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '2.0.0';
        $this->_versions = array(
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
            'Mumsys_Parser_Abstract' => '2.0.0',
        );
        $this->_object = new Mumsys_Parser_Factory;
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }


    public function test__construct()
    {
        $this->assertingInstanceOf( 'Mumsys_Parser_Factory', $this->_object );
    }


    /**
     * @covers Mumsys_Parser_Factory::getAdapter
     */
    public function testGetAdapter()
    {
        $actualA = Mumsys_Parser_Factory::getAdapter(
            'Default', $format = '%Y', array('%Y' => '(\d{4})')
        );

        $this->assertingInstanceOf( 'Mumsys_Parser_Factory', $this->_object );
        $this->assertingInstanceOf( 'Mumsys_Parser_Default', $actualA );
    }


    /**
     * @covers Mumsys_Parser_Factory::getAdapter
     */
    public function testGetAdapterInvalidAdaptername()
    {
        $this->expectingException( 'Mumsys_Parser_Exception' );
        $this->expectingExceptionMessage(
            'Invalid characters in adapter name "Mumsys_Parser_$$D"'
        );

        Mumsys_Parser_Factory::getAdapter( '$$D' );
    }


    /**
     * @covers Mumsys_Parser_Factory::getAdapter
     */
    public function testGetAdapterAdapterNotExists()
    {
        $this->expectingException( 'Mumsys_Parser_Exception' );
        $this->expectingExceptionMessage(
            'Adapter "Mumsys_Parser_LucieSpecial" not available'
        );

        Mumsys_Parser_Factory::getAdapter( 'LucieSpecial' );
    }


    /**
     * @covers Mumsys_Parser_Factory::getAdapter
     */
    public function testGetAdapterAdapterMissInterface()
    {
        $this->expectingException( 'Mumsys_Parser_Exception' );
        $this->expectingExceptionMessage(
            'Adapter "Mumsys_Parser_FactoryTestClassNoInterface" does not '
            . 'implement interface "Mumsys_Parser_Interface'
        );

        Mumsys_Parser_Factory::getAdapter( 'FactoryTestClassNoInterface' );
    }


    /**
     * Version checks
     */
    public function testVersions()
    {
        $this->assertingEquals( $this->_version, $this->_object::VERSION );
        $this->checkVersionList( $this->_object->getVersions(), $this->_versions );
    }

}
