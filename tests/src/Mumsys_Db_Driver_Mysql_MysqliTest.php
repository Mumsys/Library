<?php

/**
 * Mumsys_Db_Driver_Mysql_Mysqli Test
 *
 * @deprecated since version php > 8.1
 */
class Mumsys_Db_Driver_Mysql_MysqliTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Db_Driver_Mysql_Mysqli
     */
    protected $_object;

    /**
     * @var Mumsys_Context
     */
    protected $_context;

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
        // A >= B
        if ( version_compare( PHP_VERSION, '8.1.0', '>=' ) ) {
            $this->markTestSkipped( 'php 8.1 todo or use a different driver' );
        }

        $this->_context = new Mumsys_Context();
        //$this->_context = MumsysTestHelper::getContext();

        $this->_configs = MumsysTestHelper::getConfigs();
        $this->_configs['database']['type'] = 'mysql:mysqli';

        try {
            $this->_object = Mumsys_Db_Factory::getInstance(
                $this->_context, $this->_configs['database']
            );
            $this->_object->connect();
        }
        catch ( Exception $ex ) {
            // xxxxxxxxxx
            echo $ex->getMessage();
            echo $ex->getTraceAsString();

            $this->markTestSkipped(
                'Connection failure. Check DB config to connect to the db'
            );
        }

        $this->_tempTable = 'mumsysunittesttemp';
        $this->_createTempTable( $this->_tempTable );
        $this->_createTempTableData( $this->_tempTable );
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        $this->_dropTempTable( $this->_tempTable );
        $this->_object->close();
        $this->_object = null;
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::connect
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::_setError
     */
    public function testConnect()
    {
        $config = $this->_configs['database'];

        $this->assertingInstanceOf( 'mysqli', $this->_object->connect() );

        // compression
        $config['compress'] = true;
        $object = Mumsys_Db_Factory::getInstance( $this->_context, $config );
        $this->assertingInstanceOf( 'mysqli', $object->connect() );

        /** @todo not connected w/o exception, will throw it anyway! */
        $config['compress'] = false;
        $config['host'] = '127.0.0.9'; //invalidHostname
        $config['throwErrors'] = false;
        $object = Mumsys_Db_Factory::getInstance( $this->_context, $config );
        $this->assertingFalse( $object->connect() );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::close
     */
    public function testClose()
    {
        $this->assertingTrue( $this->_object->close() );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::setCharset
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::getCharset
     */
    public function testGetSetCharset()
    {
        $actual1 = $this->_object->getCharset();
        $actual2 = $this->_object->setCharset( 'utf8' );
        $this->_object->setThrowErrors( false );
        $actual3 = $this->_object->setCharset( 'invalid charset' );

        $this->assertingInstanceOf( 'stdClass', $actual1 );
        $this->assertingTrue( $actual2 );
        $this->assertingFalse( $actual3 );

        $this->_object->setThrowErrors( true );
        $this->expectingExceptionMessageRegex(
            '/(Setting client character set failt)/i'
        );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->_object->setCharset( 'invalid charset' );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::getCharset
     */
    public function testGetCharsetErrors()
    {
        // no connected
        $config = $this->_configs['database'];
        $config['throwErrors'] = false;
        $object = Mumsys_Db_Factory::getInstance( $this->_context, $config );
        $actualA = $object->getCharset();

        $this->assertingFalse( $actualA );

        $config['throwErrors'] = true;
        $object = Mumsys_Db_Factory::getInstance( $this->_context, $config );
        $this->expectingExceptionMessageRegex(
            '/(Getting character set failt)/i'
        );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $object->getCharset();
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::selectDB
     */
    public function testSelectDB()
    {
        $actual1 = $this->_object->selectDB( 'information_schema' );
        $actual2 = $this->_object->selectDB( $this->_configs['database']['db'] );
        $actual3 = $this->_object->selectDB( $this->_configs['database']['db'] );

        $this->assertingTrue( $actual1 );
        $this->assertingTrue( $actual2 );
        $this->assertingTrue( $actual3 ); // for code coverage
        // access denied message expected
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->_object->selectDB( 'testdbnotexists' );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::showDBs
     */
    public function testShowDBs()
    {
        $actual1 = $this->_object->showDBs();
        $this->assertingTrue( isset( $actual1['information_schema'] ) );
        $this->assertingTrue( isset( $actual1[$this->_configs['database']['db']] ) );
    }


    /**
     * just 4 code coverage.
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::showTables
     */
    public function testShowTables()
    {
        // not available on travis-ci
        if ( Mumsys_Php_Globals::getRemoteUser() === 'travis' ) {
            $this->markTestSkipped( 'Not in for travis yet' );
        }

        $actual1 = $this->_object->showTables();

//        Not implementet yet:
        foreach ( $this->_configs['database']['tables'] as $k => $table ) {
            $this->assertingEquals( $table, $actual1[$table] );
        }
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::stat
     */
    public function testStat()
    {
        $actual1 = $this->_object->stat();
        // Uptime: 45020  Threads: 2  Questions: 548980  Slow queries: 0
        // Opens: 234216  Flush tables: 2  Open tables: 4  Queries per second
        // avg: 12.194
        $find = array(
            'Uptime', 'Threads', 'Questions', 'Slow queries', 'Opens',
            //'Flush tables', // not in on travis
            'Open tables', 'Queries per second avg'
        );
        foreach ( $find as $key ) {
            $this->assertingTrue(
                ( preg_match( '/(' . $key . ')/', $actual1 ) === 1 ),
                "key: '$key', actual: " . print_r( $actual1, true ),
            );
        }
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::query
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::_setError
     */
    public function testQuery()
    {
        $this->_object->setThrowErrors( false );
        $this->_object->setDebugMode( true );
        $actual1 = $this->_object->query( 'SELECT \'abc\', \'def\'' );
        $actual2 = $this->_object->query( 'SELECT \'abc\', \'def\'', true );
        $actual3 = $this->_object->query( '' );

        $this->assertingInstanceOf(
            'Mumsys_Db_Driver_Mysql_Mysqli_Result', $actual1
        );
        $this->assertingFalse( $actual2 );
        $this->assertingFalse( $actual3 );

        // for code coverage
        $this->_object->close();
        $actual4 = $this->_object->query( 'SELECT 1,2,3' );
        $this->assertingInstanceOf(
            'Mumsys_Db_Driver_Mysql_Mysqli_Result', $actual4
        );
        // for code coverage query error
        $actual5 = $this->_object->query( 'SELECT 1,2,3 FROM notExists' );
        $this->assertingFalse( $actual5 );

        $this->_object->setThrowErrors( true );
        $error = '/(Query empty. Cant not query empty sql statment)/i';
        $this->expectingExceptionMessageRegex( $error );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->_object->query( '' );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::queryUnbuffered
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::setThrowErrors
     */
    public function testQueryUnbuffered()
    {
        $this->_object->setThrowErrors( false );
        $errorMsg = 'Unbuffered querys not implemented yet';

        $actual1 = $this->_object->queryUnbuffered( 'SELECT \'abc\', \'def\'' );
        $actual2 = $this->_object->getErrorMessage();

        $this->assertingFalse( $actual1 );
        $this->assertingEquals( $errorMsg, $actual2 );

        $this->_object->setThrowErrors( true );
        $this->expectingExceptionMessageRegex( '/(' . $errorMsg . ')/i' );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->_object->queryUnbuffered( 'SELECT \'abc\', \'def\'' );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::isError
     */
    public function testIsError()
    {
        $res1 = $this->_object->query( 'SELECT \'abc\', \'def\'' );
        $actual1 = $this->_object->isError( $res1 );

        $this->_object->setThrowErrors( false );
        $res2 = $this->_object->query( 'SELECT' );
        $actual2 = $this->_object->isError( $res2 );

        $this->assertingFalse( $actual1 );
        $this->assertingTrue( $actual2 );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::sqlError
     */
    public function testSqlError()
    {
        $this->_object->setThrowErrors( false );
        $this->_object->query( 'SELECT' );
        $actual1 = $this->_object->sqlError();
        $this->assertingTrue(
            preg_match( '/(You have an error in your SQL syntax)/', $actual1 ) == 1
        );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::sqlErrno
     */
    public function testSqlErrno()
    {
        $this->_object->setThrowErrors( false );

        $this->_object->query( 'SELECT' );
        $actual1 = $this->_object->sqlErrno();

        $this->_object->query( 'SELECT 123' );
        $actual2 = $this->_object->sqlErrno();

        $this->assertingEquals( 1064, $actual1 );
        $this->assertingEquals( 0, $actual2 );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::fetchData
     */
    public function testFetchData()
    {
        $actual1 = $this->_object->fetchData( 'SELECT \'abc\'', 'ASSOC' );
        $expected1 = array(array('abc' => 'abc'));

        $actual2 = $this->_object->fetchData( 'SELECT \'abc\'', 'GETIDS' );
        $expected2 = array(0 => 'abc');

        $actual3 = $this->_object->fetchData( 'SELECT \'abc\'', 'LINE' );
        $expected3 = array('abc' => 'abc');

        $actual4 = $this->_object->fetchData( 'SELECT \'abc\'', 'ROW' );
        $expected4 = array(0 => 'abc');

        $actual5 = $this->_object->fetchData( 'SELECT \'abc\', 123', 'KEYGOVAL' );
        $expected5 = array('abc' => '123');

        $actual6 = $this->_object->fetchData( 'SELECT \'abc\'', 'KEYGOKEY' );
        $expected6 = array('abc' => 'abc');

        $actual7 = $this->_object->fetchData(
            'SELECT \'a b c\', 1,2,3', 'KEYGOASSOC'
        );
        $expected7 = array(
            'a b c' => array(
                'a b c' => 'a b c', 1 => '1', 2 => '2', 3 => '3'
            )
        );

        $actual7 = $this->_object->fetchData(
            'SELECT \'a b c\', 1,2,3', 'defaultASassoc'
        );
        $expected7 = array(array('a b c' => 'a b c', 1 => '1', 2 => '2', 3 => '3'));

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );
        $this->assertingEquals( $expected4, $actual4 );
        $this->assertingEquals( $expected5, $actual5 );
        $this->assertingEquals( $expected6, $actual6 );
        $this->assertingEquals( $expected7, $actual7 );

        $this->_object->setThrowErrors( false );
        $actualFalse = $this->_object->fetchData( 'SELECT *', 'defaultASassoc' );
        $this->assertingFalse( $actualFalse );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::showColumns
     */
    public function testShowColumns()
    {
        $this->_object->setDebugMode( false ); // disable debug output

        $expectedA = $this->_getTempTableColumnValues( $this->_tempTable );

        $actualA = $this->_object->showColumns( $this->_tempTable );
        $this->assertingEquals( $expectedA, $actualA );

        $this->_object->setThrowErrors( false );

        $actualAA = $this->_object->showColumns( 'tableNotExists' );
        $this->assertingFalse( $actualAA );

        $actualB = $this->_object->showColumns( $this->_tempTable, 'ida' );
        $this->assertingEquals( array($expectedA[0]), $actualB );

        $actualC = $this->_object->showColumns(
            $this->_tempTable, 'fieldNotExists'
        );
        $this->assertingFalse( $actualC ); // php mysql_query() bughandle in class

        $actualE = $this->_object->showColumns();
        $this->assertingFalse( $actualE );

        $this->_object->setThrowErrors( true );
        $serverinfo = $this->_object->getServerInfo();
        if ( preg_match( '/mariadb/i', $serverinfo ) ) {
            $errorMsg = 'You have an error in your SQL syntax; check the '
                . 'manual that corresponds to your MariaDB server version for '
                . 'the right syntax to use near \'\' at line 1';
        } else if ( preg_match( '/mysql/i', $serverinfo ) ) {
            $errorMsg = 'You have an error in your SQL syntax; check the '
                . 'manual that corresponds to your MySQL server version for '
                . 'the right syntax to use near \'\' at line 1';
        } else {
            $errorMsg = null;
        }

        $this->expectingExceptionMessageRegex( '/(' . $errorMsg . ')/i' );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->_object->showColumns();
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::showColumns
     */
    public function testShowColumnsExceptionA()
    {
        $this->_object->setThrowErrors( true );
        $this->_object->setDebugMode( false ); // disable debug output
        $this->expectingExceptionMessageRegex(
            '/(tableNotExists\' doesn\'t exist)/i'
        );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->_object->showColumns( 'tableNotExists' );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::showColumns
     */
    public function testShowColumnsExceptionB()
    {
        $this->_object->setThrowErrors( true );
        $this->_object->setDebugMode( false ); // disable debug output
        $this->expectingExceptionMessageRegex(
            '/(Error getting columns. Does the columne "fieldNotExists" '
            . 'exists\?)/'
        );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->_object->showColumns( $this->_tempTable, 'fieldNotExists' );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::update
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::_save
     */
    public function testUpdate()
    {
        // update one
        $params['fields'] = array(
            'texta' => 'textaNew', 'textb' => 'not null', 'textc' => 'now()'
        );
        $params['table'] = $this->_tempTable;
        $params['where'] = array(
            'AND' => array(
                array('=' => array('ida' => '1')),
                array('=' => array('ida' => 1)), // with nummeric array key
            )
        );
        $this->_object->update( $params );
        $queryA = $this->_object->getQuery();

        $actual = $this->_object->fetchData(
            'SELECT * FROM ' . $this->_tempTable . ' WHERE ida = 1', 'ASSOC'
        );
        $this->assertingEquals(
            'UPDATE mumsysunittesttemp SET `texta`=\'textaNew\',`textb`=\'not null\','
            . '`textc`=NOW() WHERE (`ida`=\'1\' AND `ida`=1)', $queryA
        );
        $this->assertingEquals( 'textaNew', $actual[0]['texta'] );
        $this->assertingEquals( 'not null', $actual[0]['textb'] );
        $this->assertingRegExp(
            MUMSYS_REGEX_DATETIME_MYSQL, $actual[0]['textc']
        );

        // update all
        $params['where'] = array();
        $params['updateall'] = true;
        $params['fields'] = array('textc' => 'textaNew');
        $this->_object->update( $params );
        $queryA = $this->_object->getQuery();
        $this->assertingEquals(
            'UPDATE mumsysunittesttemp SET `textc`=\'textaNew\' WHERE 1=1',
            $queryA
        );
        $actual = $this->_object->fetchData(
            'SELECT * FROM ' . $this->_tempTable . ' WHERE 1', 'ASSOC'
        );

        foreach ( $actual as $item ) {
            $this->assertingEquals( 'textaNew', $item['textc'] );
        }

        // test failure as return
        $this->_object->setDebugMode( false ); // hide std out
        $this->_object->setThrowErrors( false );
        $params = array();
        $actual = $this->_object->update( $params );
        $this->assertingFalse( $actual );

        // test failure Exception
        $this->_object->setThrowErrors( true );
        $this->expectingExceptionMessageRegex(
            '/(Unknown key or empty values. No "update" action)/i'
        );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->_object->update( $params );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::_save
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::select
     */
    public function testSelect()
    {
        $params = array(
            'table' => $this->_tempTable,
            'fields' => array('ida', 'idb', 'idc'),
            'where' => array(
                'AND' => array(
                    array('>' => array('ida' => 0)),
                ),
            ),
            'order' => array('ida' => 'ASC'),
            'limit' => array(10)
        );

        $result = $this->_object->select( $params );
        $queryA = $this->_object->getQuery();

        $sql = 'SELECT `ida`,`idb`,`idc` FROM mumsysunittesttemp WHERE '
            . '(`ida`>0) ORDER BY `ida` ASC LIMIT 10';
        $expected = $this->_object->fetchData( $sql, 'ASSOC' );
        $queryB = $this->_object->getQuery();

        $i = 0;
        while ( $row = $result->fetch( 'assoc' ) ) {
            $this->assertingEquals( $expected[$i], $row );
            $i++;
        }
        $this->assertingEquals( 3, $result->numRows() );
        $this->assertingEquals( $queryA, $queryB );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::_save
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::insert
     */
    public function testInsert()
    {
        $params = array(
            'table' => $this->_tempTable,
            'fields' => array('ida' => 4, 'idb' => 4, 'idc' => 4),
        );

        $this->assertingEquals( 4, $this->_object->insert( $params ) );

        $this->_object->setDebugMode( false ); // hide std out
        $this->_object->setThrowErrors( false );
        $actual = $this->_object->insert( $params );
        $this->assertingFalse( $actual );

        $this->_object->setThrowErrors( true );
        $this->expectingExceptionMessageRegex(
            '/(Duplicate entry \'4\' for key \'PRIMARY\')/i'
        );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $actual = $this->_object->insert( $params );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::_save
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::replace
     */
    public function testReplace()
    {
        $this->_object->setThrowErrors( true );

        // insert test record
        $params = array(
            'table' => $this->_tempTable,
            'fields' => array('ida' => 4, 'idb' => 4, 'idc' => 4),
        );
        $lastInsertId = $this->_object->insert( $params );
        $this->assertingEquals( 4, $lastInsertId );

        // replace test record
        $params = array(
            'table' => $this->_tempTable,
            'fields' => array(
                'ida' => 4, 'textc' => 'lot of text'),
        );
        // affectedRows: 2 -> for the insert (which was found for this id) and
        // one for the replace
        $this->assertingEquals( 2, $this->_object->replace( $params ) );

        // test replacement
        $select = array(
            'table' => $this->_tempTable,
            'fields' => array('ida', 'idb', 'idc', 'textc'),
            'where' => array('AND' => array(array('=' => array('ida' => 4))))
        );
        $expected = array(
            array('ida' => 4, 'idb' => 0, 'idc' => 0, 'textc' => 'lot of text')
        );
        $result = $this->_object->select( $select );
        $this->assertingEquals( 1, $result->numRows() );
        $data = $result->fetchAll( 'assoc' );
        $this->assertingEquals( $expected, $data );

        // replace failure
        $this->_object->setDebugMode( false );
        $this->_object->setThrowErrors( false );
        $this->assertingFalse( $this->_object->replace( array() ) );

        $this->_object->setThrowErrors( true );
        $this->expectingExceptionMessageRegex(
            '/(Unknown key or empty values. No "replace" action)/i'
        );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->assertingFalse( $this->_object->replace( array() ) );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::_save
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::delete
     */
    public function testDelete()
    {
        $this->_object->setDebugMode( false );

        $params = array(
            'table' => $this->_tempTable,
            'fields' => array(),
            'where' => array(
                'AND' => array(
                    array('=' => array('ida' => 3)),
                )
            )
        );
        $result = $this->_object->delete( $params );
        $this->assertingEquals( 1, $result->affectedRows() );
        $expected = $this->_object->fetchData(
            'SELECT * FROM mumsysunittesttemp WHERE 1', 'ASSOC'
        );
        $this->assertingEquals( 2, count( $expected ) );
    }


    /**
     * Test failure for operator not '_' and key not a string.
     * Other tests in testCompileQueryWhere()
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQueryExpression
     */
    public function testCompileQueryExpressionErrorA()
    {
        $exprA = array('=' => array(0 => 'value'));

        $this->_object->setThrowErrors( false );
        $this->_object->setDebugMode( false );

        $actualA = $this->_object->compileQueryExpression( $exprA );
        $this->assertingFalse( $actualA );

        $this->_object->setThrowErrors( true );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->expectingExceptionMessageRegex(
            '/(Invalid expression key "0" for where expression: values '
            . '\(json\): "value")/i'
        );
        $this->_object->compileQueryExpression( $exprA );
    }


    /**
     * Test failure for operator not '_' and key/val is a string.
     * Other tests in testCompileQueryWhere()
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQueryExpression
     */
    public function testCompileQueryExpressionErrorB()
    {
        $exprB = array('=' => 'key/value string');

        $this->_object->setThrowErrors( false );
        $this->_object->setDebugMode( false );

        $actualB = $this->_object->compileQueryExpression( $exprB );
        $this->assertingFalse( $actualB );

        $this->_object->setThrowErrors( true );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->expectingExceptionMessage(
            'Invalid input for where expression. Array expected. Operator: '
            . '"=" values (json): "key\/value string'
        );
        $this->_object->compileQueryExpression( $exprB );
    }


    /**
     * Test failure for exception for IN operator: list values not numeric
     * and not string.
     * Other tests in testCompileQueryWhere()
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQueryExpression
     */
    public function testCompileQueryExpressionErrorC()
    {
        $exprC = array('=' => array('key' => array(array(1), array(2))));

        $this->_object->setThrowErrors( false );
        $this->_object->setDebugMode( false );

        $actualC = $this->_object->compileQueryExpression( $exprC );
        $this->assertingFalse( $actualC );

        $this->_object->setThrowErrors( true );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->expectingExceptionMessageRegex(
            '/(Invalid value list for where expression. Strings|numbers '
            . 'expected. operator: "IN" values \(json\): {"key":[[1],[2]]})/i'
        );
        $this->_object->compileQueryExpression( $exprC );
    }


    /**
     * Test failure for exception for '_' operator: value list not strings
     * Other tests in testCompileQueryWhere()
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQueryExpression
     */
    public function testCompileQueryExpressionErrorD()
    {
        $exprD = array('_' => array('a < b', array('err < here')));

        $this->_object->setThrowErrors( false );
        $this->_object->setDebugMode( false );

        $actualD = $this->_object->compileQueryExpression( $exprD );
        $this->assertingFalse( $actualD );

        $this->_object->setThrowErrors( true );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->expectingExceptionMessageRegex(
            '/(Invalid value list for where expression. String expected. '
            . 'Operator: "_" values \(json\))/i'
        );
        $this->_object->compileQueryExpression( $exprD );
    }


    /**
     * Test failure for exception for '_' operator and value not array|string
     * Other tests in testCompileQueryWhere()
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQueryExpression
     */
    public function testCompileQueryExpressionErrorE()
    {
        $exprE = array('_' => 12345);

        $this->_object->setThrowErrors( false );
        $this->_object->setDebugMode( false );

        $actualE = $this->_object->compileQueryExpression( $exprE );
        $this->assertingFalse( $actualE );

        $this->_object->setThrowErrors( true );
        $this->expectingExceptionMessageRegex(
            '/(Invalid value for where expression. Array|string expected. '
            . 'Operator: "_" values \(json\): "12345")/i'
        );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->_object->compileQueryExpression( $exprE );
    }


    /**
     * Test failure for invalid operator
     * Other tests in testCompileQueryWhere()
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQueryExpression
     */
    public function testCompileQueryExpressionErrorF()
    {
        $expr = array('notExists' => array('key' => 'value'));

        $this->_object->setThrowErrors( false );
        $this->_object->setDebugMode( false );

        $actual = $this->_object->compileQueryExpression( $expr );
        $this->assertingFalse( $actual );

        $this->_object->setThrowErrors( true );
        $this->expectingExceptionMessageRegex(
            '/(Unknown operator "notExists" to create expression)/i'
        );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->_object->compileQueryExpression( $expr );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQuery
     */
    public function testCompileQuery()
    {
        $optionsA = array(
            // array|empty Cols to be select; default * if empty
            'cols' => array('*'),
            // array|string table(s) to fetch from in inner join if array given
            'table' => 'table',
            // 'a=b AND id = 1',
            'where' => array(
                'AND' => array(
                    array('=' => array('a' => 'b')),
                    array('=' => array('id' => 1)))
            ),
            'having' => 'MAX(col2) = 2', // no having
            'group' => array('id'),
            'order' => array('id'),
            'limit' => array(1, 2),
        );

        $actualA = $this->_object->compileQuery( $optionsA );
        $expectedA = 'SELECT * FROM table   WHERE (`a`=\'b\' AND `id`=1) '
            . 'GROUP BY `id` HAVING MAX(col2) = 2  ORDER BY `id` ASC '
            . 'LIMIT 2 OFFSET 1';

        $optionsB = $optionsA;
        $optionsB['cols'] = array('a', 'b', 'c');
        $optionsB['where'] = array(
            'AND' => array(
                array('=' => array('a' => 'value a')),
                array('=' => array('b' => 'value b')),
                array('=' => array('c' => 1)))
        );
        $actualB = $this->_object->compileQuery( $optionsB );
        $expectedB = 'SELECT `a`,`b`,`c` FROM table   WHERE (`a`=\'value a\''
            . ' AND `b`=\'value b\' AND `c`=1) GROUP BY `id` HAVING '
            . 'MAX(col2) = 2  ORDER BY `id` ASC LIMIT 2 OFFSET 1';

        // for code coverage:
        $optionsC = $optionsA;
        unset( $optionsC['cols'] );
        $optionsC['table'] = array(
            'table' => '',
            'table2' => 'table.id = table2.id',
            'table3' => 'table3.id = table2.id'
        );
        $optionsC['having'] = array('MAX(col2) = 2', 'id > 1');
        $optionsC['group'] = array('id', 'col2');
        $optionsC['order'] = array('id' => 'ASC', 'col2' => 'DESC');
        $optionsC['limit'] = array(1, 2);

        $actualC = $this->_object->compileQuery( $optionsC );
        $expectedC = 'SELECT * FROM table,table2,table3  WHERE '
            . '(table.id = table2.id) AND (table3.id = table2.id) AND (`a`'
            . '=\'b\' AND `id`=1) GROUP BY `id`,`col2` HAVING (MAX(col2) = 2) '
            . 'AND (id > 1)  ORDER BY `id` ASC,`col2` DESC LIMIT 2 OFFSET 1';

        // test
        $this->assertingEquals( $expectedA, $actualA );
        $this->assertingEquals( $expectedB, $actualB );
        $this->assertingEquals( $expectedC, $actualC );

        // error, exception
        $optionsD = $optionsA;
        unset( $optionsD['table'] );
        $this->_object->setThrowErrors( false );
        $this->_object->setDebugMode( false );
        $actualD = $this->_object->compileQuery( $optionsD );
        $this->assertingFalse( $actualD );

        $this->_object->setThrowErrors( true );
        $this->expectingExceptionMessageRegex( '/(No tables given to compile)/i' );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $actualD = $this->_object->compileQuery( $optionsD );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQuerySelect
     */
    public function testCompileQuerySelect()
    {
        $this->_object->setDebugMode( false );
        $this->_object->setThrowErrors( false );

        $fieldsA = array('A', 'B', 'C');
        $fieldsB = array('fieldA' => 'A', 'B', 'C');
        $fieldsC = array(
            '_' => 'count(*) AS cnt, UNIX_TIMESTAMP(tbl3.`dtime`)',
            'dd' => 'D',
            'ee' => 'E',
            'F'
        );
        $fieldsD = array('_' => array('A', 'b' => 'B')); // test casting errors
        $fieldsE = array('*');

        $actualA = $this->_object->compileQuerySelect( $fieldsA );
        $actualB = $this->_object->compileQuerySelect( $fieldsB );
        $actualC = $this->_object->compileQuerySelect( $fieldsC );
        $actualD = $this->_object->compileQuerySelect( $fieldsD );
        $actualE = $this->_object->compileQuerySelect( $fieldsE );

        $expectedA = '`A`,`B`,`C`';
        $expectedB = '`A` AS fieldA,`B`,`C`';
        $expectedC = 'count(*) AS cnt, UNIX_TIMESTAMP(tbl3.`dtime`),`D` AS '
            . 'dd,`E` AS ee,`F`';
        $expectedE = '*';

        $this->assertingEquals( $expectedA, $actualA );
        $this->assertingEquals( $expectedB, $actualB );
        $this->assertingEquals( $expectedC, $actualC );
        $this->assertingFalse( $actualD );
        $this->assertingEquals( $expectedE, $actualE );

        $this->_object->setThrowErrors( true );

        $this->expectingExceptionMessageRegex(
            '/(Error casting column "array" to string. Values \(json\) '
            . '{"0":"A","b":"B"})/i'
        );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->_object->compileQuerySelect( $fieldsD );
    }


    /**
     * Test escape errors
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQuerySelect
     */
    public function testCompileQuerySelectErrorA()
    {
        $this->_object->setDebugMode( false );
        $fields = array(array('A'), array('B'));

        $this->expectingExceptionMessageRegex(
            '/(Escape failt. Not a scalar type: "array")/i'
        );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->_object->compileQuerySelect( $fields );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQuerySet
     */
    public function testCompileQuerySet()
    {
        $fields = array(
            '_' => 'a=c',
            'texta' => 'textaNew', 'textb' => 'null', 'textc' => 'now()'
        );
        $actual = $this->_object->compileQuerySet( $fields );
        $expected = ' SET a=c,`texta`=\'textaNew\',`textb`=NULL,`textc`=NOW()';
        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQueryWhere
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::_compileQueryWhere
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQueryExpression
     */
    public function testCompileQueryWhere()
    {
        $array = array();
        $arrayA = array(
            'AND' => array(
                array('=' => array('name' => 'value'))
            )
        );
        $arrayB = array(
            'OR' => array(
                array(
                    'AND' => array(
                        array('=' => array('name' => 'value')),
                        // not quoted
                        array('_' => 'date >= now()'),
                        array('_' => 'string >= \'2000\''),
                        array('=' => array('list' => array(1, 2, 3, 4))),
                        // the following for code coverage
                        array('=' => array('key' => array('v1', 'v2'))),
                        array('_' =>
                            array('date <= now()', "date >= '2000-12-31'")
                        ),
                        array('LIKE' => array('like' => 'search')),
                        array('NOTLIKE' => array('notlike' => 'xyz')),
                        array('xLIKE' => array('xlike' => 'xyz')),
                        array('xNOTLIKE' => array('xnotlike' => 'xyz')),
                        array('LIKEx' => array('likex' => 'xyz')),
                        array('NOTLIKEx' => array('notlikex' => 'xyz')),
                    ),
                ),
                array(
                    'AND' => array(
                        array('<=' => array('key' => 'value')),
                    ),
                )
            ),
        );
        // a more realistic test
        $arrayC = array(
            'AND' => array(
                array('=' => array('name' => 'value')),
                array(
                    'OR' => array(
                        array('>=' => array('x' => 123)),
                        array('<=' => array('x' => 567))
                    )
                ),
                array('LIKE' => array('date' => 'foo')),
            )
        );

        $expected = ' WHERE 1=1';
        $expectedA = ' WHERE (`name`=\'value\')';
        $expectedB = ' WHERE (`name`=\'value\' AND date >= now() AND string >= '
            . '\'2000\' AND `list` IN (1,2,3,4) AND `key` IN (\'v1\',\'v2\') '
            . 'AND (date <= now() AND date >= \'2000-12-31\') AND '
            . '`like` LIKE \'%search%\' AND `notlike` NOT LIKE \'%xyz%\' AND '
            . '`xlike` LIKE \'%xyz\' AND `xnotlike` NOT LIKE \'%xyz\' AND '
            . '`likex` LIKE \'xyz%\' AND `notlikex` NOT LIKE \'xyz%\') OR '
            . '(`key`<=\'value\')';
        $expectedC = ' WHERE (`x`>=123 OR `x`<=567) AND (`name`=\'value\' '
            . 'AND `date` LIKE \'%foo%\')';

        $actual = $this->_object->compileQueryWhere( $array );
        $actualA = $this->_object->compileQueryWhere( $arrayA );
        $actualB = $this->_object->compileQueryWhere( $arrayB );
        $actualC = $this->_object->compileQueryWhere( $arrayC );

        $this->assertingEquals( $expected, $actual );
        $this->assertingEquals( $expectedA, $actualA );
        $this->assertingEquals( $expectedB, $actualB );
        $this->assertingEquals( $expectedC, $actualC );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQueryWhere
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::_compileQueryWhereSimple
     */
    public function testCompileQueryWhereCompatMode()
    {
        $arrayA = array('name' => 'Name', 'date' => '2000-12-31');
        $arrayB = array('_' => 'name >= \'Name\'', 'date' => '2000-12-31');
        $arrayC = array('escaped = \'value\'');

        $actualA = $this->_object->compileQueryWhere( $arrayA );
        $actualB = $this->_object->compileQueryWhere( $arrayB );
        $actualC = $this->_object->compileQueryWhere( $arrayC );

        $expectedA = ' WHERE `name`=\'Name\' AND `date`=\'2000-12-31\'';
        $expectedB = ' WHERE name >= \'Name\' AND `date`=\'2000-12-31\'';
        $expectedC = ' WHERE escaped = \\\'value\\\'';

        $this->assertingEquals( $expectedA, $actualA );
        $this->assertingEquals( $expectedB, $actualB );
        $this->assertingEquals( $expectedC, $actualC );
    }


    /**
     * error tests and for code coverage
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQueryWhere
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::_compileQueryWhere
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::_setError
     */
    public function testCompileQueryWhereErrorB()
    {
        $array = array(
            'OR' => array(
                'expression not an array'
            )
        );

        $this->_object->setThrowErrors( false );
        $this->_object->setDebugMode( false );
        $actual = $this->_object->compileQueryWhere( $array );
        $this->assertingFalse( $actual );

        $this->_object->setThrowErrors( true );
        $this->_object->setDebugMode( false );
        $regex = '/(Invalid sub-expression. Must be \'\[operator\] => '
            . '\[key\/value\]\'. Found \(json\): "expression not an array")/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->_object->compileQueryWhere( $array );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQueryGroupBy
     */
    public function testCompileQueryGroupBy()
    {
        $groupbyA = array('a', 'b', 'c');
        $expectedA = ' GROUP BY `a`,`b`,`c`';
        $actualA = $this->_object->compileQueryGroupBy( $groupbyA );
        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQueryOrderBy
     */
    public function testCompileQueryOrderBy()
    {
        $orderA = array('a', 'b', 'c');
        $orderB = array('a' => 'DESC', 'b' => 'DESC', 'c' => 'ConvertToASC');

        $expectedA = ' ORDER BY `a` ASC,`b` ASC,`c` ASC';
        $expectedB = ' ORDER BY `a` DESC,`b` DESC,`c` ASC';

        $actualA = $this->_object->compileQueryOrderBy( $orderA );
        $actualB = $this->_object->compileQueryOrderBy( $orderB );

        $this->assertingEquals( $expectedA, $actualA );
        $this->assertingEquals( $expectedB, $actualB );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::compileQueryLimit
     */
    public function testCompileQueryLimit()
    {
        $limitA = array(0, 10); // return 10 beginning from 0
        $limitB = array(10); // return 10 depending on sortation
        $limitC = array();

        $expectedA = ' LIMIT 10 OFFSET 0';
        $expectedB = ' LIMIT 10';
        $expectedC = '';

        $actualA = $this->_object->compileQueryLimit( $limitA );
        $actualB = $this->_object->compileQueryLimit( $limitB );
        $actualC = $this->_object->compileQueryLimit( $limitC );

        $this->assertingEquals( $expectedA, $actualA );
        $this->assertingEquals( $expectedB, $actualB );
        $this->assertingEquals( $expectedC, $actualC );
        $this->assertingEmpty( $actualC );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::sqlImplode
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::_setError
     *
     * @ todo // deprecated! api change needed,now 100% code coverage
     *         'deprecated_Test IS NOT NULL' => false,
     * @ todo asString => false ??
     *      'suggested_Test' => 'IS NOT NULL',
     */
    public function testSqlImplode()
    {
        // --- setup -----------------------------------------------------------
        $separator = ',';
        $data = array(
            'key' => 'value',
            'a' => 'b',
            'integer' => 123,
            'float' => 1.23,
            'timestamp' => '1970-12-31 23:58:59',
            'mix' => 'mix val',
            'defenum' => 'ab',
            /** @todo // deprecated! api change needed,now 100% code coverage */
//            'deprecated_Test IS NOT NULL' => false,
//            /** @todo asString => false ?? */
//            'suggested_Test' => 'IS NOT NULL', // ->
        );
        $withKeys = true;
        $defaults = array(
            'key' => array('type' => 'char', 'asstring' => true, 'default' => ''),
            'a' => array('type' => 'char', 'asstring' => true, 'default' => ''),
            'integer' => array(
                'type' => 'integer', 'asstring' => false, 'default' => 0
            ),
            'float' => array(
                'type' => 'float', 'asstring' => false, 'default' => 0.00
            ),
            'timestamp' => array(
                'type' => 'timestamp', 'asstring' => true,
                'default' => 'CURRENT_TIMESTAMP'
            ),
            'mix' => array(
                'type' => 'unknown', 'asstring' => true, 'default' => 'a default'
            ),
            'defenum' => array(
                'type' => 'unknown', 'asstring' => true, 'default' => array('a', 'b')
            ),
            'istest' => array(
                /** @todo test value */
                'type' => 'unknown', 'asstring' => false, 'default' => '',
            ),
        );

        $valwrap = '´';
        $keyValWrap = '=';
        $keyWrap = '"';

        // --- end setup -------------------------------------------------------

        $actualA = $this->_object->sqlImplode(
            $separator, $data, $withKeys, $defaults, $valwrap, $keyValWrap,
            $keyWrap
        );
        $expectedA = '"key"=´value´,"a"=´b´,"integer"=123,"float"=1.23,'
            . '"timestamp"=´1970-12-31 23:58:59´,"mix"=´a default´,'
            . '"defenum"=´a´';

        $this->assertingEquals( $expectedA, $actualA );

        // test without defaults
        $actualB = $this->_object->sqlImplode(
            $separator, $data, $withKeys, $defaults = array(), $valwrap,
            $keyValWrap, $keyWrap
        );
        $expectedB = '"key"=´value´,"a"=´b´,"integer"=´123´,'
            . '"float"=´1.23´,"timestamp"=´1970-12-31 23:58:59´,'
            . '"mix"=´mix val´,"defenum"=´ab´'
            . '';
        $this->assertingEquals( $expectedB, $actualB );

        // test without keys, no defaults: normal implode
        $actualC = $this->_object->sqlImplode(
            $separator, $data, false, array(), $valwrap, $keyValWrap, $keyWrap
        );
        $expectedC = 'value,b,123,1.23,1970-12-31 23:58:59,mix val,ab';
        $this->assertingEquals( $expectedC, $actualC );

        //
        // exception when valWrap is no string
        $this->_object->setDebugMode( false );
        $this->_object->setThrowErrors( false );
        $actual = $this->_object->sqlImplode(
            $separator, $data, $withKeys = true, $defaults = array(),
            $valwrap = array(), $keyValWrap, $keyWrap
        );
        $this->assertingFalse( $actual );
        $x = $this->_object->getErrors();
        $message = $x[0]['message'];
        $this->assertingEquals(
            'Value could not be used. Value warp: "array"', $message
        );

        // as exception
        $this->_object->setThrowErrors( true );
        $this->expectingExceptionMessageRegex(
            '/(Value could not be used. Value warp: "array")/i'
        );
        $this->expectingException( 'Mumsys_Db_Exception' );

        $this->_object->sqlImplode(
            $separator, $data, true, array(), array(), $keyValWrap, $keyWrap
        );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::getServerInfo
     *
     * Possible:
     * 10.0.17-MariaDB-log
     * 5.5.5-10.0.17-MariaDB-log
     * 5.5.5-10.3.11-MariaDB
     * 5.5.5-MySQL-log
     */
    public function testGetServerInfo()
    {
        $actual = $this->_object->getServerInfo();
        //
        $test = preg_match(
            '/^(\d{1,3}.\d{1,3}.\d{1,3})(.*)((mariadb|mysql).*)/i',
            $actual
        );
        $this->assertingTrue( ( $test == 1 ), print_r( $actual, true ) );
    }


    /**
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::escape
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::_setError
     */
    public function testEscape()
    {
        $test = array(
            "\x00" => '\0',
            "\x1a" => '\Z',
            '\n' => '\\\n',
            "\n" => "\\n",
            '\r' => '\\\r',
            "\r" => "\\r",
            '\\' => '\\\\',
            'now()' => 'now()',
            'NOW()' => 'NOW()',
            'öäüß?' => 'öäüß?',
        );

        foreach ( $test as $toTest => $expected ) {
            $actual = $this->_object->escape( $toTest );
            $this->assertingEquals( $expected, $actual );
        }

        // with re connect
        $closed = $this->_object->close();
        $this->assertingTrue( $closed );
        $actual = $this->_object->escape( '\n' );
        $this->assertingEquals( '\\\n', $actual );

        // not scalar exception
        $this->expectingExceptionMessageRegex( '/(Not a scalar type: "array")/i' );
        $this->expectingException( 'Mumsys_Db_Exception' );
        $this->_object->escape( array(1, 2, 3) );
    }

        /**
         * Test escape errors
         * @covers Mumsys_Db_Driver_Mysql_Mysqli::escape
         */
    //    public function testEscapeErrorA()
    //    {
    //        $this->_object->setThrowErrors(false);
    //        $this->_object->setDebugMode(false);
    //
    //        $toTest = array();
    //
    //        $this->_object->setThrowErrors(true);
    //        $this->expectingExceptionMessageRegex('Escape failt. Not a scalar type:
    //        "array"');
    //        $this->expectingException('Mumsys_Db_Exception');
    //        $this->_object->escape($toTest);
    //    }
    //
    //    public function testSaveEscapedCharsetDefaultAndUtf8()
    //    {
    //        $object = $this->_object;
    //
    //        $params = array(
    //            'fields' => array('texta' => 'öäüß?&$%§'),
    //            'table' => $this->_tempTable,
    //            'where' => array('ida' => 1)
    //        );
    //
    //        $object->update($params);
    //        $actualA = $object->getQuery();
    //        // query in utf8, connection in latin1, ok
    //        $expectedA = 'UPDATE mumsysunittesttemp SET `texta`=\'öäüß?&$%§\'
    //        WHERE `ida`=\'1\'';
    //
    //        $data = $object->fetchData('SELECT texta FROM mumsysunittesttemp
    //        WHERE ida=1', 'LINE');
    //        $actualB = $data['texta'];
    //        $expectedB = 'öäüß?&$%§';
    //
    //        $this->assertingEquals($expectedA, $actualA);
    //        $this->assertingEquals($expectedB, $actualB);
    //
    //        // what is in the DB when using utf8 connection?
    //        $this->_dbConfig['charset'] = 'utf8';
    //        $object = new Mumsys_Db_Driver_Mysql($this->_dbConfig);
    //
    //        $data = $object->fetchData('SELECT texta FROM mumsysunittesttemp
    //        WHERE ida=1', 'LINE');
    //        $actualC = $data['texta'];
    //        $expectedC = 'Ã¶Ã¤Ã¼ÃŸ?&$%Â§'; // crap is correct! if not this
    //        will be a problem!
    //
    //        $this->assertingEquals($expectedC, $actualC);
    //        $this->assertingEquals('utf8', $object->getCharset());
    //    }


    /**
     * just quoting!! not escaping!
     * @covers Mumsys_Db_Driver_Mysql_Mysqli::quote
     */
    public function testQuote()
    {
        $test = array(
            '"' => '\'"\'',
            '\'myQuote\'' => '\'\'myQuote\'\'',
            1234 => 1234
        );

        foreach ( $test as $toTest => $expected ) {
            $actual = $this->_object->quote( $toTest );
            $this->assertingEquals( $expected, $actual );
        }
    }


    //
    // --- helper -------------------------------------------------------------
    //


    private function _createTempTable( $table = 'mumsysunittesttable' )
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

        $res = $this->_object->query( $sql );

        return;
    }


    /**
     * Drops the test table
     * @param string $table
     */
    private function _dropTempTable( $table = 'mumsysunittesttable' )
    {
        $res = $this->_object->query( 'DROP TABLE `' . $table . '`' );
    }


    private function _createTempTableData( $table = 'mumsysunittesttable' )
    {
        // insert test data
        $data = array(
            'INSERT INTO ' . $table
            . ' SET ida = 1, idb = 1, idc = 1, texta=\'texta1\', textb=\'textb1\'',
            'INSERT INTO ' . $table
            . ' SET ida = 2, idb = 2, idc = 2, texta=\'texta2\', textb=\'textb2\'',
            'INSERT INTO ' . $table
            . ' SET ida = 3, idb = 3, idc = 3, texta=\'texta3\', textb=\'textb3\'',
        );

        foreach ( $data as $sql ) {
            $r = $this->_object->query( $sql );
        }
    }


    private function _getTempTableColumnValues()
    {
        return array(
            array(
                'field' => 'ida',
                'type' => 'int',
                'null' => 'NO',
                'key' => 'PRI',
                'default' => null,
                'extra' => 'auto_increment',
                'typeval' => '10',
                'typeattr' => 'unsigned',
            ),
            array(
                'field' => 'idb',
                'type' => 'tinyint',
                'null' => 'NO',
                'key' => '',
                'default' => '0',
                'extra' => '',
                'typeval' => '1',
            ),
            array(
                'field' => 'idc',
                'type' => 'smallint',
                'null' => 'NO',
                'key' => '',
                'default' => '0',
                'extra' => '',
                'typeval' => '2',
            ),
            array(
                'field' => 'idd',
                'type' => 'bigint',
                'null' => 'NO',
                'key' => '',
                'default' => '0',
                'extra' => '',
                'typeval' => '1',
            ),
            array(
                'field' => 'numa',
                'type' => 'float',
                'null' => 'NO',
                'key' => '',
                'default' => '0.0000',
                'extra' => '',
                'typeval' => '8,4',
                'typeattr' => 'unsigned',
            ),
            array(
                'field' => 'numb',
                'type' => 'decimal',
                'null' => 'NO',
                'key' => '',
                'default' => '0.0000',
                'extra' => '',
                'typeval' => '8,4',
                'typeattr' => 'unsigned',
            ),
            array(
                'field' => 'numc',
                'type' => 'double',
                'null' => 'NO',
                'key' => '',
                'default' => '0.0000',
                'extra' => '',
                'typeval' => '8,4',
                'typeattr' => 'unsigned',
            ),
            array(
                'field' => 'numd',
                'type' => 'float unsigned',
                'null' => 'NO',
                'key' => '',
                'default' => '0',
                'extra' => '',
                'typeval' => false,
            ),
            array(
                'field' => 'vartexta',
                'type' => 'enum',
                'null' => 'NO',
                'key' => '',
                'default' => 'a',
                'extra' => '',
                'typeval' => array(
                    0 => '',
                    1 => 'a',
                    2 => 'b',
                    3 => 'c',
                ),
            ),
            array(
                'field' => 'vartextb',
                'type' => 'set',
                'null' => 'NO',
                'key' => '',
                'default' => 'a',
                'extra' => '',
                'typeval' => array(
                    0 => '',
                    1 => 'a',
                    2 => 'b',
                    3 => 'c',
                ),
            ),
            array(
                'field' => 'texta',
                'type' => 'char',
                'null' => 'NO',
                'key' => 'UNI',
                'default' => null,
                'extra' => '',
                'typeval' => '255',
            ),
            array(
                'field' => 'textb',
                'type' => 'varchar',
                'null' => 'NO',
                'key' => 'UNI',
                'default' => '',
                'extra' => '',
                'typeval' => '255',
            ),
            array(
                'field' => 'textc',
                'type' => 'text',
                'null' => 'NO',
                'key' => '',
                'default' => '\'\'',
                'extra' => '',
            ),
            array(
                'field' => 'textd',
                'type' => 'tinytext',
                'null' => 'NO',
                'key' => '',
                'default' => '\'\'',
                'extra' => '',
            ),
        );
    }

}
