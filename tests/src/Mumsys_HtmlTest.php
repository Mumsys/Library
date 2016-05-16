<?php


/**
 * Mumsys_Html Test
 */
class Mumsys_HtmlTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @var Html
     */
    protected $_object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_version = '3.2.1';
        $this->_versions = array(
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
            'Mumsys_Html' => $this->_version,
            'Mumsys_Xml_Abstract' => '3.2.1',
        );
        $this->_object = new Mumsys_Html;
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->_object = null;
    }


    public function testAttributesCreate()
    {
        $array = array('id' => 123, 'name' => 'attributeName');
        $string = $this->_object->attributesCreate($array);
        $this->assertEquals('id="123" name="attributeName"', $string);

        $this->setExpectedException('Mumsys_Html_Exception');
        $array2 = array('id' => 123, 'name' => array(1, 2));
        $string2 = $this->_object->attributesCreate($array2);
    }


    public function testStripNoWhitelistToBePlainText()
    {
        $htmlcode = '<p>hello world</p><p>hello world</p>';
        $code = $this->_object->strip($htmlcode, $against_whitelist = false);

        $this->assertEquals('hello worldhello world', $code);
    }


    public function testStripWithWhitelist()
    {
        $htmlcode = '<p align="left">hello world</p><p>hello world</p><hr><table cellpadding="2" title="aGlobalAllowAttribute" xyzattr="toStrip"></table>';
        $expected = '<p align="left">hello world</p><p>hello world</p><hr /><table cellpadding="2" title="aGlobalAllowAttribute"></table>';
        $code = $this->_object->strip($htmlcode, $against_whitelist = true);

        $this->assertEquals($expected, $code);
    }


    public function testAttributesFilter1()
    {
        $code1 = $this->_object->attributesFilter('table',
            ' cellpadding="2" title="aGlobalAllowAttribute" xyzattr="toStrip"');
        $expected1 = 'cellpadding="2" title="aGlobalAllowAttribute"';

        // = in a attribute
        $code2 = $this->_object->attributesFilter('table',
            ' class="x=y" cellpadding="2" title="aGlobalAllowAttribute" xyzattr="toStrip"');
        $expected2 = 'class="x=y" cellpadding="2" title="aGlobalAllowAttribute"';

        // TODO! will be cutted now but is wrong in this case!
        // ' ' (space) in a attribute
        $code3 = $this->_object->attributesFilter('table',
            ' class="classa classb" cellpadding="2" title="aGlobalAllowAttribute" xyzattr="toStrip"');
        $expected3 = 'class="classa classb" cellpadding="2" title="aGlobalAllowAttribute"';

        // drop crap at the end as long there is no = char over there
        $code4 = $this->_object->attributesFilter('table',
            ' class="classa classb" cellpadding="2" title="aGlobalAllowAttribute"" a"');
        $expected4 = 'class="classa classb" cellpadding="2" title="aGlobalAllowAttribute"';

        // add missing quotes. only ad the end!
        $code5 = $this->_object->attributesFilter('td',
            ' class="classa classb" cellpadding="2" title="atitle" nowrap=nowrap');
        $expected5 = 'class="classa classb" title="atitle" nowrap="nowrap"';

        // wrong single quotes fix
        $code6 = $this->_object->attributesFilter('td',
            ' class="classa classb" style=\'xxx\' title=\'atitle\' nowrap=nowrap');
        $expected6 = 'class="classa classb" style="xxx" title="atitle" nowrap="nowrap"';

        // no attributes
        $code7 = $this->_object->attributesFilter('td', ' xyz="abc"');
        $expected7 = '';

        $this->assertEquals($expected1, $code1);
        $this->assertEquals($expected2, $code2);
        $this->assertEquals($expected3, $code3);
        $this->assertEquals($expected4, $code4);
        $this->assertEquals($expected5, $code5);
        $this->assertEquals($expected6, $code6);
        $this->assertEquals($expected7, $code7);
    }


    public function testFilter1()
    {
        $html1 = '<!-- my html comment --><p align="left">hello world</p>
<p title="hello world">hello world</p>
<hr>
<table cellpadding="2" title="aGlobalAllowAttribute" xyzattr="toStrip">
<tr>
    <td>col1 link: http://www/?a=b&c=d <img src="image.jpg" title="image"></td>
</tr>
</table>';
        $expected1 = '<p align="left">hello world</p>
<p title="hello world">hello world</p>
<hr />
<table cellpadding="2" title="aGlobalAllowAttribute">
<tr>
    <td>col1 link: http://www/?a=b&c=d <img src="image.jpg" title="image" /></td>
</tr>
</table>';

        $code1 = $this->_object->filter($html1);

        $this->assertEquals($expected1, $code1);
    }


    public function testAttributesValidate()
    {
        $whitelist = array('name');

        $properties = array('name' => 'value');
        $universalAttributesAllow = false;
        $actual = $this->_object->attributesValidate($whitelist, $properties, $universalAttributesAllow);
        $this->assertEquals(array('name' => 'value'), $actual);

        // check universal attributes
        $universalAttributesAllow = true;
        $properties = array('name' => 'value', 'id' => 999);
        $actual = $this->_object->attributesValidate($whitelist, $properties, $universalAttributesAllow);
        $this->assertEquals(array('name' => 'value', 'id' => 999), $actual);
    }


    /**
     * @covers Mumsys_Html::getVersion
     * @covers Mumsys_Html::getVersionID
     * @covers Mumsys_Html::getVersions
     */
    public function testVersionsInAbstractClass()
    {
        $this->assertEquals(get_class($this->_object) . ' ' . $this->_version, $this->_object->getVersion());

        $this->assertEquals($this->_version, $this->_object->getVersionID());

        $possible = $this->_object->getVersions();

        foreach ($this->_versions as $must => $value) {
            $this->assertTrue(isset($possible[$must]));
            $this->assertTrue(($possible[$must] == $value));
        }
    }

}