<?php


/**
 * Mumsys_Request_Abstract test
 */
class Mumsys_Request_AbstractTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Request_Abstract
     */
    protected $_object;

    /**
     * @var array
     */
    protected $_input;
    /**
     * @var array
     */
    protected $_options;

    /**
     * @var string
     */
    protected $_version;

    /**
     * @var array
     */
    protected $_versions;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '1.0.1';
        $this->_versions = array(
            'Mumsys_Request_Abstract' => '1.0.1',
            'Mumsys_Request_Default' => '1.1.0',
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
        );

        $this->_options = array(
            'programKey' => 'programTest',
            'controllerKey' => 'controllerTest',
            'actionKey' => 'actionTest'
        );

        $_GET['programTest'] = 'prg';
        $_GET['controllerTest'] = 'ctrl';
        $_GET['actionTest'] = 'act';

        $this->_input = $_GET;
        $_COOKIE = array('HIDDEN' => 'COOK');

        $this->_object = new Mumsys_Request_Default( $this->_options );
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        $_COOKIE = array();
        unset( $this->_object );
    }

    /**
     * @covers Mumsys_Request_Abstract::__construct
     */
    public function test_Construct()
    {
        $this->_object = new Mumsys_Request_Default( $this->_options );
        $this->assertingEquals( 'programTest', $this->_object->getProgramKey() );
        $this->assertingEquals( 'controllerTest', $this->_object->getControllerKey() );
        $this->assertingEquals( 'actionTest', $this->_object->getActionKey() );
    }

    /**
     * @covers Mumsys_Request_Abstract::getRequestId
     */
    public function testGetRequestId()
    {
        $actual1 = $this->_object->getRequestId();

        $this->assertingTrue( ( strlen( $actual1 )===23 ) );
        $this->assertingEquals( $actual1, $this->_object->getRequestId() );
    }

    /**
     * @covers Mumsys_Request_Abstract::getRequestId
     * @runInSeparateProcess
     */
    public function testGetRequestIdWithUuid5()
    {
        $uuid5 = 'e129f27c-5103-5c5c-844b-cdf0a15e160d-err';
        $_SERVER['HTTP_X_REQUEST_ID'] = $uuid5;
        $actual1 = $this->_object->getRequestId();

        $this->assertingNotEquals( $uuid5, $actual1 );
    }

    /**
     * @covers Mumsys_Request_Abstract::getProgramName
     * @covers Mumsys_Request_Abstract::setProgramName
     */
    public function testGetSetProgramName()
    {
        $actual1 = $this->_object->getProgramName();
        $actual2 = $this->_object->setProgramName( 'test' );
        $actual3 = $this->_object->getProgramName();

        $this->assertingEquals( 'prg', $actual1 );
        $this->assertingInstanceOf( 'Mumsys_Request_Default', $actual2 );
        $this->assertingEquals( 'Test', $actual3 );
    }


    /**
     * @covers Mumsys_Request_Abstract::setControllerName
     * @covers Mumsys_Request_Abstract::getControllerName
     */
    public function testGetSetControllerName()
    {
        $actual1 = $this->_object->getControllerName();
        $actual2 = $this->_object->setControllerName( 'lower' );
        $actual3 = $this->_object->getControllerName();

        $this->assertingEquals( 'ctrl', $actual1 );
        $this->assertingInstanceOf( 'Mumsys_Request_Default', $actual2 );
        $this->assertingEquals( 'Lower', $actual3 );
    }


    /**
     * @covers Mumsys_Request_Abstract::getActionName
     * @covers Mumsys_Request_Abstract::setActionName
     */
    public function testGetSetActionName()
    {
        $actual1 = $this->_object->getActionName();
        $actual2 = $this->_object->setActionName( 'AcTiOnNaMe' );
        $actual3 = $this->_object->getActionName();
        $actual4 = $this->_object->setActionName( null );

        $this->assertingEquals( 'act', $actual1 );
        $this->assertingInstanceOf( 'Mumsys_Request_Default', $actual2 );
        $this->assertingEquals( 'actionname', $actual3 );
        $this->assertingInstanceOf( 'Mumsys_Request_Default', $actual4 );
    }


    /**
     * @covers Mumsys_Request_Abstract::getProgramKey
     * @covers Mumsys_Request_Abstract::setProgramKey
     */
    public function testGetSetProgramKey()
    {
        $actual1 = $this->_object->getProgramKey();
        $this->assertingEquals( 'programTest', $actual1 );
    }


    /**
     * @covers Mumsys_Request_Abstract::setControllerKey
     * @covers Mumsys_Request_Abstract::getControllerKey
     */
    public function testGetSetControllerKey()
    {
        $actual1 = $this->_object->setControllerKey( 'cntl' );
        $actual2 = $this->_object->getControllerKey();

        $this->assertingInstanceOf( 'Mumsys_Request_Default', $actual1 );
        $this->assertingEquals( 'cntl', $actual2 );
    }


    /**
     * @covers Mumsys_Request_Abstract::getActionKey
     * @covers Mumsys_Request_Abstract::setActionKey
     */
    public function testGetSetActionKey()
    {
        $actual = $this->_object->getActionKey();
        $this->_object->setActionKey( 'index' );

        $this->assertingEquals( 'actionTest', $actual );
        $this->assertingEquals( 'index', $this->_object->getActionKey() );
    }


    /**
     * @covers Mumsys_Request_Abstract::getParam
     * @covers Mumsys_Request_Abstract::setParam
     */
    public function testGetSetParam()
    {
        $actual1 = $this->_object->setParam( 'a', '111' );
        $actual2 = $this->_object->setParam( 'a', null );
        $actual3 = $this->_object->getParams();
        $actual4 = $this->_object->getParam( 'programTest', false );
        $actual5 = $this->_object->getParam( 'notSet', false );
        $expected3 = $this->_input;

        $this->assertingInstanceOf( 'Mumsys_Request_Default', $actual1 );
        $this->assertingEquals( $expected3, $actual3 );
        $this->assertingEquals( 'prg', $actual4 );
        $this->assertingFalse( $actual5 );
    }


    /**
     * Plural version.
     * @covers Mumsys_Request_Abstract::getParams
     * @covers Mumsys_Request_Abstract::setParams
     */
    public function testGetSetParams()
    {
        $params = array('abc' => 1, 'def' => 2, 'ghi' => 3, 'null' => null);
        $actual1 = $this->_object->setParams( $params );
        $actual2 = $this->_object->getParams();
        $expected = $params + array(
            'programTest' => 'prg', 'controllerTest' => 'ctrl', 'actionTest' => 'act'
        );
        unset( $expected['null'] );

        $this->assertingInstanceOf( 'Mumsys_Request_Default', $actual1 );
        $this->assertingEquals( $expected, $actual2 );
    }


    /**
     * @covers Mumsys_Request_Abstract::clearParams
     */
    public function testClearParams()
    {
        $this->_object->clearParams();
        $this->assertingEquals( array(), $this->_object->getParams() );
    }


    /**
     * @covers Mumsys_Request_Abstract::getInputCookie
     */
    public function testGetCookie()
    {
        $actual1 = $this->_object->getInputCookie( 'HIDDEN', false );
        $actual2 = $this->_object->getInputCookie( null );
        $actual3 = $this->_object->getInputCookie( 'notSet', false );

        $this->assertingEquals( 'COOK', $actual1 );
        $this->assertingEquals( $_COOKIE, $actual2 );
        $this->assertingFalse( $actual3 );
    }


    public function testVersions()
    {
         $this->assertingEquals( $this->_version, Mumsys_Request_Abstract::VERSION );

         $this->checkVersionList( $this->_object->getVersions(), $this->_versions );
    }

}
