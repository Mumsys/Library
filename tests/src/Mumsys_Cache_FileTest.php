<?php declare(strict_types=1);

/**
 * Mumsys_Cache_Default
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2013 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Cache
 * Created: 2013-12-10
 */

/**
 * Mumsys_Cache_File Tests
 */
class Mumsys_Cache_FileTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Cache_File
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
     * @var string
     */
    private $_pathTmpDir;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '2.3.2';
        $this->_versions = array(
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
            'Mumsys_Cache_File' => $this->_version,
        );
        $this->_object = new Mumsys_Cache_File( 'group', 'id' );

        $this->_pathTmpDir = MumsysTestHelper::getTestsBaseDir() . '/tmp';
        $this->_object->setPath( $this->_pathTmpDir );
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        $filename = $this->_object->getFilename();
        if ( file_exists( $filename ) ) {
            unlink( $filename );
        }

        unset( $this->object );
    }


    /**
     * @covers Mumsys_Cache_File::__construct
     * @covers Mumsys_Cache_File::write
     */
    public function testWrite()
    {
        $content = 'data to cache';
        $actualA = $this->_object->isCached();
        $this->_object->write( 1, $content );
        $actualB = $this->_object->isCached();
        $actualC = $this->_object->read();

        $this->assertingFalse( $actualA );
        $this->assertingTrue( $actualB );
        $this->assertingEquals( $content, $actualC );
    }


    /**
     * @covers Mumsys_Cache_File::read
     */
    public function testRead()
    {
        $content = 'data to cache';
        $this->_object->write( 2, $content );
        $actual = $this->_object->read();

        $this->assertingEquals( $content, $actual );

        $this->_object->removeCache();
        $errBak = error_reporting();
        error_reporting( 0 );
        $this->expectingException( 'Mumsys_Cache_Exception' );
        $this->expectingExceptionMessage( 'Can not read cache. File not found' );
        try {
            $this->_object->read();
        }
        catch ( Exception $ex ) {
            error_reporting( $errBak );
            throw $ex;
        }
        error_reporting( $errBak );
    }


    /**
     * @covers Mumsys_Cache_File::isCached
     */
    public function testIsCached()
    {
        $this->testWrite();
        // 4CC to unlink the file
        sleep( 1 );
        $actualA = $this->_object->isCached();
        $this->assertingFalse( $actualA );
    }


    /**
     * @covers Mumsys_Cache_File::removeCache
     */
    public function testRemoveCache()
    {
        $actualA = $this->_object->isCached();
        $this->_object->removeCache();
        $actualB = $this->_object->isCached();

        $content = 'data to cache';
        $this->_object->write( 2, $content );
        $actualC = $this->_object->isCached();
        $this->_object->removeCache();
        $actualD = $this->_object->isCached();

        $this->assertingFalse( $actualA );
        $this->assertingFalse( $actualB );
        $this->assertingTrue( $actualC );
        $this->assertingFalse( $actualD );
    }


    /**
     * @covers Mumsys_Cache_File::setPrefix
     * @covers Mumsys_Cache_File::getPrefix
     */
    public function testGetSetPrefix()
    {
        $this->_object->setPrefix( 'prfx' );
        $this->assertingEquals( 'prfx', $this->_object->getPrefix() );
    }


    /**
     * @covers Mumsys_Cache_File::setPath
     * @covers Mumsys_Cache_File::getPath
     */
    public function testSetPath()
    {
        $this->_object->setPath( $this->_pathTmpDir . '//' );
        $this->assertingEquals( $this->_pathTmpDir . '/', $this->_object->getPath() );
    }


    /**
     * @covers Mumsys_Cache_File::setEnable
     */
    public function testSetEnable()
    {
        $this->_object->setEnable( false );
        $this->assertingFalse( $this->_object->isCached() );
        $this->_object->setEnable( 0 );
        $this->assertingFalse( $this->_object->isCached() );

        $this->_object->setEnable( true );
        $this->assertingFalse( $this->_object->isCached() );
        $this->_object->setEnable( 1 );
        $this->assertingFalse( $this->_object->isCached() );
    }


    /**
     * @covers Mumsys_Cache_File::getFilename
     */
    public function testGetFilename()
    {
        $actualA = $this->_object->getFilename();

        $expectedA = $this->_pathTmpDir . '/cache_group_' . md5( 'id' );
        $this->assertingEquals( $expectedA, $actualA );
    }

    //
    // test abstract class
    //

    /**
     * @covers Mumsys_Cache_File::getVersion
     */
    public function testGetVersion()
    {
        $this->assertingEquals(
            'Mumsys_Cache_File ' . $this->_version, $this->_object->getVersion()
        );
    }


    /**
     * @covers Mumsys_Cache_File::getVersionID
     */
    public function testgetVersionID()
    {
        $this->assertingEquals( $this->_version, $this->_object->getVersionID() );
    }


    /**
     * @covers Mumsys_Cache_File::getVersions
     */
    public function testgetVersions()
    {
        $this->checkVersionList( $this->_object->getVersions(), $this->_versions );
    }

}
