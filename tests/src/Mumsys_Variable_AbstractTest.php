<?php


/**
 * Mumsys_Variable_Abstract Test
 */
class Mumsys_Variable_AbstractTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Variable_Abstract
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
        $this->_version = '1.2.2';
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
     * @covers Mumsys_Variable_Abstract::getTypes
     */
    public function testCheckConstants()
    {
        $expected = array(
            'string', 'char', 'varchar', 'text', 'tinytext', 'longtext',
            'int', 'integer', 'smallint',
            'float', 'double',
            'numeric',
            'boolean', 'array', 'object',
            'date',
            'datetime', 'timestamp',
            'email',
            'ipv4', 'ipv6',
            'unixtime',
            'unittest',
        );

        $actual = Mumsys_Variable_Abstract::getTypes();
        $this->assertingEquals( $expected, $actual );
        $this->assertingEquals( count( $expected ), count( $actual ) );
        $this->assertingEquals( $this->_version, Mumsys_Variable_Abstract::VERSION );
    }

}
