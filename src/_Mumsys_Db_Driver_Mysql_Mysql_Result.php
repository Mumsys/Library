<?php

/* {{{ */
/**
 * Mumsys_Db_Driver_Mysql_Mysql_Result
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 * ----------------------------------------------------------------------------
 * @author Florian Blasel <flobee.code@gmail.com>
 * @copyright Copyright (c) 2009 by Florian Blasel for FloWorks Company
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * ----------------------------------------------------------------------------
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Db
 * Created: 2009-11-27
 */
/* }}} */


/**
 * Result object of a DB-resultset.
 *
 * This class is used to have mysql functions after a query to the database was
 * placed. Fetching, seeking, count rows etc..
 *
 * @category Mumsys
 * @package Mumsys_Library
 * @subpackage Mumsys_Db
 */
class Mumsys_Db_Driver_Mysql_Mysql_Result
    implements Mumsys_Db_Driver_Result_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '3.0.1';

    /**
     * Database resource.
     *
     * @var ressorce The database connection (resource)
     * @access private
     */
    private $_dbc;

    /**
     * @var ressorce Result of a mysql query
     * @access private
     */
    private $_result;

    /**
     * Count of rows of a sql query
     * @var integer
     */
    private $_numRows;


    /**
     * Initialization of database result (mysqldbr)
     *
     * @param object $oDB mysql object
     * @param resource $result Result set of the sql query
     * @param array $options Array of options; NOT IMPLEMENTED YET!
     */
    public function __construct(Mumsys_Db_Driver_Interface &$oDB, &$result,
        array $options=array())
    {
        $this->_dbc = $oDB->connect();
        $this->_result = $result;
    }


    /**
     * Returns the resultset of a mysql query.
     *
     * @return resource|boolean Result of a mysql query
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Fetch data from a mysql query in a given way
     *
     * @param string $way type to fetch the data, by default assoc will be used.
     * Also possible values are: "num", "array", "row" or "object".
     * @param object $result the resultset of a different sql-resource
     * @return array return an array of fetched values
     */
    public function fetch( $way='assoc', $result=false )
    {
        if ( $result === false ) {
            $result = $this->_result;
        }

        switch ( $way ) {
            case 'ASSOC':
            case 'assoc':
                $row = mysql_fetch_assoc($result);
                break;
            case 'ARRAY':
            case 'array':
                $row = mysql_fetch_array($result, MYSQL_BOTH);
                break;
            case 'NUM':
            case 'num':
                $row = mysql_fetch_array($result, MYSQL_NUM);
                break;
            case 'ROW':
            case 'row':
                $row = mysql_fetch_row($result);
                break;
            case 'OBJECT':
            case 'object':
                $row = mysql_fetch_object($result);
                break;
        }

        return $row;
    }


    /**
     * Fetch all data from an result set.
     *
     * @todo To be tested
     *
     * @param string $way The type, in lower or upper case, to return the data
     * set. Default: 'assoc'; possible values: 'ASSOC','OBJECT','ARRAY','NUM'.
     * @return array return an data set as array or false on failure
     * @param resource $result Result set of the sql query
     * @return array List of records
     */
    public function fetchAll( $way='assoc', $result=false )
    {
        if ( !$result ) {
            $oRes = $this->_result;
        } else {
            $oRes = $result;
        }

        if ( $oRes === false ) {
            return false;
        }

        $data = array();
        switch ( strtoupper($way) )
        {
            case 'ASSOC':
            case 'ARRAY':
            case 'OBJECT':
            case 'NUM':
                while ( $row = $oRes->fetch($way) ) {
                    $data[] = $row;
                }
                break;

            default:
                while ( $row = $oRes->fetch('ASSOC') ) {
                    $data[] = $row;
                }
                break;
        }
        $oRes->free();

        return $data;
    }


    /**
     * Returns the number of rows found.
     *
     * Note: If you use mysql_unbuffered_query(), numRows() will not return the
     * correct value until all the rows in the result set have been retrieved.
     *
     * @todo $numRows === null ? test against false make more sence and thowing
     * exception
     *
     * @param object $result optional The result set of a different mysql-query
     * otherwise the number of rows form the last query will be returned
     * @return integer Returns the number of rows
     * @throws Mumsys_Db_Exception If calculation of num rows fails
     */
    public function numRows( $result=false )
    {
        $numRows = null;
        if ( $result !== false ) {
            $numRows = @mysql_num_rows($result);
        } else {
            if ( $this->_numRows !== null ) {
                $numRows = $this->_numRows;
            } else {
                $numRows = mysql_num_rows($this->_result);
            }
        }

        if ( $numRows === null ) {
            throw new Mumsys_Db_Exception(
                'Error getting number of found rows.', 1
            );
        }

        $this->_numRows = $numRows;

        return $numRows;
    }


    /**
     * Get the number of affected rows by the last INSERT, UPDATE, REPLACE or
     * DELETE query associated with link_identifier.
     *
     * Note: Transactions
     * If you are using transactions, you need to call mysql_affected_rows()
     * after your INSERT, UPDATE, or DELETE query, not after the COMMIT.
     *
     * @param resource $dbc optional The MySQL connection. If the link
     * identifier is not specified, the last link opened by mysql_connect() is
     * assumed. If no such link is found, it will try to create one as if
     * mysql_connect() was called with no arguments. If no connection is found
     * or established, an E_WARNING level error is generated.
     * @return integer Returns the number of affected rows on success, and -1 if
     * the last query failed. If the last query was a DELETE query with no WHERE
     * clause, all of the records will have been deleted from the table but this
     * function will return zero with MySQL versions prior to 4.1.2.
     * When using UPDATE, MySQL will not update columns where the new value is
     * the same as the old value. This creates the possibility that
     * mysql_affected_rows() may not actually equal the number of rows matched,
     * only the number of rows that were literally affected by the query.
     * The REPLACE statement first deletes the record with the same primary key
     * and then inserts the new record. This function returns the number of
     * deleted records plus the number of inserted records.
     *
     * @todo Seems to be buggy!! $result with $this->_result
     */
    public function affectedRows($dbc=false)
    {
        if ( $dbc ) {
            return mysql_affected_rows($dbc);
        }
        return mysql_affected_rows($this->_dbc);
    }


    /**
     * Retrieves the ID generated for an AUTO_INCREMENT column by the previous
     * query (usually INSERT).
     *
     * @Todo BIGINT: query: $sql = 'LAST_INSERT_ID()'
     *
     * Caution:
     * mysql_insert_id() will convert the return type of the native MySQL C API
     * function mysql_insert_id() to a type of long (named int in PHP). If your
     * AUTO_INCREMENT column has a column type of BIGINT (64 bits) the
     * conversion may result in an incorrect value. Instead, use the internal
     * MySQL SQL function LAST_INSERT_ID() in an SQL query. For more about PHPs
     * maximum integer values, please see the integer documentation.
     * @see http://php.net/manual/en/function.mysql-insert-id.php
     *
     * @param resource $dbc optional The MySQL connection.
     * @return integer The ID generated for an AUTO_INCREMENT column by the
     * previous query on success, 0 if the previous query does not generate an
     * AUTO_INCREMENT value, or FALSE if no MySQL connection was established.
     */
    public function lastInsertId($dbc=false)
    {
        if ( $dbc ) {
            return mysql_insert_id($dbc);
        }

        return mysql_insert_id($this->_dbc);
    }


    /**
     * alias method of lastInsertId().
     *
     * @see lastInsertId()
     */
    public function insertID($dbc=false)
    {
        return $this->lastInsertId($dbc);
    }


    /**
     * Get a mysql_result()
     * Retrieves the contents of one cell from a MySQL result set.
     *
     * When working on large result sets, you should consider using one of the
     * functions that fetch an entire row (specified below). As these functions
     * return the contents of multiple cells in one function call, they're MUCH
     * quicker than mysql_result(). Also, note that specifying a numeric offset
     * for the field argument is much quicker than specifying a fieldname or
     * tablename.fieldname argument.
     *
     * @param integer $row The row number from the result that's being
     * retrieved. Row numbers start at 0.
     * @param string|integer $field The name or offset of the field being
     * retrieved. It can be the field's offset, the field's name, or the fields
     * table dot field name (tablename.fieldname). If the column name has been
     * aliased ('select foo as bar from...'), use the alias instead of the
     * column name. If undefined, the first field is retrieved.
     * @param resource $res The result resource that is being evaluated. This
     * result comes from a call to mysql_query().
     * @return s|false The contents of one cell from a MySQL result set on
     * success, or FALSE on failure.
     */
    public function getFirst($row=0, $field=0, $res=false)
    {
        if ( !$res ) {
            $res = $this->_result;
        }

        if ( $field ) {
            return @mysql_result($res, $row, $field);
        }

        return @mysql_result($res, $row);
    }

    /**
     * Alias of getFirst() method
     *
     * @deprecated since version interface 3.0.0
     *
     * @param type $row
     * @param type $field
     * @param type $res
     * @return type
     */
    public function sqlResult($row=0, $field=0, $res=false)
    {
        return $this->getFirst($row, $field, $res);
    }


    /**
     * mysql_data_seek() moves the internal row pointer of the MySQL result
     * associated with the specified result identifier to point to the specified
     * row number. The next call to a MySQL fetch function, such as
     * mysql_fetch_assoc(), would return that row.
     * row_number starts at 0. The row_number should be a value in the range
     * from 0 to mysql_num_rows() -1. However if the result set is empty
     * (mysql_num_rows() == 0), a seek to 0 will fail with an E_WARNING and
     * mysql_data_seek() will return FALSE.
     *
     * @param integer $n Row number to seek to
     * @param resource $res Result set of a mysql query
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function seek( $n=0, $res=false )
    {
        if ( $this->numRows() <= $n ) {
            return false;
        }

        if ( $res ) {
            $_res = $res;
        } else {
            $_res = $this->_result;
        }

        return mysql_data_seek($_res, $n);
    }


    /**
     * free() will free all memory associated with the result identifier result.
     *
     * mysql_free_result() only needs to be called if you are concerned about
     * how much memory is being used for queries that return large result sets.
     * All associated result memory is automatically freed at the end of the
     * script's execution.
     *
     * @param resource $res The result resource that is being evaluated. This
     * result comes from a call to mysql_query().
     * @return boolean Returns TRUE on success or FALSE on failure.
     * If a non-resource is used for the result , an error of level E_WARNING
     * will be emitted. It's worth noting that mysql_query() only returns a
     * resource for SELECT, SHOW, EXPLAIN, and DESCRIBE queries.
     */
    public function free($res=false)
    {
        if ( $res ) {
            return mysql_free_result($res);
        }
        return mysql_free_result($this->_result);
    }

}
