<?php

/**
 * Test class for the mailer factory
 */
class Mumsys_Mail_MyTestMailer {

}


/**
 * Mumsys_Mail_Factory Test
 */
class Mumsys_Mail_FactoryTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mumsys_Mail_Factory
     */
    protected $_object;


    protected function setUp()
    {
        $this->_object = new Mumsys_Mail_Factory();
    }


    protected function tearDown()
    {
        $this->_object = null;
    }


    /**
     * @covers Mumsys_Mail_Factory::getAdapter
     */
    public function testGetAdapter()
    {
        $actual1 = $this->_object->getAdapter('Default');
        $actual2 = $this->_object->getAdapter('PHPMailer');

        $this->assertInstanceOf('Mumsys_Mail_Interface', $actual1);
        $this->assertInstanceOf('Mumsys_Mail_Interface', $actual2);
        $this->assertInstanceOf('Mumsys_Mail_Default', $actual1);
        $this->assertInstanceOf('Mumsys_Mail_PHPMailer', $actual2);
    }

    /**
     * @covers Mumsys_Mail_Factory::getAdapter
     */
    public function testGetAdapterException1()
    {
        $regex = '/(Invalid characters in adapter name "Mumsys_Mail_PHP\$Mailer")/i';
        $this->setExpectedExceptionRegExp('Mumsys_Mail_Exception', $regex);
        $actual1 = $this->_object->getAdapter('PHP$Mailer');
    }

    /**
     * @covers Mumsys_Mail_Factory::getAdapter
     */
    public function testGetAdapterException2()
    {
        $regex = '/(Adapter "Mumsys_Mail_MyXMailer" not available)/i';
        $this->setExpectedExceptionRegExp('Mumsys_Mail_Exception', $regex);
        $actual1 = $this->_object->getAdapter('MyXMailer');
    }

    /**
     * @covers Mumsys_Mail_Factory::getAdapter
     */
    public function testGetAdapterException3()
    {
        $regex = '/(Adapter "Mumsys_Mail_MyTestMailer" does not implement interface "Mumsys_Mail_Interface")/i';
        $this->setExpectedExceptionRegExp('Mumsys_Mail_Exception', $regex);
        $actual1 = $this->_object->getAdapter('MyTestMailer');
    }

}