<?php

/**
 * Test class for Mumsys_Lock.
 */
class Mumsys_LockTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Lock
     */
    private $_object;
    private $_version;
    private $_versions;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '3.1.0';
        $this->_versions = array(
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
            'Mumsys_Lock' => $this->_version,
        );
        $this->_object = new Mumsys_Lock();
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        if ( is_object( $this->_object ) ) {
            $this->_object->unlock();
        }
        unset( $this->_object );
    }


    public function testLock()
    {
        $this->assertingTrue( $this->_object->lock() );
    }


    public function testLockException1()
    {
        $this->_object->lock();
        $regex = '/(Can not lock! Lock "\/tmp\/Mumsys_Lock.php_default.lock" '
            . 'exists)/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Exception' );
        $this->_object->lock();
    }


    // test file with different owner rights
    public function testLockException2()
    {
        $this->_object = new Mumsys_Lock( '/root/nix.tmp' );
        $regex = '/(Locking failt for file "\/root\/nix.tmp")/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Exception' );

        $this->_object->lock();
    }


    public function testUnlock()
    {
        $this->_object->lock();
        $this->assertingTrue( $this->_object->unlock() );
    }


    public function testUnlockException()
    {
        $tmpFile = '/tmp/.ICE-unix';
        $o = new Mumsys_Lock( $tmpFile ); //file with different owner
        $regex = '/(Unlock failt for: "'
            . str_replace( '/', '\/', $tmpFile )
            . '")/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Exception' );
        $o->unlock();
    }


    public function testIsLocked()
    {
        $this->_object->lock();
        $this->assertingTrue( $this->_object->isLocked() );
        $this->_object->unlock();
        $this->assertingFalse( $this->_object->isLocked() );
    }


    public function testIsLocked2()
    {
        $tmpFile = '/tmp/where/the/hell/are/you';
        $o = new Mumsys_Lock( $tmpFile );
        $regex = '#(Lock directory "/tmp/where/the/hell/are/you" not exists)#i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Exception' );
        $o->isLocked();
    }

    // test abstracts


    /**
     * @covers Mumsys_Lock::getVersion
     */
    public function testGetVersion()
    {
        $this->assertingEquals(
            'Mumsys_Lock ' . $this->_version, $this->_object->getVersion()
        );
    }


    /**
     * @covers Mumsys_Lock::getVersionID
     */
    public function testgetVersionID()
    {
        $this->assertingEquals( $this->_version, $this->_object->getVersionID() );
    }


    /**
     * @covers Mumsys_Lock::getVersions
     */
    public function testGetVersions()
    {
        $possible = $this->_object->getVersions();

        foreach ( $this->_versions as $must => $value ) {
            $this->assertingTrue( isset( $possible[$must] ) );
            $this->assertingTrue( ( $possible[$must] == $value ) );
        }
    }

}
