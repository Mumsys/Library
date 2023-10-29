<?php

/**
 * Mumsys_Service_Spss_ReaderTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2015 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 * @version     1.0.0
 * Created: 2017-11-30
 */


/**
 * Mumsys_Service_Spss_Reader Test
 */
class Mumsys_Service_Spss_ReaderTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Service_Spss_Reader
     */
    private $_object;

    /**
     * Spss demo file location
     * @var string
     */
    private $_spssFile;

    /**
     * Version string
     * @var string
     */
    private $_version;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '2.2.0';

        $this->_spssFile = __DIR__ . '/../testfiles/Domain/Service/Spss/pspp.sav';
        $parser = \SPSS\Sav\Reader::fromString( file_get_contents( $this->_spssFile ) )->read();

        $this->_object = new Mumsys_Service_Spss_Reader( $parser );
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        unset( $this->_object );
    }


    public function testCompareSetup()
    {
        $actual = md5_file( $this->_spssFile );
        $expected = '768a8f9e58224b25cd9f7226b7162b16';

        $mesg = sprintf(
            'The source .sav file "%1$s" seems to be changed.',
            $this->_spssFile
        );
        $this->assertingEquals( $expected, $actual, $mesg );
    }

    /**
     * @covers Mumsys_Service_Spss_Reader::getVariableItems
     * @covers Mumsys_Service_Spss_Reader::_checkVariableNameExists
     */
    public function testGetVariableItems()
    {
        $list = array('v1int');
        $obj = new SPSS\Sav\Record\Variable;
        $obj->name = 'V1INT';
        $obj->label = 'v1';
        $obj->width = 0;
        $obj->realPosition = 0;
        $obj->missingValues = array(1.0, 2.0, 1.0);
        $obj->missingValuesFormat = -3;
        $obj->print = array(
            0 => 0,
            1 => 5,
            2 => 3,
            3 => 0,
        );
        $obj->write = array(
            0 => 0,
            1 => 5,
            2 => 3,
            3 => 2,
        );

        $expected = array($obj);
        $actual = $this->_object->getVariableItems( $list );

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_Service_Spss_Reader::getVariableMapByLabels
     */
    public function testGetVariableMapByLabels()
    {
        $labels = array(
            'v1int', 'v2float', 'v3string'
        );
        $actual = $this->_object->getVariableMapByLabels( $labels );
        $expected = array(
            'V1INT' => 'v1int',
            'V2FLOAT' => 'v2float',
            'V3STRING' => 'v3string'
        );

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_Service_Spss_Reader::getVariableMapByKeys
     */
    public function testGetVariableMapByKeys()
    {
        $keys = array(
            'V1INT', 'V2FLOAT', 'V3STRING'
        );
        $actual = $this->_object->getVariableMapByKeys( $keys );
        $expected = array(
            'V1INT' => 'v1int',
            'V2FLOAT' => 'v2float',
            'V3STRING' => 'v3string'
        );

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_Service_Spss_Reader::getVariableMapByRegex
     */
    public function testGetVariableMapByRegex()
    {
        $regex = array(
            '/(v)/i'
        );
        $actual1 = $this->_object->getVariableMapByRegex( $regex );
        $actual2 = $this->_object->getVariableMapByRegex();
        $expected1 = array(
            'V1INT' => 'v1int',
            'V2FLOAT' => 'v2float',
            'V3STRING' => 'v3string'
        );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected1, $actual2 );

        ini_set( "error_reporting", 0 );
        $this->expectingException( 'Mumsys_Service_Spss_Exception' );
        $this->expectingExceptionMessage( 'Regex error' );
        $this->_object->getVariableMapByRegex( array('/f#') );
        ini_set( "error_reporting", -1 );
    }


    /**
     * @covers Mumsys_Service_Spss_Reader::getVariableMap
     */
    public function testGetVariableMap()
    {
        $actual1 = $this->_object->getVariableMap();
        $expected = array(
            'V1INT' => 'v1int',
            'V2FLOAT' => 'v2float',
            'V3STRING' => 'v3string'
        );

        $actual2 = $this->_object->getVariableMap();

        $this->assertingEquals( $expected, $actual1 );
        $this->assertingEquals( $expected, $actual2 );
    }


    /**
     * @covers Mumsys_Service_Spss_Reader::getData
     */
    public function testGetData()
    {
        $actual = $this->_object->getData();
        $expected = array(
            array(1, 1.23, 'ab'),
            array(2.0, 2.3400, 'bc'),
            array(3, 3.45, 'cd')
        );

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_Service_Spss_Reader::getDocumentInfo
     */
    public function testGetDocumentInfo()
    {
        $actual = $this->_object->getDocumentInfo();
        $expected = array(
            'comment to file',
            'Added by pspp:  (17 Nov 2017 eingegeben)',
            '',
            '(17 Nov 2017 eingegeben)'
        );

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_Service_Spss_Reader::getEncoding
     */
    public function testGetEncoding()
    {
        $actual1 = $this->_object->getEncoding();
        $actual2 = $this->_object->getEncoding();
        $expected = 'UTF-8';

        $this->assertingEquals( $expected, $actual1 );
        $this->assertingEquals( $expected, $actual2 );
    }


    /**
     * @covers Mumsys_Service_Spss_Reader::getVersionOfSource
     */
    public function testGetVersionOfSource()
    {
        $actual1 = $this->_object->getVersionOfSource();
        $actual2 = $this->_object->getVersionOfSource();
        $expected = array(0,10,2);

        $this->assertingEquals( $expected, $actual1 );
        $this->assertingEquals( $expected, $actual2 );
    }


    /**
     * @covers Mumsys_Service_Spss_Reader::getFloatingPointInfo
     */
    public function testGetFloatingPointInfo()
    {
        $actual1 = $this->_object->getFloatingPointInfo();
        $actual2 = $this->_object->getFloatingPointInfo();
        $expected = array(
            'sysmis' => -1.7976931348623157E+308,
            'highest' => 1.7976931348623157E+308,
            'lowest' => -1.7976931348623155E+308,
        );

        $this->assertingEquals( $expected, $actual1 );
        $this->assertingEquals( $expected, $actual2 );
    }



    /**
     * @c o v e r s Mumsys_Service_Spss_Reader::VERSION
     */
    public function testVersion()
    {

        $actualA = $this->_object->getVersionID();
        $actualB = Mumsys_Service_Spss_Reader::VERSION;

        $this->assertingEquals( $this->_version, $actualA );
        $this->assertingEquals( $this->_version, $actualB );
    }
}
