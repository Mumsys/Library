<?php

/*{{{*/
/**
 * Mumsys_Db_Driver_Mysql_Query
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 * ----------------------------------------------------------------------------
 * @author Florian Blasel <flobee.code@gmail.com>
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * ----------------------------------------------------------------------------
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Db
 * Created on 23.06.2007 as querytool.php
 */
/* }}} */


/**
 * Mysql Query generator
 *
 * Note: For the future, currently in the drivers included
 *
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Db
 */
class Mumsys_Db_Driver_Mysql_Query
    implements Mumsys_Db_Driver_Query_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '3.0.0';

    /**
     * Query comparison values
     *
     * @var array Multi-dimensional array
     * array('internal key'=> array(
     *      'public key to map to'=>'public value of key to show')
     * ): eg: array('AND' => array('and', 'in an AND condition' )
     * @access protected
     * @see Mumsys_DataList.php
     */
    protected $_queryCompareValues = array(
        'AND' => array('And', 'And'),
        'OR' => array('Or', 'Or'),
    );

    /**
     * Query operators.
     *
     * @var array Multi-dimensional array
     * array('internal key'=> array(
     *      'public key to map to'=>'public value of key to show')
     * )
     *
     * @see Mumsys_DataList.php
     */
    protected $_queryOperators = array(
        '=' => array('==', 'is equal'),
        '>' => array('&gt;', 'is greater than'),
        '<' => array('&lt;', 'is less than'),
        '>=' => array('&gt;=', 'is greater or equal'),
        '<=' => array('&lt;=', 'is less or equal'),
        '!=' => array('!=', 'is not equal'),
        'LIKE' => array('contains', 'contains'),
        'NOTLIKE' => array('contains not', 'contains not'),
        'xLIKE' => array('ends with', 'ends with'),
        'xNOTLIKE' => array('ends not with', 'ends not with'),
        'LIKEx' => array('beginns with', 'beginns with'),
        'NOTLIKEx' => array('begins not with', 'begins not with'),
    );

    /** @todo To be implemented*/
    protected $_querySortations = array(
        'ASC' => 'Ascending (a-z, 0-9)',
        'DESC' => 'Descending (z-a, 9-0)'
    );

    /**
     * Debug mode
     *
     * @var boolean True to enable false by default
     * @access protected
     */
    protected $_debug = false;

    /**
     * List of errors the program can collect.
     * An error contains:
     *  message => error message,
     *  code => error code
     * for each item.
     * Note that collecting alle error can blow up the memory. Collecting will
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
     * Returns the query compare values.
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
     * array('internal key'=> array(
     *      'public key to map to'=>'public value of key to show')
     * )
     * eg (default): array(
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
                return $this->_setError( 'Invalid query operators configuration' );
            }
        }

        $this->_queryCompareValues = $comparison;
    }


    /**
     * Returns the query operators. Multi-dimensional array
     * array('internal key'=> array(
     *      'public key to map to'=>'public value of key to show')
     * )
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
     * array('internal key'=> array(
     *      'public key to map to'=>'public value of key to show')
     * )
     * @return false|void Returns false on errors
     * @throws Mumsys_Db_Exception On errors if setThrowErrors was set
     */
    public function replaceQueryOperators( array $operators )
    {
        foreach ( $operators as $key => $list ) {
            if ( is_numeric( $key ) || count( $list ) != 2 ) {
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
     * @return string|false The query string or false on error
     * @throws Mumsys_Db_Exception
     */
    private function _save( array $params, $action = 'update' )
    {
        $r = false;

        try
        {
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

                $r = $sql;
            }

        } catch ( Exception $ex ) {
            $r = false;
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
     * @return string|false The query string or false on error
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
     * @return string|false The query string or false on error
     * @throws Mumsys_Db_Exception
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
     * @return string|false The query string or false on error
     */
    public function insert( $params )
    {
        return $this->_save( $params, 'insert' );
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
     * @return string|false The query string or false on error
     */
    public function replace( $params )
    {
        return $this->_save( $params, 'replace' );
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
     * [limit] optional array containing the offset (the start value), limit count
     * or just the limit count e.g. array(limit count)
     *
     * @return string|false The query string or false on error
     */
    public function delete( $params )
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
     * @return string|boolean Returns the created expression or false on error
     * @throws Mumsys_Db_Exception Throws exception on errors if throw errors was set
     */
    public function compileQueryExpression( array $expression )
    {
        list($operator, $keyval) = each( $expression );

        if ( is_array( $keyval ) && $operator !== '_' ) {
            list($key, $value) = each( $keyval );
            if ( !is_string( $key ) ) {
                $msg = sprintf(
                    'Invalid expression key "%1$s" for where expression: '
                    . 'values (json): %2$s', $key, json_encode( $value )
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
                $operator, json_encode( $keyval )
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
                            . ' values (json): "%1$s"', json_encode( $value )
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
                if ( $valIsInt ) {
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
     *     SELECT a
     *         FROM b
     *         WHERE c
     *         HAVING h
     *         GROUP g
     *         ORDER o
     *         LIMIT l
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
    public function compileQuery( array $opts = array( ) )
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
        }

        // table
        if ( empty( $opts['table'] ) ) {
            return $this->_setError( 'No tables given to compile' );
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
     * @return string|flase Column list for the select statment or false on error
     * @throws Mumsys_Db_Exception Throws exception on errors if throw errors was set
     */
    public function compileQuerySelect( array $fields )
    {
        $result = array();
        foreach ( $fields as $alias => $column ) {
            if ( $column =='*' ) {
                $result[] = '*';
                continue;
            }
            if ( is_numeric( $alias ) ) {
                $result[] =  '`' . $this->escape( $column ) . '`';

            } else if ( $alias === '_' ) {
                // '_' un-escaped, un-quoted values!
                try {
                    $result[] = (string)$column;
                } catch ( Exception $e ) {
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
    public function compileQuerySet( array $set = array() )
    {
        $data = array();
        foreach ( $set as $col => $value ) {
            switch ( $col )
            {
                case '_':
                    $data[] = (string) $value;
                    break;

                case ( is_null( $value )===true ):
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
     * @see compileSqlExpression() This belongs to security problems.
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

        foreach ( $where as $oCmp => $exprlists ) {
            $outerCmp = $oCmp; // hold outside while stmt
            foreach ( $exprlists as $i => $exprPart ) {
                if ( !is_array( $exprPart ) || empty( $exprPart ) ) {
                    $msg = sprintf(
                        'Invalid sub-expression. Must be \'[operator] => [key/value]\'. Found '
                        . '(json): %1$s ', json_encode( $exprPart )
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
     * Returns the 'group by' clause query statement.
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
     * Returns the 'order by' clause query statement.
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
                $way = 'ASC';
            } else {
                if ( !isset( $this->_querySortations[$way] ) ) {
                    $way = 'ASC';
                }
            }
            $res .= '`' . $column . '` ' . $way . '';
        }

        return $res;
    }

    /**
     * Returns the 'limit, offset' clause query statement.
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
        if ( $cnt===1 ) {
            $res = ' LIMIT ' . (int) $limit[0];
        } else if ( $cnt === 2 ) {
            $res = ' LIMIT ' . (int) $limit[1]
                . ' OFFSET ' . (int) $limit[0];
        } else {
            $res = '';
        }

        return $res;
    }


    /**
     * Implode query conditions.
     *
     * @todo this method should be available in a xml creator too e.g. for attributes
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
    public function sqlImplode($separator = ',', array $array = array(),
        $withKeys = false, $defaults = array(), $valwrap = '', $keyValWrap = '',
        $keyWrap = '')
    {
        if ( $withKeys ) {
            $r = array();
            while ( list($key, $value) = each( $array ) ) {
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
                                $value = (int)$value;
                                break;

                            case 'float':
                            case 'double':
                                $value = (float)$value;
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
                                $value = $this->escape( (string)$value );
                                break;

                            case 'timestamp':
                                $value = $this->escape( (string)$value );
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


    /**
     * Returns the list of errors the program has detected and collected.
     *
     * @return array List of errors with 'message' => error message,
     * 'code' => error code
     */
    public function getErrors()
    {
        return $this->_errorList;
    }


    /**
     * Sets the flag to throw errors or not.
     *
     * @param boolean $flag True for throw errors or false to collect errors.
     */
    public function setThrowErrors( $flag )
    {
        $this->_throwErrors = (boolean)$flag;
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
        $this->_debug = (boolean)$flag;
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
        if ( $this->_debug ) {
            //this blows up the memory! use carefully
            $this->_errorList[] = array('message' => $message, 'code' => $code);
        } else {
            $this->_errorList[0] = array('message' => $message, 'code'=>$code);
        }

        if ( $this->_throwErrors ) {
            throw new Mumsys_Db_Exception( $message, $code, $previous );
        }

        return false;
    }

}
