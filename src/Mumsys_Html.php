<?php

/* {{{ */
/**
 * Mumsys_Html
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 * ----------------------------------------------------------------------------
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 * ----------------------------------------------------------------------------
 * @category Mumsys
 * @package Mumsys_Library
 * @subpackage Mumsys_Html
 * @version 0.1 - Created on 2009-11-12
 * Moved to git: $Id: Mumsys_Html.php 2980 2013-12-19 13:40:29Z flobee $
 */
/* }}} */


/**
 * Class for html code creation, validation and filtering
 *
 * @category Mumsys
 * @package Mumsys_Library
 * @subpackage Mumsys_Html
 */
class Mumsys_Html
    extends Mumsys_Xml_Abstract
{
    /**
     * Version ID information
     */
    const VERSION = '3.2.1';

    /**
     * Universal Html attributes which are always allowed in here
     * @var array
     */
    public static $attributesDefaultAllowed = array(
        'title',
        'style',
        'id',
        'class',
        'lang',
        'dir',
    );

    /**
     * Html tags which are allowed when parsing incomming html code
     * @var array
     */
    public static $htmlTagsDefaultAllowed = array(
        'noscript' => array(),
        'a' => array('href', 'name', 'alt'),
        'b' => array(),
        'blockquote' => array(),
        'br' => array(),
        'code' => array(),
        'div' => array(),
        'dd' => array(),
        'dl' => array(),
        'dt' => array(),
        'em' => array(),
        'font' => array(),
        'h1' => array('align'),
        'h2' => array('align'),
        'h3' => array('align'),
        'h4' => array('align'),
        'h5' => array('align'),
        'h6' => array('align'),
        'hr' => array('align'),
        'i' => array(),
        'img' => array('src', 'title', 'border', 'heigth', 'width', 'hspace', 'vspace'),
        'li' => array(),
        'ol' => array(),
        'p' => array('align'),
        'pre' => array(),
        'strong' => array(),
        'span' => array(),
        'table' => array('border', 'cellpadding', 'cellspacing'),
        'tt' => array(),
        'th' => array(),
        'tr' => array('align', 'width'),
        'td' => array('align', 'width', 'nowrap', 'colspan', 'rowspan'),
        'u' => array(),
        'ul' => array(),
    );


    /**
     * Returns xml/Html attributes string.
     *
     * @param array $array List of key/value pairs
     *
     * @return string Attribute string eg: 'name="val" id="key" '
     */
    public static function attributesCreate( array $array = array() )
    {
        try {
            $attributes = parent::attributesCreate( $array );
        }
        catch ( Exception $e ) {
            throw new Mumsys_Html_Exception( $e->getMessage() );
        }

        return $attributes;
    }


    /**
     * Check incomming data agains a whitelist and return all allowed key -value pair.
     *
     * @param array $whitelist List of allowed values
     * @param array $properties List of attributes to be checked agains the whitelist
     * @param boolean $universalAttributesAllow Flag to test also again the
     * universal attributes default: true => enabled
     *
     * @return array Returns a list of key/value pairs allowed
     */
    public static function attributesValidate( array $whitelist = array(),
        array $properties = array(), $universalAttributesAllow = true )
    {
        $attributes = array();

        if ( $universalAttributesAllow ) {
            $whitelist = array_merge(
                $whitelist, self::$attributesDefaultAllowed
            );
        }

        foreach ( $properties as $k => &$v ) {
            if ( in_array( $k, $whitelist ) ) {
                $attributes[$k] = $v;
            }
        }
        return $attributes;
    }


    /**
     * Filter a attribute line for a given tag.
     *
     * Eg: <table cellpadding="2" title="a Global Allowed Attribute">
     * First the hole string will be parsed and needed whitespaces will be
     * fixed.
     * Invalid quote style will be droped.
     * After it the string will be compared with a whitelist: All code which
     * is not in the whitelist or in a global whitelist will be droped.
     * see $htmlTagsDefaultAllowed or $attributesDefaultAllowed
     *
     * @param string $tag The tag for current attributes
     * @param string $stringAttributes Attributes from an incoming html string
     *
     * @return string Returns a new formatted filtered an validated attribute
     * string
     */
    public static function attributesFilter( $tag, $stringAttributes )
    {
        $tmp = '';    // string buffer
        $result = ''; // result string
        $i = 0;
        $attrib = -1; // Are we in an HTML attribute? -1: no attrib, 0: name of
                      // the attrib, 1: value of the atrib
        $quote = 0;   // a string quote delimited opened ? 0=no, 1=yes

        while ( $i < strlen( $stringAttributes ) ) {
            switch ( $stringAttributes[$i] )
            {
                // a quote
                case '"':
                    if ( $quote == 0 ) {
                        $quote = 1;
                    } else {
                        $quote = 0;
                        if ( ( $attrib > 0 ) && ( $tmp != '' ) ) {
                            $result .= '="' . $tmp . '"';
                        }
                        $tmp = '';
                        $attrib = -1;
                    }
                    break;

                // equal delimiter
                case '=':
                    if ( $quote == 0 ) {    // found in a string ?
                        $attrib = 1;
                        if ( $tmp != '' ) {
                            $result .= ' ' . $tmp;
                        }
                        $tmp = '';
                    } else {
                        $tmp .= '=';
                    }
                    break;

                // wrong single quotes?
                case '\'':
                    if ( $quote == 0 ) {
                        $quote = 1;
                    } else {
                        $quote = 0;
                        if ( ( $attrib > 0 ) && ( $tmp != '' ) ) {
                            $result .= '="' . $tmp . '"';
                        }
                        $tmp = '';
                        $attrib = -1;
                    }
                    break;

                // replace spaces for now
                case ' ':
                    if ( $attrib > 0 ) {
                        $tmp .= '|||';
                    }
                    break;

                default:
                    if ( $attrib < 0 ) {
                        $attrib = 0;
                    }
                    $tmp .= $stringAttributes[$i];
                    break;
            }

            $i++;
        }

        // check, old html attributes are in standalone mode like "nowrap"?
        if ( ( $quote == 0 ) && ( $tmp != '' ) ) {
            if ( $attrib == 1 ) {
                // the name of an attrib, add the '='
                $result .= '=';
            }
            $result .= '"' . $tmp . '"';
        }

        // attributes validation/fixes done
        // now, filter all
        // - bring source to array
        // - compare with whitelist
        $attributes = array();

        $listAttribs = explode( ' ', $result );
        foreach ( $listAttribs as $partAttr ) {
            // replace back the spaces
            $partAttr = str_replace( '|||', ' ', $partAttr );

            $attrToCheck = explode( '="', $partAttr );
            if ( count( $attrToCheck ) == 2 ) {
                if ( in_array( $attrToCheck[0], self::$htmlTagsDefaultAllowed[$tag] )
                    || in_array( $attrToCheck[0], self::$attributesDefaultAllowed )
                ) {
                    if ( substr( $attrToCheck[1], -1 ) == '"' ) {
                        $attrToCheck[1] = substr( $attrToCheck[1], 0, -1 );
                    }
                    $attributes[$attrToCheck[0]] = $attrToCheck[1];
                }
            }
        }

        if ( $attributes ) {
            $result = Mumsys_Html::attributesCreate( $attributes );
        } else {
            $result = '';
        }

        unset(
            $tmp, $attrToCheck, $listAttribs, $partAttr, $attributes, $attrib,
            $stringAttributes, $tag
        );

        return $result;
    }


    /**
     * Strip Html code and attributes from a given string
     *
     * If whitelist flag is set the html code will be parsed and filtered to get
     * the best result in return. if $againstWhitelist flag is not given all tags,
     * attributes und comments will be striped out
     *
     * @param string $htmlcode The Html code to be striped
     * @param boolean $againstWhitelist Flag to validate/check the code against a whitelist or not
     *
     * @return string Returns the stripped html text
     */
    public static function strip( $htmlcode = '', $againstWhitelist = false )
    {
        $htmlcode = stripslashes( $htmlcode );

        if ( $againstWhitelist ) {
            $keys = array_keys( self::$htmlTagsDefaultAllowed );
            $tagWhitelist = '<' . implode( '><', $keys ) . '>';
            $cleanedHtmlcode = strip_tags( $htmlcode, $tagWhitelist );
            unset( $keys, $tagWhitelist );

            $newHtmlcode = self::filter( $cleanedHtmlcode );
        } else {
            $newHtmlcode = strip_tags( $htmlcode );
        }

        return $newHtmlcode;
    }


    /**
     * Filter a html code snippet.
     *
     * A whitelist (@see $htmlTagsDefaultAllowed) will care to drop all
     * unknown/ unwanted parts.
     *
     * @param string $htmlcode Html code to parse and filter
     *
     * @return string The filtered html code
     */
    public static function filter( $htmlcode )
    {
        $htmlTags = self::$htmlTagsDefaultAllowed;

        $str = stripslashes( $htmlcode );

        // Delete all spaces from html tags eg: < h1 >, <h1 > to be <h1>
        // Attibutes which can have also more whitespaces eg: <h1   class=...>
        // Delete spaces or invalid tags
        $htmlcode = preg_replace( "/<\s*(\w+)\s*>/im", '<\1>', $htmlcode );

        $tmp = '';
        while ( preg_match( "|<(/?[[:alnum:]]*)[[:space:]]*([^>]*)>|im", $htmlcode, $reg ) ) {
            $i = strpos( $htmlcode, $reg[0] );
            $l = strlen( $reg[0] );

            $_tag = '';
            if ( isset( $reg[1][0] ) && $reg[1][0] == '/' ) {
                $tag = $_tag = strtolower( substr( $reg[1], 1 ) );
            } else {
                $tag = $_tag = strtolower( $reg[1] );
            }

            if ( isset( $htmlTags[$tag] ) ) {
                $a = $htmlTags[$tag];

                if ( $reg[1][0] == '/' ) {
                    $tag = '</' . $tag . '>';
                } elseif ( empty( $reg[2] ) ) {
                    $tag = self::_formatTag( $tag, false );
                } else {
                    // double quotes and syntax check/ fix
                    $attrbList = self::attributesFilter( $_tag, $reg[2] );
                    // fix & to &amp;
                    //old, wrong!:
                    //$attrbList = preg_replace('/&(?!(#[0-9]|amp)+;)/s', '&amp;', $attrbList);
                    // ok, but attribute values should not be converted!?
                    //$attrbList = preg_replace('/&(?!(amp)+;)/s', '&amp;', $attrbList);

                    $tag = self::_formatTag( $tag, $attrbList );
                }
            } else {
                // tag not allowed or no tag: eg. comments
                $tag = '';
            }
            $tmp .= substr( $htmlcode, 0, $i ) . $tag;
            $htmlcode = substr( $htmlcode, $i + $l );
        }

        $htmlcode = $tmp . $htmlcode;
        unset( $tag, $attrbList, $htmlTags, $tmp, $tag, $_tag );

        return $htmlcode;
    }


    /**
     * Re-format the html tag.
     *
     * @param string $tag String of the html tag
     * @param string|false $stringAttributes String with attributes for the tag
     * @return string formated tag e.g.: <hr /> <img .. /> <li> ...
     */
    protected static function _formatTag( $tag, $stringAttributes = false )
    {
        switch ( $tag )
        {
            case 'br':
            case 'hr':
            case 'input':
            case 'img':
            case 'link':
            case 'meta':
                if ( $stringAttributes ) {
                    $tag = '<' . $tag . ' ' . $stringAttributes . ' />';
                } else {
                    $tag = '<' . $tag . ' />';
                }
                break;
            default:
                if ( $stringAttributes ) {
                    $tag = '<' . $tag . ' ' . $stringAttributes . '>';
                } else {
                    $tag = '<' . $tag . '>';
                }
                break;
        }

        return $tag;
    }

}
