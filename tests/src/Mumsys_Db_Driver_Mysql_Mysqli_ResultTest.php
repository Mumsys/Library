<?php

/**
 * Mumsys_Db_Driver_Mysql_Mysqli_Result Test
 */
class Mumsys_Db_Driver_Mysql_Mysqli_ResultTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Db_Driver_Mysql_Mysqli_Result|Mumsys_Db_Driver_Result_Interface
     */
    private $_object;
    private $_dbConfig;

    /** @var Mumsys_Db_Driver_Mysql_Mysqli */
    private $_dbDriver;

    /**
     * @var Mumsys_Context
     */
    private $_context;

    /**
     * @var array
     */
    private $_configs;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_context = new Mumsys_Context();

        $this->_configs = MumsysTestHelper::getConfigs();
        $this->_configs['database']['type'] = 'mysql:mysqli';

        $this->_dbConfig = $this->_configs['database'];

        try {
            $oDB = Mumsys_Db_Factory::getInstance( $this->_context, $this->_configs['database'] );
            $oDB->connect();
        }
        catch ( Exception $ex ) {
            $mesg = 'Connection failure. Check DB config to connect to the db';
            $this->markTestSkipped( $mesg );
        }

        $this->_dbDriver = new Mumsys_Db_Driver_Mysql_Mysqli( $this->_context, $this->_dbConfig );

        $this->_object = $this->_dbDriver->query( 'SELECT 1+1 AS colname' );
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        $this->_dbDriver->close();
        unset( $this->_object );
    }


    public function testConstruct()
    {
        $actual1 = new Mumsys_Db_Driver_Mysql_Mysqli( $this->_context, $this->_dbConfig );
        $actual2 = $this->_dbDriver->query( 'SELECT 1+1 AS colname' );

        $this->assertingInstanceOf( 'Mumsys_Db_Driver_Mysql_Mysqli', $actual1 );
        $this->assertingInstanceOf( 'Mumsys_Db_Driver_Mysql_Mysqli_Result', $actual2 );
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
            $actual = $this->_object->fetch( $way );
            $this->_object->seek( 0 );

            $this->assertingEquals( $expected, $actual );
        }
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli_Result::fetchAll
     * @covers Mumsys_Db_Driver_Mysql_Mysqli_Result::fetch
     */
    public function testFetchAll()
    {
        $table = 'mumsysunittesttable';
        $this->_dropTempTable( $table );
        $this->_createTempTable( $table );
        $this->_createTempTableData( $table );

        $oRes = $this->_dbDriver->query( 'SELECT * FROM ' . $table );

        $expected = $this->_getTempTableValues();
        $actual1 = $oRes->fetchAll();

        $actual2 = $oRes->fetchAll( 'assoc', true );

        // cleanup
        $this->_dropTempTable( $table );

        $this->assertingEquals( $expected, $actual1 );
        $this->assertingFalse( $actual2 );
    }


    public function testNumRows()
    {
        $n = $this->_object->numRows();
        $this->assertingEquals( 1, $n );

        $n = $this->_object->numRows();
        $this->assertingEquals( 1, $n );

        $o = $this->_dbDriver->query( 'SELECT 1 AS colname' );

        $n = $o->numRows();

        $this->assertingEquals( 1, $n );

        $this->expectingExceptionMessageRegex( '/(Invalid result set)/i' );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $n = $o->numRows( true ); // fakin result as parameter
    }


    public function testAffectedRows()
    {
        $table = 'mumsysunittesttable';
        $this->_createTempTable( $table );
        //$this->_createTempTableData($table);

        $sql = 'INSERT INTO ' . $table . ' ( ida, idb, idc, texta, textb)
            VALUES (1, 1, 1, \'texta1\', \'textb1\' ) ,
            (2, 2, 2, \'texta2\', \'textb2\' ) ,
            (3, 3, 3, \'texta3\', \'textb3\' )';
        $result = $this->_dbDriver->query( $sql );
        $n = $result->affectedRows();
        $this->assertingEquals( 3, $n );

        $link = $this->_dbDriver->connect();
        $n = $result->affectedRows( $link );
        $this->assertingEquals( 3, $n );
    }


    public function testLastInsertId()
    {
        $table = 'mumsysunittesttable';
        $this->_dropTempTable( $table );
        $this->_createTempTable( $table );
        //$this->_createTempTableData($table);

        $sql = 'INSERT INTO ' . $table . ' ( ida, idb, idc, texta, textb)
            VALUES (98, 3, 3, \'texta3\', \'textb3\' )';
        $result = $this->_dbDriver->query( $sql );
        $n = $result->lastInsertId();
        $this->assertingEquals( 98, $n );

        $link = $this->_dbDriver->connect();
        $n = $result->lastInsertId( $link );
        $this->assertingEquals( 98, $n );

        $this->_dropTempTable( $table );
    }


    public function testInsertID()
    {
        $table = 'mumsysunittesttable';
        $this->_createTempTable( $table );
        //$this->_createTempTableData($table);

        $sql = 'INSERT INTO ' . $table . ' ( ida, idb, idc, texta, textb)
            VALUES (99, 3, 3, \'texta3\', \'textb3\' )';
        $result = $this->_dbDriver->query( $sql );

        $n = $result->lastInsertId();
        $this->assertingEquals( 99, $n );

        $link = $this->_dbDriver->connect();
        $n = $result->lastInsertId( $link );
        $this->assertingEquals( 99, $n );
    }


    public function testGetFirst_SqlResult()
    {
        $table = 'mumsysunittesttable';
        $this->_dropTempTable( $table );
        $this->_createTempTable( $table );
        $this->_createTempTableData( $table );

        $result = $this->_dbDriver->query( 'SELECT * FROM ' . $table );
        $xA = $result->getFirst( 0 );

        $result = $this->_dbDriver->query( 'SELECT * FROM ' . $table );
        $xB = $result->getFirst( 1 );

        $result = $this->_dbDriver->query( 'SELECT * FROM ' . $table );
        $xC = $result->getFirst( 2 );

        $result = $this->_dbDriver->query( 'SELECT * FROM ' . $table );
        $xD = $result->getFirst( 0, 'noIdxExists' );

        $result = $this->_dbDriver->query( 'SELECT * FROM ' . $table );
        $xE = $result->getFirst( 0, 'idc' );

        $this->assertingEquals( 1, $xA );
        $this->assertingEquals( 2, $xB );
        $this->assertingEquals( 3, $xC );
        $this->assertingEquals( false, $xD );
        $this->assertingEquals( 1, $xE );

        $this->expectingExceptionMessageRegex( '/(Seeking to row 10 failed)/i' );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $result = $this->_dbDriver->query( 'SELECT * FROM ' . $table );
        $result->getFirst( 10 ); // old: $result->sqlResult( 10 );

        // cleanup
        $this->_dropTempTable( $table );
    }


    public function testSeek()
    {
        $table = 'mumsysunittesttable';
        $this->_dropTempTable( $table );
        $this->_createTempTable( $table );
        $this->_createTempTableData( $table );

        $result = $this->_dbDriver->query( 'SELECT * FROM ' . $table );
        $result->seek( 0 );
        $i = 1;
        while ( $row = $result->fetch( 'assoc' ) ) {
            $this->assertingEquals( $i++, $row['ida'] );
        }

        $mysqlresult = $result->getResult();
        $result->seek( 2, $mysqlresult );
        $row = $result->fetch( 'assoc' );
        $this->assertingEquals( 3, $row['ida'] );

        $x = $result->seek( 99 );
        $this->assertingEquals( false, $x );

        // cleanup
        $this->_dropTempTable( $table );
    }


    public function testFree()
    {
        $table = 'mumsysunittesttable';
        $this->_dropTempTable( $table );
        $this->_createTempTable( $table );
        $this->_createTempTableData( $table );

        $result = $this->_dbDriver->query( 'SELECT * FROM ' . $table );
        $xA = $result->free();

        $result = $this->_dbDriver->query( 'SELECT * FROM ' . $table );
        $mysqlresult = $result->getResult();
        $xB = $result->free( $mysqlresult );

        // cleanup
        $this->_dropTempTable( $table );

        $this->assertingEquals( true, $xA );
        $this->assertingEquals( true, $xB );

        $regex =
            // php8
            '/((mysqli_free_result\(\): Argument #1 \(\$result\) must be of type mysqli_result, string given)'
            // php7.3|4
            . '|(mysqli_free_result\(\) expects parameter 1 to be mysqli_result, string given))/';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $result->free( 'crapRx' );
    }


    private function _createTempTable( $table = 'unittesttable' )
    {
        $sql = 'CREATE TABLE if not exists `' . $table . '` (
            ida INT UNSIGNED NOT NULL AUTO_INCREMENT,
            idb TINYINT (1) NOT NULL DEFAULT 0,
            idc smallint (2) NOT NULL DEFAULT 0,
            idd BIGINT (1) NOT NULL DEFAULT 0,

            numa float (8,4) UNSIGNED NOT NULL DEFAULT 0,
            numb decimal (8,4) UNSIGNED NOT NULL DEFAULT 0,
            numc double (8,4) UNSIGNED NOT NULL DEFAULT 0,
            -- # max limit by hardware, float without a limit!
            numd float UNSIGNED NOT NULL DEFAULT 0,

            `vartexta` enum(\'a\',\'b\',\'c\') COLLATE utf8_unicode_ci NOT NULL DEFAULT \'a\',
            `vartextb` set(\'a\',\'b\',\'c\') COLLATE utf8_unicode_ci NOT NULL DEFAULT \'a\',

            texta CHAR( 255 ) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
            textb VARCHAR( 255 ) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
            textc TEXT COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
            textd tinytext COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',

            PRIMARY KEY (`ida`),
            UNIQUE KEY `texta` (`texta`),
            UNIQUE KEY `textb` (`textb`)
            )';
        $this->_dbDriver->query( $sql );

        return;
    }


    /**
     * Drops the test table
     * @param string $table
     */
    private function _dropTempTable( $table = 'unittesttable' )
    {
        $res = $this->_dbDriver->query( 'DROP TABLE IF EXISTS `' . $table . '`' );
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
            $this->_dbDriver->query( $sql );
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
                'vartextb' => 'a',
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
                'vartextb' => 'a',
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
                'vartextb' => 'a',
                'texta' => 'texta3',
                'textb' => 'textb3',
                'textc' => '',
                'textd' => '',
            ),
        );
    }

}
