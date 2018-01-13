<?php


/**
 * Mumsys_Session_Default Test
 */
class Mumsys_Session_DefaultTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Session_Default
     */
    protected $_object;

    /**
     * Version ID string
     * @var string
     */
    protected $_version;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_version = '1.1.0';
        $this->_object = new Mumsys_Session_Default();
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->_object->clear();
        $this->_object = NULL;
        session_write_close();
    }


    /**
     * Test nearly all methodes because of the problematic of php sessions
     * itselves to test them.
     * @runInSeparateProcess
     */
    public function testAllMethodes()
    {
        $this->_object = new Mumsys_Session_Default();

        // for code coverage
        $this->_object = new Mumsys_Session_Default();

        // for code coverage
        $this->_object->clear();
        $actual6 = $this->_object->getAll();
        $expected6 = array();

        // test setter
        $this->_object->replace('testkey', array('val1', 'val2'));

        $actual1 = $this->_object->get('testkey');
        $expected1 = array('val1', 'val2');

        $actual2 = $this->_object->getAll();
        $this->_object->__destruct();
        $expected2 = $_SESSION;

        $actual3 = $this->_object->getID();
        $expected3 = key($_SESSION);

        $actual5 = $this->_object->getCurrent();
        $expected5 = $expected2[$expected3];

        $this->_object->register('newkey', array('val5', 'val6'));
        $actual4 = $this->_object->get('newkey');
        $expected4 = array('val5', 'val6');
        // test default return
        $actual7 = $this->_object->get('notsetbefor', 'dingding');
        $expected7 = 'dingding';

        $actual8 = $this->_object->remove('notsetbefor');
        $actual9 = $this->_object->remove('newkey');

        // get
        $this->assertEquals($expected1, $actual1);
        // __destruct
        $this->assertEquals($expected2, $actual2);
        // getID
        $this->assertEquals($expected3, $actual3);
        // register
        $this->assertEquals($expected4, $actual4);
        // getCurrent
        $this->assertEquals($expected5, $actual5);
        // clear
        $this->assertEquals($expected6, $actual6);
        // test default return
        $this->assertEquals($expected7, $actual7);
        // removed but wasnt set before
        $this->assertFalse($actual8);
        $this->assertTrue($actual9);

        // version checks
        $this->assertEquals($this->_version, $this->_object->getVersionID());

        // test register existing
        $this->expectExceptionMessageRegExp('/(Session key "testkey" exists)/i');
        $this->expectException('Mumsys_Session_Exception');
        $this->_object->register('testkey', array('val5', 'val6'));
    }

}
