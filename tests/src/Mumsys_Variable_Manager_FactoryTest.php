<?php


/**
 * Mumsys_Variable_Manager_Factory Test
 */
class Mumsys_Variable_Manager_FactoryTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * Version ID of Mumsys_Variable_Manager_Factory
     * @var string
     */
    private $_version;


    protected function setUp(): void
    {
        $this->_version = '1.1.3';
    }


    protected function tearDown(): void
    {

    }


    /**
     * @covers Mumsys_Variable_Manager_Factory::createManager
     */
    public function testCreateManager()
    {
        $object = Mumsys_Variable_Manager_Factory::createManager( 'Default' );
        $this->assertingInstanceOf( 'Mumsys_Variable_Manager_Interface', $object );
    }


    /**
     * @covers Mumsys_Variable_Manager_Factory::createManager
     */
    public function testCreateManagerException1()
    {
        $this->expectingExceptionMessageRegex( '/(Invalid manager name: "1 - \$5DefaultExit")/i' );
        $this->expectingException( 'Mumsys_Variable_Manager_Exception' );
        Mumsys_Variable_Manager_Factory::createManager( '1 - $5DefaultExit' );
    }


    /**
     * @covers Mumsys_Variable_Manager_Factory::createManager
     */
    public function testCreateManagerException2()
    {
        $this->expectingExceptionMessageRegex(
            '/(Initialisation of "Mumsys_Variable_Manager_Xxx" failed. Not found\/ exists)/i'
        );
        $this->expectingException( 'Mumsys_Variable_Manager_Exception' );
        Mumsys_Variable_Manager_Factory::createManager( 'xxx' );
    }


    /**
     * Version check
     */
    public function testCheckVersion()
    {
        $message = 'On error a new Version exists. You should have a look at '
            . 'the code coverage to verify all code was tested and not only '
            . 'the tests!';
        $this->assertingEquals( $this->_version, Mumsys_Variable_Manager_Factory::VERSION, $message );
    }

}
