<?php


/**
 * Test class for Mumsys_Db_Driver_Mysql_Result.
 * Generated by PHPUnit on 2011-09-14 at 13:07:49.
 */
class Mumsys_Db_Driver_Mysql_ResultTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Mumsys_Db_Driver_Mysql_Result
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        //if (PHP_VERSION_ID > 70000) {
            $this->markTestSkipped();
            return false;
        //}

        $this->_configs = $this->_config = MumsysTestHelper::getConfig();
        $this->_configs['database']['type'] = 'mysql:mysql';

        $this->_dbConfig = $this->_configs['database'];

        $this->_driverMysql = new Mumsys_Db_Driver_Mysql_Mysql($this->_dbConfig);

        $this->_object = $this->_driverMysql->query('SELECT 1+1 AS colname');
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->_driverMysql->close();
        $this->_object = null;
    }


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

        foreach ( $tests as $rule => $expected ) {
            $actual = $this->_object->fetch($rule);
            $this->_object->seek(0);
            $this->assertEquals($expected, $actual);
        }
    }


    public function testNumRows()
    {
        $n = $this->_object->numRows();
        $this->assertEquals(1, $n);

        $n = $this->_object->numRows();
        $this->assertEquals(1, $n);

        $o = $this->_driverMysql->query('SELECT 1 AS colname');

        $n = $o->numRows();

        $this->assertEquals(1, $n);

        $this->setExpectedException(
            'Mumsys_Db_Exception', 'Error getting number of found rows.'
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
        $result = $this->_driverMysql->query($sql);
        $n = $result->affectedRows();
        $this->assertEquals(3, $n);

        $link = $this->_driverMysql->connect();
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
        $result = $this->_driverMysql->query($sql);
        $n = $result->lastInsertId();
        $this->assertEquals(98, $n);

        $link = $this->_driverMysql->connect();
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
        $result = $this->_driverMysql->query($sql);

        $n = $result->insertID();
        $this->assertEquals(99, $n);

        $link = $this->_driverMysql->connect();
        $n = $result->lastInsertId($link);
        $this->assertEquals(99, $n);
    }


    public function testSqlResult()
    {
        $table = 'mumsysunittesttable';
        $this->_createTempTable($table);
        $this->_createTempTableData($table);

        $result = $this->_driverMysql->query('SELECT * FROM ' . $table);

        $xA = $result->sqlResult(0);
        $xB = $result->sqlResult(1);
        $xC = $result->sqlResult(2);
        $xD = $result->sqlResult(3);
        $xE = $result->sqlResult(0, 'idc');

        $this->assertEquals(1, $xA);
        $this->assertEquals(2, $xB);
        $this->assertEquals(3, $xC);
        $this->assertEquals(false, $xD);
        $this->assertEquals(1, $xE);
    }


    public function testSeek()
    {
        $table = 'mumsysunittesttable';
        $this->_createTempTable($table);
        $this->_createTempTableData($table);

        $result = $this->_driverMysql->query('SELECT * FROM ' . $table);
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

        $result = $this->_driverMysql->query('SELECT * FROM ' . $table);
        $x = $result->free();
        $this->assertEquals(true, $x);

        $result = $this->_driverMysql->query('SELECT * FROM ' . $table);
        $mysqlresult = $result->getResult();
        $x = $result->free($mysqlresult);
        $this->assertEquals(true, $x);
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
        $this->_driverMysql->query($sql);

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
            $this->_driverMysql->query($sql);
        }
    }


}
