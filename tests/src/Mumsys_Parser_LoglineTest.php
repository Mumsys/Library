<?php


/**
 * Test class for Mumsys_Parser_Logline
 */
class Mumsys_Parser_LoglineTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Parser_Logline
     */
    protected $_object;
    protected $_format;
    protected $_patterns;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_logContent = '1;whichhas;semicolon;as;delimiter;opt' . PHP_EOL
            . '2;which has;semicolon;as;delimiter;opt' . PHP_EOL
            . '33;"which has";"semicolon";"as";"delimiter";' . PHP_EOL
            . '' . PHP_EOL
            . '5;"which has";"semicolon";"as";"d";"wont work because of the quotes"' . PHP_EOL
        ;

        $this->_format = 'id;c1;c2;c3;c4;c5';
        $this->_patterns = array(
            'id' => '(?P<id>\w+)', // match number
            'c1' => '(?P<col_1>.+)',
            'c2' => '(?P<col_2>.+)',
            'c3' => '(?P<col_3>.+)',
            'c4' => '(?P<col_4>.+)',
            'c5' => '(?P<col_5>\w*)', // optional
        );
        $this->_object = new Mumsys_Parser_Logline($this->_format, $this->_patterns);
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->_object = NULL;
        $this->_format = '';
        $this->_patterns = '';
        $this->_logContent = '';
    }


    /**
     * @covers Mumsys_Parser_Logline::__construct
     * @covers Mumsys_Parser_Logline::setFormat
     */
    public function test__construct()
    {
        // default loglines
        $object = new Mumsys_Parser_Logline();
        // own parameters
        $this->_format = 'id;c1;c2;c3;c4;c5';
        $this->_patterns = array(
            'id' => '(?P<id>\w+)', // match number dosnt work :-?
            'c1' => '(?P<col_1>.+)',
            'c2' => '(?P<col_2>.+)',
            'c3' => '(?P<col_3>.+)',
            'c4' => '(?P<col_4>.+)',
            'c5' => '(?P<col_5>\w*)', // optional
        );
        $object = new Mumsys_Parser_Logline($this->_format, $this->_patterns);
    }


    /**
     * @covers Mumsys_Parser_Logline::setFormat
     */
    public function testSetFormat()
    {
        $this->_object->setFormat($this->_format);
    }


    /**
     * @covers Mumsys_Parser_Logline::setPattern
     */
    public function testSetPattern()
    {
        $this->_object->setPattern('c5', '(?P<col_5>(\w*))');
    }


    /**
     * @covers Mumsys_Parser_Logline::setHideFilterResults
     */
    public function testSetHideFilterResults()
    {
        $this->_object->setHideFilterResults();
    }


    /**
     * @covers Mumsys_Parser_Logline::setShowFilterResults
     */
    public function testSetShowFilterResults()
    {
        $this->_object->setShowFilterResults();
    }


    /**
     * @covers Mumsys_Parser_Logline::setFilterCondition
     */
    public function testSetFilterCondition()
    {
        $this->_object->setFilterCondition('AND');
        $this->_object->setFilterCondition('OR');

        $this->setExpectedExceptionRegExp('Mumsys_Parser_Exception', '/(Invalid filter condition)/');
        $this->_object->setFilterCondition('FAILT');
    }


    /**
     * @covers Mumsys_Parser_Logline::addFilter
     */
    public function testAddFilter()
    {
        $this->_object->addFilter('c5', 'delim', true);
        $this->_object->addFilter('c5', array('delim', 'del'), false);
    }


    /**
     * @covers Mumsys_Parser_Logline::parse
     * @covers Mumsys_Parser_Logline::_getExpression
     * @covers Mumsys_Parser_Logline::_applyFilters
     */
    public function testParse()
    {
        $records = explode(PHP_EOL, $this->_logContent);

        $actual1 = $this->_object->parse ('id;c1;c2;c3;c4;c5'); //trim($records[2])
        $expected1 = array(
            'id' => 'id',
            'col_1' => 'c1',
            'col_2' => 'c2',
            'col_3' => 'c3',
            'col_4' => 'c4',
            'col_5' => 'c5'
        );
        // default
        $actual2 = $this->_object->parse(trim($records[0]));
        $expected2 = array(
            'id' => '1',
            'col_1' => 'whichhas',
            'col_2' => 'semicolon',
            'col_3' => 'as',
            'col_4' => 'delimiter',
            'col_5' => 'opt'
        );
        // space in "which has"
        $actual3 = $this->_object->parse(trim($records[1]));
        $expected3 = array(
            'id' => '2',
            'col_1' => 'which has',
            'col_2' => 'semicolon',
            'col_3' => 'as',
            'col_4' => 'delimiter',
            'col_5' => 'opt'
        );
        // empty "opt",
        $actual4 = $this->_object->parse(trim($records[2]));
        $expected4 = array(
            'id' => '33',
            'col_1' => '"which has"',
            'col_2' => '"semicolon"',
            'col_3' => '"as"',
            'col_4' => '"delimiter"',
            'col_5' => ''
        );
        // empty line
        $actual5 = $this->_object->parse($records[3]);

        $this->assertEquals($actual1, $expected1);
        $this->assertEquals($actual2, $expected2);
        $this->assertEquals($actual3, $expected3);
        $this->assertEquals($actual4, $expected4);
        $this->assertFalse($actual5);

        // crap in "opt"
        $this->setExpectedExceptionRegExp(
            'Mumsys_Parser_Exception',
            '/('
            . 'Format of log line invalid \(expected:"#\^id;c1;c2;c3;c4;c5\$#"\); '
            . 'Line was "5;"which has";"semicolon";"as";'
            . '"d";"wont work because of the quotes""; regex: '
//            . '"#^(?P<id>\w+);(?P<col_1>.+);(?P<col_2>.+);(?P<col_3>.+)'
//            . ';(?P<col_4>.+);(?P<col_5>\w*)$#"'
            . ')/'
        );
        $actual5 = $this->_object->parse(trim($records[4]));
    }

    /**
     * @covers Mumsys_Parser_Logline::parse
     */
    public function testParseTimestampFeature() {
        // test timestamp feature
        $line = '999;2016-01-17 07:35:14';
        $format = 'id;timeIn';
        $this->_object->setPattern('id', '(?P<id>\w+)');
        // outgoing "time" is relevant for a match to generate the "stamp" key
        $this->_object->setPattern('timeIn', '(?P<time>.+)');

        $this->_object->setFormat($format);

        $actual1 = $this->_object->parse($line);
        $expected1 = array(
            'id' => '999',
            'time' => '2016-01-17 07:35:14',
            'stamp' => '1453012514',
            );

        $this->assertEquals($actual1, $expected1);
    }

    /**
     * @covers Mumsys_Parser_Logline::_applyFilters
     */
    public function testParseApplyFilters()
    {
        $line = '999;2016-01-17 07:35:14';

        // test timestamp feature
        $format = 'id;timeIn';
        $this->_object->addFilter('time', '2016', false);

        $this->_object->setPattern('id', '(?P<id>\w+)');
        $this->_object->setPattern('timeIn', '(?P<time>.+)');
        $this->_object->setFormat($format);

        $actual1 = $this->_object->parse($line);
        $expected1 = array(
            'id' => '999',
            'time' => '2016-01-17 07:35:14',
            'stamp' => '1453012514',
        );

        // for codecoveage
        $this->_object->setFilterCondition('OR');
        $actual2 = $this->_object->parse($line);
        $expected2 = array(
            'id' => '999',
            'time' => '2016-01-17 07:35:14',
            'stamp' => '1453012514',
        );

        // strict filter
        $object = new Mumsys_Parser_Logline();
        $object->setPattern('id', '(?P<id>\w+)');
        $object->setPattern('timeIn', '(?P<time>.+)');
        $object->setFormat($format);
        $object->addFilter('id', 'abc', true);
        $actual3 = $object->parse('ABC;2016-01-17 07:35:14');
        $expected3 = array(
            // should not match
        );

        // hide matches (filteredHide)
        //

        $this->assertEquals($actual1, $expected1);
        $this->assertEquals($actual2, $expected2);
        $this->assertEquals($actual3, $expected3);
    }

}
