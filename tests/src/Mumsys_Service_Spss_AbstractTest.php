<?php

/**
 * Mumsys_Service_Spss_AbstractTest
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

class Mumsys_Service_Spss_AbstractTestClass
    extends Mumsys_Service_Spss_Abstract
{

}


/**
 * Mumsys_Service_Spss_Abstract Test
 */
class Mumsys_Service_Spss_AbstractTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Service_Spss_Abstract
     */
    private $_object;

    /**
     * File location to a test file
     * @var string
     */
    private $_spssFile;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_spssFile = __DIR__ . '/../testfiles/Domain/Service/Spss/pspp.sav';
        $parser = \SPSS\Sav\Reader::fromString( file_get_contents( $this->_spssFile ) );
        $this->_object = new Mumsys_Service_Spss_AbstractTestClass( $parser );
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
     * @covers Mumsys_Service_Spss_Abstract::__construct
     */
    public function test__construct()
    {
        $parser = \SPSS\Sav\Reader::fromString( file_get_contents( $this->_spssFile ) );
        $this->_object = new Mumsys_Service_Spss_AbstractTestClass( $parser );

        $this->expectingException( 'Mumsys_Service_Spss_Exception' );
        $this->expectingExceptionMessage( 'Invalid Reader/Writer instance' );
        new Mumsys_Service_Spss_AbstractTestClass( '' );
    }


    /**
     * @covers Mumsys_Service_Spss_Abstract::getAdapter
     */
    public function testGetInterface()
    {
        $actual = $this->_object->getAdapter();
        $this->assertingInstanceOf( 'SPSS\Sav\Reader', $actual );

    }

}
