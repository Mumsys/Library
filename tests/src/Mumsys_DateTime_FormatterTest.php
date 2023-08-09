<?php declare(strict_types=1);

/**
 * Mumsys_DateTime_FormatterTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) Florian Blasel, 2021
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  DateTime
 * Created: 2021-12-17
 */

/**
 * Mumsys_DateTime_Formatter Test
 *
 * Generated on 2021-12-17 at 17:34:03.
 */
class Mumsys_DateTime_FormatterTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_DateTime_Formatter
     */
    private $_object;

    /**
     * Pattern to convert to.
     * @var string
     */
    private $_pattern;

    /**
     * Locale to be used.
     * @var string
     */
    private $_locale;

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
        $this->_pattern = 'Y-m-d H:i:s';
        $this->_locale = 'en_US';
        $this->_object = new Mumsys_DateTime_Formatter( $this->_pattern, $this->_locale );

        $this->_version = '1.0.0';
        $this->_versions = array(
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
        );
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
     * @covers Mumsys_DateTime_Formatter::__construct
     * @covers Mumsys_DateTime_Formatter::__toString
     */
    public function test_construct_toString()
    {
        $objectA = new Mumsys_DateTime_Formatter( $this->_pattern, $this->_locale );

        $datetime = new DateTime( '2010-03-28' );
        $objectB = new Mumsys_DateTime_Formatter( $this->_pattern, $this->_locale, $datetime );
        $objectB->setPattern( 'MMMM' );

        $this->assertingInstanceOf( 'Mumsys_DateTime_Formatter', $objectA );
        $this->assertingInstanceOf( 'Mumsys_DateTime_Formatter', $objectB );
        $this->assertingEquals( 'March', $objectB->__toString() );

        // exception
        $this->expectingException( 'Mumsys_DateTime_Exception' );
        $this->expectingExceptionMessage( 'DateTime not set' );
        $objectC = new Mumsys_DateTime_Formatter( $this->_pattern, $this->_locale );
        $objectC->__toString();
    }


    /**
     * @covers Mumsys_DateTime_Formatter::setLocale
     * @covers Mumsys_DateTime_Formatter::setPattern
     */
    public function testSetLocaleSetPattern()
    {
        $this->_object->setLocale( 'de_DE' );
        $this->_object->setPattern( 'MMMM' ); // long month name
        $datetime = new DateTime( '2010-03-28' );
        $actualA = $this->_object->formatLocale( $datetime );
        $this->assertingEquals( 'März', $actualA );
    }


    /**
     * @covers Mumsys_DateTime_Formatter::formatLocale
     */
    public function testFormatLocale()
    {
        $locales = array('de_DE', 'it_IT', 'fr_FR', 'en_UK');

        $this->_object->setPattern( 'MMMM' ); // Short month names

        $datetime = new DateTime( '2010-02-28' );

        $result = array();

        foreach ( $locales as $locale ) {
            $this->_object->setLocale( $locale );
            $result[] = $this->_object->formatLocale( $datetime );
        }
        $expected = array(
            0 => 'Februar',
            1 => 'febbraio',
            2 => 'février',
            3 => 'February',
        );

        $this->assertingEquals( $expected, $result );
    }

    // --- abstract

    /**
     * @covers Mumsys_DateTime_Formatter::getVersions
     */
    public function testGetVersions()
    {
        $possible = $this->_object->getVersions();

        foreach ( $this->_versions as $must => $value ) {
            $this->assertingTrue( isset( $possible[$must] ) );
            $this->assertingTrue( ( $possible[$must] === $value ) );
        }

        $this->assertingTrue( $this->_version === $this->_object->getVersionID() );
    }

}
