<?php

/* {{{ */
/**
 * Mumsys_Db_Driver_None_None
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2013 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Db
 * Created: 2017-04-30
 */
/* }}} */


/**
 * None Driver for database purpose
 *
 * @category Mumsys
 * @package Mumsys_Library
 * @subpackage Mumsys_Db
 */
class Mumsys_Db_Driver_None_None
    extends Mumsys_Db_Driver_Abstract
    implements Mumsys_Db_Driver_Interface, Mumsys_Db_Driver_Query_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '3.0.0';


    /**
     * Create a database connection an select current database and client charset if given.
     *
     * @return mysqli|resource|false Retruns the database resource on succsess or throws exception
     * @throws Mumsys_Db_Exception On connection error or current database can not be selected
     */
    public function connect()
    {
        try
        {
            if ( $this->_isConnected && $this->_dbc ) {
                return $this->_dbc;
            }

            $options = array(
                $this->_dbc,
                $this->_host,
                $this->_username,
                $this->_password,
                $this->_dbName,
                $this->_port,
                $this->_socket
            );
            $this->_dbc = new stdClass();
            $this->_dbc->options = $options;
            $this->_isConnected = true;

        }
        catch ( Exception $e ) {
            $msg = 'Connection to database failed. Messages: "' . $e->getMessage()
                . '", "' . $this->sqlError() . '"';
            return $this->_setError( $msg, null, $e );
        }

        return $this->_dbc;
    }


    /**
     * Closes the db connection.
     *
     * This methode will be called at least when __destuct event occur.
     *
     * @return bool Returns true on success
     */
    public function close()
    {
        $return = true;
        $this->_isConnected = false;
        $this->_dbc = null;

        return $return;
    }


    /**
     * Sets the client character set.
     *
     * @param string $charset A valid character set name
     *
     * @return boolean Retruns true
     */
    public function setCharset( $charset )
    {
        return true;
    }


    /**
     * Returns the client character set.
     *
     * @return string|null Name of the character or null
     */
    public function getCharset()
    {
        return $this->_clientCharacterSet;
    }


    /**
     * Select a database.
     *
     * @param string $db Name of the db
     * @return boolean Returns true on success or false or throws exception if set
     * @throws Mumsys_Db_Exception Throws exception if database can not be selected
     */
    public function selectDB( $dbName )
    {
        $this->_dbName = (string) $dbName;

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
        return array($this->_dbName => $this->_dbName);
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
        return array();
    }


    /**
     * Execute a given sql statement.
     *
     * @param string $sql Query to be executed
     * @param boolean $unbuffered Flag to executed an unbuffered query
     * default: false
     *
     * @return Mumsys_Db_Driver_Result_Interface|Mumsys_Db_Driver_None_None_Result|false
     *
     * @throws Mumsys_Db_Exception Throws exception if
     *  - database connection was not made and fails
     *  - on empty sql statement (if throw errors was set)
     *  - on query error (if throw errors was set)
     */
    public function query( $sql = false, $unbuffered = false )
    {
        if ( $sql ) {
            $this->_sql = (string) $sql;
        } else {
            return $this->_setError( 'Query empty. Can not query empty sql statment' );
        }

        if ( $this->_dbc === null ) {
            $this->connect();
        }

        $this->_errorNumber = 0;
        $this->_errorMessage = '';

        $this->_numQuerys++;

        if ( $this->_debug ) {
            $this->_querys[] = $sql;
        }

        if ( ( $error = $this->sqlError() ) ) {
            return $this->_setError( $error );
        }

        $result = new stdClass();
        $result->sql = $this->_sql;
        $oRes = new Mumsys_Db_Driver_None_None_Result( $this, $result );

        return $oRes;
    }


    /**
     * Execute an unbuffered query
     * @todo Not implemented yet
     */
    public function queryUnbuffered( $sql = false )
    {
        return $this->_setError( 'Unbuffered querys not implemented yet' );
    }


    /**
     * Test if given resource (None_Dbr) from a query is in error state
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
        return '';
    }


    /**
     * Returns the error number from the last MySQL function, or 0 (zero) if no
     * error occurred.
     *
     * @return integer Error code
     */
    public function sqlErrno()
    {
        return 0;
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
     * @return array Returns the result as array or false on failure or if no more record exists
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
        return array();
    }


    /**
     * Get current system status for uptime, threads, queries, open tables,
     * flush tables and queries per second.
     *
     * @return string|false Returns a string or false if something went wrong
     */
    public function stat()
    {
        return '';
    }


    /**
     * Retruns the server info and version string.
     * Requires an activ connection on mysql.
     *
     * Returns for example:
     * 10.0.17-MariaDB-log
     * 5.5.5-10.0.17-MariaDB-log
     * 5.5.5-MySQL-log
     *
     * @return string Version string including server name
     */
    public function getServerInfo()
    {
        return 'None';
    }


    /**
     * Sets the current error.
     * If $_throwErrors flag is enabled (default) the error will be thrown
     * otherwise a list of errors will be created an the program will go on except on connection errors!
     *
     * @param string $message The error message
     * @param integer $code The error code
     * @param Exception $previous = NULL The previous exception used for the
     * exception chaining.
     *
     * @throws Mumsys_Db_Exception If connection can't be made or ThrowErrors was set
     */
    protected function _setError( $message, $code = null, $previous = null )
    {
        if ( $code === null ) {
            $code = $this->sqlErrno();
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
     * @param $action Action to decide: update(default)|insert|delete|replace
     *
     * @return Mumsys_Db_Driver_None_None_Result|false Result object or false
     */
    protected function _save( $params, $action = 'update' )
    {
        //ups!?: return $this->query( $sql );
        throw new Exception( 'Not implemented' );
    }


    /**
     * Update data from database.
     *
     * @return Mumsys_Db_Driver_None_Result|false Mumsys_Db_Driver_None_Result
     * object or false on error
     */
    public function update( array $params = array() )
    {
        return $this->_save( $params, 'update' );
    }


    /**
     * Select data from the database.
     *
     * @return Mumsys_Db_Driver_None_Result Object or false on error
     */
    public function select( array $params = array() )
    {
        return $this->_save( $params, 'select' );
    }


    /**
     * Insert data to the storage.
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
        }

        return $r;
    }


    /**
     * Delete data from storage.
     *
     * @param array $params Parameter as key->value pairs to delete
     *
     * @return Mumsys_Db_Driver_None_Result|false Returns false on error
     */
    public function delete( array $params = array() )
    {
        return $this->_save( $params, 'delete' );
    }


    /**
     * Retruns a single sql expression basicly made for a sql where clause.
     *
     * @param array $expression
     *
     * @return string|boolean Returns the created expression or false on error
     */
    public function compileQueryExpression( array $expression )
    {
        return '';
    }


    /**
     *
     * @param array $opts Options to set
     *
     * @return string Returns a compiled sql statement
     */
    public function compileQuery( array $opts )
    {
        return '';
    }


    /**
     * Returns select statment by given configuration list.
     *
     * @param array $fields List of fields to select.
     *
     * @return string|flase Column list for the select statment or false on error
     * @throws Mumsys_Db_Exception Throws exception on errors if throw errors was set
     */
    public function compileQuerySelect( array $fields )
    {
        return '';
    }


    /**
     * Returns set statement for insert or update statement by given configuration
     * list.
     *
     * @param array $set List of key/value pairs for the set statement
     *
     * @return string Returns the full set statement
     */
    public function compileQuerySet( array $set )
    {
        return '';
    }


    /**
     * Retruns complex sql expression basicly made for a sql where clause.
     *
     * @param array $where Configuration list for the where statment.
     *
     * @return string|false Returns the expression string or false for error if
     * throw errors was set to false
     */
    public function compileQueryWhere( array $where = array() )
    {
        return '';
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
        return '';
    }


    /**
     * Retuns the where expressions. Main methode for compileQueryWhere().
     *
     * @return string|false Returns the expression string or false for error if
     * throw errors was set to false
     */
    private function _compileQueryWhere( array $where = array() )
    {
        return '';
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
        return '';
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
        return '';
    }


    /**
     * Returns the 'limit, offset' clause sql statement.
     *
     * @param array $limit array
     * @return string Returns the created limit, offset clause or empty string
     */
    public function compileQueryLimit( array $limit )
    {
        return '';
    }

    // --- end compileQuery* --------------------------------------------------


    /**
     * Implode sql conditions.
     *
     * @return string|false seperated string by given separator
     */
    public function sqlImplode( $separator = ',', array $array = array(),
        $withKeys = false, $defaults = array(), $valwrap = '', $keyValWrap = '',
        $keyWrap = '' )
    {
        return '';
    }

}
