<?php


/**
 * Test class for Mumsys_Array2Xml.
 * $Id: Mumsys_Array2XmlTest.php 3254 2016-02-09 20:57:53Z flobee $
 */
class MumsysDependencyChecks
    extends Mumsys_Unittest_Testcase
{
    /**
     * List of required extensions.
     * @var array
     */
    private $_requiredExtensions = array();


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_requiredExtensions = array(
            'json',
            'iconv',
            'mbstring'
        );
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }


    public function test_CheckPhpExtensionsLoeaded()
    {
        foreach ( $this->_requiredExtensions as $ext ) {
            $mesg = sprintf( '"%1$s" extension not installed/ found', $ext );
            $this->assertTrue( extension_loaded( $ext ), $mesg );
        }
    }

}