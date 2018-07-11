<?php
/**
 * Test helper class
 */
class Mumsys_Xml_AbstractTestHelper
    extends Mumsys_Xml_Abstract
{

}


/**
 * Mumsys_Xml_Abstract Test
 */
class Mumsys_Xml_AbstractTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Xml_Abstract
     */
    protected $_object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new Mumsys_Xml_AbstractTestHelper;
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->_object = NULL;
    }


    /**
     * @covers Mumsys_Xml_Abstract::attributesCreate
     */
    public function testAttributesCreate()
    {
        $input = array('name' => 'value', 'id' => 12345, 'customAttribute' => 'custom value');
        $expected = 'name="value" id="12345" customAttribute="custom value"';
        $actual = $this->_object->attributesCreate($input);

        $this->assertEquals($expected, $actual);

        $this->expectExceptionMessageRegExp('/(Invalid attribute value for key: "0": "array")/i');
        $this->expectException('Mumsys_Xml_Exception');
        $input = array(array(1 => 2), array(2));
        $this->_object->attributesCreate($input);
    }

}