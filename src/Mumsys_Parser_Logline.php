<?php declare(strict_types=1);

/**
 * Mumsys_Parser_Logline
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2015 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Parser
 * Created: 2015-08-11
 */


/**
 * Class to parse a log line into it's parts from a common log file like apache
 * or nginx or by given special format.
 *
 * Example:
 * <pre>
 * # apache vhost configuration
 * $logFormat = '%v:%p %h %l %u %t "%r" %>s %O "%{Referer}i" "%{User-Agent}i"';
 * $logFile = '/tmp/other_vhosts_access.log';
 * // master patterns to be set or defaults take affect
 * $patterns = array();
 * $parser = new Mumsys_Parser_Logfile($logFormat, $patterns);
 * // is already default: $parser->setFilterCondition('AND');
 * // is already default: $parser->setShowFilterResults();
 * // show the opposite, all which not match to the filters
 * // $parser->setHideFilterResults();
 * // show only http code 304
 * $parser->addFilter('httpcode', '304', true);// note: "30" matches to 300 301...
 * // check only port 80
 * $parser->addFilter('port', '80', true);
 * $file = new SplFileObject($logFile);
 * while (!$file->eof())  {
 *      $line = $file->fgets();
 *      $record = $parser->parse($line);
 * </pre>
 */
class Mumsys_Parser_Logline
    extends Mumsys_Parser_Abstract
    implements Mumsys_Parser_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '2.0.0';

    /**
     * Default log format.
     *
     * E.g.: Check your server config for details of the format:
     * Apache e.g "vhost_combined", "combined", "common" or just "referer":
     * vhost_combined:   "%v:%p %h %l %u %t \"%r\" %>s %O "%{Referer}i" "%{User-Agent}i"
     * combined:        "%h %l %u %t \"%r\" %>s %O "%{Referer}i" "%{User-Agent}i"
     * common:          "%h %l %u %t \"%r\" %>s %O"
     * referer:         "%{Referer}i -> %U"
     * agent:            "%{User-agent}i"
     * @var string
     */
    private $_inputFormatDefault = '%h %l %u %t "%r" %>s %O';

    /**
     * Default pattern list.
     * List of key/value pairs to substitute the key with the regular
     * expression. These are common patterns to be used in apache or nginx.
     *
     * @var array<string, string>
     */
    private $_patternsDefault = array(
        '%%' => '(?P<percent>\%)',
        '%a' => '(?P<remoteIP>(((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}'
        . '(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))|([0-9A-Fa-f]{1,4}'
        . '(?::[0-9A-Fa-f]{1,4}){7})|(([0-9A-Fa-f]{1,4})?(:[0-9A-Fa-f]'
        . '{1,4}){0,7}:(:[0-9A-Fa-f]{1,4}){1,7}))',
        '%A' => '(?P<localIP>(((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}'
        . '(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))|([0-9A-Fa-f]{1,4}'
        . '(?::[0-9A-Fa-f]{1,4}){7})|(([0-9A-Fa-f]{1,4})?(:[0-9A-Fa-f]'
        . '{1,4}){0,7}:(:[0-9A-Fa-f]{1,4}){1,7}))',
        '%h' => '(?P<host>[a-zA-Z0-9\-\._:]+)',
        '%m' => '(?P<method>OPTIONS|GET|HEAD|POST|PUT|DELETE|TRACE|CONNECT)',
        '%p' => '(?P<port>\d+)',
        '%r' => '(?P<request>(?:(?:[A-Z]+) .+? HTTP/1.(?:0|1))|-|)',
        '%u' => '(?P<user>(?:-|[\w-]+))',
        '%U' => '(?P<url>.+?)',
        '%v' => '(?P<serverName>([a-zA-Z0-9]+)([a-z0-9.-]*))',
        '%V' => '(?P<canonicalServerName>([a-zA-Z0-9]+)([a-z0-9.-]*))',
        '%>s' => '(?P<httpcode>\d{3}|-)',
        '%b' => '(?P<respBytes>(\d+|-))',
        '%T' => '(?P<reqTime>(\d+\.?\d*))',
        '%O' => '(?P<txBytes>[0-9]+)',
        '%I' => '(?P<rxBytes>[0-9]+)',
        '%D' => '(?P<timeServeRequest>[0-9]+)',
        '%t' => '\[(?P<time>\d{2}/(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|'
        . 'Nov|Dec)/\d{4}:\d{2}:\d{2}:\d{2} (?:-|\+)\d{4})\]',
        '%l' => '(?P<logname>(?:-|[\w-]+))',
    );


    /**
     * Initialise the object.
     *
     * Optional with a format and if needed your own configuration of patterns
     * to not use the features of this variant (Logline).
     *
     * @param string|null $format Optional Format of a string/ logline.
     * @param array<string, string>|null $patterns Optional patterns to be set. Otherwise default
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
