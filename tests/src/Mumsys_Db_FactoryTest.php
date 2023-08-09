<?php

/**
 * Mumsys_Db_Factory Test
 */
class Mumsys_Db_FactoryTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Db_Factory
     */
    private $_object;

    /**
     * @var Mumsys_Context
     */
    private $_context;

    /**
     * @var array
     */
    private $_configs;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_context = new Mumsys_Context();
        $this->_configs = MumsysTestHelper::getConfigs();
        $this->_configs['database']['type'] = 'mysql:mysqli';

        $this->_object = new Mumsys_Db_Factory;
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
     * @covers Mumsys_Db_Factory::getInstance
     */
    public function testGetInstance()
    {
        $actual = $this->_object->getInstance( $this->_context, $this->_configs['database'] );
        $this->assertingInstanceOf( 'Mumsys_Db_Driver_Interface', $actual );
    }


    /**
     * @covers Mumsys_Db_Factory::getInstance
     */
    public function testGetInstanceException1()
    {
        $options = $this->_configs['database'];
        $options['type'] = 'xxx';

        $this->expectingExceptionMessageRegex( '/(Invalid Db driver. Can not create instance)/i' );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $actual = $this->_object->getInstance( $this->_context, $options );
    }

}
