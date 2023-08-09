<?php

/**
 * Mumsys_Db_Driver_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2009 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Db
 * @version     3.1.0
 * Created: 2009-11-27
 */


// @ToDo: SQL_CALC_ROWS and FOUND_ROWS()


/* {{{ */  // table defauls-construct (not related, just an example)
/*
  $availTreeTables = array( $W_CONFIG['db_name'] =>
    array(
        'COMMENT' => 'db comment',
        'TYPE' => 'MYISAM',
        'DEFAULT CHARACTER SET' => '',
        'COLLATE' => '',
        'TABLES' => array( 'dp_groups_global' =>
            array(
                'COMMENT' => 'table comment',
                'CHARACTER SET' => 'latin1',
                'COLLATE' => 'latin1_general_ci',
                'PRIMARY KEY' => array( 'id' ),
                // 'UNIQUE KEY'=>array('NameKey'=>array('col1','col2...')),
                // 'KEY'=>array('NameKey'=>array('col1','col2...')),
                'COLS' => array( 'id' => array(
                        'field' => 'id',
                        'comment' => 'ID/ Serial',
                        'default' => '',
                        'extra' => 'auto_increment',
                        'type' => 'int',
                        'null' => '',
                        // 'key'=>'PRI',           ???
                        'typevalue' => '11',
                        'typeattr' => 'unsigned',
                        'character set' => 'latin1',
                        'collate' => 'latin1_general_ci',
                        'asstring' => false,
                    ),
            ) ) ) ) );
  */
/* }}} */



/**
 * Common class for all database drivers
 *
 * Set up credentials for your driver, basic methodes which may be different for
 * a specific database or do not exists are in basic behavior in here or in your
 * own implementation to fit the interface.
 *
 * Note: most implementation is close to the mysql functionality
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Db
 */
abstract class Mumsys_Db_Driver_Abstract
{
    // ToDo: vars needs to be protected or privat when abstact instead or
    // extends but used public in business controll
    // also: getter, setter are needed to change stuff for class propertys

    /**
     * Version ID information
     */
    const VERSION = '3.2.0';

    /**
     * Database resource
     * @var ressorce The database connection (resource)
     */
    protected $_dbc;

    /**
     * Database name and the name of the current selected database.
     * @var string
     */
    protected $_dbName;

    /**
     * Username for the database connection
     *
     * @var string
     */
    protected $_username;

    /**
     * Password for the database connection
     *
     * @var string
     */
    protected $_password;

    /**
     * Hostname or ip address of the database server
     * @var string
     */
    protected $_host = 'localhost';

    /**
     * Server port to connect to.
     * @var integer
     */
    protected $_port;

    /**
     * Socket connection
     *
     * Specifies the socket or named pipe that should be used.
     *
     * @var string
     */
    protected $_socket;

    /**
     * A valid client character set to be used (depends on db server).
     *
     * @var string
     */
    protected $_clientCharacterSet;

    /**
     * Use connection compression or not. Default is: false
     * @var boolean
     */
    protected $_conCompession = false;

    /**
     * Tells if connection was made or not.
     * @var boolean
     */
    protected $_isConnected;

    /**
     * Latest sql statment which was executed.
     *
     * @var string
     */
    protected $_sql;

    /**
     * Query tracker; Container for all sql statments.
     *
     * @var array
     */
    protected $_querys = array( );

    /**
     * Internal counter of executed sql statements
     *
     * @var integer
     */
    protected $_numQuerys = 0; // static

    /**
     * Query comparison values
     *
     * @var array Multi-dimensional array
     * array('internal key'=> array(
     *      'public key to map to'=>'public value of key to show')
     * ): eg: array('AND' => array('and', 'in an AND condition' )
     * @see Mumsys_DataList.php
     */
    protected $_queryCompareValues;

    /**
     * Sql operators.
     *
     * @var array Multi-dimensional array
     * array('internal key'=> array(
     *      'public key to map to'=>'public value of key to show')
     * )
     *
     * @see Mumsys_DataList.php
     */
    protected $_queryOperators;

    /**
     * @todo To be implemented
     * List of sortations e.g: ASC => ascending
     * @var array
     */
    protected $_querySortations;

    /**
     * Debug mode
     *
     * @var boolean True to enable false by default
     */
    protected $_debug = false;

    /**
     * List of errors the program can collect.
     * An error contains:
     *  message => error message,
     *  code => error code
     * for each item.
     * Note that collecting errors can blow up the memory. Collecting will
     * be made only in debug mode!
     *
     * @see setThrowErrors()
     *
     * @var array()
     */
    protected $_errorList = array();

    /**
     * Flag to throw errors or collect errors.
     *
     * @see getErrors()
     *
     * @var boolean
     */
    protected $_throwErrors = true;

    /**
     * String of a error message
     * @var string
     */
    protected $_errorMessage = null;

    /**
     * Error code from database server
     *
     * @var integer Number of the error (database specific)
     */
    protected $_errorNumber = null;


    /**
     * Initialization of database and features
     *
     * @param Mumsys_Context_Item $context Context item
     * @param array $args Possible values:
     * - 'db' optional Database name
     * - 'username' optional Database username
     * - 'password' optional Database password
     * - 'host' optional Database hostname/ip
     * - 'port' optional Database port
     * - 'charset' client character set to be used. If given it will be set when
     *    connection will be made
     * - 'socket' boolean optional
     * - 'debug' boolean optional
     * - 'throwErrors' boolean optional default: true
     * - 'compress' boolean optional Deside to compress the connection or not.
     */
    public function __construct( Mumsys_Context_Item $context,
        array $args = array() )
    {
        unset( $context ); // 4SCA

        if ( isset( $args['host'] ) ) {
            $this->_host = (string) $args['host'];
        }

        if ( isset( $args['username'] ) ) {
            $this->_username = (string) $args['username'];
        }

        if ( isset( $args['password'] ) ) {
            $this->_password = (string) $args['password'];
        }

        if ( isset( $args['db'] ) ) {
            $this->_dbName = (string) $args['db'];
        }

        if ( isset( $args['port'] ) ) {
            $this->_port = (int) $args['port'];
        }

        if ( isset( $args['charset'] ) ) {
            $this->_clientCharacterSet = (string) $args['charset'];
        }

        if ( isset( $args['socket'] ) ) {
            $this->_socket = $args['socket'];
        }

        if ( isset( $args['debug'] ) ) {
            $this->_debug = (boolean) $args['debug'];
        }

        if ( isset( $args['throwErrors'] ) ) {
            $this->_throwErrors = (boolean) $args['throwErrors'];
        }

        if ( isset( $args['compress'] ) ) {
            $this->_conCompession = (boolean) $args['compress'];
        }

        if ( !defined( '_CMS_ISEQUAL' ) ) {
            /** @todo translations? */
            define( '_CMS_AND', 'And' );
            define( '_CMS_OR', 'Or' );
            define( '_CMS_ISEQUAL', 'is equal' );
            define( '_CMS_ISGREATERTHAN', 'is greater than' );
            define( '_CMS_ISLESSTHAN', 'is less than' );
            define( '_CMS_ISGREATERTHANOREQUAL', 'is greater or equal' );
            define( '_CMS_ISLESSTHANOREQUAL', 'is less or equal' );
            define( '_CMS_ISNOTEQUAL', 'is not equal' );
            define( '_CMS_CONTAINS', 'contains' );
            define( '_CMS_CONTAINS_NOT', 'contains not' );
            define( '_CMS_ENDSWITH', 'ends with' );
            define( '_CMS_ENDSNOTWITH', 'ends not with' );
            define( '_CMS_BEGINSWITH', 'beginns with' );
            define( '_CMS_BEGINSNOTWITH', 'begins not with' );
        }

        $this->_queryCompareValues = array(
            'AND' => array(_CMS_AND, _CMS_AND),
            'OR' => array(_CMS_OR, _CMS_OR),
        );

        $this->_queryOperators = array(
            '=' => array('==', _CMS_ISEQUAL),
            '>' => array('&gt;', _CMS_ISGREATERTHAN),
            '<' => array('&lt;', _CMS_ISLESSTHAN),
            '>=' => array('&gt;=', _CMS_ISGREATERTHANOREQUAL),
            '<=' => array('&lt;=', _CMS_ISLESSTHANOREQUAL),
            '!=' => array('!=', _CMS_ISNOTEQUAL),
            'LIKE' => array(_CMS_CONTAINS, _CMS_CONTAINS),
            'NOTLIKE' => array(_CMS_CONTAINS_NOT, _CMS_CONTAINS_NOT),
            'xLIKE' => array(_CMS_ENDSWITH, _CMS_ENDSWITH),
            'xNOTLIKE' => array(_CMS_ENDSNOTWITH, _CMS_ENDSNOTWITH),
            'LIKEx' => array(_CMS_BEGINSWITH, _CMS_BEGINSWITH),
            'NOTLIKEx' => array(_CMS_BEGINSNOTWITH, _CMS_BEGINSNOTWITH),
        );

        $this->_querySortations = array(
            'ASC' => 'Ascending (a-z, 0-9)',
            'DESC' => 'Descending (z-a, 9-0)'
        );
    }


    /**
     * Destructor. Close current connection.
     *
     * @return boolean Return the status for closing the connection. True on success.
     */
    public function __destruct()
    {
        return $this->close();
    }


    /**
     * Default escaping for a string in db context
     *
     * @see php.net/manual/en/function.addslashes.php
     * @param string $string String to be escaped
     * @return string Returns the escaped string
     */
    public function escape( $string = '' )
    {
        return addslashes( $string );
    }


    /**
     * Quote string
     *
     * @param string $s String to be quoted
     * @param string $q Quote type to be added
     * @return string The quoted string
     */
    public function quote( $s, $q = '\'' )
    {
        return $q . $s . $q;
    }


    /**
     * Returns the name of the database.
     *
     * @return string Database name
     */
    public function getDbName()
    {
        return $this->_dbName;
    }


    /**
     * Returns the number of querys.
     *
     * @return integer Number of querys this DB dirver has taken
     */
    public function getNumQuerys()
    {
        return $this->_numQuerys;
    }


    /**
     * Returns the latest sql statment which was executed.
     *
     * @return string Latest sql statment which was executed.
     */
    public function getQuery()
    {
        return $this->_sql;
    }


    /**
     * Returns the list of sql statments.
     *
     * @return array List of sql statments.
     */
    public function getQueryStmts()
    {
        return $this->_querys;
    }


    /**
     * Returns the sql compare values.
     *
     * @return array
     */
    public function getQueryCompareValues()
    {
        return $this->_queryCompareValues;
    }


    /**
     * Replaces query comparison values
     *
     * @param array $comparison Multi-dimensional array
     *  array('internal key'=> array(
     *      'public key to map to'=>'public value of key to show')
     *  )
     *  eg (default): array(
     *     'AND' => array('And', 'And'),
     *     'OR' => array('Or', 'Or'),
     *
     * @return false|void Returns false on errors
     * @throws Mumsys_Db_Exception On errors if setThrowErrors was set
     */
    public function replaceQueryCompareValues( array $comparison )
    {
        foreach ( $comparison as $key => $list ) {
            if ( is_numeric( $key ) || count( $list ) != 2 ) {
                return $this->_setError(
                    'Invalid query compare value configuration'
                );
            }
        }

        $this->_queryCompareValues = $comparison;
    }


    /**
     * Returns the query operators. Multi-dimensional array
     *  array('internal key'=> array(
     *      'public key to map to'=>'public value of key to show')
     *  )
     * @return array
     */
    public function getQueryOperators()
    {
        return $this->_queryOperators;
    }


    /**
     * Replaces query operators.
     *
     * @param array $operators Multi-dimensional array
     *  array('internal key'=> array(
     *      'public key to map to'=>'public value of key to show')
     *  )
     *
     * @return false|void Returns false on errors
     * @throws Mumsys_Db_Exception On errors if setThrowErrors was set
     */
    public function replaceQueryOperators( array $operators )
    {
        foreach ( $operators as $key => $list ) {
            if ( is_numeric( $key ) ) {
                return $this->_setError( 'Invalid query operators configuration' );
            }
        }

        $this->_queryOperators = $operators;
    }


    /**
     * Returns the query sortations
     *
     * @return array List of key/value pairs for the sortation
     */
    public function getQuerySortations()
    {
        return $this->_querySortations;
    }


    /**
     * Replaces query sortations
     *
     * @param array $sortations List of sortations eg: array(
     *     'ASC' => 'Ascending (a-z, 0-9)',
     *     'DESC' => 'Descending (z-a, 9-0)'
     * )
     * @return false|void Returns false on errors
     * @throws Mumsys_Db_Exception On errors if setThrowErrors was set
     */
    public function replaceQuerySortations( array $sortations )
    {
        foreach ( $sortations as $key => $value ) {
            if ( is_numeric( $key ) || !is_string( $value ) ) {
                return $this->_setError( 'Invalid query sortations configuration' );
            }
        }

        $this->_querySortations = $sortations;
    }


    /**
     * Returns the list of errors the program has detected and collected.
     *
     * @return array List of errors with message => error message,
     * code => error code
     */
    public function getErrors()
    {
        return $this->_errorList;
    }


    /**
     * Returns the latest error message.
     * @return string Error message
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }


    /**
     * Returns the latest error number/code (depending on DB driver)
     * @return string Error message
     */
    public function getErrorCode()
    {
        return $this->_errorNumber;
    }


    /**
     * Sets the flag to throw errors or not.
     *
     * @param boolean $flag True for throw errors or false to collect errors.
     */
    public function setThrowErrors( $flag )
    {
        $this->_throwErrors = (boolean) $flag;
    }


    /**
     * Returns the status if throw errors is enabled or not.
     * @return boolean
     */
    public function getThrowErrors()
    {
        return $this->_throwErrors;
    }


    /**
     * Sets the flag for the debug handling.
     *
     * @param boolean $flag True for enable debug mode.
     */
    public function setDebugMode( $flag )
    {
        $this->_debug = (boolean) $flag;
    }


    /**
     * Returns debug mode is enabled or not.
     *
     * @return boolean
     */
    public function getDebugMode()
    {
        return $this->_debug;
    }


    /**
     * Sets the current error.
     * If $_throwErrors flag is enabled (default) the error will be thrown
     * otherwise a list of errors will be created an the program will go on.
     *
     * @param string $message The error message
     * @param integer $code The error code
     * @param Exception $previous = NULL The previous exception used for the
     * exception chaining.
     */
    abstract protected function _setError( $message, $code = null,
        $previous = null );


    /**
     * Closes the db connection.
     *
     * This methode will be called at least when __destuct event occur.
     *
     * @return boolean True on success or false
     */
    abstract public function close();
}
