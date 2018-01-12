<?php

/**
 * Mumsys_Db_Driver_Mysql_Mysqli_Result Test
 */
class Mumsys_Db_Driver_Mysql_Mysqli_ResultTest extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Db_Driver_Mysql_Mysqli_Result
     */
    protected $_object;
    protected $_dbConfig;

    /** @var Mumsys_Db_Driver_Mysql_Mysqli */
    protected $_dbDriver;
    /**
     * @var Mumsys_Context
     */
    protected $_context;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_context = new Mumsys_Context();

        $this->_configs = $this->_config = MumsysTestHelper::getConfigs();
        $this->_configs['database']['type'] = 'mysql:mysqli';

        $this->_dbConfig = $this->_configs['database'];

        try {
            $oDB = Mumsys_Db_Factory::getInstance($this->_context, $this->_configs['database']);
            $oDB->connect();
        } catch (Exception $ex) {
            $this->markTestSkipped('Connection failure. Check DB config to connect to the db');
        }

        $this->_dbDriver = new Mumsys_Db_Driver_Mysql_Mysqli($this->_context, $this->_dbConfig);

        $this->_object = $this->_dbDriver->query('SELECT 1+1 AS colname');
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->_dbDriver->close();
        $this->_object = null;
    }


    public function testConstruct()
    {
        $actual1 = new Mumsys_Db_Driver_Mysql_Mysqli($this->_context, $this->_dbConfig);
        $actual2 = $this->_dbDriver->query('SELECT 1+1 AS colname');

        $this->assertInstanceOf('Mumsys_Db_Driver_Mysql_Mysqli', $actual1);
        $this->assertInstanceOf('Mumsys_Db_Driver_Mysql_Mysqli_Result', $actual2);
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli_Result::fetch
     */
    public function testFetch()
    {
        $obj = new stdClass;
        $obj->colname = 2;
        $tests = array(
            'assoc' => array('colname' => 2),
            'num' => array(2),
            'array' => array(2, 'colname' => 2),
            'row' => array(2),
            'object' => $obj,
        );

        foreach ( $tests as $way => $expected ) {
            $actual = $this->_object->fetch($way);
            $this->_object->seek(0);

            $this->assertEquals($expected, $actual);
        }
    }

    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli_Result::fetchAll
     * @covers Mumsys_Db_Driver_Mysql_Mysqli_Result::fetch
     */
    public function testFetchAll()
    {
        $table = 'mumsysunittesttable';
        $this->_createTempTable($table);
        $this->_createTempTableData($table);

        $oRes = $this->_dbDriver->query('SELECT * FROM ' . $table);

        $expected = $this->_getTempTableValues();
        $actual1 = $oRes->fetchAll();

        $actual2 = $oRes->fetchAll('assoc', true);

        $this->assertEquals($expected, $actual1);
        $this->assertFalse($actual2);
    }


    public function testNumRows()
    {
        $n = $this->_object->numRows();
        $this->assertEquals(1, $n);

        $n = $this->_object->numRows();
        $this->assertEquals(1, $n);

        $o = $this->_dbDriver->query('SELECT 1 AS colname');

        $n = $o->numRows();

        $this->assertEquals(1, $n);

        $this->setExpectedExceptionRegExp(
            'Mumsys_Db_Exception', '/(Error getting number of found rows)/i'
        );
        $n = $o->numRows(true); // fakin result as parameter
    }


    public function testAffectedRows()
    {
        $table = 'mumsysunittesttable';
        $this->_createTempTable($table);
        //$this->_createTempTableData($table);

        $sql = 'INSERT INTO ' . $table . ' ( ida, idb, idc, texta, textb)
            VALUES (1, 1, 1, \'texta1\', \'textb1\' ) ,
            (2, 2, 2, \'texta2\', \'textb2\' ) ,
            (3, 3, 3, \'texta3\', \'textb3\' )';
        $result = $this->_dbDriver->query($sql);
        $n = $result->affectedRows();
        $this->assertEquals(3, $n);

        $link = $this->_dbDriver->connect();
        $n = $result->affectedRows($link);
        $this->assertEquals(3, $n);
    }


    public function testLastInsertId()
    {
        $table = 'mumsysunittesttable';
        $this->_createTempTable($table);
        //$this->_createTempTableData($table);

        $sql = 'INSERT INTO ' . $table . ' ( ida, idb, idc, texta, textb)
            VALUES (98, 3, 3, \'texta3\', \'textb3\' )';
        $result = $this->_dbDriver->query($sql);
        $n = $result->lastInsertId();
        $this->assertEquals(98, $n);

        $link = $this->_dbDriver->connect();
        $n = $result->lastInsertId($link);
        $this->assertEquals(98, $n);
    }


    public function testInsertID()
    {
        $table = 'mumsysunittesttable';
        $this->_createTempTable($table);
        //$this->_createTempTableData($table);

        $sql = 'INSERT INTO ' . $table . ' ( ida, idb, idc, texta, textb)
            VALUES (99, 3, 3, \'texta3\', \'textb3\' )';
        $result = $this->_dbDriver->query($sql);

        $n = $result->insertID();
        $this->assertEquals(99, $n);

        $link = $this->_dbDriver->connect();
        $n = $result->lastInsertId($link);
        $this->assertEquals(99, $n);
    }


    public function testGetFirst_SqlResult()
    {
        $table = 'mumsysunittesttable';
        $this->_createTempTable($table);
        $this->_createTempTableData($table);

        $result = $this->_dbDriver->query('SELECT * FROM ' . $table);
        $xA = $result->getFirst(0);

        $result = $this->_dbDriver->query('SELECT * FROM ' . $table);
        $xB = $result->getFirst(1);

        $result = $this->_dbDriver->query('SELECT * FROM ' . $table);
        $xC = $result->getFirst(2);

        $result = $this->_dbDriver->query('SELECT * FROM ' . $table);
        $xD = $result->getFirst(0, 'noIdxExists');

        $result = $this->_dbDriver->query('SELECT * FROM ' . $table);
        $xE = $result->getFirst(0, 'idc');



        $this->assertEquals(1, $xA);
        $this->assertEquals(2, $xB);
        $this->assertEquals(3, $xC);
        $this->assertEquals(false, $xD);
        $this->assertEquals(1, $xE);

        $this->setExpectedExceptionRegExp('Mumsys_Db_Exception', '/(Seeking to row 10 failed)/i');
        $result = $this->_dbDriver->query('SELECT * FROM ' . $table);
        $result->sqlResult(10);

    }


    public function testSeek()
    {
        $table = 'mumsysunittesttable';
        $this->_createTempTable($table);
        $this->_createTempTableData($table);

        $result = $this->_dbDriver->query('SELECT * FROM ' . $table);
        $result->seek(0);
        $i = 1;
        while ( $row = $result->fetch('assoc') ) {
            $this->assertEquals($i++, $row['ida']);
        }

        $mysqlresult = $result->getResult();
        $result->seek(2, $mysqlresult);
        $row = $result->fetch('assoc');
        $this->assertEquals(3, $row['ida']);

        $x = $result->seek(99);
        $this->assertEquals(false, $x);
    }


    public function testFree()
    {
        $table = 'mumsysunittesttable';
        $this->_createTempTable($table);
        $this->_createTempTableData($table);

        $result = $this->_dbDriver->query('SELECT * FROM ' . $table);
        $xA = $result->free();

        $result = $this->_dbDriver->query('SELECT * FROM ' . $table);
        $mysqlresult = $result->getResult();
        $xB = $result->free($mysqlresult);

        $this->assertEquals(true, $xA);
        $this->assertEquals(true, $xB);

        $msg = '/(mysqli_free_result\(\) expects parameter 1 to be mysqli_result, string given)/i';
        $this->setExpectedExceptionRegExp('Mumsys_Db_Exception', $msg);
        $xC = $result->free('crapRx');
    }


    private function _createTempTable( $table = 'unittesttable' )
    {
        $sql = 'CREATE TEMPORARY TABLE ' . $table . ' (
            ida INT NOT NULL AUTO_INCREMENT,
            idb TINYINT (1) NOT NULL,
            idc smallint (2) NOT NULL,
            idd BIGINT (1) NOT NULL,

            numa float (8,4) UNSIGNED NOT NULL,
            numb decimal (8,4) UNSIGNED NOT NULL,
            numc double (8,4) UNSIGNED NOT NULL,
            -- # max limit by hardware, float without a limit!
            numd float UNSIGNED NOT NULL,

            `vartexta` enum(\'a\',\'b\',\'c\') COLLATE utf8_unicode_ci NOT NULL,
            `vartextb` set(\'a\',\'b\',\'c\') COLLATE utf8_unicode_ci NOT NULL,

            texta CHAR( 255 ) COLLATE utf8_unicode_ci NOT NULL,
            textb VARCHAR( 255 ) COLLATE utf8_unicode_ci NOT NULL,
            textc TEXT COLLATE utf8_unicode_ci NOT NULL,
            textd tinytext COLLATE utf8_unicode_ci NOT NULL,

            PRIMARY KEY (`ida`),
            UNIQUE KEY `texta` (`texta`),
            UNIQUE KEY `textb` (`textb`)
            )';
        $this->_dbDriver->query($sql);

        return;
    }


    private function _createTempTableData( $table )
    {
        // insert test data
        $data = array(
            'INSERT INTO ' . $table . ' SET ida = 1, idb = 1, idc = 1, texta=\'texta1\', textb=\'textb1\'',
            'INSERT INTO ' . $table . ' SET ida = 2, idb = 2, idc = 2, texta=\'texta2\', textb=\'textb2\'',
            'INSERT INTO ' . $table . ' SET ida = 3, idb = 3, idc = 3, texta=\'texta3\', textb=\'textb3\'',
        );

        foreach ( $data as $sql ) {
            $this->_dbDriver->query($sql);
        }
    }

    private function _getTempTableValues()
    {
        return array(
            0 => array(
                'ida' => '1',
                'idb' => '1',
                'idc' => '1',
                'idd' => '0',
                'numa' => '0.0000',
                'numb' => '0.0000',
                'numc' => '0.0000',
                'numd' => '0',
                'vartexta' => 'a',
                'vartextb' => '',
                'texta' => 'texta1',
                'textb' => 'textb1',
                'textc' => '',
                'textd' => '',
            ),
            1 => array(
                'ida' => '2',
                'idb' => '2',
                'idc' => '2',
                'idd' => '0',
                'numa' => '0.0000',
                'numb' => '0.0000',
                'numc' => '0.0000',
                'numd' => '0',
                'vartexta' => 'a',
                'vartextb' => '',
                'texta' => 'texta2',
                'textb' => 'textb2',
                'textc' => '',
                'textd' => '',
            ),
            2 => array(
                'ida' => '3',
                'idb' => '3',
                'idc' => '3',
                'idd' => '0',
                'numa' => '0.0000',
                'numb' => '0.0000',
                'numc' => '0.0000',
                'numd' => '0',
                'vartexta' => 'a',
                'vartextb' => '',
                'texta' => 'texta3',
                'textb' => 'textb3',
                'textc' => '',
                'textd' => '',
            ),
        );
    }

}
