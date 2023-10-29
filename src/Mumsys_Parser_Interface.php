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
 * @version     2.0.0
 * Created: 2023-07-31
 */

/**
 * Parser interface for implementations.
 */
interface Mumsys_Parser_Interface
{
    /**
     * Initialise the object with a format and pattern list.
     *
     * @param string|null $format Format of a string.
     * @param array<string, string>|null $patterns Initial patterns to be set. Otherwise default
     * patterns (for this implementation) will be used.
     */
    public function __construct( ?string $format = null, ?array $patterns = null );

    /**
     * Set the format of a log line.
     *
     * E.g: 'id;c1;c2;c3;c4;c5'
     *
     * @param string $format Format of a sting to parse
     */
    public function setFormat( string $format ): void;

    /**
     * Sets the patterns list of key/value pairs.
     *
     * @param array<string, string> $patterns Patterns to be set
     */
    public function setPatternList( array $patterns ): void;

    /**
     * Sets or adds a pattern to pattern list.
     *
     * Default list of key/value pairs to substitute the key with the regular
     * expression. These are common patterns to be used
     * @see $_patternsDefault of any implementation
     *
     * @param string $key Pattern identifier
     * @param string $pattern The pattern/ expression
     */
    public function setPattern( $key, $pattern ): void;

    /**
     * Set the filters to return all NONE matching items from the filters.
     *
     * Note: This works only if filters are set.
     */
    public function setHideFilterResults(): void;

    /**
     * Set the filters to return all matches from the filters.
     * Hint: Search for matches by given filter rules.
     *
     * Note: This works only if filters are set.
     */
    public function setShowFilterResults(): void;

    /**
     * Sets the filter condition mode. Apply all filters in AND or OR condition.
     *
     * @param string $orOrAnd Condition to be set: "AND" or "OR"; Default: "AND"
     *
     * @throws Exception If incoming parameter is whether the string "AND" nor
     * "OR"
     */
    public function setFilterCondition( $orOrAnd ): void;

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
    public function addFilter( $key, $value = array(), $sensitive = false ): void;

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
    public function parse( string $line, bool $stayStrict = true );
}
