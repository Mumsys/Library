<?php


/**
 * Mumsys_Config_File Test
 */
class Mumsys_Config_FileTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Config_File
     */
    private $_object;

    /**
     * Version ID
     * @var string
     */
    private $_version = '3.0.0';

    /**
     * @var array
     */
    private $_versions;

    /**
     * @var array
     */
    private $_configs;

    /**
     * @var array
     */
    private $_paths;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_versions = array(
            'Mumsys_Config_File' => '3.0.0',
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
        );
        $this->_configs = array('testkey' => 'test value');
        $this->_paths = array(
            __DIR__ . '/', //testconfig.php
            __DIR__ . '/../config/', //credentials.php and sub paths
        );
        $this->_object = new Mumsys_Config_File( $this->_configs, $this->_paths );
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
     * For code coverage
     * @covers Mumsys_Config_File::__construct
     */
    public function test__construct()
    {
        $this->_object = new Mumsys_Config_File( $this->_configs, $this->_paths );

        $this->assertingInstanceOf( 'Mumsys_Config_File', $this->_object );
        $this->assertingInstanceOf( 'Mumsys_Config_Interface', $this->_object );
    }


    /**
     * @covers Mumsys_Config_File::get
     * @covers Mumsys_Config_File::_get
     * @covers Mumsys_Config_File::_load
     * @covers Mumsys_Config_File::_merge
     * @covers Mumsys_Config_File::_include
     */
    public function testGet()
    {
        $_obj = $this->_object;
        $actual1 = $_obj->get( 'testkey' );
        $actual2 = $_obj->get( 'credentials/database/host', false );
        $actual3 = $_obj->get( 'credentials/database/mumsys/config/set', false );
        $actual4 = $_obj->get( array('credentials', 'database', 'host'), false );
        $actual5 = $_obj->get( 'database/mumsys/config/item/search', false );
        $expected1 = 'test value';

        $oConfig = MumsysTestHelper::getContext()->getConfig();
        $expected2 = $oConfig->get( 'credentials/database/host', 0 );
        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
        $this->assertingFalse( $actual3 );
        $this->assertingEquals( $expected2, $actual4 );
        $this->assertingEquals( 'SELECT * FROM mumsys_config', $actual5 );
    }


    /**
     * @covers Mumsys_Config_File::getAll
     */
    public function testGetAll()
    {
        $this->assertingEquals( $this->_configs, $this->_object->getAll() );
    }


    /**
     * @covers Mumsys_Config_File::replace
     * @covers Mumsys_Config_File::_replace
     */
    public function testReplace()
    {
        $this->_object->replace( 'testkey', 'value test' );
        $actual = $this->_object->get( 'testkey' );

        $this->_object->replace( 'new key', 'new value' );
        $actual2 = $this->_object->get( 'new key' );

        // with path
        $expected3 = array('a' => 'b', 'c' => 'd');
        $this->_object->replace( 'tests/somevalues', $expected3 );
        $actual3 = $this->_object->get( 'tests/somevalues' );
        $this->_object->replace( 'tests', array() );
        $actual4 = $this->_object->get( 'tests' );

        $this->assertingEquals( 'value test', $actual );
        $this->assertingEquals( 'new value', $actual2 );
        $this->assertingEquals( $expected3, $actual3 );
        $this->assertingEquals( array(), $actual4 );
    }


    /**
     * @covers Mumsys_Config_File::register
     */
    public function testRegister()
    {
        $this->_object->register( 'testkey2', 'test' );
        $actual = $this->_object->get( 'testkey2', false );

        // with path
        $expected3 = array('a' => 'b', 'c' => 'd');
        $this->_object->register( 'tests/somevalues', $expected3 );
        $actual3 = $this->_object->get( 'tests/somevalues' );

        $this->assertingEquals( 'test', $actual );
        $this->assertingEquals( $expected3, $actual3 );

        $this->expectingExceptionMessageRegex( '/(Config key "tests\/somevalues" already exists)/i' );
        $this->expectingException( 'Mumsys_Config_Exception' );
        $this->_object->register( 'tests/somevalues', array() );
    }


    /**
     * @covers Mumsys_Config_File::addPath
     */
    public function testAddpath()
    {
        $this->_object->addPath( __DIR__ . '/../config' );

        $this->expectingExceptionMessageRegex( '/(Path not found: "(.*)")/i' );
        $this->expectingException( 'Mumsys_Config_Exception' );
        $this->_object->addPath( __DIR__ . '/config' );
    }


    /**
     * @covers Mumsys_Config_File::load
     */
    public function testLoad()
    {
        $this->expectingExceptionMessageRegex( '/(Not implemented yet)/i' );
        $this->expectingException( 'Mumsys_Config_Exception' );
        $this->_object->load();
    }


    /**
     * @covers Mumsys_Abstract::getVersions
     */
    public function testVersions()
    {
        $this->assertingEquals( $this->_version, Mumsys_Config_File::VERSION );
        $this->checkVersionList( $this->_object->getVersions(), $this->_versions );
    }

}
