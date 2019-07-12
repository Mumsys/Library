<?php


/**
 * Mumsys_Variable_Item_Abstract Test
 */
class Mumsys_Variable_Item_AbstractTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Variable_Item_Default
     */
    protected $_object;

    /**
     * Version ID
     * @var string
     */
    private $_version;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '1.2.1';

        $this->_config = array(
            'name' => 'somevariable',
            'value' => 'init value',

            'label' => 'somevariable',
            'desc' => 'some description',
            'type' => 'string', // string, array (list), email, numeric, float, integer, date, datetime
            'minlen' => 1,
            'maxlen' => 45,
            'allowEmpty' => false,
            'required' => true,
            'regex' => '/\w/',
            'default' => '',

            'errors' => array('invalidArgument' => 'Item property "invalid" can\'t be set for value "invalid key"'),
            'invalid' => 'invalid key',
            'filters' => array('onView' => array('trim')),
        );
        $this->_object = new Mumsys_Variable_Item_Default( $this->_config );
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        $this->_object = null;
    }

    /**
     * @covers Mumsys_Variable_Item_Abstract::__construct
     */
    public function test_construct()
    {
        // test filtering
        $config = array('filters' => array('onView' => array('trim') ) );
        $expected = array(
            'onView' => array(
                array('cmd' => 'trim', 'params' => null)
            )
        );

        $object = new Mumsys_Variable_Item_Default( $config );

        $this->assertEquals( $expected, $object->filtersGet( true ) );
    }

    /**
     * @covers Mumsys_Variable_Item_Abstract::__toString
     */
    public function test__toString()
    {
        $this->_object->setValue( 'unittest' );
        $this->assertEquals( $this->_object->getValue(), $this->_object );
    }


    /**
     * @covers Mumsys_Variable_Item_Abstract::getName
     * @covers Mumsys_Variable_Item_Abstract::setName
     */
    public function testGetSetName()
    {
        $this->_object->setName( 'somevariable2' );
        $this->assertEquals( 'somevariable2', $this->_object->getName() );

        $x = $this->_object->setName( 'somevariable2' );
        $this->assertNull( $x );
    }


    /**
     * @covers Mumsys_Variable_Item_Abstract::getValue
     * @covers Mumsys_Variable_Item_Abstract::setValue
     */
    public function testGetSetValue()
    {
        $this->_object->setValue( 'somevariable2' );
        $this->assertEquals( 'somevariable2', $this->_object->getValue() );

        $x = $this->_object->setValue( 'somevariable2' );
        $this->assertNull( $x );
    }


    /**
     * @covers Mumsys_Variable_Item_Abstract::getErrorMessages
     * @covers Mumsys_Variable_Item_Abstract::setErrorMessage
     * @covers Mumsys_Variable_Item_Abstract::setErrorMessages
     */
    public function testGetSetErrorMessages()
    {
        $expected = array('invalidArgument' => 'Item property "invalid" can\'t be set for value "invalid key"');
        $this->assertEquals( $expected, $this->_object->getErrorMessages() );

        $this->_object->setErrorMessage( 'k1', 'v1' );
        $this->_object->setErrorMessage( 'k2', 'v2' );

        $list = $this->_object->getErrorMessages();
        $this->_object->setErrorMessages( $list );

        $this->assertEquals( $list, $this->_object->getErrorMessages() );
    }

    /**
     * @covers Mumsys_Variable_Item_Abstract::isValid
     * @covers Mumsys_Variable_Item_Abstract::setValidated
     */
    public function testIsValid()
    {
        $this->assertFalse( $this->_object->isValid() );

        $this->_object->setValidated( true );
        $this->assertTrue( $this->_object->isValid() );

        $this->_object->setValidated( true );
        $this->assertTrue( $this->_object->isValid() );
    }


    /**
     * @covers Mumsys_Variable_Item_Abstract::filterAdd
     * @covers Mumsys_Variable_Item_Abstract::_filterSet
     * @covers Mumsys_Variable_Item_Abstract::filtersGet
     * @covers Mumsys_Variable_Item_Abstract::_initExternalCalls
     * @covers Mumsys_Variable_Item_Abstract::_initExternalType
     * @covers Mumsys_Variable_Item_Abstract::_setExternalCall
     */
    public function testFilterGetSetAdd()
    {
        // test with existing filters on init

        $expected1 = array(
            'onView' => array( array('cmd' => 'trim', 'params' => null)),
            'onSave' => array( array('cmd' => 'trim', 'params' => array('%value%'))),
            'onEdit' => array( array('cmd' => 'trim', 'params' => null)),
            'before' => array( array('cmd' => 'trim', 'params' => null)),
            'after' => array( array('cmd' => 'trim', 'params' => null)),
        );

        // altn. usage: $this->_object->filterAdd('onEdit', 'trim');
        $this->_config['filters'] += array(
            'onEdit' => 'trim',
            'before' => 'trim',
            'after' => 'trim',
        );
        $this->_object = new Mumsys_Variable_Item_Default( $this->_config );

        $this->_object->filterAdd( 'onSave', 'trim', array('%value%') );
        $actual1 = $this->_object->filtersGet( true );

        $this->_object->stateSet( 'onView' );
        $actual2 = $this->_object->filtersGet();

        $this->_object->stateSet( 'onSave' );
        $actual3 = $this->_object->filtersGet();

        $this->_object->stateSet( 'onEdit' );
        $actual4 = $this->_object->filtersGet();

        $this->_object->stateSet( 'before' );
        $actual5a = $this->_object->filtersGet();
        $actual5b = $this->_object->filtersGet( 'before' );

        $this->_object->stateSet( 'after' );
        $actual6a = $this->_object->filtersGet();
        $actual6b = $this->_object->filtersGet( 'after' );

        // test get() filters after init item with filters; for code coverage
        $this->_object = new Mumsys_Variable_Item_Default( $this->_config );
        $expected5 = $expected1;
        unset( $expected5['onSave'] );
        $actual5 = $this->_object->filtersGet( true );
        // for code coverage, get none existing filters
        $this->_object->stateSet( 'onSave' );
        $expected6 = array();
        $actual6 = $this->_object->filtersGet();

        // test get() without existing filters on init

        unset( $this->_config['filters'] );
        $this->_object = new Mumsys_Variable_Item_Default( $this->_config );
        $this->_object->stateSet( 'onSave' );
        $expected7 = array();
        $actual7 = $this->_object->filtersGet( true );

        $this->assertEquals( $expected1, $actual1 );
        $this->assertEquals( $expected1['onView'], $actual2 );
        $this->assertEquals( $expected1['onSave'], $actual3 );
        $this->assertEquals( $expected1['onEdit'], $actual4 );
        $this->assertEquals( $expected1['before'], $actual5a );
        $this->assertEquals( $expected1['before'], $actual5b );
        $this->assertEquals( $expected1['after'], $actual6a );
        $this->assertEquals( $expected1['after'], $actual6b );
        $this->assertEquals( $expected5, $actual5 );
        $this->assertEquals( $expected6, $actual6 );
        $this->assertEquals( $expected7, $actual7 );
    }


    /**
     * @covers Mumsys_Variable_Item_Abstract::callbackAdd
     * @covers Mumsys_Variable_Item_Abstract::_callbackSet
     * @covers Mumsys_Variable_Item_Abstract::callbacksGet
     * @covers Mumsys_Variable_Item_Abstract::_initExternalCalls
     * @covers Mumsys_Variable_Item_Abstract::_initExternalType
     * @covers Mumsys_Variable_Item_Abstract::_setExternalCall
     */
    public function testCallbackGetSetAdd()
    {
        // test with existing filters on init

        $expected1 = array(
            'onView' => array(
                array('cmd' => 'trim', 'params' => null ),
                array('cmd' => 'trim', 'params' => array('%value%') )
            ),
            'onSave' => array( array('cmd' => 'trim', 'params' => array('%value%'))),
            'onEdit' => array( array('cmd' => 'trim', 'params' => null)),
            'before' => array( array('cmd' => 'is_int', 'params' => null)),
            'after' => array( array('cmd' => 'is_string', 'params' => null)),
        );

        unset( $this->_config['filters'] );
        $this->_config['callbacks'] = array(
            'onEdit' => 'trim',
            'onView' => array('trim'),
            'onSave' => array('trim' => array('%value%')),
            'before' => array('is_int'),
            'after' => array('is_string'),
        );

        $this->_object = new Mumsys_Variable_Item_Default( $this->_config );
        $this->_object->callbackAdd( 'onView', 'trim', array('%value%') );
        $actual1 = $this->_object->callbacksGet( true );

        $this->_object->stateSet( 'onView' );
        $actual2 = $this->_object->callbacksGet();

        $this->_object->stateSet( 'onSave' );
        $actual3 = $this->_object->callbacksGet();

        $this->_object->stateSet( 'onEdit' );
        $actual4 = $this->_object->callbacksGet();

        $this->_object->stateSet( 'before' );
        $actual5a = $this->_object->callbacksGet();
        $actual5b = $this->_object->callbacksGet( 'before' );

        $this->_object->stateSet( 'after' );
        $actual6a = $this->_object->callbacksGet();
        $actual6b = $this->_object->callbacksGet( 'after' );

        // test get() filters after init item with filters; for code coverage
        $this->_object = new Mumsys_Variable_Item_Default( $this->_config );
        // for code coverage, get none existing filters
        $this->_object->stateSet( 'onSave' );
        $expected6 = array( array('cmd' => 'trim', 'params' => array('%value%')));
        $actual6 = $this->_object->callbacksGet();

        // test get() without existing callbacks on init

        unset( $this->_config['callbacks'] );
        $this->_object = new Mumsys_Variable_Item_Default( $this->_config );
        $this->_object->stateSet( 'onSave' );
        $expected7 = array();
        $actual7 = $this->_object->callbacksGet();

        $this->assertEquals( $expected1, $actual1 );
        $this->assertEquals( $expected1['onView'], $actual2 );
        $this->assertEquals( $expected1['onSave'], $actual3 );
        $this->assertEquals( $expected1['onEdit'], $actual4 );
        $this->assertEquals( $expected1['before'], $actual5a );
        $this->assertEquals( $expected1['after'], $actual6a );
        $this->assertEquals( $expected1['after'], $actual6b );

        $this->assertEquals( $expected6, $actual6 );
        $this->assertEquals( $expected7, $actual7 );
    }

    /**
     * @covers Mumsys_Variable_Item_Abstract::isModified
     * @covers Mumsys_Variable_Item_Abstract::setModified
     */
    public function testIsSetModified()
    {
        $this->_object->setModified();
        $this->assertTrue( $this->_object->isModified() );
    }

    /**
     * @covers Mumsys_Variable_Item_Abstract::stateGet
     * @covers Mumsys_Variable_Item_Abstract::stateSet
     * @covers Mumsys_Variable_Item_Abstract::_stateCheck
     */
    public function testStateGetSet()
    {
        $this->_object->stateSet( 'onEdit' );
        $this->assertEquals( 'onEdit', $this->_object->stateGet() );

        $this->expectExceptionMessageRegExp( '/(State "xxx" unknown)/i' );
        $this->expectException( 'Mumsys_Variable_Item_Exception' );
        $this->_object->stateSet( 'xxx' );
    }

    /**
     * Plural version
     * @covers Mumsys_Variable_Item_Abstract::statesGet
     */
    public function testStatesGet()
    {
        $expected = array('onEdit','onView', 'onSave', 'before', 'after');
        $this->assertEquals( $expected, $this->_object->statesGet() );
    }

    /**
     * Version check
     */
    public function testCheckVersion()
    {
        $message = 'A new version exists. You should have a look at '
            . 'the code coverage to verify all code was tested and not only '
            . 'all existing tests where checked!';
        $this->assertEquals( $this->_version, Mumsys_Variable_Item_Abstract::VERSION, $message );
    }

}
