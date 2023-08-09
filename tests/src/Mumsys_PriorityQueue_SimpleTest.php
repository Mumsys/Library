<?php

/**
 * Mumsys_PriorityQueue_Simple Test
 */
class Mumsys_PriorityQueue_SimpleTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_PriorityQueue_Simple
     */
    private $_object;
    private $_defaults;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_defaults = array('default' => array(1, 2, 3));
        $this->_object = new Mumsys_PriorityQueue_Simple( $this->_defaults );
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
     * @covers Mumsys_PriorityQueue_Simple::__construct
     * @covers Mumsys_PriorityQueue_Simple::add
     * @covers Mumsys_PriorityQueue_Simple::getQueue
     */
    public function testAddSimple()
    {
        $this->_object->add( 'AAA', 'AAA' );
        $this->_object->add( 'BBB', 'BBB' );
        $this->_object->add( 'just adding items to an array', 'just adding items to an array' );

        $expected = array(
            'default' => array(1, 2, 3),
            'AAA' => 'AAA',
            'BBB' => 'BBB',
            'just adding items to an array' => 'just adding items to an array',
        );

        $this->assertingEquals( $expected, $this->_object->getQueue() );
    }

    /**
     * @covers Mumsys_PriorityQueue_Simple::__construct
     * @covers Mumsys_PriorityQueue_Simple::add
     * @covers Mumsys_PriorityQueue_Simple::getQueue
     */
    public function testAddSimpleExeption()
    {
        $this->_object->add( 'AAA', 'AAA' );

        $this->expectingExceptionMessageRegex( '/(Identifier "AAA" already set)/i' );
        $this->expectingException( 'Mumsys_PriorityQueue_Exception' );
        $this->_object->add( 'AAA', 'AAA' );
    }

    /**
     * @covers Mumsys_PriorityQueue_Simple::add
     * @covers Mumsys_PriorityQueue_Simple::_getPos
     * @covers Mumsys_PriorityQueue_Simple::getQueue
     */
    public function testAddExtended()
    {
        $this->_object->add( 'AAA', '111' );
        $this->_object->add( 'BBB', '222' );
        $this->_object->add( 'CCC', '333', 'before', 'AAA' );
        $this->_object->add( 'DDD', '444', 'before', 'BBB' );
        $this->_object->add( 'EEE', '555', 'after', 'BBB' );
        $this->_object->add( 'cool man', 'cool man' );

        $expected = array(
            'default' => array(1, 2, 3),
            'CCC' => '333',
            'AAA' => '111',
            'DDD' => '444',
            'BBB' => '222',
            'EEE' => '555',
            'cool man' => 'cool man',
        );

        $this->assertingEquals( $expected, $this->_object->getQueue() );
    }

    /**
     * @covers Mumsys_PriorityQueue_Simple::__construct
     * @covers Mumsys_PriorityQueue_Simple::add
     * @covers Mumsys_PriorityQueue_Simple::_getPos
     */
    public function testAddExtendedExeption()
    {
        $this->expectingExceptionMessageRegex( '/(Position way "WayNotExists" not implemented)/i' );
        $this->expectingException( 'Mumsys_PriorityQueue_Exception' );
        $this->_object->add( 'AAA', 'AAA', 'WayNotExists', 'default' );
    }

    /**
     * @covers Mumsys_PriorityQueue_Simple::getQueue
     */
    public function testGetQueue()
    {
        $this->assertingEquals( $this->_defaults, $this->_object->getQueue() );
    }

}
