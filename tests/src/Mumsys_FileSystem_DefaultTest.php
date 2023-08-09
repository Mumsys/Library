<?php

/**
 * Test class for Mumsys_FileSystem_Default.
 */
class Mumsys_FileSystem_DefaultTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_FileSystem_Default
     */
    private $_object;

    /**
     * List of test files/dirs to be created and removed
     * @var array
     */
    private $_testdirs;

    /**
     * Test base directory.
     * @var string
     */
    private $_testsBaseDir;

    /**
     * @var string
     */
    private $_version;

    /**
     * @var array
     */
    private $_versions;


    protected function setUp(): void
    {
        $this->_version = '3.1.0';
        $this->_versions = array(
            'Mumsys_FileSystem_Default' => $this->_version,
            'Mumsys_FileSystem_Common_Abstract' => '3.1.1',
        );

        $this->_testsBaseDir = realpath( dirname( __FILE__ ) . '/../' );
        $this->_testdirs = array(
            'rm1' => $this->_testsBaseDir . '/tmp/unittest-mkdir/mkdirs/testfile',
            'rm2' => $this->_testsBaseDir . '/tmp/unittest-mkdir/testfile',
            'dir' => $this->_testsBaseDir . '/tmp/unittest-mkdir',
            'dirs' => $this->_testsBaseDir . '/tmp/unittest-mkdir/mkdirs',
            'file' => $this->_testsBaseDir . '/tmp/unittest',
            'file2' => $this->_testsBaseDir . '/tmp/unittest_2',
            'invalid' => '/root/goes/here',
            'rm3' => $this->_testsBaseDir . '/tmp/mkdirs',
            'rm4' => $this->_testsBaseDir . '/tmp/unittest-mkdir.lnk',
        );
        touch( $this->_testdirs['file'] );

        $this->_object = new Mumsys_FileSystem_Default();
    }


    protected function tearDown(): void
    {
        foreach ( $this->_testdirs as $dir ) {
            if ( !@rmdir( $dir ) ) {
                @unlink( $dir );
            }
        }
    }


    /**
     * @covers Mumsys_FileSystem_Default::__construct
     */
    public function test__constructor()
    {
        $this->_object = new Mumsys_FileSystem_Default();
        $this->assertingInstanceOf( 'Mumsys_FileSystem_Default', $this->_object );
        $this->assertingInstanceOf( 'Mumsys_Abstract', $this->_object );
    }


    /**
     * @covers Mumsys_FileSystem_Default::scanDirInfo
     */
    public function testScanDirInfo()
    {
        @mkdir( $this->_testdirs['dir'], 0755 );
        @mkdir( $this->_testdirs['dirs'], 0755 );
        @touch( $this->_testdirs['dir'] . '/testfile' );
        @touch( $this->_testdirs['dirs'] . '/testfile' );

        $filters = array('/(unittest)/i');

        // simple directory
        $actual1 = $this->_object->scanDirInfo(
            $this->_testdirs['dir'], true, false, array(), -1, 1001
        );
        $path11 = $this->_testsBaseDir . '/tmp/unittest-mkdir/mkdirs';
        $path12 = $this->_testsBaseDir . '/tmp/unittest-mkdir/testfile';
        $actual1[$path11]['size'] =
            ( ( $actual1[$path11]['size'] === 22 ) || ( $actual1[$path11]['size'] === 4096 ) ) ? true : false
        ;
        $actual1[$path12]['size'] =
            ( ( $actual1[$path12]['size'] === 22 ) || ( $actual1[$path12]['size'] === 4096 ) ) ? true : false
        ;
        $expected1 = array(
            $this->_testsBaseDir . '/tmp/unittest-mkdir/mkdirs' => array(
                'file' => $this->_testsBaseDir . '/tmp/unittest-mkdir/mkdirs',
                'name' => 'mkdirs',
                'path' => $this->_testsBaseDir . '/tmp/unittest-mkdir',
                'size' => true,
                'type' => 'dir',
            ),
            $this->_testsBaseDir . '/tmp/unittest-mkdir/testfile' => array(
                'file' => $this->_testsBaseDir . '/tmp/unittest-mkdir/testfile',
                'name' => 'testfile',
                'path' => $this->_testsBaseDir . '/tmp/unittest-mkdir',
                'size' => 0,
                'type' => 'file',
            )
        );

        // recursive directory + filter
        $actual2 = $this->_object->scanDirInfo(
            $this->_testdirs['dir'], true, true, $filters
        );

        $path2A = $this->_testsBaseDir . '/tmp/unittest-mkdir/mkdirs';
        $path2B = $this->_testsBaseDir . '/tmp/unittest-mkdir/testfile';
        $path2C = $this->_testsBaseDir . '/tmp/unittest-mkdir/mkdirs/testfile';

        $actual2[$path2A]['size'] =
            ( ( $actual2[$path2A]['size'] == 22 ) || ( $actual2[$path2A]['size'] == 4096 ) ) ? true : false
        ;
        $actual2[$path2B]['size'] =
            ( ( $actual2[$path2B]['size'] == 22 ) || ( $actual2[$path2B]['size'] == 4096 ) ) ? true : false
        ;
        $actual2[$path2C]['size'] =
            ( ( $actual2[$path2C]['size'] == 22 ) || ( $actual2[$path2C]['size'] == 4096 ) ) ? true : false
        ;
        $expected2 = array(
            $this->_testsBaseDir . '/tmp/unittest-mkdir/mkdirs' => array(
                'file' => $this->_testsBaseDir . '/tmp/unittest-mkdir/mkdirs',
                'name' => 'mkdirs',
                'path' => $this->_testsBaseDir . '/tmp/unittest-mkdir',
                'size' => true,
                'type' => 'dir',
            ),
            $this->_testsBaseDir . '/tmp/unittest-mkdir/testfile' => array(
                'file' => $this->_testsBaseDir . '/tmp/unittest-mkdir/testfile',
                'name' => 'testfile',
                'path' => $this->_testsBaseDir . '/tmp/unittest-mkdir',
                'size' => 0,
                'type' => 'file',
            ),
            $this->_testsBaseDir . '/tmp/unittest-mkdir/mkdirs/testfile' => array(
                'file' => $this->_testsBaseDir . '/tmp/unittest-mkdir/mkdirs/testfile',
                'name' => 'testfile',
                'path' => $this->_testsBaseDir . '/tmp/unittest-mkdir/mkdirs',
                'size' => 0,
                'type' => 'file',
            ),
        );
        unlink( $this->_testdirs['dir'] . '/testfile' );
        unlink( $this->_testdirs['dirs'] . '/testfile' );

        // test unreadable path
        $actual3 = $this->_object->scanDirInfo( '/root', true, true );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
        $this->assertingFalse( $actual3 );
    }


    /**
     * @covers Mumsys_FileSystem_Default::getFileDetails
     * @covers Mumsys_FileSystem_Default::_getFileDetailsPrepare
     */
    public function testGetFileDetails()
    {
        $actual1 = $this->_object->getFileDetails( __FILE__ );
        $expected1 = array(
            'file' => __FILE__,
            'name' => basename( __FILE__ ),
            'size' => filesize( __FILE__ ),
            'type' => 'file',
            'path' => dirname( __FILE__ ),
        );

        $actual2 = $this->_object->getFileDetails(
            dirname( __FILE__ ), basename( __FILE__ )
        );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected1, $actual2 );

        $this->expectingExceptionMessageRegex( '/(File "\/i\/don\/t\/exist" not found)/' );
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $actual2 = $this->_object->getFileDetails( '/i/don/t/exist' );
    }


    /**
     * @covers Mumsys_FileSystem_Default::getFileDetailsExtended
     * @covers Mumsys_FileSystem_Default::_getFileDetailsPrepare
     */
    public function testGetFileDetailsExtended()
    {
        // info for a file
        $curFile = __FILE__;
        $actual1 = $this->_object->getFileDetailsExtended( $curFile );
        $stat = @lstat( $curFile );
        $expected1 = array(
            'file' => $curFile,
            'name' => basename( $curFile ),
            'size' => filesize( $curFile ),
            'type' => 'file',
            'path' => dirname( $curFile ),
            'is_file' => true,
            'is_dir' => false,
            'is_link' => false,
            'is_readable' => true,
            'is_writable' => true,
            'permission' => $stat['mode'],
            'owner' => $stat['uid'],
            'group' => $stat['gid'],
            'mtime' => $stat['mtime'],
            'atime' => $stat['atime'],
            'ctime' => $stat['ctime'],
            'filetype' => trim( shell_exec( 'file -b -p "' . $curFile . '";' ) ),
            'is_executable' => true,
            'ext' => 'php',
            'mimetype' => 'text/x-php',
            'owner_name' => @reset( posix_getpwuid( $stat['uid'] ) ),
            'group_name' => @reset( posix_getgrgid( $stat['gid'] ) ),
        );
        // info for a directory
        $actual2 = $this->_object->getFileDetailsExtended( $this->_testsBaseDir . '/tmp' );
        $stat = @lstat( $this->_testsBaseDir . '/tmp' );
        $expected2 = array(
            'file' => $this->_testsBaseDir . '/tmp',
            'name' => 'tmp',
            'size' => filesize( $this->_testsBaseDir . '/tmp' ),
            'type' => 'dir',
            'path' => $this->_testsBaseDir,
            'is_file' => false,
            'is_dir' => true,
            'is_link' => false,
            'is_readable' => true,
            'is_writable' => true,
            'permission' => $stat['mode'],
            'owner' => $stat['uid'],
            'group' => $stat['gid'],
            'mtime' => $stat['mtime'],
            'atime' => $stat['atime'],
            'ctime' => $stat['ctime'],
            'filetype' => trim(
                shell_exec( 'file -b -p "' . $this->_testsBaseDir . '/tmp";' )
            ),
            'is_executable' => true,
            'ext' => false,
            'owner_name' => @reset( posix_getpwuid( $stat['uid'] ) ),
            'group_name' => @reset( posix_getgrgid( $stat['gid'] ) ),
        );
        // info for a link
        touch( $this->_testdirs['file'] );
        symlink( $this->_testdirs['file'], $this->_testsBaseDir . '/tmp/link' );
        $stat = @lstat( $this->_testsBaseDir . '/tmp/link' );
        $actual3 = $this->_object->getFileDetailsExtended(
            $this->_testsBaseDir . '/tmp/link'
        );
        $expected3 = array(
            'file' => $this->_testsBaseDir . '/tmp/link',
            'name' => 'link',
            'size' => $stat['size'],
            'type' => 'link',
            'path' => $this->_testsBaseDir . '/tmp',
            'is_file' => true,
            'is_dir' => false,
            'is_link' => true,
            'is_readable' => true,
            'is_writable' => true,
            'permission' => $stat['mode'],
            'owner' => $stat['uid'],
            'group' => $stat['gid'],
            'mtime' => $stat['mtime'],
            'atime' => $stat['atime'],
            'ctime' => $stat['ctime'],
            'filetype' => $this->_object->getFileType( $this->_testsBaseDir . '/tmp/link' ),
            'is_executable' => true,
            'ext' => '',
            'mimetype' => finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $this->_testsBaseDir . '/tmp/link' ),
            'target' => $this->_testsBaseDir . '/tmp/unittest',
            'owner_name' => @reset( posix_getpwuid( $stat['uid'] ) ),
            'group_name' => @reset( posix_getgrgid( $stat['gid'] ) ),
        );
        @unlink( $this->_testsBaseDir . '/tmp/link' );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );
    }


    /**
     * @covers Mumsys_FileSystem_Default::getFileType
     */
    public function testGetFileType()
    {
        $actual = $this->_object->getFileType( '/bin/sh' );

        // OS related output
        $expecteds = array(
            "cannot open `/usr/bin/sh' (No such file or directory)\n",
            "ERROR: cannot open `/usr/bin/sh' (No such file or directory)\n",
            "finfo::file(/usr/bin/sh): failed to open stream: No such file or "
            . "directory",
            "ELF 32-bit LSB shared object, Intel 80386, version 1 (SYSV)",
            'ELF 64-bit LSB executable, x86-64, version 1 (SYSV)',
            'ELF 64-bit LSB shared object, x86-64, version 1 (SYSV)',
            'ELF 64-bit LSB pie executable, x86-64, version 1 (SYSV)',
            "symbolic link to dash\n",
        );

        $this->assertingTrue( in_array( $actual, $expecteds ) );
    }


    /**
     * @covers Mumsys_FileSystem_Default::copy
     */
    public function testCopy()
    {
        $actual1 = $this->_object->copy(
            $this->_testdirs['file'], $this->_testdirs['file2']
        );
        $expected1 = $this->_testdirs['file2'];
        // keep copy
        $actual2 = $this->_object->copy(
            $this->_testdirs['file'], $this->_testdirs['file2'], true, 1
        );
        $expected2 = $this->_testdirs['file2'] . '.2';
        @unlink( $expected2 );
        // target is a directory
        $actual3 = $this->_object->copy(
            $this->_testdirs['file'], $this->_testsBaseDir . '/tmp', true
        );
        $expected3 = $this->_testdirs['file'] . '.1';
        @unlink( $expected3 );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );

        // source is a dir exception
        $regex = '/(Source file: A directory was found. only file copying is '
            . 'implemented)/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->copy( $this->_testsBaseDir . '/tmp/', '/home/' );
    }


    /**
     * @covers Mumsys_FileSystem_Default::copy
     */
    public function testCopyException()
    {
        $regex = '/(Copy error)/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->copy( $this->_testdirs['file'], '/' );
    }


    /**
     * @covers Mumsys_FileSystem_Default::rename
     */
    public function testRename()
    {
        // default copy
        $source = $this->_testdirs['dir'] . '/testfile';
        $expected1 = $this->_testdirs['dir'] . '/filetest';
        @mkdir( $this->_testdirs['dir'], 0755 );
        @touch( $source );
        $actual1 = $this->_object->rename( $source, $expected1 );
        // target exists
        @touch( $source );
        $actual2 = $this->_object->rename( $source, $expected1, true );
        $expected2 = $this->_testdirs['dir'] . '/filetest.1';
        //stream context
        @touch( $source );
        $streamCtx = stream_context_create();
        $actual3 = $this->_object->rename(
            $source, $expected1, false, $streamCtx
        );
        $expected3 = $this->_testdirs['dir'] . '/filetest';

        @unlink( $expected1 );
        @unlink( $expected2 );
        @rmdir( $this->_testdirs['dir'] );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );

        // source emty exception
        $regex = '/(Rename failt for reason: Source "" is no directory and no file)/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->rename( '', $this->_testsBaseDir . '/tmp/something' );
    }


    /**
     * @covers Mumsys_FileSystem_Default::rename
     */
    public function testRenameException()
    {
        // rename permission error
        $msg[] = 'Rename failt for reason: Copy error for: "'
            . $this->_testsBaseDir . '/tmp/unittest" '
            . 'copy(/root//unittest): failed to open stream: Permission denied';
        $msg[] = 'Rename failt for reason: rename(): Permission denied';

        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->rename( $this->_testdirs['file'], '/root/' );
    }


    /**
     * @covers Mumsys_FileSystem_Default::link
     */
    public function testLink()
    {
        $actual1 = $this->_object->link(
            $this->_testsBaseDir . '/tmp', $this->_testdirs['dir'], 'soft', 'abs',
            false
        );
        $expected1 = $this->_testdirs['dir'];
        // is_link OK
        $actual2 = $this->_object->link(
            $this->_testsBaseDir . '/tmp', $this->_testdirs['dir'], 'soft', 'abs',
            false
        );
        @unlink( $this->_testdirs['dir'] );

        // hard link (owned by myself ok, otherwise possible error/exceptions
        // with write perms)
        touch( $this->_testdirs['dir'] );
        $actual3 = $this->_object->link(
            $this->_testdirs['dir'], $this->_testsBaseDir . '/tmp/lnkname', 'hard',
            'abs', false
        );
        $expected3 = $this->_testsBaseDir . '/tmp/lnkname';
        @unlink( $this->_testsBaseDir . '/tmp/lnkname' );
        @unlink( $this->_testdirs['dir'] );

        // relative links
        $actual4 = $this->_object->link(
            $this->_testsBaseDir . '/tmp', $this->_testdirs['dir'], 'soft', 'rel',
            false
        );
        $expected4 = $this->_testdirs['dir'];

        // keep copy
        $actual5 = $this->_object->link(
            $this->_testsBaseDir . '/tmp', $this->_testdirs['dir'], 'soft', 'abs',
            true
        );
        $expected5 = $this->_testdirs['dir'] . '.lnk';

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected1, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );
        $this->assertingEquals( $expected4, $actual4 );
        $this->assertingEquals( $expected5, $actual5 );

        // test realpath exception + last, catched exception
        unlink( $this->_testdirs['dir'] );
        $regex = '/(Linking failt for source: "'
            . str_replace( '/', '\/', $this->_testsBaseDir ) . '\/tmp"; target: "'
            . str_replace( '/', '\/', $this->_testsBaseDir )
            . '\/tmp\/unittest-mkdir\/mkdirs". '
            . 'Real path not found for "'
            . str_replace( '/', '\/', $this->_testsBaseDir )
            . '\/tmp\/unittest-mkdir")/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->link(
            $this->_testsBaseDir . '/tmp', $this->_testdirs['dirs'], 'soft', 'rel',
            false
        );
    }


    /**
     * @covers Mumsys_FileSystem_Default::link
     */
    public function testLinkException()
    {
        // invalid link type
        $regex = '/('
            . 'Linking failt for source: "'
            . str_replace( '/', '\/', $this->_testsBaseDir )
            . '\/tmp"; target: "' . str_replace( '/', '\/', $this->_testsBaseDir )
            . '\/tmp\/unittest-mkdir"\. Invalid link type "invalidType" '
            . '\(Use soft\|hard\)'
            . ')/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->link(
            $this->_testsBaseDir . '/tmp', $this->_testdirs['dir'], 'invalidType',
            'rel', false
        );
    }


    /**
     * @covers Mumsys_FileSystem_Default::unlink
     * @covers Mumsys_FileSystem_Default::rmFile
     */
    public function testUnlinkRmFile()
    {
        $dir = $this->_testdirs['dir'];
        $this->_object->mkdir( $dir );
        touch( ( $file = $dir . '/toUnlink.test' ) );
        $link = $file . '.lnk';
        symlink( $file, $link );

        $this->assertingTrue( $this->_object->unlink( $dir ) ); // not a file
        $this->assertingTrue( $this->_object->unlink( $file ) );
        $this->assertingTrue( $this->_object->unlink( $link ) );
        $this->assertingFalse( is_link( $link ) );

        $phpbin = MumsysTestHelper::getBinary( 'bash' );
        $fileDifferentOwnership = trim( (string) $phpbin );
        $errRepBak = error_reporting();
        try {
            error_reporting( 0 );
            $msg = 'Testing unlink() exception failed! '
                . 'Please create a file where you don\'t have write access but'
                . ' read access and with a different ownership! '
                . 'E.g.: ~/unittests/somefile.test (owner whether you, your '
                . 'team and not root) und try to test with this file location';
            // security check, backup
            if ( copy( $fileDifferentOwnership, $fileDifferentOwnership . '.bak' ) !== false ) {
                $this->markTestIncomplete( 'Security abort!!! ' . $msg );
            }

            $this->_object->rmFile( $fileDifferentOwnership );

            $this->markTestIncomplete( $msg );
        }
        catch ( Exception $e ) {
            // check success
            $message = sprintf(
                'Can not delete file "%1$s"', $fileDifferentOwnership
            );
            $this->assertingEquals( $message, $e->getMessage() );
            $this->assertingInstanceOf( 'Mumsys_FileSystem_Exception', $e );
        }

        error_reporting( $errRepBak );
    }


    /**
     * @covers Mumsys_FileSystem_Default::mkdir
     */
    public function testMkdir()
    {
        $dir = $this->_testdirs['dir'];
        $this->_object->mkdir( $dir );

        $this->assertingTrue( file_exists( $dir ) );
        $this->assertingFalse( $this->_object->mkdir( $dir ) ); // exists error

        $errBak = error_reporting();
        error_reporting( 0 );
        $regex = '/(Can not create dir: "\/xyz" mode: "755". Message: '
            . 'mkdir\(\): Permission denied)/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->mkdir( '/xyz' );
        error_reporting( $errBak );
    }


    /**
     * @covers Mumsys_FileSystem_Default::mkdirs
     * @covers Mumsys_FileSystem_Default::mkdir
     */
    public function testMkdirs()
    {
        $dir = $this->_testdirs['dirs'];
        $this->_object->mkdirs( $dir );

        $this->assertingTrue( file_exists( $dir ) ); // created
        $this->assertingTrue( $this->_object->mkdirs( $dir ) ); // exists
    }


    /**
     * Test mkdir fails and rm already created.
     *
     * @covers Mumsys_FileSystem_Default::mkdirs
     * @covers Mumsys_FileSystem_Default::mkdir
     */
    public function testMkdirsException()
    {
        $dir = $this->_testdirs['dirs'] . '/x/';
        $message = '';
        $this->_object->mkdirs(
            $this->_testdirs['dirs'] . '/x/../../home/user', 0700
        );
        $this->assertingFalse( file_exists( $dir ) );

        $this->assertingTrue( $this->_object->mkdirs( $dir ) ); // created
        $this->assertingTrue( file_exists( $dir ) );

        @rmdir( $dir );
    }


    /**
     * @covers Mumsys_FileSystem_Default::rmdir
     * @covers Mumsys_FileSystem_Default::rmdirs
     */
    public function testRmDir()
    {
        $dir = $this->_testdirs['dirs'];
        $this->_object->mkdirs( $dir );

        $this->assertingTrue( $this->_object->rmdir( '/not/existing/path' ) );
        $this->assertingTrue( $this->_object->rmdir( $dir ) );

        $this->_object->mkdirs( $dir );
        $this->assertingTrue( $this->_object->rmdirs( '/not/existing/path' ) );
        $this->assertingTrue( $this->_object->rmdirs( $this->_testdirs['dir'] ) );

        $regex = '/(Can not delete directory "\/tmp")/';
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->expectingExceptionMessageRegex( $regex );
        $this->assertingTrue( $this->_object->rmdir( '/tmp' ) );
    }


    /**
     * Removes a directory recusivly.
     * @covers Mumsys_FileSystem_Default::rmdirs
     */
    public function testRmDirsException()
    {
        $this->expectingException( 'Exception' );
        $this->_object->rmdirs( '/root/' );
    }


    /**
     * @covers Mumsys_FileSystem_Default::getRelativeDir
     */
    public function testGetRelativeDir()
    {
        $actual1 = $this->_object->getRelativeDir( '/tmp/here', '/home/there' );
        $expected1 = '../../home/there/';

        $actual2 = $this->_object->getRelativeDir(
            '/home/user/mydir', '/share/data/thatdir'
        );
        $expected2 = '../../../share/data/thatdir/';

        $actual3 = $this->_object->getRelativeDir(
            './myfile', './data/thatdir'
        );
        $expected3 = '../data/thatdir/';

        $actual4 = $this->_object->getRelativeDir(
            $this->_testsBaseDir . '/tmp/',
            $this->_testsBaseDir . '/tmp/unittest-mkdir/mkdirs/'
        );
        $expected4 = './unittest-mkdir/mkdirs/';

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );
        $this->assertingEquals( $expected4, $actual4 );
    }


    /**
     * @covers Mumsys_FileSystem_Default::coolfilesize
     */
    public function testCoolfilesize()
    {
        $actual = array();
        $actual[] = $this->_object->coolfilesize( 1000, 2 );
        $actual[] = $this->_object->coolfilesize( 10000, 2 );
        $actual[] = $this->_object->coolfilesize( 10000000, 2 );
        $actual[] = $this->_object->coolfilesize( 10000000000, 2 );
        $actual[] = $this->_object->coolfilesize( 10000000000000, 2 );
        $actual[] = $this->_object->coolfilesize( 1000000000000000, 2 );
        $expected = array(
            '1000 Bytes',
            '10 KB',
            '10 MB',
            '10 GB',
            '10 TB',
            '1000 TB',
        );
        $this->assertingEquals( $expected, $actual );
    }

    // --- test abstract and versions


    /**
     * @covers Mumsys_FileSystem_Common_Abstract::extGet
     */
    public function testextGet()
    {
        $actual1 = $this->_object->extGet( 'filename.ext' );
        $actual2 = $this->_object->extGet( 'info' );

        $this->assertingEquals( 'ext', $actual1 );
        $this->assertingEquals( '', $actual2 );
    }


    /**
     * @covers Mumsys_FileSystem_Common_Abstract::nameGet
     */
    public function testnameGet()
    {
        $actual1 = $this->_object->nameGet( 'filename.ext' );
        $actual2 = $this->_object->nameGet( '/some/file/at/filename' );

        $this->assertingEquals( 'filename', $actual1 );
        $this->assertingEquals( 'filename', $actual2 );
    }


    /**
     * @covers Mumsys_Abstract::getVersions
     */
    public function testGetVersions()
    {
        $message = 'A new version exists. You should have a look at '
            . 'the code coverage to verify all code was tested and not only '
            . 'all existing tests where checked!';
        $this->assertingEquals(
            $this->_version, Mumsys_FileSystem_Default::VERSION, $message
        );

        $possible = $this->_object->getVersions();

        foreach ( $this->_versions as $must => $value ) {
            $message = 'Invalid: ' . $must . '::' . $value;
            $this->assertingTrue( isset( $possible[$must] ), $message );
            $this->assertingEquals( $possible[$must], $value, $message );
        }
    }

}
