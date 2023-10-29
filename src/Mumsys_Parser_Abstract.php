<?php declare(strict_types=1);

/**
 * Mumsys_Parser_Default
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2023 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Parser
 * Created: 2023-07-31
 */


abstract class Mumsys_Parser_Abstract
    extends Mumsys_Abstract
    implements Mumsys_Parser_Interface
{
    /**
     * Version ID information.
     * Based on version 1.1.1 of Mumsys_Parser_Logline
     */
    const VERSION = '2.0.0';

    /**
     * Log format to be used internally
     * @var string
     */
    private $_inputFormat;

    /**
     * Patterns list to be used internally
     * @var array<string, string>
     */
    private $_patterns = array();


    /**
     * List of filters
     * @var array<string, array<int, array{values:array<string>,case: string|bool}>>>
     */
    private $_filters = array();

    /**
     * Flag for the way the filter will perform.
     * By default (false) the filter is set as whitelist which means only
     * matches of the filter will return.
     * @var boolean
     */
    private $_filteredHide = false;

    /**
     * Flag to change the filter/ search behavior.
     * By default and condition will be used (all filter must match for an entry)
     * @var boolean
     */
    private $_filtersInAndConditions = true;


    /**
     * Set the format of a log line.
     *
     * E.g: 'id;c1;c2;c3;c4;c5'
     *
     * @param string $format Format of a sting to parse
     */
    public function setFormat( string $format ): void
    {
        $this->_inputFormat = "#^{$format}$#";
    }


    /**
     * Sets the patterns list of key/value pairs.
     *
     * @param array<string, string> $patterns Patterns to be set
     */
    public function setPatternList( array $patterns ): void
    {
        $this->_patterns = $patterns;
    }


    /**
     * Sets or adds a pattern to pattern list.
     *
     * Default list of key/value pairs to substitute the key with the regular
     * expression. These are common patterns to be used in apache or nginx:
     * @see $this->_patternsDefault
     *
     * @param string $key Pattern identifier
     * @param string $pattern The pattern/ expression
     */
    public function setPattern( $key, $pattern ): void
    {
        $this->_patterns[$key] = $pattern;
    }


    /**
     * Set the filters to return all NONE matching items from the filters.
     *
     * Note: This works only if filters are set.
     */
    public function setHideFilterResults(): void
    {
        $this->_filteredHide = true;
    }


    /**
     * Set the filters to return all matches from the filters.
     * Hint: Search for matches by given filter rules.
     *
     * Note: This works only if filters are set.
     */
    public function setShowFilterResults():void
    {
        $this->_filteredHide = false;
    }


    /**
     * Sets the filter condition mode. Apply all filters in AND or OR condition.
     *
     * @param string $orOrAnd Condition to be set: "AND" or "OR"; Default: "AND"
     *
     * @throws Exception If incoming parameter is whether the string "AND" nor
     * "OR"
     */
    public function setFilterCondition( $orOrAnd ):void
    {
        $chk = strtoupper( $orOrAnd );

        if ( $chk == 'AND' || $chk == 'OR' ) {
            if ( $chk == 'AND' ) {
                $this->_filtersInAndConditions = true;
            } else {
                $this->_filtersInAndConditions = false;
            }
        } else {
            throw new Mumsys_Parser_Exception( 'Invalid filter condition' );
        }
    }


    /**
     * Adds a filter/ search rule.
     * Note: Don't forget to escape special chars for the regular expressions.
     *
     * @param string $key Keyword based on the pattern rules to be expected:
     * eg.: httpcode, user, time ...
     * @param array<string>|string $value Value or list of values to look/ search for.
     * matching tests! not exact tests!
     * @param boolean $sensitive Flag to enable sensitive mode or not. Default:
     * false (case insensitive)
     */
    public function addFilter( $key, $value = array(), $sensitive = false ):void
    {
        if ( is_string( $value ) ) {
            $value = array($value);
        }

        foreach ( $value as $i => &$raw ) {
            $value[$i] = preg_quote( $raw, '#' );
        }

        $this->_filters[$key][] = array('values' => $value, 'case' => $sensitive);
    }


    /**
     * Parse a log line and return its parts.
     *
     * @param string $line A line of the log file.
     * @param bool $stayStrict Default true to report any error. false will not
     * report empty matches and returns empty array
     *
     * @return array<string|int, scalar>|false Returns array with found properties or empty array if
     * filters take affect or false for an empty line.
     *
     * @throws Mumsys_Parser_Exception If format doesn't match the line format.
     */
    public function parse( string $line, bool $stayStrict = true )
    {
        if ( empty( $line ) ) {
            return false;
        }

        $regex = $this->_getExpression();

        if ( false === preg_match( $regex, $line, $matches ) ) {
            $mesg = sprintf(
                'Regex error detected: "%3$s" Line: "%2$s"; Format: "%1$s"',
                $this->_inputFormat, $line, $regex
            );

            throw new Mumsys_Parser_Exception( $mesg );
        }

        /* allow empty results? to not end with an exception !?
         * $stayStrict=true : you dont loose a focus on important data
         * $stayStrict=false: simple things dont match, validate in business logic */
        if ( $stayStrict === true && 0 === preg_match( $regex, $line, $matches ) ) {
            $mesg = sprintf(
                'Format of the value is invalid (expected: "%1$s"); Line: "%2$s"; Regex: "%3$s"',
                $this->_inputFormat, $line, $regex
            );

            throw new Mumsys_Parser_Exception( $mesg );
        }

        $result = array();

        foreach ( array_filter( array_keys( $matches ), 'is_string' ) as $key ) {
            if ( 'time' === $key && true !== $stamp = strtotime( $matches[$key] ) ) {
                $result['stamp'] = $stamp;
            }

            $result[$key] = $matches[$key];
        }

        $return = array();
        if ( ( $ok = $this->_applyFilters( $result ) ) ) {
            $return = $ok;
        }

        return $return;
    }


    /**
     * Returns the reqular expression based on format and patterns.
     *
     * @return string Regular expression
     * @throw Mumsys_Parser_Exception If logformat is invalid
     */
    private function _getExpression()
    {
        $expr = $this->_inputFormat;
        foreach ( $this->_patterns as $key => $replace ) {
            $expr = preg_replace( "/{$key}/", $replace, $expr );
            // @codeCoverageIgnoreStart
            if ( $expr === null ) {
                throw new Mumsys_Parser_Exception( 'Invalid input format' );
            }
            // @codeCoverageIgnoreEnd
        }

        return $expr;
    }


    /**
     * Apply filters.
     *
     * @param array<string|int, scalar> $result List of parameters from parser result to apply filters.
     *
     * @return array<string|int, scalar> Retruns an empty array if filters take affect otherwise the
     * it returns the incoming result array
     */
    private function _applyFilters( array $result )
    {
        if ( !$this->_filters ) {
            return $result;
        }

        $numMatches = 0;
        $itMatchesInOrCondition = false;

        foreach ( $this->_filters as $key => $paramsList ) {
            if ( isset( $result[$key] ) && $result[$key] ) {
                foreach ( $paramsList as $i => $params ) {
                    foreach ( $params['values'] as $value ) {
                        $modifier = 'i';
                        if ( $params['case'] ) {
                            $modifier = '';
                        }

                        $regex = sprintf( '/(%1$s)/%2$s', $value, $modifier );
                        if ( preg_match( $regex, (string)$result[$key] ) ) {
                            $numMatches += 1;
                            $itMatchesInOrCondition = true;
                        }
                    }
                }
            }
        }

        if ( $this->_filtersInAndConditions ) {
            $itMatches = false;
            if ( count( $this->_filters ) == $numMatches ) {
                $itMatches = true;
            }
        } else {
            $itMatches = $itMatchesInOrCondition;
        }

        if ( $itMatches === $this->_filteredHide ) {
            return array();
        } else {
            return $result;
        }
    }

}
