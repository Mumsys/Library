<?php

/**
 * Mumsys_Array2Xml_Abstract
 * for MUMSYS Library for Multi User Management System
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright (c) 2005 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Array2Xml
 * @version 0.1 - Created on 2005-01-15
 * $Id: Mumsys_Array2Xml.php 2114 2011-02-12 20:38:48Z flobee $
 */


/**
 * Abstract class for array to xml implementations.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Array2Xml
 * @see class.File.php Needed if cache is activated
 */
abstract class Mumsys_Array2Xml_Abstract
    extends Mumsys_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * XML tag case: lower case
     */
    const TAG_CASE_LOWER = 0;

    /**
     * XML tag case: UPPER CASE
     */
    const TAG_CASE_UPPER = 1;

    /**
     * XML tag case: Like thE iNput, not change
     */
    const TAG_CASE_AS_IS = -1;

    /**
     * Internal configurations vars.
     * @var array
     */
    protected $_cfg = array();

    /**
     * Array of data to generate the xml
     * @var array
     */
    protected $_data = array();

    /**
     * root elements including head with doctype attribute and the footer.
     * array indexes are: 0 = header; 1 = footer
     * @var array
     */
    protected $_root = array();

    /**
     * Xml string to be generated.
     * @var string
     */
    protected $_xml;

    /**
     * Current node name to work on.
     * @var string
     */
    protected $_curNodeName;

    /**
     * @var array
     */
    protected $_error = array();

    /**
     * Writer interface to write or cache temp. xml content.
     *
     * @var Mumsys_Logger_Writer_Interface
     */
    protected $_writer;


    /**
     * Initilise the array to xml object.
     *
     * @param array $params Arguments to setup the object
     *  - [cachefile] string Optional Location of the cachefile, default:
     *    /tmp/Array2XmlCache'.date('YmdHis').'cache.xml
     *  - [cache] boolean Optional Flag to enable (true) or disable (false)
     *    default: false, @see setWriter()
     *  - [data] array Optional The data in a configured multidimentinal array
     *    to create the xml from
     *  - [charset_to] string Optional Encoding to create the xml string,
     *    default: iso-8859-1
     *  - [charset_from] string Optional Encoding of the incomming data,
     *    default: iso-8859-1
     *  - [version] string Optional Version of the xml-dokument see
     *    getDoctype(), default: 1.0
     *  - [cdata_escape] boolean Optional Set to true if escaping cdata section
     *    is needed, default: false
     *  - [spacer] Optional Char for indentation, default; \t
     *  - [linebreak] Optional Default: \n
     *  - [tag_case] Mumsys_Array2Xml_Abstract::TAG_CASE_AS_IS (default),
     *    Mumsys_Array2Xml_Abstract::TAG_CASE_LOWER,
     *    Mumsys_Array2Xml_Abstract::TAG_CASE_UPPER;
     *  - [debug] boolean Optional flag to enable/disable debug mode,
     *    default: false
     */
    public function __construct( array $params = array() )
    {
        $this->_cfg = array(
            // IMPORTANT! : if you work with huge data > 1, 2 MB you may get
            // memory problems!
            // To solve this we write every little thing to a cache file, free
            // memory and go on. Set to true to enable. but use setWriter()
            // after construction.
            'cache' => false,
            'cachefile' => '/tmp/Array2XmlCache' . date( 'YmdHis' ) . 'cache.xml',
            // charset_to , utf-8 out not implemented
            'charset_from' => 'iso-8859-1',
            'charset_to' => 'iso-8859-1',
            'version' => parent::VERSION,
            // true for cdata escaping and false for xmlencode escaping
            'cdata_escape' => false,
            // "\t" , ' ', ...
            'spacer' => "\t",
            'linebreak' => "\n",
            // do notting = -1 | CASE_LOWER = 0 | CASE_UPPER = 1
            'tag_case' => self::TAG_CASE_AS_IS,
            'debug' => false,
            // wrapper configuration for incomming data
            // @see class.Array2Xml.readme.php
            'ID' => array(
                'NN' => 'nodeName',
                'NA' => 'nodeAttr',
                'NV' => 'nodeValues'
            ),
            'data' => '',
        );

        if ( isset( $params['cachefile'] ) ) {
            $this->_cfg['cachefile'] = (string) $params['cachefile'];
        }

        if ( isset( $params['cache'] ) ) {
            $this->_cfg['cache'] = (bool) $params['cache'];
        }

        if ( isset( $params['data'] ) ) {
            $this->_data = $params['data'];
        }

        if ( isset( $params['charset_to'] ) ) {
            $this->_cfg['charset_to'] = strtolower( $params['charset_to'] );
        }

        if ( isset( $params['charset_from'] ) ) {
            $this->_cfg['charset_from'] = strtolower( $params['charset_from'] );
        }

        if ( isset( $params['version'] ) ) {
            $this->_cfg['version'] = $params['version'];
        }

        if ( isset( $params['cdata_escape'] ) ) {
            $this->_cfg['cdata_escape'] = $params['cdata_escape'];
        }

        if ( isset( $params['spacer'] ) ) {
            $this->_cfg['spacer'] = $params['spacer'];
        }

        if ( isset( $params['linebreak'] ) ) {
            $this->_cfg['linebreak'] = $params['linebreak'];
        }

        if ( isset( $params['tag_case'] ) ) {
            switch ( $params['tag_case'] ) {
                case ( $params['tag_case'] === Mumsys_Array2Xml_Abstract::TAG_CASE_AS_IS ):
                case ( $params['tag_case'] === Mumsys_Array2Xml_Abstract::TAG_CASE_LOWER ):
                case ( $params['tag_case'] === Mumsys_Array2Xml_Abstract::TAG_CASE_UPPER ):
                    $this->_cfg['tag_case'] = $params['tag_case'];
                    break;

                default:
                    throw new Mumsys_Array2Xml_Exception( 'Invalid tag case' );
            }
        }
    }


    /**
     * Buffer given xml part or write to cache file if cache is enabled.
     *
     * @uses Mumsys_Logger_Writer_Interface Uses write() if cache is enabled
     *
     * @param string $value String to buffer
     */
    public function buffer( $value )
    {
        if ( $this->_cfg['cache'] && $value > '' ) {
            if ( !( $this->_writer instanceof Mumsys_Logger_Writer_Interface ) ) {
                $mesg = 'Can not buffer. Writer not set';
                throw new Mumsys_Array2Xml_Exception( $mesg );
            }
            $this->_writer->write( $value );
        } else {
            $this->_xml .= $value;
        }
    }


    /**
     * Returns the writer object.
     *
     * @return Mumsys_Logger_Writer_Interface Returns the writer object
     */
    public function getWriter()
    {
        return $this->_writer;
    }


    /**
     * Sets the writer to store the xml contents.
     *
     * @param Mumsys_Logger_Writer_Interface $value
     */
    public function setWriter( Mumsys_Logger_Writer_Interface $value )
    {
        $this->_writer = $value;
    }


    /**
     * Sets the tag case of the current value.
     *
     * Changing character case of the tagt in an xml document.
     *
     * @param  string $value String to alter the string case if set
     *
     * @return string Returns the modified or untouched string
     * @throws Mumsys_Array2Xml_Exception If an invalid case was set
     */
    public function getCase( $value ): string
    {
        $case = $this->_cfg['tag_case'];
        switch ( $case )
        {
            case Mumsys_Array2Xml_Abstract::TAG_CASE_LOWER:
                $ret = strtolower( $value );
                break;

            case Mumsys_Array2Xml_Abstract::TAG_CASE_UPPER:
                $ret = strtoupper( $value );
                break;

            case Mumsys_Array2Xml_Abstract::TAG_CASE_AS_IS:
            default:
                $ret = $value;
                break;
        }

        return $ret;
    }

}
