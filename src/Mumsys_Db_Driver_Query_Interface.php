<?php

/*{{{*/
/**
 * ----------------------------------------------------------------------------
 * Mumsys_Db_Driver_Query_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 * ----------------------------------------------------------------------------
 * @author Florian Blasel <flobee.code@gmail.com>
 * ----------------------------------------------------------------------------
 * @copyright Copyright (c) 2007 by Florian Blasel for FloWorks Company
 * ----------------------------------------------------------------------------
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * ----------------------------------------------------------------------------
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Db
 * @version     1.0.0
 * 0.1 - Created on 2007-06-23 as querytool.php
 * -----------------------------------------------------------------------
 */
/* }}} */


/**
 * Query generator interface
 *
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Db
 */
interface Mumsys_Db_Driver_Query_Interface
{
    /**
     * Retruns a single sql expression basicly made for a sql where clause.
     * E.g.: WHERE ( `a` LIKE '%b%' )
     * An expression looks like: array('operator'=>array('column' => 'value'))
     * @see $_sqlOperators array keys of possilble operators.
     * Speacial operators:
     * - '_' string|array Can be used for unescaped and unquoted special
     * comparisons.
     * <b>Important:</b> If array values given:
     * - All values will NOT be escaped and NOT quoted. (security problem! Be
     * sure you know what you are doing)
     * - All values will be set as AND comparison.
     * - Array keys will be ignored.
     * - '=' array key/value pair or key/list of values. If list of values
     * given in mysql IN () operator will be used. Values can be numeric or
     * string. If values contains sttings they will be quoted.
     *
     * Examples for '_' operator:
     * <code>
     * array('_'=> array('date < now() AND date > \'2000-12-31 00:00:00\')
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
     * @param array $expression
     * @return string|boolean Returns the created expression or false on error
     * @throws Mumsys_Db_Exception On errors if throw errors was set
     */
    public function compileQueryExpression( array $expression );

    /**
     * tested but not in use, compile* methodes are used in @see _save() method
     */
    public function compileQuery( array $options = array() );

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
     * <code>
     * array('_' => 'count(*)')
     * // count(*) AS cnt,`thisId` AS id,`name`
     * array('_', array('count(*) AS cnt', 'id' => 'thisId', 'name'))
     * </code>
     *
     * @param array $fields List of fields to select.
     *
     * @return string|flase Column list for the select statment or false on error
     * @throws Mumsys_Db_Exception Throws exception on errors if throw errors was set
     */
    public function compileQuerySelect( array $fields );

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
    public function compileQuerySet( array $set = array() );

    /**
     * Retruns complex sql expression basicly made for a sql where clause.
     *
     * The configuration input looks as follows: A compare value (see array
     * key of $_sqlCompareValues) followed by a list of expressions the
     * expressions should be compared with.
     *
     * E.g: array('[AND|OR]' => array( [list of expressions])).
     *
     * An expression looks like array('[operator] => array('key' => 'value')).
     * @see $_sqlOperators Array keys of it.
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
    public function compileQueryWhere( array $where = array() );

    /**
     * Returns the 'group by' clause sql statement.
     *
     * @param array $groupby List of columns to set the 'group by' clause.
     *
     * @return string The created group by clause
     */
    public function compileQueryGroupBy( array $groupby = array() );

    /**
     * Returns the 'order by' clause sql statement.
     *
     * @param array $orderby List of key/value pairs where 'key' is the column
     * and the 'value' the sortation way. If key is not given the value will be
     * used and column and the sortation will be 'ASC'
     *
     * @return string Returns the created order by clause
     */
    public function compileQueryOrderBy( array $orderby );

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
     * @param array $limit array, see description
     * @return string Returns the created limit, offset clause or empty string
     */
    public function compileQueryLimit( array $limit );

}