<?php declare(strict_types=1);

/**
 * Mumsys_Parser_Default
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2023 by Florian Blasel
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Parser
 * Created: 2023-07-31
 */


/**
 * Class to parse and anayse parts of a strings  for futher handling.
 *
 * Currently extends Mumsys_Parser_Logline with is more special
 */
class Mumsys_Parser_Default
    extends Mumsys_Parser_Abstract
    implements Mumsys_Parser_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '2.0.0';


    /**
     * Default input format for this implementation.
     *
     * @var string
     */
    private $_inputFormatDefault = '%Y-%m-%d';


    /**
     * Default pattern list.
     *
     * List of key/value pairs to substitute the key with the regular
     * expression. These are common patterns to be used for a later date/time
     * handling.
     *
     * For more date/time values see: https://www.php.net/manual/en/datetime.format.php
     *
     * @var array<string, string>
     */
    private $_patternsDefault = array(
        '%Y' => '(?P<year>\d{4})',
        '%m' => '(?P<month>\d{2})',
        '%d' => '(?P<day>\d{2})',

        '%H' => '(?P<hour>\d{2})',
        '%i' => '(?P<minute>\d{2})',
        '%s' => '(?P<second>\d{2})',

        // e - Timezone identifier - Examples: UTC, GMT, Atlantic/Azores
        //'%e' => '(?P<timezone>(\w{3})|(\w\/\w)',

        // c - ISO 8601 date - 2004-02-12T15:19:21+00:00
        '%c' => '(?P<ISO8601datetimezone>\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2})',
        // unix timestamp since epoch
        '%U' => '(?P<unix_timestamp>\d)',

        // other
        // anyting you need as prefix. only to be used as prefix!
        '%pre' => '(?P<prefix>.*)',
        // anyting you need as suffix. only to be used as suffix!
        '%suf' => '(?P<suffix>.*)',
    );

    /**
     * Initialise the object with a format and pattern list.
     *
     * @param string|null $format Format of a string.
     * @param array<string, string>|null $patterns Initial patterns to be set. Otherwise default
     * patterns (for this implementation) will be used.
     */
    public function __construct( ?string $format = null, ?array $patterns = null )
    {
        if ( $patterns ) {
            $this->setPatternList( $patterns );
        } else {
            $this->setPatternList( $this->_patternsDefault );
        }

        if ( $format ) {
            $this->setFormat( $format );
        } else {
            $this->setFormat( $this->_inputFormatDefault );
        }
    }
}
