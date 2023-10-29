<?php

/**
 * Mumsys_Php_Encoding
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2019 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Php
 * Created on 2019-03-02
 */


/**
 * Class for string encodings.
 *
 * This class extends or adds php features which are to be used to encode strings.
 * Mostly it implements wrapper/ helper functions for "mb_*"  or "iconv" extension
 *
 * All methodes should be called staticly.
 *
 * Example:
 * <code>
 * <?php
 * $value = Mumsys_Php_Encoding::toUtf8('some sting');
 * ?>
 * </code>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Php
 */
class Mumsys_Php_Encoding
    extends Mumsys_Php
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';


    /**
     * Returns the to utf8 encoded string.
     *
     * Hints:
     *  - https://github.com/neitanod/forceutf8
     *  - http://php.net/mb_detect_encoding
     *  - http://php.net/mb_convert_encoding
     *  - http://php.net/manual/en/function.iconv.php
     *  - https://stackoverflow.com/questions/8215050/replacing-invalid-utf-8-characters-by-question-marks-mbstring-substitute-charac
     *  - https://stackoverflow.com/questions/1401317/remove-non-utf8-characters-from-string
     *
     * @param string $string Input string to be converted
     * @param string $action Not implemented yet. Handling about invalid characters: ""
     * (default), 'ignore', 'translit'
     * @param array $encodings List of encodings to convert to Utf8. Default: 'UTF-8',
     * 'ISO-8859-1', 'ISO-8859-15', 'Windows-1252'
     *
     * @return string The encoded string
     */
    public static function toUtf8( $string, string $action = '', array $encodings = array() )
    {
        if ( $action === 'ignore' || $action == 'trans' ) {
//            switch ( $action )
//            {
//                case 'ignore':
//                case 'trans':
//                    $_meth = '//' . strtoupper( $action );
//                    break;
//
//                default:
//                    $_meth = '';
//            }
//            // drops invalid chars (glic related, chk OS, ,0docs!)
//            // $text = iconv("UTF-8","UTF-8//IGNORE",$text);
//            if ( !iconv( "UTF-8", "UTF-8$_meth", $string ) ) {
//                throw new Mumsys_Php_Exception('iconv error');
//            }
        }

        if ( !$encodings ) {
            $encodings = array('UTF-8', 'ISO-8859-1', 'ISO-8859-15', 'Windows-1252');
        }
        $_encodings = implode( ', ', $encodings );

        if ( ( $encoding = mb_detect_encoding( $string, "$_encodings", true ) ) === false ) {
            throw new Mumsys_Php_Exception( 'Error detecting input encoding' );
        }

        return mb_convert_encoding( $string, 'UTF-8', $encoding );
    }

}
