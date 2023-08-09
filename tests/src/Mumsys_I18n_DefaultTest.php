<?php

/**
 * Mumsys_I18n_Default Test
 */
class Mumsys_I18n_DefaultTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_I18n_Default
     */
    private $_object;

    /**
     * @var string
     */
    private $_version;

    /**
     * @var array
     */
    private $_versions;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '3.2.1';
        $this->_versions = array(
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
            'Mumsys_I18n_Default' => $this->_version,
            'Mumsys_I18n_Abstract' => '3.2.1',
        );

        $this->_object = new Mumsys_I18n_Default( 'ru' );
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
     * For code coverage.
     * @covers Mumsys_I18n_Default::__construct
     */
    public function test_construct()
    {
        $this->setUp();

        $this->expectingExceptionMessageRegex( '/(Invalid locale "biglocale")/i' );
        $this->expectingException( 'Mumsys_I18n_Exception' );
        $o = new Mumsys_I18n_Default( 'biglocale' );
    }


    /**
     * @covers Mumsys_I18n_Default::_t
     */
    public function test_t()
    {
        $expected = 'to translate';
        $actual = $this->_object->_t( $expected );

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_I18n_Default::_dt
     */
    public function test_dt()
    {
        $expected = 'to translate';
        $actual = $this->_object->_dt( 'domain', $expected );

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_I18n_Default::_dtn
     */
    public function test_dtn()
    {
        $this->_object->setlocale( 'de' );
        $singular = 'Flower';
        $plural = 'Flowers';
        $actual1 = $this->_object->_dtn( 'domain', $singular, $plural, 1 ); //one flower
        $actual2 = $this->_object->_dtn( 'domain', $singular, $plural, 2 ); //two flowers

        $this->assertingEquals( $singular, $actual1 );
        $this->assertingEquals( $plural, $actual2 );
    }


    /**
     * @covers Mumsys_I18n_Abstract::getVersion
     * @covers Mumsys_I18n_Abstract::getVersionID
     * @covers Mumsys_I18n_Abstract::getVersions
     */
    public function testAbstractClass()
    {
        $this->assertingEquals(
            'Mumsys_I18n_Default ' . $this->_version,
            $this->_object->getVersion()
        );

        $this->assertingEquals( $this->_version, $this->_object->getVersionID() );

        $possible = $this->_object->getVersions();

        foreach ( $this->_versions as $must => $value ) {
            $this->assertingTrue( isset( $possible[$must] ) );
            $this->assertingTrue( ( $possible[$must] == $value ) );
        }
    }

}
