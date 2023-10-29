<?php /** @todo declare(strict_types=1);*/

/**
 * Php
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2006 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Php
 * Created on 2006-04-30
 */


/**
 * Class for php improvements.
 *
 * Improved or missing functionality you will find here. Missing functionality
 * you will find at its best @pear's "Compat" package.
 *
 * Improved or missing functionality you will find here.
 * This comes from old times where functionality not exists but still
 * implemented somewhere.

 * All methodes should be called staticly.
 *
 * Example:
 * <code>
 * <?php
 * $value = Mumsys_Php::float('123');
 * ?>
 * </code>
 */
class Mumsys_Php
    extends Mumsys_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '3.2.1';

    /**
     * @var string
     */
    public static $os;


    /**
     * Initialisation of PHP class
     */
    public function __construct()
    {
        self::$os = strtoupper( substr( PHP_OS, 0, 3 ) );
    }


    /**
     * Magic getter
     *
     * @param string $key Key to test class property
     *
     * @return mixed Returns the value by given key
     */
    public function __get( $key )
    {
        $return = null;
        switch ( $key )
        {
            case 'os':
                $return = self::$os;
                break;
        }

        return $return;
    }


    /**
     * Magic setter
     *
     * Only "get_magic_quotes_gpc" can be set at the moment.
     *
     * @param string $k Key to be set.
     * @param mixed $v Value to be set
     *
     * @throws Mumsys_Php_Exception If key is not implemented to react on. This will
     * prevent public access to this class
     */
    public function __set( $k, $v )
    {
        switch ( $k )
        {
            default:
                $mesg =  '__set: "' . $k . '"="' . $v . '" not allowed.';
                throw new Mumsys_Php_Exception( $mesg );
                //break;
        }
    }


    /**
     * php >= 5.3.0
     * public Mixed __call():
     * Re-route all function calls to the PHP-functions
     *
     * Note: Don't use it! if you have a better/ different solution. Performance
     * reasons!
     */
    public static function __callStatic( $function, $arguments )
    {
        return call_user_func_array( $function, $arguments );
    }


    /**
     * Re-route php function calls to the PHP-functions.
     * Note: Don't use it! if you have a better/ different solution. Performance
     * reasons!
     *
     * @param string $function Function to call
     * @param array $arguments Mixed arguments
     * @return mixed
     */
    public function __call( string $function, array $arguments )
    {
        return call_user_func_array( $function, $arguments );
    }

    //
    // +-- start helper methodes  ---------------------------------------------
    //
    //
    // --- Variable handling Functions ----------------------------------------


    /**
     * Check if a value is an integer
     *
     * @param integer $value Value to be checked
     *
     * @return boolean Returns the casted integer value or false if value
     * is not a nummeric type
     */
    public static function is_int( $value )
    {
        if ( is_numeric( $value ) ) {
            if ( intval( $value ) == $value ) {
                return true;
            }
        }

        return false;
    }


    /**
     * Cast to float and check if a given value is a float value.
     * If the value contains colons or dot's the given value will be cleand to
     * be a technical value.
     * Note: Behavior belongs to setlocale().
     * @see setlocale()
     *
     * @param mixed $value
     *
     * @return float The float value of the given variable. Empty arrays return 0,
     * non-empty arrays return 1. Strings will most likely return 0 although this
     * depends on the leftmost characters of the string. The common rules of
     * float casting apply.
     */
    public static function floatval( $value ): float
    {
        $result = $value;
        if ( strstr( $value, ',' ) ) {
            // replace dots (thousand seps) with blancs
            $string = str_replace( '.', '', $value );
            // replace ',' with '.'
            $result = str_replace( ',', '.', $string );
        }

        return floatval( $result );
    }

    //
    // --- Filesystem functions ------------------------------------------------


    /** {{{
     * Test if a file exists
     *
     * altn. function to file_exists(). std file_exists() overwrites the last
     * access time (atime). If php version >= 5 and the url (in given parameter)
     * contains a scheme the test will use fopen instead of php internal
     * file_exists().
     * For local files you may use "file://" as prefix to not change the atime.
     *
     * @see http://php.net/manual/en/function.file-exists.php Standard documentation
     *
     * @param string $url Location or url of the file to be checked
     *
     * @return boolean Returns TRUE if the file or directory specified by $url
     * exists; FALSE otherwise. This function will return FALSE for symlinks
     * pointing to non-existing files.
     * }}} */
    public static function file_exists( string $url = '' )
    {
        if ( empty( $url ) ) {
            return false;
        }

        // @see http://php.net/manual/en/function.parse-url.php
        // scheme: http:// https:// ftp:// file:// php:// c:\ d:\ etc..
        $scheme = self::parseUrl( $url, PHP_URL_SCHEME );

        if ( ( PHP_VERSION_ID >= 50208 ) && !empty( $scheme ) ) {
            if ( ( $fp = fopen( $url, 'r' ) ) !== false ) {
                fclose( $fp );
                return true;
            } else {
                return false;
            }
        } else {
            // file_exists overwrites the last access (atime) !
            return file_exists( $url );
        }
    }

    /*
      public static function url_exists($url) {
      // Version 4.x supported
      $handle   = curl_init($url);
      if (false === $handle)
      {
      return false;
      }
      curl_setopt($handle, CURLOPT_HEADER, false);
      curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
      curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0
      (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/
      2.0.0.15") ); // request as if Firefox
      curl_setopt($handle, CURLOPT_NOBODY, true);
      curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
      $connectable = curl_exec($handle);
      curl_close($handle);
      return $connectable;
      }
     */


    /**
     * Get a php.ini variable and return a more technical useable value.
     *
     * E.g: If memory limit returns 32M -> 32*1048576 will be returned
     *
     * @param string $key Key to get from php.ini
     *
     * @return string|integer|null Returns the ini value or translated numeric value
     * if a numeric value was detected or null if the key was not found
     * @throws Mumsys_Php_Exception If detection/ calculation of a numeric
     * values fails
     */
    public static function ini_get( $key )
    {
        if ( ( $value = ini_get( $key ) ) === false ) {
//            throw new Mumsys_Php_Exception( sprintf('ini_get(%1$s) not exists', $key) );
        }

        $value = trim( $value );

        if ( empty( $value ) ) {
            return null;
        }

        $result = self::str2bytes( $value, true );

        return $result;
    }

    //
    // --- String methodes -----------------------------------------------------


    /**
     * Returns given size representation in bytes. IEC prefix form.
     *
     * @param string $value Size string. E.g: 1G 15K 10b
     * @param boolean $binType IEC prefix calculation or not. Not implemented
     * yet.
     *
     * @return integer Number of bytes.
     * @throws Mumsys_Php_Exception If detection/ calculation fails
     */
    public static function str2bytes( string $value, $binType = true ): int
    {
        $value = trim( $value );

        if ( $value < 0 ) {
            return (int)$value;
        }

        if ( empty( $value ) ) {
            return 0;
        }

        $lastChar = $value[strlen( $value ) - 1];
        switch ( $lastChar )
        {
            case 'k':
            case 'K':
                return (int) $value * 1024;
                //break;

            case 'm':
            case 'M':
                return (int) $value * 1048576;
                //break;

            case 'g':
            case 'G':
                $value = (int) $value * 1073741824;
                break;

            case 't':
            case 'T':
                $value = (int) $value * 1099511627776;
                break;

            case 'p':
            case 'P':
                $value = (int) $value * 1125899906842624;
                break;

            default:
                if ( !is_numeric( $value ) ) {
                    $mesg = 'Detection of size failt for "' . $lastChar . '"';
                    throw new Mumsys_Php_Exception( $mesg );
                }
                break;
        }

        return $value;
    }


    /**
     * Check if a string is in another string.
     *
     * Alias like function of str(i)str()
     * If needle is not a string, it is converted to an integer and applied as
     * the ordinal value of a character.
     *
     * @param string $needle Part of a string to be checked
     * @param string $haystack Complete string
     * @param boolean $insensitive If true needle will be checked insensitive
     * @param boolean $beforeNeedle If true, strstr() returns the part of the
     * haystack before the first occurrence of the needle.
     *
     * @return string|false Returns the portion of string, or false if needle
     * was not found.
     */
    public static function in_string( $needle, $haystack, $insensitive = false,
        $beforeNeedle = false )
    {
        if ( $beforeNeedle ) {
            if ( $insensitive ) {
                return stristr( $haystack, $needle, $beforeNeedle );
            } else {
                return strstr( $haystack, $needle, $beforeNeedle );
            }
        } else {
            if ( $insensitive ) {
                return stristr( $haystack, $needle );
            } else {
                return strstr( $haystack, $needle );
            }
        }
    }


    /**
     * Convert special characters to HTML entities. Improved version
     *
     * The translations performed are:
     *  # '&' (ampersand) becomes '&amp;'
     *  # '"' (double quote) becomes '&quot;' when ENT_NOQUOTES is not set.
     *  # ''' (single quote) becomes '&#039;' only when ENT_QUOTES is set.
     *  # '<' (less than) becomes '&lt;'
     *  # '>' (greater than) becomes '&gt;'
     *
     * Improvment to this function belongs to the forward look up to only
     * convert "&" not &#123; and not &amp; seems to be fixed since php > 5
     *
     * @see http://php.net/manual/en/function.htmlspecialchars.php
     * @param string $str The string being converted.
     * @param mixed $style by default: ENT_QUOTES ; ENT_COMPAT (convert ");
     * ENT_QUOTES convert both; ENT_NOQUOTES no quote conversation
     *
     * @return string
     */
    public static function htmlspecialchars( string $str = '', $style = ENT_QUOTES )
    {
        // use forward look up to only convert & not &#abc; and not &amp;
        if ( ( $strA = preg_replace( '/&(?!(#[0-9]|amp)+;)/s', "&amp;", $str ) ) === null ) {
            throw new Mumsys_Php_Exception( 'Regex error' );
        }

        //$str = preg_replace("/&(?![0-9a-z]+;)/s",'&amp;', $str );
        $strB = str_replace( '<', '&lt;', $strA );
        $string = str_replace( '>', '&gt;', $strB );
        switch ( $style )
        {
            case ENT_COMPAT:
                $result = str_replace( '"', "&quot;", $string );
                break;

            case ENT_NOQUOTES:
                // no quotes translation
                $result = $string;
                break;

            case ENT_QUOTES:    // both quotes translations
            default:
                $resultA = str_replace( '"', "&quot;", $string );
                $result = str_replace( "'", '&#039;', $resultA );
        }

        return $result;
    }


    /**
     * Re Convert HTML entities (htmlspecialchars reverse)
     *
     * @param string $str The string being converted.
     * @param mixed $style by default: ENT_QUOTES ; ENT_COMPAT (convert ");
     * ENT_QUOTES convert both; ENT_NOQUOTES no quote conversation
     *
     * @return string Returns the re-converted html entity
     * */
    public static function xhtmlspecialchars( $str = '', $style = ENT_QUOTES )
    {
        $str = str_replace( '&amp;', '&', $str );
        $str = str_replace( '&lt;', '<', $str );
        $str = str_replace( '&gt;', '>', $str );

        switch ( $style )
        {
            case ENT_COMPAT:
                $str = str_replace( '&quot;', '"', $str );
                break;

            case ENT_NOQUOTES:
                // no quotes translation
                break;

            case ENT_QUOTES:    // both quotes translations
            default:
                $str = str_replace( '&quot;', '"', $str );
                $str = str_replace( '&#039;', '\'', $str );
        }

        return $str;
    }


    /**
     * nl2br — Inserts HTML line breaks before all newlines in a string \n -> <br />\n
     *
     * @todo "\n -> <br />\n"; currently: "\n -> <br />"
     *
     * @param string $string String to be check/added with <br> tags
     *
     * @return string Returns nl2br  ( string $string  [, bool $is_xhtml=true] )
     */
    public static function nl2br( string $string, $isXhtml = true )
    {
        if ( $isXhtml ) {
            $str = '<br />';
        } else {
            $str = '<br>';
        }

        return strtr( $string, array("\r\n" => $str, "\r" => $str, "\n" => $str) );
    }


    /**
     * Convert html "br" tags to newline break's
     *
     * @param string $string String to be converted
     * @param string $breakChar Element for the breakline eg: \n, \rn, \r; default: \n
     * @return string Returns the converted string
     */
    public static function br2nl( $string = '', $breakChar = "\n" )
    {
        $search = array('<br />', '<br/>', '<br>');
        $replace = array($breakChar, $breakChar, $breakChar);
        $result = str_replace( $search, $replace, $string );

        return $result;
    }


    /**
     * parseUrl — Parse a URL and return its components.
     *
     * @param string $url The URL to parse. Invalid characters are replaced by _.
     * @param int $component Specify one of PHP_URL_SCHEME, PHP_URL_HOST,
     * PHP_URL_PORT, PHP_URL_USER, PHP_URL_PASS, PHP_URL_PATH, PHP_URL_QUERY or
     * PHP_URL_FRAGMENT to retrieve just a specific URL component as a string.
     *
     * @return array|string|false|null On seriously malformed URLs, parse_url()
     * may return FALSE. If the component parameter is omitted, an associative
     * array is returned. At least one element will be present within the array.
     * Potential keys within this array are:<br />
     *  scheme - e.g. http <br />
     *  host - e.b.: localhost<br />
     *  port<br />
     *  user<br />
     *  pass<br />
     *  path<br />
     *  query - after the question mark ?<br />
     *  fragment - after the hashmark #<br />
     * If the component parameter is not specified, parseUrl() returns a string
     * instead of an array. If the requested component doesn't exist within the
     * given URL, NULL will be returned.
     *
     * @thows Mumsys_Php_Exception Throws exception if parseUrl would return false.
     */
    public static function parseUrl( string $url, int $component = null )
    {
        if ( isset( $component ) ) {
            $result = parse_url( $url, $component );
        } else {
            $result = parse_url( $url );
        }
        if ( $result === false ) {
            throw new Mumsys_Php_Exception( 'parseUrl() failt.', 1 );
        }

        return $result;
    }


    /**
     * Parses a string into array.
     *
     * Similar to php's parse_url except an exception will be thrown if an
     * empty result will return.
     *
     * Note:
     * To get the current QUERY_STRING, you may use the variable
     * $_SERVER['QUERY_STRING'].
     * Also, you may want to read the section on variables from external
     * sources.
     * The magic_quotes_gpc setting affects the output of this function, as
     * parse_str() uses the same mechanism that PHP uses to populate the
     * $_GET, $_POST, etc. variables.
     *
     * Example:
     * <code>
     *  $string = "first=value&arr[]=foo+bar&arr[]=baz";
     *  $output = Mumsys_Php::parse_str($string);
     *  echo $output['first'];  // value
     *  echo $output['arr'][0]; // foo bar
     *  echo $output['arr'][1]; // baz
     * </code>
     *
     * @link http://php.net/manual/en/function.parse-str.php
     *
     * @param string $string String to parse.
     *
     * @return array Returns all portions in an associative array
     * @throws Mumsys_Php_Exception Throws exception if string could not be converted.
     */
    public static function parseStr( string $string )
    {
        $res = null;
        parse_str( $string, $res );

        if ( empty( $res ) ) {
            throw new Mumsys_Php_Exception( 'Mumsys_Php::parseStr() failt.', 1 );
        }

        return $res;
    }


    /**
     * Pad a number (int) to a certain length with another string as prefix.
     * Adds prefixes to an integer number for a given length.
     * E.g: You want number 123 to be 6 chars lenght and want zero fills as
     * prefix like: 000123
     * Note: Given number will be casted to integer.
     * This is a simple helper and alias method of php's str_pad()
     *
     * @param integer $integer Un/signed number
     * @param integer $digits Number of characters your number should contain
     * @param string $padString String to be used as prefix char
     *
     * @return string The padded string
     */
    public static function numberPad( int $integer, int $digits, $padString = '0' )
    {
        return str_pad( (string)$integer, $digits, $padString, STR_PAD_LEFT );
    }


    //
    // --- Array methodes ------------------------------------------------------
    //


    /**
     * Combines two arrays.
     *
     * Like array_merge_* but replaces the values from the right to the left and
     * returns the new array. Go it? Left is the default, right is the new data
     * we want and if exists on left replace with right.
     *
     * @param array $left Array with default values
     * @param array $right Array to insert/replace to the left
     *
     * @return array The combined array
     */
    public static function array_combine( array $left, array $right )
    {
        foreach ( $right as $key => $value ) {
            if ( isset( $left[$key] ) && is_array( $left[$key] ) && is_array( $value ) ) {
                $left[$key] = self::array_combine( $left[$key], $value );
            } else {
                $left[$key] = $value;
            }
        }

        return $left;
    }


    /**
     * Return the current element in an array by reference.
     *
     * The current function simply returns the value of the array element that's
     * currently being pointed to by the internal pointer. It does not move the
     * pointer in any way. If the internal pointer points beyond the end of the
     * elements list or the array is empty, current returns false.
     *
     * @see http://php.net/manual/en/function.current.php
     *
     * @return mixed Returns the current value by reference
     */
    public static function &current( &$s )
    {
        return $s[key( $s )];
    }


    /**
     * Compare an array (list of values or list of key=>val pairs) with another
     * array and test if a smaler array with their key or values exists in a
     * bigger array.
     * E.g.: Testing an array as whitelist against an array with current
     * submitted data.
     *
     * @todo test if the 'keys' variant works
     *
     * @param array $have Array original for the comparison (bigger array)
     * @param array $totest Array to test against $have (smaler array)
     * @param string $way Type of array to check values or array keys: vals|keys
     *
     * @return array Returns the result portion on difference or an empty array
     * for no changes between the arrays
     */
    public static function compareArray( array $have = array(),
        array $totest = array(), $way = 'vals' )
    {
        $res = array();
        if ( $have !== $totest ) {
            foreach ( $have as $keyA => $valA ) {
                foreach ( $totest as $keyB => $valB ) {
                    switch ( $way )
                    {
                        case 'keys':
                            if ( $keyA === $keyB ) {
                                if ( isset( $res[$keyA] ) ) {
                                    unset( $res[$keyA] );
                                }
                                break;
                            } else {
                                $res[$keyA] = $keyB;
                            }
                            break;
                        case 'vals':
                            // working
                            if ( $valA === $valB ) {
                                if ( isset( $res[$valA] ) ) {
                                    unset( $res[$valA] );
                                }
                                break;
                            } else {
                                /* if (is_array($val_a)) {
                                  $res[ $key_a ] = $val_a;
                                  } else {
                                  $res[ $key_a ] = $val_a;
                                  } */
                                $res[$keyA] = $valA;
                            }
                            break;
                    }
                }
            }
        }
        // echo '<pre>'; print_r($res); echo '</pre>';
        return $res;
    }


    /**
     * Check if a key exists in a multidimensional associative array
     *
     * @author Florian Blasel <info@flo-W-orks.com>
     * @param string $needle Needle to look for
     * @param array $haystack Array to be checked
     *
     * @return boolean Returns true if the search key was found
     */
    public static function array_keys_search_recursive_check( $needle, $haystack )
    {
        foreach ( $haystack as $key => $value ) {
            if ( $key === $needle ) {
                return true;
            }

            $match = self::array_keys_search_recursive( $needle, $value, true );
            if ( is_array( $value ) && $match ) {
                return true;
            }
        }
        return false;
    }


    /**
     * Search for a given key in a multidimensional associative array.
     * If a match was found a list of matches will be returned by reference
     *
     * @todo To be check if php offers improved handling! This methode is
     * really old!
     * @todo $stopOnFirstMatch do not work! break nested stuff; static
     * $stopOnFirstMatch ?
     *
     * Example:
     * <code>
     *  $bigarray =  array(
     *      'key1' => array(
     *          'key2' => array(
     *              'a' => array( 'text'=>'something'),
     *              'b' => array( 'id'=>737),
     *              'c' => array( 'name'=>'me'),
     *          ),
     *      )
     *  );
     *  $matchedKeys = array_keys_search_recursive( 'name',$bigarray);
     *  // returns by reference: array( 0=>array( 'name'=>'me');
     * </code>
     *
     * @param string $needle Needle to look for
     * @param array $haystack Array to be scanned
     * @param boolean|string $stopOnFirstMatch Flag
     *
     * @return array Returns a list of key->value pairs by reference  array
     * indexes to the specified key. Last value
     * contains the searched $needle; if the array is empty nothing were found
     */
    public static function array_keys_search_recursive( $needle, &$haystack,
        $stopOnFirstMatch = false )
    {
        $matches = array();
        foreach ( $haystack as $key => &$value ) {

            if ( ( $stopOnFirstMatch && $stopOnFirstMatch === 'break' ) ) {
                break;
            }
            // echo ":$needle:=:$key: m: $stopOnFirstMatch\n";
            if ( $key === $needle ) {
                $matches[] = & $haystack;
                if ( $stopOnFirstMatch ) {
                    $stopOnFirstMatch = 'break';
                    break;
                }
            } else {
                // go deeper
                if ( is_array( $value ) ) {
                    $array = self::array_keys_search_recursive(
                        $needle, $value, $stopOnFirstMatch
                    );
                    $matches = array_merge( $matches, $array );
                }
            }
        }

        return $matches;
    }


    /**
     * Merge two or more arrays recursively.
     *
     * The difference between this and php's array_merge_recursive are that
     * values of the same key wont be appended.
     * If the input arrays have the same string keys, then the later value for
     * that key will overwrite the previous one.
     *
     * @todo to be tested deeply
     *
     * @ param array|null $array List of arrays to be be merged or null to use
     * func_get_args()
     *
     * @return array Returns the merged array
     * @throws Mumsys_Php_Exception Throws exception on unexpercted behaviour
     */
    public static function array_merge_recursive()
    {
        if ( func_num_args() < 2 ) {
            $mesg = __METHOD__ . ' needs at least two arrays as arguments';
            throw new Mumsys_Php_Exception( $mesg );
        }

        $arrays = func_get_args();
        $merged = array();

        while ( $arrays ) {
            $array = array_shift( $arrays );

            if ( !is_array( $array ) ) {
                $mesg = __METHOD__ . ' given argument is not an array "'
                    . $array . '"';
                throw new Mumsys_Php_Exception( $mesg );
            }

            if ( !$array ) {
                continue;
            }

            foreach ( $array as $key => $value ) {
                if ( is_string( $key ) ) {
                    if ( is_array( $value )
                        && array_key_exists( $key, $merged )
                        && is_array( $merged[$key] )
                    ) {
                        $merged[$key] =
                            self::array_merge_recursive( $merged[$key], $value );
                    } else {
                        $merged[$key] = $value;
                    }
                } else {
                    $merged[] = $value;
                }
            }
        }

        return $merged;
    }

}
