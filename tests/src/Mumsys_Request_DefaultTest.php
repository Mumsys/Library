<?php

/**
 * Mumsys_Request_Default Test
 */
class Mumsys_Request_DefaultTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Request_Default
     */
    private $_object;

    /**
     * @var array
     */
    private $_inputGet;

    /**
     * @var array
     */
    private $_inputPost;

    /**
     * @var string
     */
    private $_version;

    /**
     * @var array
     */
    private $_versions;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '1.1.0';
        $this->_versions = array(
            'Mumsys_Request_Abstract' => '1.0.1',
            'Mumsys_Request_Default' => '1.1.0',
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
        );

        $options = array(
            'programKey' => 'programTest',
            'controllerKey' => 'controllerTest',
            'actionKey' => 'actionTest'
        );

        $_GET['programTest'] = 'prg';
        $_GET['controllerTest'] = 'ctrl';
        $_GET['actionTest'] = 'act';
        $this->_inputGet = $_GET;

        $_POST['programTest'] = 'prg';
        $_POST['controllerTest'] = 'ctrl';
        $_POST['actionTest'] = 'act';
        $this->_inputPost = $_POST;

        $_COOKIE = array('HIDDEN' => 'COOK');

        $this->_object = new Mumsys_Request_Default( $options );
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
     * Just for code coverage
     * @covers Mumsys_Request_Default::__construct
     */
    public function test_construct()
    {
        $options = array(
            'programKey' => 'programTest',
            'controllerKey' => 'controllerTest',
            'actionKey' => 'actionTest'
        );

        $_GET['programTest'] = 'prg';
        $_GET['controllerTest'] = 'ctrl';
        $_GET['actionTest'] = 'act';

        $_COOKIE = array('HIDDEN' => 'COOK');

        $this->_object = new Mumsys_Request_Default( $options );
        $this->assertingInstanceOf( 'Mumsys_Request_Default', $this->_object );
    }


    /**
     * @covers Mumsys_Request_Default::getInputPost
     * @covers Mumsys_Request_Default::setInputPost
     */
    public function testGetSetInputPost()
    {
        $this->_object->setInputPost( 'programTest', 'prg' );
        $this->_object->setInputPost( 'notExists', '123' );
        $this->_object->setInputPost( 'notExists', null );// 4 CC

        $actual1 = $this->_object->getInputPost();
        $actual2 = $this->_object->getInputPost( 'programTest' );
        $actual3 = $this->_object->getInputPost( 'notExists', 123 );

        $this->assertingEquals( $this->_inputPost, $actual1 );
        $this->assertingEquals( $this->_inputPost['programTest'], $actual2 );
        $this->assertingEquals( '123', $actual3 );
    }


    /**
     * @covers Mumsys_Request_Default::getInputGet
     * @covers Mumsys_Request_Default::setInputGet
     */
    public function testGetSetInputGet()
    {
        $this->_object->setInputGet( 'programTest', 'prg' );
        $this->_object->setInputGet( 'notExists', '123' );
        $this->_object->setInputGet( 'notExists', null );// 4 CC

        $actual1 = $this->_object->getInputGet();
        $actual2 = $this->_object->getInputGet( 'programTest' );
        $actual3 = $this->_object->getInputGet( 'notExists', 123 );

        $this->assertingEquals( $this->_inputGet, $actual1 );
        $this->assertingEquals( $this->_inputGet['programTest'], $actual2 );
        $this->assertingEquals( '123', $actual3 );
    }

    public function testVersions()
    {
         $this->assertingEquals( $this->_version, Mumsys_Request_Default::VERSION );

         $this->checkVersionList( $this->_object->getVersions(), $this->_versions );
    }
}
