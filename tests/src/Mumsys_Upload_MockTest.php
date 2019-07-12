<?php

/**
 * Mumsys_Upload_MockTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2018 by Florian Blasel
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Upload
 * Created: 2018-12
 */


/**
 * Mumsys_Upload_Mock Test
 * Generated on 2018-12-21 at 21:34:54.
 */
class Mumsys_Upload_MockTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Upload_Mock
     */
    protected $_object;

    /**
     * Base dir for test files
     * @var string
     */
    private $_dirTestFiles;

    /**
     * Tmp file for testing
     * @var string
     */
    private $_dirTmpFiles;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_object = new Mumsys_Upload_Mock();

        $this->_dirTestFiles = MumsysTestHelper::getTestsBaseDir() . '/testfiles/Domain/Upload';
        $this->_dirTmpFiles = MumsysTestHelper::getTestsBaseDir() . '/tmp';
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        if ( file_exists( $this->_dirTestFiles . '/b.file' ) ) {
            copy( $this->_dirTestFiles . '/b.file', $this->_dirTestFiles . '/a.file' );
        } else {
            copy( $this->_dirTestFiles . '/a.file', $this->_dirTestFiles . '/b.file' );
        }

        $this->_object = null;
    }


    /**
     * @covers Mumsys_Upload_Mock::move_uploaded_file
     */
    public function testMove_uploaded_file()
    {
        $to = $this->_dirTestFiles . '/b.file';

        if ( file_exists( $to ) ) {
            $from = $this->_dirTestFiles . '/b.file';
            $to = $this->_dirTestFiles . '/a.file';
        } else {
            $from = $this->_dirTestFiles . '/a.file';
            $to = $this->_dirTestFiles . '/b.file';
        }
        $actual1 = $this->_object->move_uploaded_file( $from, $to );

        $this->assertTrue( $actual1 );

        $this->expectException( 'Mumsys_Upload_Exception' );
        $this->expectExceptionMessage( 'Upload file not found/ exists' );
        $this->_object->move_uploaded_file(
            '/root/.ssh/config', $this->_dirTmpFiles . '/c.file'
        );
    }


    /**
     * writeable?
     * @covers Mumsys_Upload_Mock::move_uploaded_file
     */
    public function testMove_uploaded_fileEception1()
    {
        $to = $this->_dirTestFiles . '/notExists/c.file';
        $from = $this->_dirTestFiles . '/c.file';

        $this->expectException( 'Mumsys_Upload_Exception' );
        $mesg = '/(Target path )(.*)( not writeable)/i';
        $this->expectExceptionMessageRegExp( $mesg );
        $this->_object->move_uploaded_file( $from, $to );
    }


    /**
     * @covers Mumsys_Upload_Mock::move_uploaded_file
     */
    public function testMove_uploaded_fileEception3()
    {
        $from = $this->_dirTestFiles . '/a.file';
        $to =  '/tmp' . '/Upload_MockTest/test';
        // works with /tmp but not eg. in /home/... hmm.
        //$to =  $this->_dirTmpFiles . '/Upload_MockTest/test'; //
        $fail = false;
        $errBak = error_reporting();

        error_reporting( 0 );
        mkdir( dirname( $to ), 0755, true );
        touch( $to );
        chmod( $to, 0400 );

        try {
            $this->_object->move_uploaded_file( $from, $to );
            $fail = true;
        }
        catch ( Exception $ex ) {
            $this->assertEquals( 'Upload error.', $ex->getMessage() );
        }

        unlink( $to );
        rmdir( dirname( $to ) );
        // make sure this re-set performs!
        error_reporting( $errBak );

        if ( $fail ) {
            $this->fail();
        }
    }


    /**
     * @covers Mumsys_Upload_Mock::is_uploaded_file
     */
    public function testIs_uploaded_file()
    {
        $fromA = $this->_dirTestFiles . '/a.file';
        $fromB = $this->_dirTestFiles . '/c.file';

        $actual1 = $this->_object->is_uploaded_file( $fromA );
        $expected1 = file_exists( $fromA );

        $this->assertEquals( $expected1, $actual1 );

        $this->expectException( 'Mumsys_Upload_Exception' );
        $this->expectExceptionMessage( 'Upload error' );
        $this->_object->is_uploaded_file( $fromB );
    }

}
