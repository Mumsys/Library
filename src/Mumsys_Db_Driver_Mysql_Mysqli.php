<?php

/**
 * Mumsys_Db_Driver_Mysql_Mysqli
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2013 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Db
 * Created: 2013-12-13
 */


/**
 * Mysqli Driver for database purpose
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Db
 */
class Mumsys_Db_Driver_Mysql_Mysqli
    extends Mumsys_Db_Driver_Abstract
    implements Mumsys_Db_Driver_Interface, Mumsys_Db_Driver_Query_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '3.1.0';


    /**
     * Create a database connection an select current database and client
     * charset if given.
     *
     * @return mysqli|resource|false Retruns the database resource on succsess
     * or throws exception
     * @throws Mumsys_Db_Exception On connection error or current database can
     * not be selected
     */
    public function connect()
    {
        try
        {
            if ( $this->_isConnected && $this->_dbc ) {
                return $this->_dbc;
            }

            $this->_dbc = mysqli_init();

            /* mysqli_options($this->_dbc, MYSQLI_INIT_COMMAND, 'SET NAMES \'utf8\'');
             *
             * MYSQLI_CLIENT_COMPRESS       Use compression protocol
             * MYSQLI_CLIENT_FOUND_ROWS     return number of matched rows, not the number of affected rows
             * MYSQLI_CLIENT_IGNORE_SPACE   Allow spaces after function names. Makes all function names reserved words.
             * MYSQLI_CLIENT_INTERACTIVE    Allow interactive_timeout seconds (instead of wait_timeout seconds) of
             *                              inactivity before closing the connection
             * MYSQLI_CLIENT_SSL            Use SSL (encryption)
             */

            if ( $this->_conCompession ) {
                $chk = mysqli_real_connect(
                    $this->_dbc,
                    $this->_host,
                    $this->_username,
                    $this->_password,
                    $this->_dbName,
                    $this->_port,
                    $this->_socket,
                    MYSQLI_CLIENT_COMPRESS
                );
            } else {
                $chk = mysqli_real_connect(
                    $this->_dbc,
                    $this->_host,
                    $this->_username,
                    $this->_password,
                    $this->_dbName,
                    $this->_port,
                    $this->_socket
                );
            }

            if ( !$chk ) {
                throw new Mumsys_Db_Exception( 'Connection failure' );
            }
            $this->_isConnected = (bool) $chk;

            if ( $this->_clientCharacterSet ) {
                $this->setCharset( $this->_clientCharacterSet );
            }
        }
        catch ( Exception $e ) {
            try {
                $errStr = $this->sqlError();
            } catch( Error $err ) {
                $errStr = '';
            }
//            throw $e;
            $msg = 'Connection to database failed. Messages: "' . $e->getMessage()
                . '", "' . $errStr . '"';
            return $this->_setError( $msg, null, $e );
        }

        return $this->_dbc;
    }


    /**
     * Closes the db connection.
     *
     * This methode will be called at least when __destuct event occur.
     *
     * @return bool Returns the result of mysqli_close() function. True for
     * success
     */
    public function close()
    {
        $return = true;
        if ( $this->_dbc ) {
            $return = @mysqli_close( $this->_dbc );
        }
        $this->_isConnected = false;
        $this->_dbc = null;

        return $return;
    }


    /**
     * Sets the client character set.
     * MySQL server >= 5.0.7
     * @see http://php.net/manual/en/mysqlinfo.concepts.charset.php
     *
     * @param string $charset A valid character set name
     * @return boolean True on success or false on failure
     * @throws Mumsys_Db_Exception if throw errors was set
     */
    public function setCharset( $charset )
    {
        if ( ( $result = mysqli_set_charset( $this->_dbc, $charset ) ) == false ) {
            return $this->_setError( 'Setting client character set failt' );
        }

        return $result;
    }


    /**
     * Returns the client character set.
     *
     * @return string|false Name of the character or false on error
     * @throws Mumsys_Db_Exception if throw errors was set
     */
    public function getCharset()
    {
        try {
            $result = mysqli_get_charset( $this->_dbc );
        } catch ( Error $ex ) {
            return $this->_setError( 'Getting character set failt: "' . $ex->getMessage() . '"' );
        }

        return $result;
    }


    /**
     * Select a database.
     *
     * @param string $db Name of the db
     * @return boolean Returns true on success or false or throws exception if
     * set
     * @throws Mumsys_Db_Exception Throws exception if database can not be
     * selected
     */
    public function selectDB( $dbName )
    {
        $dbName = (string) $dbName;

        if ( $dbName == $this->_dbName ) {
            return true;
        }

        if ( mysqli_select_db( $this->_dbc, $dbName ) === false ) {
            $error = 'Can\'t select db. ' . $this->sqlError();
            return $this->_setError( $error );
        }

        $this->_dbName = $dbName;

        return true;
    }


    /**
     * Fetch Database names form current connection.
     *
     * @return array Returns an associative array with the database-name as key
     * and as value or false on failure
     */
    public function showDBs()
    {
        // $res = @mysql_list_dbs($this->_dbc);
        $sql = 'SHOW DATABASES';
        return $this->fetchData( $sql, 'KEYGOKEY' );
    }


    /**
     * Fetch tabel names from current db and their connection information
     *
     * @param string $db Database name
     * @return array assoc array which the tables as key and as array value or
     * false on failure
     */
    public function showTables()
    {
        $sql = 'SHOW TABLES FROM ' . $this->escape( $this->_dbName );
        return $this->fetchData( $sql, 'KEYGOKEY' );
    }


    /**
     * Execute a given sql statement.
     *
     * If connection was not made befor it will be created.
     * General info: For SELECT, SHOW, DESCRIBE, EXPLAIN and other
     * statements returning resultset, query() returns a
     * Mumsys_Db_Driver_Mysql_Result on success, or FALSE on error.
     * For other type of SQL statements, INSERT, UPDATE, DELETE, DROP, etc.
     * mysql_query() returns TRUE on success or FALSE on error.
     *
     * @param string $sql Query to be executed
     * @param boolean $unbuffered Flag to executed an unbuffered query
     * default: false
     *
     * @return Mumsys_Db_Driver_Result_Interface|false Mumsys_Db_Driver_Mysql_Result
     * object or false on error.
     * @throws Mumsys_Db_Exception Throws exception if
     * - database connection was not made and fails
     * - on empty sql statement (if throw errors was set)
     * - on query error (if throw errors was set)
     */
    public function query( $sql = false, $unbuffered = false )
    {
        if ( $sql ) {
            $this->_sql = (string) $sql;
        } else {
            return $this->_setError( 'Query empty. Cant not query empty sql statment' );
        }

        if ( $this->_dbc === null ) {
            $this->connect();
        }

        $this->_errorNumber = 0;
        $this->_errorMessage = '';

        if ( $unbuffered ) {
            return $this->_setError( 'Unbuffered querys not implemented yet' );
        } else {
            try {
                $result = mysqli_query( $this->_dbc, $this->_sql );
            }
            catch ( Throwable $ex ) {
                // ???
            }
        }
        $this->_numQuerys++;

        if ( $this->_debug ) {
            $this->_querys[] = $sql;
        }

        if ( ( $error = $this->sqlError() ) ) {
            return $this->_setError( $error );
        }

        $oRes = new Mumsys_Db_Driver_Mysql_Mysqli_Result( $this, $result );

        return $oRes;
    }


    /**
     * Execute an unbuffered query
     * Alias methode of query()
     * @see query()
     */
    public function queryUnbuffered( $sql = false )
    {
        return $this->_setError( 'Unbuffered querys not implemented yet' );
    }


    /**
     * Test if given resource (Mysql_Dbr) from a query is in error state
     *
     * @param resource $res The result set of a mysql_query
     * @return boolean return true on error, false on no error
     */
    public function isError( $res )
    {
        if ( $res === false ) {
            return true;
        }

        return false;
    }


    /**
     * Get error message from a query error.
     *
     * @return string Returns the error text from the last MySQL function, or
     * '' (empty string) for no error.
     */
    public function sqlError()
    {
        return mysqli_error( $this->_dbc );
    }


    /**
     * Returns the error number from the last MySQL function, or 0 (zero) if no
     * error occurred.
     *
     * @return integer Error code
     */
    public function sqlErrno()
    {
        return (int) @mysqli_errno( $this->_dbc );
    }


    /**
     * Fetch the complete data of a sql query and return the list of data.
     * Possible rules can be fetched: <br/>
     * - 'OBJECT',
     * - 'ARRAY'
     * - 'NUM'
     * - 'GETIDS'
     * - 'ASSOC' Returns a list of records within an associativ array
     * - 'LINE' get the first record. An associativ array will return
     * - 'ROW' get the first record. A numeric array will return
     * - 'KEYGOVAL' First column will be used as array key (eg: the id) the
     * second col as value, So, selecting two colums is required
     * - 'KEYGOKEY' First column will be used as array key and value,
     * - 'KEYGOASSOC' First column will be used as array key and all values
     * of a row as array value
     *
     * Note: With huge data you may get problems. Take care of it.
     *
     * @param string $sql The sql query to be performed
     * @param string $way The type, in lower or upper case, to return the data
     * set. Default: 'assoc'; possible values: 'OBJECT', 'ARRAY', 'NUM',
     * 'GETIDS', 'LINE', 'ROW', 'KEYGOVAL', 'KEYGOKEY'.
     * @return array Returns the result as array or false on failure or if no
     * more record exists
     */
    public function fetchData( $sql, $way = 'ASSOC' )
    {
        $oRes = $this->query( $sql );

        if ( $oRes === false ) {
            return false;
        }

        $data = array();
        switch ( strtoupper( $way ) )
        {
            case 'ASSOC':
            case 'ARRAY':
            case 'OBJECT':
            case 'NUM':
                while ( $row = $oRes->fetch( $way ) ) {
                    $data[] = $row;
                }
                break;

            case 'GETIDS':
                while ( $row = $oRes->fetch( 'ROW' ) ) {
                    array_push( $data, $row[0] );
                }
                break;

            case 'LINE':
                $data = $oRes->fetch( 'ASSOC' );
                break;

            case 'ROW':
                $data = $oRes->fetch( 'ROW' );
                break;

            case 'KEYGOVAL':
                while ( $row = $oRes->fetch( 'NUM' ) ) {
                    $data[$row[0]] = $row[1];
                }
                break;

            case 'KEYGOKEY':
                while ( $row = $oRes->fetch( 'NUM' ) ) {
                    $data[$row[0]] = $row[0];
                }
                break;

            case 'KEYGOASSOC':
                while ( $row = $oRes->fetch( 'ASSOC' ) ) {
                    $data[reset( $row )] = $row;
                }
                break;

            default:
                while ( $row = $oRes->fetch( 'ASSOC' ) ) {
                    $data[] = $row;
                }
                break;
        }

        $oRes->free();

        return $data;
    }


    /**
     * Show propertys of a table or a given column.
     *
     * @todo More examples for expected return values
     *
     * @param string $table Table to show the columns from
     * @param string $field Optional columne to get informations from
     *
     * @return array|false The columns propertys with lower case array keys
     * @throws Mumsys_Db_Exception Throws exception on error
     */
    public function showColumns( $table = '', $field = null )
    {
        $data = array();
        if ( isset( $field ) ) {
            $this->selectDB( $this->_dbName );
            $oRes = $this->query( 'DESCRIBE ' . $table . ' ' . $field );
        } else {
            $oRes = $this->query(
                'SHOW COLUMNS FROM ' . $this->_dbName . '.' . $table
            );
        }

        if ( $oRes === false ) {
            return $this->_setError( 'Error getting columns' );
        }

        $i = 0;
        while ( $xrow = $oRes->fetch( 'ASSOC' ) ) {
            $row = array();
            foreach ( $xrow as $key => &$val ) {
                $row[strtolower( $key )] = $val;
            }
            unset( $xrow );

            $rule = '@^(set|enum|int|tinyint|smallint|mediumint|bigint|char'
                . '|varchar|decimal|float|double|tinytext)\((.+)\)(\D*)$@i';

            if ( preg_match( $rule, $row['type'], $tmp ) ) {
                $row['type'] = preg_replace( '@\(.*@s', '', $row['type'] );
            }

            switch ( $row['type'] )
            {
                /**
                 * [NATIONAL] VARCHAR(M) [CHARACTER SET charset_name] [COLLATE collation_name]
                 * A variable-length string. M represents the maximum column length in characters. The range of
                 * M is 0 to 65,535. The effective maximum length of a VARCHAR is subject to the maximum row size
                 * (65,535 bytes, which is shared among all columns) and the character set used. For example, utf8
                 * characters can require up to three bytes per character, so a VARCHAR column that uses the utf8
                 * character set can be declared to be a maximum of 21,844 characters. See Section E.7.4, “Table
                 * Column-Count and Row-Size Limits”.
                 * MySQL stores VARCHAR values as a one-byte or two-byte length prefix plus data. The length prefix
                 * indicates the number of bytes in the value. A VARCHAR column uses one length byte if values
                 * require no more than 255 bytes, two length bytes if values may require more than 255 bytes.
                 * Note: MySQL 5.1 follows the standard SQL specification, and does not remove trailing spaces from
                 * VARCHAR values.
                 * VARCHAR is shorthand for CHARACTER VARYING. NATIONAL VARCHAR is the standard SQL way to define
                 * that a VARCHAR column should use some predefined character set. MySQL 4.1 and up uses utf8 as
                 * this predefined character set. Section 9.1.3.6, “National Character Set”. NVARCHAR is shorthand
                 * for NATIONAL VARCHAR.
                 */
                case 'varchar':
                /**
                 * [NATIONAL] CHAR[(M)] [CHARACTER SET charset_name] [COLLATE collation_name]
                 * A fixed-length string that is always right-padded with spaces to the specified length when
                 * stored. M represents the column length in characters. The range of M is 0 to 255. If M is
                 * omitted, the length is 1.
                 * Note: Trailing spaces are removed when CHAR values are retrieved unless the
                 * PAD_CHAR_TO_FULL_LENGTH SQL mode is enabled.
                 * CHAR is shorthand for CHARACTER. NATIONAL CHAR (or its equivalent short form, NCHAR) is the
                 * standard SQL way to define that a CHAR column should use some predefined character set. MySQL
                 * 4.1 and up uses utf8 as this predefined character set. Section 9.1.3.6,"National Character Set".
                 * The CHAR BYTE data type is an alias for the BINARY data type. This is a compatibility feature.
                 * MySQL permits you to create a column of type CHAR(0). This is useful primarily when you have
                 * to be compliant with old applications that depend on the existence of a column but that do not
                 * actually use its value. CHAR(0) is also quite nice when you need a column that can take only
                 * two values: A column that is defined as CHAR(0) NULL occupies only one bit and can take only
                 * the values NULL and '' (the empty string).
                 */
                case 'char':
                /**
                 * INT | INTEGER
                 * INT[(M)] [UNSIGNED] [ZEROFILL]
                 * A normal-size integer. The signed range is -2147483648 to
                 * 2147483647. The unsigned range is 0 to 4294967295.
                 */
                case 'int':

                case 'tinyint':
                /**
                 * SMALLINT[(M)] [UNSIGNED] [ZEROFILL]
                 * A small integer. The signed range is -32768 to 32767. The
                 * unsigned range is 0 to 65535.
                 */
                case 'smallint':
                /**
                 * MEDIUMINT[(M)] [UNSIGNED] [ZEROFILL]
                 * A medium-sized integer. The signed range is -8388608 to
                 * 8388607. The unsigned range is 0 to 16777215.
                 */
                case 'mediumint':
                /**
                 * BIGINT
                 * BIGINT[(M)] [UNSIGNED] [ZEROFILL]
                 * A large integer. The signed range is -9223372036854775808
                 * to 9223372036854775807. The unsigned range is 0 to
                 * 18446744073709551615.
                 */
                case 'bigint':
                /**
                 * SERIAL is an alias for BIGINT UNSIGNED NOT NULL
                 * AUTO_INCREMENT UNIQUE.
                 */
                case 'serial':
                /**
                 * DECIMAL[(M[,D])] [UNSIGNED] [ZEROFILL]
                 * A packed “exact” fixed-point number. M is the total number of digits (the precision) and
                 * D is the number of digits after the decimal point (the scale). The decimal point and
                 * (for negative numbers) the “-” sign are not counted in M. If D is 0, values have no decimal
                 * point or fractional part. The maximum number of digits (M) for DECIMAL is 65. The maximum
                 * number of supported decimals (D) is 30. If D is omitted, the default is 0. If M is omitted,
                 * the default is 10.
                 * UNSIGNED, if specified, disallows negative values.
                 * All basic calculations (+, -, *, /) with DECIMAL columns are done with a precision of 65 digits
                 */
                case 'decimal':
                /**
                 * FLOAT[(M,D)] [UNSIGNED] [ZEROFILL]
                 * A small (single-precision) floating-point number. Permissible values are -3.402823466E+38
                 * to -1.175494351E-38, 0, and 1.175494351E-38 to 3.402823466E+38. These are the theoretical
                 * limits, based on the IEEE standard. The actual range might be slightly smaller depending
                 * on your hardware or operating system.
                 * M is the total number of digits and D is the number of digits following the decimal point.
                 * If M and D are omitted, values are stored to the limits permitted by the hardware. A
                 * single-precision floating-point number is accurate to approximately 7 decimal places.
                 * UNSIGNED, if specified, disallows negative values.
                 * Using FLOAT might give you some unexpected problems because all calculations in MySQL are
                 * done with double precision
                 */
                case 'float':
                case 'float unsigned':
                /**
                 * DOUBLE[(M,D)] [UNSIGNED] [ZEROFILL]
                 * A normal-size (double-precision) floating-point number. Permissible values are
                 * -1.7976931348623157E+308 to -2.2250738585072014E-308, 0, and 2.2250738585072014E-308 to
                 * 1.7976931348623157E+308. These are the theoretical limits, based on the IEEE standard.
                 * The actual range might be slightly smaller depending on your hardware or operating system.
                 * M is the total number of digits and D is the number of digits following the decimal point.
                 * If M and D are omitted, values are stored to the limits permitted by the hardware. A
                 * double-precision floating-point number is accurate to approximately 15 decimal places.
                 * UNSIGNED, if specified, disallows negative values.
                 */
                case 'double':
                    if ( isset( $tmp[2] ) ) {
                        $row['typeval'] = $tmp[2];
                    } else {
                        // float without a limit
                        $row['typeval'] = false;
                    }
                    // enum attr.
                    if ( isset( $tmp[3] ) && $tmp[3] != '' ) {
                        //$row['TypeAttr'] = explode(' ', trim($tmp[3]));
                        $row['typeattr'] = trim( $tmp[3] );
                    }

                    break;

                /**
                 * ENUM('value1','value2',...) [CHARACTER SET charset_name] [COLLATE collation_name]
                 * An enumeration. A string object that can have only one value, chosen from the list of values
                 * 'value1', 'value2', ..., NULL or the special '' error value. An ENUM column can have a maximum
                 * of 65,535 distinct values. ENUM values are represented internally as integers.
                 */
                case 'enum':
                /**
                 * SET('value1','value2',...) [CHARACTER SET charset_name] [COLLATE collation_name]
                 * A set. A string object that can have zero or more values, each of which must be chosen from the
                 * list of values 'value1', 'value2', ... A SET column can have a maximum of 64 members. SET
                 * values are represented internally as integers.
                 */
                case 'set':
                    $r = explode( ',', str_replace( "'", '', $tmp[2] ) );
                    $a = array(0 => ''); // always default in mysql
                    $row['typeval'] = array_merge( $a, $r );
                    unset( $a, $r );

                    break;

                /**
                 * TEXT[(M)] [CHARACTER SET charset_name] [COLLATE collation_name]
                 * A TEXT column with a maximum length of 65,535 (216 – 1) characters. The effective maximum
                 * length is less if the value contains multi-byte characters. Each TEXT value is stored using
                 * a two-byte length prefix that indicates the number of bytes in the value.
                 * An optional length M can be given for this type. If this is done, MySQL creates the column
                 * as the smallest TEXT type large enough to hold values M characters long.
                 */
                case 'text':
                    /** @todo to be checked why this exists actually good idea */
                    //$row['typeval'] = 65534;//65,535
                    break;
                    /** @todo to be checked why this exists actually good idea */
                case 'tinytext':
                    //wrong for tests but internal? $row['typeval'] = 254;//255
                    break;
                /*
                  case 'time':
                  case 'datetime':

                  break;
                  case 'timestamp':

                  break;
                 */
            }
            $data[] = $row;
            $i++;
        }

        $oRes->free();

        // hangling mysql_query() bug when using
        // "DESCRIBE table NotExistingField" which returns empty array*/
        if ( $field && $i == 0 && $data === array() ) {
            $msg = sprintf(
                'Error getting columns. Does the columne "%1$s" exists?',
                $field
            );

            return $this->_setError( $msg, 1 );
        }

        return $data;
    }


    /**
     * Get current system status for uptime, threads, queries, open tables,
     * flush tables and queries per second.
     *
     * For a complete list of other status variables, you have to use the
     * SHOW STATUS SQL command.
     *
     * @see http://php.net/manual/en/function.mysqli-stat.php
     *
     * @return string|false Returns a string or false if something went wrong
     */
    public function stat()
    {
        $return = false;
        if ( $this->_dbc ) {
            $resource = $this->_dbc;
        } else {
            $resource = null;
        }

        if ( ( $r = mysqli_stat( $resource ) ) ) {
            $return = $r;
        }
        return $return;
    }


    /**
     * Retruns the server info and version string.
     * Requires an activ connection on mysql.
     *
     * Returns for example:
     * 10.0.17-MariaDB-log
     * 5.5.5-10.0.17-MariaDB-log
     * 5.5.5-MySQL-log
     * @return string Version string including server name
     */
    public function getServerInfo()
    {
        return mysqli_get_server_info( $this->_dbc );
    }


    /**
     * Escape a string to send valid data with a query
     * It calls MySQL's library function mysql_real_escape_string,
     * which prepends backslashes to the following characters:
     * \x00, \n, \r, \, ', " and \x1a.
     *
     * @param string $string Value to be escaped
     * @return string Returns a valid escaped string
     */
    public function escape( $string = '' )
    {
        if ( !is_scalar( $string ) ) {
            $msg = sprintf(
                'Escape failt. Not a scalar type: "%1$s"', gettype( $string )
            );
            throw new Mumsys_Db_Exception( $msg );
        }

        switch ( strtolower( $string ) )
        {
            case 'now()':
            case 'asc':
            case 'desc':
            case ( $string === '' ):
                return $string;
        }

        if ( empty( $this->_dbc ) ) {
            $this->connect();
        }

        return mysqli_real_escape_string( $this->_dbc, $string );
    }


    /**
     * Quote a string or leave it as is if the values is an numeric value
     *
     * @param string $s string to be quoted
     * @param string $q quote type to be added; default quote value: '$s'
     * @return string the quoted string
     */
    public function quote( $s, $q = '\'' )
    {
        if ( is_numeric( $s ) ) {
            return $s;
        }
        $r = $q . $s . $q;

        return $r;
    }


    /**
     * Sets the current error.
     *
     * If $_throwErrors flag is enabled (default) the error will be thrown
     * otherwise a list of errors will be created an the program will go on
     * except on connection errors!
     *
     * @param string $message The error message
     * @param integer $code The error code
     * @param Exception $previous = NULL The previous exception used for the
     * exception chaining.
     *
     * @throws Mumsys_Db_Exception If connection can't be made or ThrowErrors
     * was set
     */
    protected function _setError( $message, $code = null, $previous = null )
    {
        if ( $code === null ) {
            try{
                $code = $this->sqlErrno();
            }
            catch ( Error $ex ) {
                $code = Mumsys_Db_Exception::ERRCODE_DEFAULT;
            }
        }

        $this->_errorNumber = $code;
        $this->_errorMessage = $message;

        if ( $this->_debug ) {
            //this blows up the memory! use carefully
            $this->_errorList[] = array('message' => $message, 'code' => $code);
        } else {
            $this->_errorList[0] = array('message' => $message, 'code' => $code);
        }

        if ( $this->_throwErrors || $this->_isConnected === false ) {
            throw new Mumsys_Db_Exception( $message, $code, $previous );
        }

        return false;
    }

    // -------------------------------------------------------------------------


    // -- compile querys ---

    /**
     * Update/ insert or delete data from database.
     *
     * Basicly this methode is used to update an existing entry by given
     * parameters but also can be used to delete or insert data by given action
     * value.
     *
     * @todo update counter = counter +1
     * @todo order by update counter = counter +1
     * @todo add tics for columnes?
     *
     * @param array $params Parametes as follow:
     * - [fields] required Fields to update or insert by given key=>value pairs
     * - [table] required Table to update
     * - [where] required Array key=>value construct for the where clause;
     * Note: only AND conditions will be made (at the moment). Values will be
     * escaped except if statment will be set in array key "_".  e.g.:
     * $where = array('_' =>
     * '(date > \'2010-12-31 00:00:00\' AND date < \'2011-12-31 23:59:59\') ')
     * - [order] optional; Set the order for select or update statements
     * - [limit] optional array containing the offset (the start value), limit count
     * for selects or just the limit count e.g. array(limit count) for select,
     * delete, updates.
     * - [updateall] optional if set an empty where parameter will be accepted
     * to update or delete ALL existing data without any restrictions.
     * be careful to use it
     *
     * @param $action Action to decide: update(default)|insert|delete|replace
     * @return Mumsys_Db_Driver_Mysql_Mysqli_Result|false Result object or false
     * @throws Mumsys_Db_Exception
     */
    protected function _save( $params, $action = 'update' )
    {
        $r = false;
        if ( ( empty( $params['updateall'] ) && ( empty( $params['where'] )
            && ( $action == 'update' || $action == 'delete' ) ) )
            || ( empty( $params['fields'] ) && $action != 'delete' )
            || empty( $params['table'] )
        ) {
            $message = 'Unknown key or empty values. No "' . $action . '" action';
            return $this->_setError( $message );
        } else {
            $where = '';
            if ( isset( $params['where'] ) ) {
                $where = $this->compileQueryWhere( $params['where'] );
            }

            $order = '';
            if ( isset( $params['order'] ) ) {
                $order = $this->compileQueryOrderBy( $params['order'] );
            }

            $limit = '';
            if ( isset( $params['limit'] ) ) {
                $limit = $this->compileQueryLimit( $params['limit'] );
            }

            $sql = '';
            $table = $this->escape( $params['table'] );

            switch ( $action )
            {
                case 'insert':
                    $sql = 'INSERT INTO ' . $table
                        . $this->compileQuerySet( $params['fields'] );
                    break;

                case 'replace':
                    $sql = 'REPLACE INTO ' . $table
                        . $this->compileQuerySet( $params['fields'] );
                    break;

                case 'update':
                    $sql = 'UPDATE ' . $table
                        . $this->compileQuerySet( $params['fields'] )
                        . $where
                        . $order
                        . $limit;
                    break;

                case 'delete':
                    $sql = 'DELETE FROM ' . $table
                        . $where . ''
                        . $limit;
                    break;

                case 'select':
                    $sql = sprintf(
                        'SELECT %1$s FROM %2$s%3$s%4$s%5$s',
                        $this->compileQuerySelect( $params['fields'] ),
                        $table,
                        $where,
                        $order,
                        $limit
                    );
                    break;
            }

            $r = $this->query( $sql );
        }
        return $r;
    }


    /**
     * Update data from database.
     *
     * @param array $params Parametes as follow:<br />
     * [fields] required Fields to update by given key=>value pairs<br />
     * [table] required Table to update<br />
     * [where] required Array key=>value pairs for the where clause;
     * Note: only AND conditions will be made<br />
     * [updateall] optional if set an empty where parameter will be accepted
     * to update ALL existing data without any restrictions. be careful to
     * use it<br/>
     * [order] optional; Set the order for select or update statements<br/>
     * [limit] optional array containing the offset (the start value), limit count
     * or just the limit count e.g. array(limit count).<br />
     *
     * @TODO return affected rows? yes/no?
     *
     * @return Mumsys_Db_Driver_Mysql_Mysqli_Result|false Result object or false on error
     * @throws Mumsys_Db_Exception
     */
    public function update( array $params = array() )
    {
        return $this->_save( $params, 'update' );
    }


    /**
     * Select data from the database.
     *
     * @see _save()
     *
     * @param array $params Parameters to be set:<br/>
     * [fields] required Fields to update or insert by a given array with
     * key=>value pairs<br/>
     * [table] required Table to update<br/>
     * [where] required Array key=>value construct for the where
     * clause; Note: only AND conditions will be made<br/>
     * [order] optional; Set the order for select or update statements<br/>
     * [limit] optional array containing the offset (the start value), limit count
     * for selects or just the limit count e.g. array(limit count) for select,
     * delete, updates.<br/>
     * @return Mumsys_Db_Driver_Mysql_Mysqli_Result Object or false on error
     */
    public function select( array $params = array() )
    {
        return $this->_save( $params, 'select' );
    }


    /**
     * Insert data to the storage.
     *
     * @see _save()
     *
     * @param array $params Parameters to be set:<br/>
     * [fields] required Fields to set in the insert command<br/>
     * [table] required Table to insert<br/>
     *
     * @return integer|false Return the last insert ID or false on error
     */
    public function insert( array $params = array() )
    {
        if ( ( $r = $this->_save( $params, 'insert' ) ) ) {
            return $r->lastInsertId();
        }

        return $r;
    }


    /**
     * Replace existing data.
     *
     * @see _save()
     *
     * @todo check return of _save()
     * @TODO return affected rows? yes/no?
     *
     * @param array $params Parameters to be set/ replaced:<br/>
     *  [fields] required Fields to set in the replace command<br/>
     *  [table] required Table to insert<br/>
     *
     * @return integer|false Returns number of affected rows or false on error
     */
    public function replace( array $params = array() )
    {
        if ( ( $r = $this->_save( $params, 'replace' ) ) ) {
            return $r->affectedRows();
            //return true;
        }
        return $r;
    }


    /**
     * Delete data from storage.
     *
     * @param array $params Parameter as key->value pairs to delete<br />
     * [table] required Table to delete from<br/>
     * [where] required Array key=>value pairs for the where clause; <br/>
     * Note: only AND conditions will be made to delete entrys
     * [where] required Array key=>value construct for the where clause;
     * Note: only AND conditions will be made<br/>
     * [updateall] optional if set an empty where parameter will be accepted
     * to update or delete ALL existing data without any restrictions.
     * be careful to use it<br/>
     * [order] optional; Set the order for select or update statements<br/>
     * [limit] optional array containing the offset (the start value), limit
     * count or just the limit count e.g. array(limit count)
     *
     * @return Mumsys_Db_Driver_Mysql_Mysqli_Result|false Returns false on error
     */
    public function delete( array $params = array() )
    {
        return $this->_save( $params, 'delete' );
    }


    /**
     * Retruns a single sql expression basicly made for a sql where clause.
     * E.g.: WHERE ( `a` LIKE '%b%' )
     * An expression looks like: array('operator'=>array('column' => 'value'))
     * @see $_queryOperators array keys of possilble operators.
     * Speacial operators:
     * - '_' string|array Can be used for unescaped and unquoted special
     * comparisons.
     * <b>Important:</b> If array values given:
     * - All values will NOT be escaped and NOT quoted. (security problem! Be
     * sure you know what you are doing)
     * - All values will be set as AND comparison.
     * - Array keys will be ignored.
     * - '=' array key/value pair or key/list of values. If list of values
     * given the mysql IN () operator will be used. Values can be numeric or
     * string. If values contains strings they will be quoted.
     *
     * Examples for '_' operator (mostly when using special functions):
     * <code>
     * array('_'=> array('date < now() OR date > \'2000-12-31 00:00:00\'))
     * array('_'=> 'date < now() AND date > \'2000-12-31 00:00:00\')
     * // ( a > 'b' ) AND ( c < 3 )
     * array('_' => array('a > \'b\'', 'c < 3')),
     * </code>
     *
     * Examples for '=' operator to switch to mysql IN () operator:
     * <code>
     * // default usage: WHERE name = 'value'
     * array('=' => array('name' => 'value'))
     * // WHERE ( list IN (1, 'string to be quoted', 3, 4) )
     * array('=' => array('list' => array(1,'string to be quoted', 3, 4)))
     * </code>
     *
     * @todo this code is CRAP and too complex! to be replaced soon!
     *
     * @param array $expression
     *
     * @return string|boolean Returns the created expression or false on error
     * @throws Mumsys_Db_Exception Throws exception on errors if throw errors
     * was set
     */
    public function compileQueryExpression( array $expression )
    {
        //list($operator, $keyval) = each($expression);
        $operator = key( $expression );
        $keyval = current( $expression );

        if ( is_array( $keyval ) && $operator !== '_' ) {
            //list($key, $value) = each($keyval);
            $key = key( $keyval );
            $value = current( $keyval );
            if ( !is_string( $key ) ) {
                $msg = sprintf(
                    'Invalid expression key "%1$s" for where expression: '
                    . 'values (json): %2$s',
                    $key,
                    json_encode( $value )
                );

                return $this->_setError( $msg );
            }
        } else if ( $operator === '_' ) {
            $key = null;
            $value = $keyval;
        } else {
            $msg = sprintf(
                'Invalid input for where expression. Array expected. '
                . 'Operator: "%1$s" values (json): %2$s',
                $operator,
                json_encode( $keyval )
            );

            return $this->_setError( $msg );
        }

        // escape / testing all
        if ( $operator == '=' && is_array( $value ) ) {
            $operator = 'IN';
            $new = array();
            foreach ( $value as $type ) {
                if ( is_string( $type ) ) {
                    $new[] = '\'' . $this->escape( $type ) . '\'';
                } else if ( is_numeric( $type ) ) {
                    $new[] = $type;
                } else {
                    $msg = sprintf(
                        'Invalid value list for where expression. Strings|'
                        . 'numbers expected. operator: "%1$s" values (json): '
                        . '%2$s', $operator, json_encode( $keyval )
                    );
                    return $this->_setError( $msg );
                }
            }

            $value = $new;

            /** @todo not escaping quotes are a security problem. find quotes,
             * drop, escape and then replace quotes back? */
        } else if ( $operator === '_' ) {
            // no quotes but escaping
            // '_' => 'date >= now()',
            // '_' => array('date >= \'2010-12-31\'', 'date <= now()')
            if ( is_array( $value ) ) {
                $new = array();
                foreach ( $value as $string ) {
                    /** @todo cast to string or test for it? */
                    if ( !is_string( $string ) ) {
                        $msg = sprintf(
                            'Invalid value list for where expression'
                            . '. String expected. Operator: "_"'
                            . ' values (json): "%1$s"',
                            json_encode( $value )
                        );

                        return $this->_setError( $msg );
                    }
                    $new[] = $string;
                }
                $value = '(' . implode( ' AND ', $new ) . ')';
            } else if ( !is_string( $value ) ) {
                $msg = sprintf(
                    'Invalid value for where expression. Array|string '
                    . 'expected. Operator: "_" values (json): "%1$s"',
                    json_encode( $keyval )
                );
                return $this->_setError( $msg );
            }
        } else {
            $valIsInt = true;
            if ( !is_int( $value ) ) {
                $valIsInt = false;
                $value = $this->escape( $value );
            }
        }

        // create expression
        switch ( $operator )
        {
            case '_':
                $stmt = (string) $value;
                break;

            case 'IN':
                $stmt = '`' . (string) $key . '` IN (' . implode( ',', $value ) . ')';
                break;

            case 'LIKE':  // "like" for "%contains%"
                $stmt = '`' . (string) $key . '` LIKE \'%' . $value . '%\'';
                break;

            case 'NOTLIKE': // "not like" for "%not contains%"
                $stmt = '`' . (string) $key . '` NOT LIKE \'%' . $value . '%\'';
                break;

            case 'xLIKE':
                $stmt = '`' . (string) $key . '` LIKE \'%' . $value . '\'';
                break;

            case 'xNOTLIKE':
                $stmt = '`' . (string) $key . '` NOT LIKE \'%' . $value . '\'';
                break;

            case 'LIKEx':
                $stmt = '`' . (string) $key . '` LIKE \'' . $value . '%\'';
                break;

            case 'NOTLIKEx':
                $stmt = '`' . (string) $key . '` NOT LIKE \'' . $value . '%\'';
                break;

            case '>':
            case '<':
            case '>=':
            case '<=':
            case '!=':
            case '=':
            case '==':
                $stmt = '`' . $key . '`' . $operator . '';
                if ( isset( $valIsInt ) && $valIsInt === true ) {
                    $stmt .= $value;
                } else {
                    $stmt .= '\'' . $value . '\'';
                }
                break;

            default:
                $msg = sprintf(
                    'Unknown operator "%1$s" to create expression', $operator
                );
                return $this->_setError( $msg );
                break;
        }

        return $stmt;
    }



    /**
     * ToDO:
     *  SELECT a
     *      FROM b
     *      WHERE c
     *      HAVING h
     *      GROUP g
     *      ORDER o
     *      LIMIT l
     * compile query by options
     * private ?
     *
     * @see http://dev.mysql.com/doc/refman/5.1/de/select.html
     *
     * @param array $opts Options to set:
     *  [cols] array|empty Cols to be selected; list of key-vaulue pairs,
     *  default * if empty
     *  [table] array|string table(s) to fetch from in inner join if array given
     *  [where] array|string columns (as array keys) value as array value
     *  [having]
     *  [group]
     *  [order]
     *  [limit] array|string array(0,1) or string 0,1
     * @return string Returns a compiled sql statement
     */
    public function compileQuery( array $opts )
    {
        $cols = '';
        $table = '';
        $where = '';
        $group = '';
        $having = '';
        $order = '';
        $limit = '';

        // select cols
        if ( empty( $opts['cols'] ) ) {
            $cols = '*';
        } else {
            $cols = $this->compileQuerySelect( $opts['cols'] );
//            if ( is_array($opts['cols']) ) {
//                foreach ( $opts['cols'] as $c => $ffunc ) {
//                    if ( $cols ) {
//                        $cols .= ',';
//                    }
//                    $cols .= $c;
//                }
//            } else {
//                // take it "as is"
//                $cols = $opts['cols'];
//            }
        }

        // table
        if ( empty( $opts['table'] ) ) {
            return $this->_setError( 'No tables given to compile.' );
        } else {
            if ( is_array( $opts['table'] ) ) {
                foreach ( $opts['table'] as $t => $theJoin ) {
                    if ( $table ) {
                        $table .= ',';
                    }
                    $table .= $t;
                    // inner join, we need to c
                    // create WHERE clause
                    if ( !empty( $theJoin ) ) {
                        if ( $where ) {
                            $where .= ' AND ';
                        } else {
                            $where = 'WHERE ';
                        }
                        $where .= '(' . $theJoin . ')';
                    }
                }
            } else {
                $table = $opts['table'];
            }
            $table .= ' ';
        }

        // where
        if ( !empty( $opts['where'] ) ) {
            if ( $where ) {
                $where .= ' AND ' . $this->_compileQueryWhere( $opts['where'] );
            } else {
                $where = $this->compileQueryWhere( $opts['where'] );
            }
        }

        // group; as methode?
        if ( !empty( $opts['group'] ) ) {
            $group = $this->compileQueryGroupBy( $opts['group'] );
        }

        // having
        // where sql filter
        // bring to having clause if set
        if ( !empty( $opts['having'] ) ) {
            if ( is_array( $opts['having'] ) ) {
                foreach ( $opts['having'] as $n => &$key ) {
                    if ( $having ) {
                        $having .= ' AND ';
                    } else {
                        $having = ' HAVING ';
                    }
                    $having .= '(' . $key . ')';
                }
            } else {
                $having = ' HAVING ' . $opts['having'];
            }
        }
        if ( $having ) {
            $having .= ' ';
        }

        // order
        if ( !empty( $opts['order'] ) ) {
            $order = $this->compileQueryOrderBy( $opts['order'] );
        }

        // limit
        if ( !empty( $opts['limit'] ) ) {
            $limit = $this->compileQueryLimit( $opts['limit'] );
        }

        $reture = sprintf(
            'SELECT %1$s FROM %2$s %3$s%4$s%5$s%6$s%7$s',
            $cols,
            $table,
            $where,
            $group,
            $having,
            $order,
            $limit
        );

        return $reture;
    }


    /**
     * Returns select statment by given configuration list.
     * E.g.:
     * - *, t2.name, DATE_FORMAT(t1.time_start, \'%d.%m.%Y %H:%i:%s\') AS time
     * - cfg.*, UNIX_TIMESTAMP(cfg.`time_lastupdate`) AS unix_lastupdate
     *
     * Usage for the configuration input:
     * - array list of columns to select (as array values)
     * - array list or key/value pairs where key is the alias and the value the
     * column e.g.: array('alias'=>'column') -> "column AS alias"
     *
     * Special operations:
     * - '_' string|array Can be used for un-escaped and un-quoted input. The
     * input will be used as is which is a security problem! Be sure you know
     * what you are doing!.
     * E.g:
     * <code>
     * array('_' => 'if (col=2, \'yes\', \'no\')')
     * array('_' => 'UNIX_TIMESTAMP(cfg.time_lastupdate)')
     * </code>
     *
     * List of special operations:
     * <pre>
     * array('_' => 'count(*)')
     * // count(*) AS cnt,`thisId` AS id,`name`
     * array('_', array('count(*) AS cnt', 'id' => 'thisId', 'name'))
     * </pre>
     *
     * @param array $fields List of fields to select.
     *
     * @return string|false Column list for the select statment or false on error
     * @throws Mumsys_Db_Exception Throws exception on errors if throw errors
     * was set
     */
    public function compileQuerySelect( array $fields )
    {
        $result = array();
        foreach ( $fields as $alias => $column ) {
            if ( $column == '*' ) {
                $result[] = '*';
                continue;
            }
            if ( is_numeric( $alias ) ) {
                $result[] = '`' . $this->escape( $column ) . '`';
            } else if ( $alias === '_' ) {
                // '_' un-escaped, un-quoted values!
                try {
                    $result[] = (string) $column;
                }
                catch ( Exception $e ) {
                    $msg = sprintf(
                        'Error casting column "%1$s" to string. Values '
                        . '(json) %2$s. Message: "%3$s"',
                        gettype( $column ),
                        json_encode( $column ),
                        $e->getMessage()
                    );
                    return $this->_setError( $msg );
                }
            } else {
                $result[] = '`' . $this->escape( $column ) . '` AS ' . $alias;
            }
        }

        return implode( ',', $result );
    }


    /**
     * Returns set statement for insert or update statement by given configuration
     * list.
     * Example:
     * <code>
     * array('text' => 'textaNew', 'textb' => null, 'textc' => 'now()');
     * </code>
     *
     * @param array $set List of key/value pairs for the set statement
     *
     * @return string Returns the full set statement
     * @throws Mumsys_Db_Exception Throws exception on errors when escaping the
     * values.
     */
    public function compileQuerySet( array $set )
    {
        $data = array();
        foreach ( $set as $col => $value ) {
            switch ( $col )
            {
                case '_':
                    $data[] = (string) $value;
                    break;

                case ( is_null( $value ) === true ):
                case $value === 'null':
                case $value === 'NULL':
                    $data[] = '`' . $col . '`=NULL';
                    break;

                case $value === 'now()':
                case $value === 'NOW()':
                    $data[] = '`' . $col . '`=NOW()';
                    break;

                default:
                    $data[] = '`' . $col . '`=\'' . $this->escape( $value ) . '\'';
            }
        }

        return ' SET ' . implode( ',', $data );
    }


    /**
     * Retruns complex sql expression basicly made for a sql where clause.
     *
     * The configuration input looks as follows: A compare value (see array
     * key of $_queryCompareValues) followed by a list of expressions the
     * expressions should be compared with.
     *
     * E.g: array('[AND|OR]' => array( [list of expressions])).
     *
     * An expression looks like array('[operator] => array('key' => 'value')).
     * @see $_queryOperators Array keys of it.
     *
     * Operator '_' can be used for special expressions. For more
     * @see compileQueryExpression() This belongs to security problems.
     *
     * Simple mode:
     * Only a list of key/value pairs are used as input. All values are
     * compared as AND condition. All operators will be '='. This is
     * useful for update statments and speed up things and reduce code.
     *
     * Example:
     * <code>
     * $array = array(
     *      // the following expressions as AND condition...
     *      'AND' => array(
     *          array('=' => array('name' => 'value')),
     *          // `name` LIKE '%value%'
     *          array('LIKE' => array('name >= 'value')),
     *      ),
     * );
     * </code>
     *
     * Example for the simple mode:
     * <code>
     * // `name`='mum sys' AND `thatid`=123
     * $array = array('name' => 'mum sys', 'thatid' => 123);
     * </code>
     *
     * @param array $where Configuration list for the where statment.
     *
     * @return string|false Returns the expression string or false for error if
     * throw errors was set to false
     * @throws Mumsys_Db_Exception Throws exception on invalid input and if
     * throw errors was set
     */
    public function compileQueryWhere( array $where = array() )
    {
        if ( empty( $where ) ) {
            return ' WHERE 1=1';
        }

        if ( !isset( $this->_queryCompareValues[key( $where )] ) ) {
            // compat. mode
            $result = $this->_compileQueryWhereSimple( $where );
        } else {
            $result = $this->_compileQueryWhere( $where );
        }

        if ( $result ) {
            return ' WHERE ' . $result;
        }

        return false;
    }


    /**
     * Returns where clause conditions by given list of key/value pair in AND
     * comparison.
     *
     * @param array $where List of key/value pairs
     * @return string Where clause
     */
    private function _compileQueryWhereSimple( array $where = array() )
    {
        $result = array();
        foreach ( $where as $col => $value ) {
            if ( is_numeric( $col ) ) {
                $result[] = $this->escape( $value );
            } else {
                // '_' un-escaped values!
                if ( $col === '_' ) {
                    $result[] = (string) $value;
                } else {
                    $result[] = '`' . (string) $col . '`=\''
                        . $this->escape( $value ) . '\'';
                }
            }
        }

        return implode( ' AND ', $result );
    }


    /**
     * Retuns the where expressions. Main methode for compileQueryWhere().
     *
     * @see compileQueryWhere() For detailed description
     *
     * @param array $where Configuration list for the where statment.
     *
     * @return string|false Returns the expression string or false for error if
     * throw errors was set to false
     * @throws Mumsys_Db_Exception Throws exception on invalid input and if
     * throw errors was set
     */
    private function _compileQueryWhere( array $where = array() )
    {
        $expressions = '';
        $outerCmp = '';

        foreach ( $where as $oCmp => $exprlists ) {
            $outerCmp = $oCmp; // hold outside while stmt
            foreach ( $exprlists as $i => $exprPart ) {
                if ( !is_array( $exprPart ) || empty( $exprPart ) ) {
                    $msg = sprintf(
                        'Invalid sub-expression. Must be \'[operator] => '
                        . '[key/value]\'. Found (json): %1$s ',
                        json_encode( $exprPart )
                    );

                    return $this->_setError( $msg );
                }

                $needle = key( $exprPart ); // check for the upcomming operator
                if ( isset( $this->_queryOperators[$needle] ) || $needle === '_' ) {
                    $compExpr[] = $this->compileQueryExpression( $exprPart );
                } else {
                    $inner[] = $this->_compileQueryWhere( $exprPart );
                }
            }

            if ( isset( $compExpr ) && $compExpr ) {
                $inner[] = '(' . implode( ' ' . $outerCmp . ' ', $compExpr ) . ')';
            }
        }

        if ( isset( $inner ) ) {
            $expressions = '' . implode( ' ' . $outerCmp . ' ', $inner ) . '';
        }

        return $expressions;
    }


    /**
     * Returns the 'group by' clause sql statement.
     *
     * @param array $groupby List of columns to set the 'group by' clause.
     *
     * @return string The created group by clause
     */
    public function compileQueryGroupBy( array $groupby = array() )
    {
        $result = '';
        if ( $groupby ) {
            foreach ( $groupby as $key ) {
                if ( $result ) {
                    $result .= ',';
                } else {
                    $result = ' GROUP BY ';
                }
                $result .= '`' . (string) $key . '`';
            }
        }

        return $result;
    }


    /**
     * Returns the 'order by' clause sql statement.
     *
     * @param array $orderby List of key/value pairs where 'key' is the column
     * and the 'value' the sortation way. If key is not given the value will be
     * used and column and the sortation will be 'ASC'
     *
     * @return string Returns the created order by clause
     */
    public function compileQueryOrderBy( array $orderby )
    {
        $res = '';
        foreach ( $orderby as $column => $way ) {
            if ( $res ) {
                $res .= ',';
            } else {
                $res = ' ORDER BY ';
            }

            if ( is_numeric( $column ) ) {
                $column = $way;
                $way = key( $this->_querySortations );
            } else {
                if ( !isset( $this->_querySortations[$way] ) ) {
                    $way = key( $this->_querySortations );
                }
            }
            $res .= '`' . $column . '` ' . $way . '';
        }

        return $res;
    }


    /**
     * Returns the 'limit, offset' clause sql statement.
     *
     * Usage: Array containing one or two values: The offset (startpoint to
     * select) and the limit (limit count, number of rows to select).
     * If both values are given e.g.:  array(0, 10)
     * - array key 0 belongs to the offset (0)
     * - array key 1 belongs to the limit count (10)
     * If only one value is given:
     * - array key 0 belongs to the limit count (10)
     * Empty array returns an empty string.
     *
     * @param array $limit array
     * @return string Returns the created limit, offset clause or empty string
     */
    public function compileQueryLimit( array $limit )
    {
        $cnt = count( $limit );
        if ( $cnt === 1 ) {
            $res = ' LIMIT ' . (int) $limit[0];
        } else if ( $cnt === 2 ) {
            $res = ' LIMIT ' . (int) $limit[1]
                . ' OFFSET ' . (int) $limit[0];
        } else {
            $res = '';
        }

        return $res;
    }

    // --- end compileQuery* --------------------------------------------------


    /**
     * Implode sql conditions.
     *
     * @todo this method should be available in a xml creator too e.g. for
     * attributes
     *
     * @param string $glue
     * @param array $array values of items to implode to make a valid statement.
     * @param boolean $withKeys Flag: if $array values having array key which
     * describes the cols of the table set this to true.
     * @param array $defaults If given, in this array are table defaults like
     *  datatype or default values to validate values;
     *  $defaults = array('key'=>array('default'=>'', 'type'=>'int|float|double
     *  |varchar|char|enum|set|text', 'asstring'=>false, ...));
     * @param string $valwrap A value to enclose the value: eg.: make a value
     * to be `value`
     * @param string $keyValWrap The value between value and a key.
     * eg.: "=": key = `value`, if the value of the data is false the key will
     * be used as is, e.g: "db.col IS NOT NULL"
     * @param string $keyWrap Value to enclose the key
     * eg.: $keyWrap = '`'; --> `key` = `value`
     * @return string|false seperated string by given separator
     * @throws Mumsys_Db_Exception Throws excetion on errors
     */
    public function sqlImplode( $separator = ',', array $array = array(),
        $withKeys = false, $defaults = array(), $valwrap = '', $keyValWrap = '',
        $keyWrap = '' )
    {
        if ( $withKeys ) {
            $r = array();
            //while ( list($key, $value) = each($array) ) {
            foreach ( $array as $key => $value ) {
                // e.g.: value = "db.col IS NOT NULL"
                if ( $value === false ) {
                    $_keyValWrap = '';
                } else {
                    $_keyValWrap = $keyValWrap;
                }

                if ( $defaults ) {
                    if ( !isset( $defaults[$key]['type'] ) ) {
                        // ignore
                        //? continue; // set after unit test
                    } else {
                        switch ( $defaults[$key]['type'] ) {
                            case 'int':
                            case 'integer':
                                $value = (int) $value;
                                break;

                            case 'float':
                            case 'double':
                                $value = (float) $value;
                                break;

                            case 'char':
                            case 'varchar':
                            case 'string':
                            case 'text':
                            case 'tinytext':
                            case 'mediumtext':
                            case 'longtext':
                            case 'enum':
                            case 'set':
                            case 'time':
                            case 'datetime':
                                $value = $this->escape( (string) $value );
                                break;

                            case 'timestamp':
                                $value = $this->escape( (string) $value );
                                /* //eg.: 2005-12-12 10:08:29
                                  if(strlen($value) != 19) {

                                  }
                                 */
                                break;

                            default:
                                /**
                                 * @todo Bug or feature? if type unknown,
                                 * exception should be thrown!?
                                 * what happen if default not exists?
                                 * escaping the default value?
                                 */
                                if ( is_array( $defaults[$key]['default'] ) ) {
                                    $value = $defaults[$key]['default'][0];
                                } else {
                                    $value = $defaults[$key]['default'];
                                }
                                break;
                        }

                        if ( $defaults[$key]['asstring'] || $value === '' ) {
                            $_valwrap = $valwrap;
                        } else {
                            $_valwrap = '';
                        }

                        $r[] = $keyWrap . $key . $keyWrap . $_keyValWrap
                            . $_valwrap . $value . $_valwrap;
                    }
                } else {
                    if ( !is_string( $valwrap ) ) {
                        $msg = sprintf(
                            _( 'Value could not be used. Value warp: "%1$s"' ),
                            gettype( $valwrap )
                        );

                        return $this->_setError( $msg );
                    } else {
                        // produce a eg: `key` = 'value'
                        $r[] = $keyWrap . $key . $keyWrap . $_keyValWrap
                            . $valwrap . $this->escape( $value ) . $valwrap;
                    }
                }
            }
        } else {
            $r = $array;
        }

        return implode( $separator, $r );
    }

}
