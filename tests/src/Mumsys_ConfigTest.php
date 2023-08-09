<?php


/**
 * Test class for Mumsys_Config.
 */
class Mumsys_ConfigTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Config
     */
    private $_object;

    /**
     * @var array
     */
    private $_config;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_config = MumsysTestHelper::getConfigs();
        $this->_object = new Mumsys_Config( $this->_config );
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        unset( $this->_object );
    }


    public function test__construct()
    {
        $expected = MumsysTestHelper::getConfigs();
        $testvalues = $this->_object->getAll();
        $this->assertingEquals( $expected, $testvalues );
    }


    public function testGet()
    {
        $key = 'debug';
        $expected1 = true;
        $testvalue1 = $this->_object->get( $key, 123 );
        $expected2 = 123;
        $testvalue2 = $this->_object->get( 'notExists', 123 );

        $this->assertingEquals( $expected1, $testvalue1 );
        $this->assertingEquals( $expected2, 123 );
    }


    public function testGetAll()
    {
        $expected = $this->_object->getAll();
        $testvalues = MumsysTestHelper::getConfigs();
        $this->assertingEquals( $expected, $testvalues );
        $this->assertingTrue( is_array( $expected ) );
    }


    public function testRegister()
    {
        // simple
        $this->_object->register( 'testkey', 'testvalue' );
        $expected1 = 'testvalue';
        $testvalue1 = $this->_object->get( 'testkey', false );

        $this->assertingEquals( $expected1, $testvalue1 );

        $this->expectingExceptionMessageRegex( '/(Config key "testkey" already exists)/i' );
        $this->expectingException( 'Mumsys_Config_Exception' );
        $this->_object->register( 'testkey', new stdClass() );
    }


    public function testReplace()
    {
        $this->_object->register( 'testkey2', 'testvalue2' );
        $this->_object->replace( 'testkey2', 'testvalue3' );

        $this->assertingEquals( 'testvalue3', $this->_object->get( 'testkey2' ) );
    }


    public function testLoad()
    {
        $this->expectingExceptionMessageRegex( '/(Not implemented yet)/i' );
        $this->expectingException( 'Mumsys_Config_Exception' );
        $this->_object->load( 'mumsys2' );
    }

}
