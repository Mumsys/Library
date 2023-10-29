<?php

/**
 * Mumsys_Db_Driver_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2007 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Db
 * @version     3.1.0
 * 0.1 - Created: 2010-12-29
 */


/**
 * Database driver interface.
 *
 * Includes std connection and operation methodes base on prim. mysql behavior
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Db
 */
interface Mumsys_Db_Driver_Interface
{
    /**
     * Initialization of database and features
     *
     * @param array $args Possible values:
     * - 'db' optional Database name
     * - 'username' optional Database username
     * - 'password' optional Database password
     * - 'host' optional Database hostname/ip
     * - 'port' optional Database port
     * - 'charset' client character set to be used. If given it will be set when
     *   connection will be made
     * - 'socket' boolean optional
     * - 'debug' boolean optional
     * - 'throwErrors' boolean optional default: true
     * - 'compress' boolean optional Deside to compress the connection or not
     * - mixed other parameters you may need for your own connection/ driver
     */
    public function __construct( Mumsys_Context_Item $context, array $args );

    /**
     * Destructor. Close current connection.
     *
     * @return boolean Return the status for closing the connection. True on
     * success.
     */
    public function __destruct();

    /**
     * Escape a given string for the database query
     *
     * @see php.net/manual/en/function.addslashes.php
     * @param string $string String to be escaped
     * @return string Returns the escaped string
     */
    public function escape( $string = '' );

    /**
     * Quote string
     *
     * @param string $string String to be quoted
     * @param string $quote Quote type to be added eg: " or '
     * @return string The quoted string
     */
    public function quote( $string, $quote = '\'' );

    /**
     * Returns the name of the currently selected database.
     *
     * @return string Database name
     */
    public function getDbName();

    /**
     * Returns the number of querys performed.
     *
     * @return integer Number of querys this DB dirver has taken
     */
    public function getNumQuerys();

    /**
     * Returns the latest query statment which was executed.
     *
     * @return string Latest query statment which was executed.
     */
    public function getQuery();

    /**
     * Returns the list of query statments performed.
     * For debugging it will return a list otherwise only the list of one
     *
     * @return array List of query statments.
     */
    public function getQueryStmts();

    /**
     * Returns the list of errors the program has detected and collected.
     *
     * @return array List of errors with ["message" => "error message", "code"
     * => "error code"]
     */
    public function getErrors();

    /**
     * Returns the latest error message.
     *
     * @return string Error message
     */
    public function getErrorMessage();

    /**
     * Returns the latest error number/code (depending on DB driver).
     *
     * @return string Error message
     */
    public function getErrorCode();

    /**
     * Sets/replaces the flag for the exeption error handling.
     *
     * @param boolean $flag True for throw errors or false to collect errors.
     */
    public function setThrowErrors( $flag );

    /**
     * Returns the status if throw errors is enabled or not.
     */
    public function getThrowErrors();

    /**
     * Sets the flag for the debug handling.
     *
     * @param boolean $flag True for enable debug mode.
     */
    public function setDebugMode( $flag );

    /**
     * Returns debug mode is enabled or not.
     *
     * @return boolean True if debug mode is on/enable otherwise false.
     */
    public function getDebugMode();

    /**
     * Create a database connection.
     *
     * @return resource|false Retruns the database connection resource on
     * succsess or throws exception
     * @throws Mumsys_Db_Exception On connection error or if initial database
     * can not be selected
     */
    public function connect();

    /**
     * Closes the db connection.
     *
     * This methode will be called at least when __destuct event occur.
     *
     * @return bool
     */
    public function close();

    /**
     * Sets the client character set.
     *
     * @param string $charset A valid character set name
     * @return boolean True on success or false on failure
     * @throws Mumsys_Db_Exception if throw errors was set
     */
    public function setCharset( $charset );

    /**
     * Returns the client character set.
     *
     * @return string|object|false Name of the character or false on error
     * @throws Mumsys_Db_Exception if throw errors was set
     */
    public function getCharset();

    /**
     * Select a database
     * On failure the connection will be closed
     *
     * @param string $database Name of the database
     * @return boolean Returns true on success
     */
    public function selectDB( $database );

    /**
     * Get a list of databases
     *
     * @return array Returns an associative array with the database-name as key
     * and as value or false on failure
     */
    public function showDBs();

    /**
     * Fetch tabel names from current database and their connection information
     *
     * @param string $db Database name
     * @return array assoc array which the tables as key and as array value or
     * false on failure
     */
    public function showTables();

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
    public function showColumns( $table = '', $field = null );

    /**
     * Get current system status for uptime, threads, queries, open tables,
     * flush tables and queries per second.
     *
     * @return string|false Returns a string or false
     */
    public function stat();

    /**
     * Retruns the server info and version string.
     * Requires an activ connection on mysql.
     *
     * Returns for example:
     *  10.0.17-MariaDB-log
     *  5.5.5-10.0.17-MariaDB-log
     *  5.5.5-MySQL-log
     * @return string Version string in n.n.n[-xy] format including server name
     */
    public function getServerInfo();

    /**
     * Execute a given sql statement.
     *
     * If connection was not made befor it will be created.
     * General info: For SELECT, SHOW, DESCRIBE, EXPLAIN and other
     * statements returning resultset, query() returns a
     * Mumsys_Db_Driver_Result_Interface on success, or FALSE on error.
     * For other type of SQL statements, INSERT, UPDATE, DELETE, DROP, etc.
     * query() returns TRUE on success or FALSE on error. If throw errors is
     * set (default) Mumsys_Db_Exception will be thrown.
     *
     * @param string $sql Query to be executed
     * @param boolean $unbuffered Flag to executed an unbuffered query
     * default: false
     *
     * @return Mumsys_Db_Driver_Result_Interface|false Object or false on error
     * @throws Mumsys_Db_Exception Throws exception if
     * - database connection was not made and fails
     * - on empty query statement (if throw errors was set)
     * - on query error (if throw errors was set)
     */
    public function query( $sql = false, $unbuffered = false );

    /**
     * Test if given resource from a query is in error state
     *
     * @param Mumsys_DB_Driver_Result_Interface $res Optional, the
     * driver result interface otherwise the default/current resultset will be
     * used.
     *
     * @return boolean return true on error, false on no error
     */
    public function isError( $res );

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
     * [limit] optional array containing the offset (the start value), limit
     * count or just the limit count e.g. array(limit count).<br />
     *
     * @return Mumsys_Db_Driver_Result_Interface|false
     * Mumsys_Db_Driver_Result_Interface object or false on error
     */
    public function update( array $params = array() );

    /**
     * Select data from the database.
     *
     * @param array $params Parameters to be set:<br/>
     * - [fields] required Fields to update or insert by a given array with key
     * =>value pairs
     * - [table] required Table to update<br/>
     * - [where] required Array key=>value construct for the where clause; Note:
     * only AND conditions will be made
     * - [order] optional; Set the order for select or update statements. List
     * of key/value pairs (columne/ASC,DESC)
     *  - [limit] optional array containing the offset (the start value), limit
     * count for selects or just the limit count e.g. array(limit count) for
     * select, delete, updates.
     *
     * @return Mumsys_Db_Driver_Result_Interface Object or false on error
     */
    public function select( array $params = array() );

    /**
     * Insert data to the storage.
     *
     * @param array $params Parameters to be set:<br/>
     * [fields] required Fields to set in the insert command<br/>
     * [table] required Table to insert<br/>
     * @return integer|false Return the last insert ID or false on error
     */
    public function insert( array $params = array() );


    /**
     * Replace existing data at the database.
     *
     * @param array $params Parameters to be set/ replaced:
     * [fields] required Fields to set in the replace command
     * [table] required Table to insert
     *
     * @return integer|false Returns number of affected rows or false on error
     */
    public function replace( array $params = array() );

    /**
     * Delete data from storage.
     *
     * @param array $params Parameters as follow:
     *  [table] required Table to delete from
     *  [where] required Array key=>value pairs for the where clause;
     * Note: only AND conditions will be made to delete entrys
     *  [where] required Array key=>value construct for the where clause;
     * Note: only AND conditions will be made
     *  [updateall] optional if set an empty where parameter will be accepted
     * to update or delete ALL existing data without any restrictions.
     * be careful to use it
     *  [limit] optional array containing the offset (the start value), limit
     * count or just the limit count e.g. array(limit count)
     *
     * @return Mumsys_Db_Driver_Result_Interface|false Returns false on error
     */
    public function delete( array $params = array() );

}
