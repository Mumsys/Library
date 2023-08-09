<?php

/**
 * Mumsys_Service_Spss_WriterTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2021 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 * @version     1.0.0
 * Created: 2021-03-09
 */


/**
 * Mumsys_Service_Spss_Writer Test
 */
class Mumsys_Service_Spss_WriterTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Service_Spss_Writer
     */
    private $_object;
    /**
     * @var string
     */
    private $_version;

    /**
     * Writer options for the construction.
     * @var array
     */
    private $_options;

     /**
     * Spss file location to write to.
     * @var string
     */
    private $_spssFile;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '2.1.0';
        $this->_spssFile = __DIR__ . '/../testfiles/Domain/Service/Spss/writerTests.sav';

        $this->_options = array(
            'header' => array(
                'prodName' => '@(#) IBM SPSS STATISTICS 64-bit Macintosh 23.0.0.0',
                'creationDate' => '01 Oct 21',
                'creationTime' => '01:36:53',
                'weightIndex' => 0,
            ),
            'variables' => array(
                array(
                    'name' => 'aaa',
                    'format' => \SPSS\Sav\Variable::FORMAT_TYPE_F,
                    'width' => 4,
                    'decimals' => 2,
                    'label' => 'test',
                    'values' => array(
                        222 => 'foo',
                        '13.22' => 'bar',
                    ),
                    'columns' => 16,
                    'alignment' => \SPSS\Sav\Variable::ALIGN_RIGHT,
                    'measure' => \SPSS\Sav\Variable::MEASURE_SCALE,
                    'attributes' => array(
                        '$@Role' => \SPSS\Sav\Variable::ROLE_PARTITION,
                    ),
                    'data' => array(1, 1, 1),
                ),
                array(
                    'name' => 'bbbb_bbbbbb12',
                    'format' => \SPSS\Sav\Variable::FORMAT_TYPE_A,
                    'width' => 28,
                    'label' => 'test',
                    'values' => array(
                        'm' => 'male',
                        'f' => 'female',
                    ),
                    'columns' => 8,
                    'alignment' => \SPSS\Sav\Variable::ALIGN_LEFT,
                    'measure' => \SPSS\Sav\Variable::MEASURE_NOMINAL,
                    'attributes' => array(
                        '$@Role' => \SPSS\Sav\Variable::ROLE_SPLIT,
                    ),
                    'data' => array('foo', 'bar', 'baz'),
                ),
                array(
                    'name' => 'BBBB_BBBBBB13',
                    'format' => \SPSS\Sav\Variable::FORMAT_TYPE_COMMA,
                    'width' => 8,
                    'decimals' => 2,
                    'columns' => 8,
                    'alignment' => \SPSS\Sav\Variable::ALIGN_RIGHT,
                    'measure' => \SPSS\Sav\Variable::MEASURE_NOMINAL,
                    'attributes' => array(
                        '$@Role' => \SPSS\Sav\Variable::ROLE_INPUT,
                    ),
                    'data' => array(1, 1, 1),
                ),
            ),
            )
        ;

        $writer = new \SPSS\Sav\Writer( $this->_options );
        $this->_object = new Mumsys_Service_Spss_Writer( $writer );
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        if ( file_exists( $this->_spssFile ) ) {
            unlink( $this->_spssFile );
        }
        unset( $this->_object );
    }


    // abstract tests


    /**
     * @covers Mumsys_Service_Spss_Writer::__construct
     * @covers Mumsys_Service_Spss_Writer::getAdapter
     * @covers Mumsys_Service_Spss_Writer::isReader
     * @covers Mumsys_Service_Spss_Writer::isWriter
     */
    public function test__construct()
    {
        /** @var \SPSS\Sav\Writer $writer 4SCA */
        $writer = $this->_object->getAdapter();
        $writer->save( $this->_spssFile );
        $writer->close();

        $isReader = $this->_object->isReader();
        $isWriter = $this->_object->isWriter();

        $this->assertingTrue( file_exists( $this->_spssFile ) );
        $this->assertingInstanceOf( '\SPSS\Sav\Writer', $writer );
        $this->assertingTrue( ( $isReader === false ) );
        $this->assertingTrue( ( $isWriter === true ) );
    }


    /**
     * @c o v e r s Mumsys_Service_Spss_Reader::VERSION
     */
    public function testVersion()
    {
        $actualA = $this->_object->getVersionID();
        $actualB = Mumsys_Service_Spss_Writer::VERSION;

        $this->assertingEquals( $this->_version, $actualA );
        $this->assertingEquals( $this->_version, $actualB );
    }
}
