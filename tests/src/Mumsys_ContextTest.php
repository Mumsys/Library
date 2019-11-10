<?php

/**
 * Mumsys_Context Test
 */
class Mumsys_ContextTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Context
     */
    protected $_object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '2.0.0';
        $this->_versions = array(
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
            'Mumsys_Context' => $this->_version,
        );
        $this->_logfile = '/tmp/' . basename( __FILE__ ) . '.log';
        $this->_object = new Mumsys_Context();
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        @unlink( $this->_logfile );
    }


    /**
     * Test abstract class
     *
     * @covers Mumsys_Context::getVersion
     * @covers Mumsys_Context::getVersionID
     * @covers Mumsys_Context::getVersions
     */
    public function testGetVersion()
    {
        $this->assertEquals( 'Mumsys_Context ' . $this->_version, $this->_object->getVersion() );
        $this->assertEquals( $this->_version, $this->_object->getVersionID() );

        $possible = $this->_object->getVersions();

        foreach ( $this->_versions as $must => $value ) {
            $this->assertTrue( isset( $possible[$must] ) );
            $this->assertTrue( ( $possible[$must] == $value ) );
        }
    }

}
