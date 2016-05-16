<?php


/**
 * Mumsys_I18n_None Test
 */
class Mumsys_I18n_NoneTest
    extends MumsysTestHelper
{
    /**
     * @var Mumsys_I18n_None
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
            'Mumsys_I18n_None' => $this->_version,
            'Mumsys_I18n_Abstract' => '3.2.1',
        );

        $this->_object = new Mumsys_I18n_None('ru');
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->_object = null;
    }


    /**
     * For code coverage.
     * @covers Mumsys_I18n_None::__construct
     */
    public function test_construct()
    {
        $this->setUp();

        $this->setExpectedException('Mumsys_I18n_Exception', 'Invalid locale "biglocale"');
        $o = new Mumsys_I18n_None('biglocale');
    }


    /**
     * @covers Mumsys_I18n_None::_t
     */
    public function test_t()
    {
        $expected = 'to translate';
        $actual = $this->_object->_t($expected);

        $this->assertEquals($expected, $actual);
    }


    /**
     * @covers Mumsys_I18n_None::_dt
     */
    public function test_dt()
    {
        $expected = 'to translate';
        $actual = $this->_object->_dt('domain', $expected);

        $this->assertEquals($expected, $actual);
    }


    /**
     * @covers Mumsys_I18n_None::_dtn
     */
    public function test_dtn()
    {
        $this->_object->setlocale('de');
        $singular = 'Flower';
        $plural = 'Flowers';
        $actual1 = $this->_object->_dtn('domain', $singular, $plural, 1); //one flower
        $actual2 = $this->_object->_dtn('domain', $singular, $plural, 2); //two flowers

        $this->assertEquals($singular, $actual1);
        $this->assertEquals($plural, $actual2);
    }


    /**
     * @covers Mumsys_I18n_Abstract::getVersion
     * @covers Mumsys_I18n_Abstract::getVersionID
     * @covers Mumsys_I18n_Abstract::getVersions
     */
    public function testAbstractClass()
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