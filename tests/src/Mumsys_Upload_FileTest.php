<?php

/**
 * Mumsys_Upload_FileTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2018 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Upload
 * Created: 2018-12
 */


/**
 * Mumsys_Upload_File Test
 * Generated on 2018-12-21 at 21:34:54.
 */
class Mumsys_Upload_FileTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Upload_File
     */
    private $_object;

    /**
     * Base dir for test files
     * @var string
     */
    private $_dirTestFiles;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_dirTestFiles = MumsysTestHelper::getTestsBaseDir() . '/testfiles/Domain/Upload';
        //$this->_dirTmpFiles = MumsysTestHelper::getTestsBaseDir() . '/tmp';

        $fileInput = array(
            'name' => 'name.file',
            'tmp_name' => '/tmp/name.file',
            'size' => 10,
            'error' => 0,
            'type' => 'unknown/unknown',
        );

        $this->_object = new Mumsys_Upload_File( $fileInput );

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
     * @covers Mumsys_Upload_File::__construct
     */
    public function testConstructExceptions()
    {
        $fileInput = array(
//            'name' => 'name.file',
//            'tmp_name' => '/tmp/name.file',
//            'size' => 10,
//            'error' => 0,
//            'type' => 'unknown/unknown',
        );
        // exception 1
        try {
            new Mumsys_Upload_File( $fileInput );
        } catch ( Exception $ex ) {
            $this->assertingEquals( 'Invalid file', $ex->getMessage() );
        }

        // exception 2
        try {
            $fileInput['tmp_name'] = '/tmp/name.file';
            new Mumsys_Upload_File( $fileInput );
        } catch ( Exception $ex ) {
            $this->assertingEquals( 'No upload proccess detected', $ex->getMessage() );
        }

        // exception 3
        try {
            $fileInput['name'] = array(1,2);
            new Mumsys_Upload_File( $fileInput );
        } catch ( Exception $ex ) {
            $this->assertingEquals( 'Multiple uploads not implemented', $ex->getMessage() );
        }

        // exception 4
        try {
            $fileInput['name'] = 'name.file';
            new Mumsys_Upload_File( $fileInput );
        } catch ( Exception $ex ) {
            $this->assertingEquals( 'Invalid file.', $ex->getMessage() );
        }

        // exception 5: some of the errors
        try {
            $fileInput['error'] = 8;
            new Mumsys_Upload_File( $fileInput );
        } catch ( Exception $ex ) {
            $expected = 'Upload error! Code: "8", Message: "A PHP extension stopped the file upload."';
            $this->assertingEquals( $expected, $ex->getMessage() );
        }

        // exception 6
        try {
            $fileInput['error'] = 0;
            $fileInput['size'] = 0;
            new Mumsys_Upload_File( $fileInput );
        } catch ( Exception $ex ) {
            $expected = 'Empty file uploads prohabited for: "name.file"';
            $this->assertingEquals( $expected, $ex->getMessage() );
        }

//        $this->_object = new Mumsys_Upload_File( $fileInput );
    }

    /**
     * @covers Mumsys_Upload_File::move_uploaded_file
     */
    public function testMove_uploaded_fileException1()
    {
        $from = $this->_dirTestFiles . '/a.file';
        $to = '/unittest/test';

        $this->expectingException( 'Mumsys_Upload_Exception' );
        $this->expectingExceptionMessage( 'Target path "/unittest" not writeable' );
        $this->_object->move_uploaded_file( $from, $to );
    }


    /**
     * @covers Mumsys_Upload_File::move_uploaded_file
     */
    public function testMove_uploaded_fileException2()
    {
        $from = $this->_dirTestFiles . '/a.file';
        $to = $this->_dirTestFiles . '/b.file';

        $this->expectingException( 'Mumsys_Upload_Exception' );
        $this->expectingExceptionMessage( 'Upload error.' );
        $this->_object->move_uploaded_file( $from, $to );
    }


    /**
     * @covers Mumsys_Upload_File::is_uploaded_file
     */
    public function testIs_uploaded_file()
    {
        $this->expectingException( 'Mumsys_Upload_Exception' );
        $this->expectingExceptionMessage( 'Upload error' );

        $from = $this->_dirTestFiles . '/a.file';
        $actual1 = $this->_object->is_uploaded_file( $from );

        $this->assertingFalse( $actual1 );
    }

}
