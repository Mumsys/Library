<?php declare (strict_types=1);

/**
 * Mumsys_Variable_Manager_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2019 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Variable
 */


/**
 * Abstract manager parts.
 */
abstract class Mumsys_Variable_Manager_Abstract
//    extends Mumsys_Variable_Abstract
    implements Mumsys_Variable_Manager_Interface
{
    /**
     * Version ID information
     */
    public const VERSION = '2.0.0';

    /**
     * Value "%1$s" (type: "%3$s") does not match the regular expression/s (json): "%2$s"
     */
    public const REGEX_FAILURE = 'REGEX_FAILURE';

    /**
     * Error in regular expression
     */
    public const REGEX_ERROR = 'REGEX_ERROR';

    /**
     * Missing required value
     */
    public const REQUIRED_MISSING = 'REQUIRED_MISSING';

    /**
     * Missing value
     */
    public const ALLOWEMPTY_ERROR = 'ALLOWEMPTY_ERROR';

    /**
     * Value (json):"%1$s" is not a "string"
     */
    public const TYPE_INVALID_STRING = 'TYPE_INVALID_STRING';

    /**
     * Value (json):"%1$s" is not an "array"
     */
    public const TYPE_INVALID_ARRAY = 'TYPE_INVALID_ARRAY';

    /**
     * Value "%1$s" is not a valid value for type "email"
     */
    public const TYPE_INVALID_EMAIL = 'TYPE_INVALID_EMAIL';

    /**
     * Value (json):"%1$s" is not a "numeric" type
     */
    public const TYPE_INVALID_NUMERIC = 'TYPE_INVALID_NUMERIC';

    /**
     * Value (json):"%1$s" is not a "float" type
     */
    public const TYPE_INVALID_FLOAT = 'TYPE_INVALID_FLOAT';

    /**
     * Value (json):"%1$s" is not an "integer" type
     */
    public const TYPE_INVALID_INT = 'TYPE_INVALID_INT';

    /**
     * Value (json):"%1$s" is not a "date" type
     */
    public const TYPE_INVALID_DATE = 'TYPE_INVALID_DATE';

    /**
     * Value (json):"%1$s" is not a "datetime" type
     */
    public const TYPE_INVALID_DATETIME = 'TYPE_INVALID_DATETIME';

    /**
     * Value (json):"%1$s" is not an "ipv4" address
     */
    public const TYPE_INVALID_IPV4 = 'TYPE_INVALID_IPV4';

    /**
     * Value (json):"%1$s" is not an "ipv6" address
     */
    public const TYPE_INVALID_IPV6 = 'TYPE_INVALID_IPV6';

    /**
     * Value (json):"%1$s" is not a "unixtime"
     */
    public const TYPE_INVALID_UNIXTIME = 'TYPE_INVALID_UNIXTIME';

    /**
     * Value "%1$s" must contain at least "%2$s" characters
     */
    public const MINMAX_TOO_SHORT_STR = 'MINMAX_TOO_SHORT_STR';

    /**
     * Value "%1$s" must contain maximum of "%2$s" characters, "%3$s" given
     */
    public const MINMAX_TOO_LONG_STR = 'MINMAX_TOO_LONG_STR';

    /**
     * Value "%1$s" must be minimum "%2$s"
     */
    public const MINMAX_TOO_SHORT_NUM = 'MINMAX_TOO_SHORT_NUM';

    /**
     * Value "%1$s" can be maximum "%2$s"
     */
    public const MINMAX_TOO_LONG_NUM = 'MINMAX_TOO_LONG_NUM';

    /**
     * Found "%1$s" values, minimum "%2$s" values
     */
    public const MINMAX_TOO_SHORT_ARRAY = 'MINMAX_TOO_SHORT_ARRAY';

    /**
     * Found "%1$s" values, maximum "%2$s" values
     */
    public const MINMAX_TOO_LONG_ARRAY = 'MINMAX_TOO_LONG_ARRAY';

    /**
     * Min/max type error "%1$s". Must be "string", "integer", "numeric",
     * "float" or "double"
     */
    public const MINMAX_TYPE_ERROR = 'MINMAX_TYPE_ERROR';

    /**
     * Value is not of type: "%1$s"
     */
    public const MINMAX_TOO_INVALID_VALUE = 'MINMAX_TOO_INVALID_VALUE';

    /**
     * Filter "%1$s" failt for label/name: "%2$s"
     */
    public const FILTER_ERROR = 'FILTER_ERROR';

    /**
     * Filter function "%1$s" not found for item: "%2$s"
     */
    public const FILTER_NOTFOUND = 'FILTER_NOTFOUND';

    /**
     * Callback "%1$s" for "%2$s" failt for value: "%3$s"'
     * %1$s = __METHODE__
     * %2$s = item label
     * %3$s = values
     */
    public const CALLBACK_ERROR = 'CALLBACK_ERROR';

    /**
     * Callback function "%1$s" not found for item: "%2$s"
     */
    public const CALLBACK_NOTFOUND = 'CALLBACK_NOTFOUND';
}
