<?php

/**
 * Mumsys_Array2Xml_Default
 * for MUMSYS Library for Multi User Management System
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright (c) 2005 by Florian Blasel
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Array2Xml
 * @version 0.1 - Created on 2005-01-15
 * $Id: Mumsys_Array2Xml.php 2114 2011-02-12 20:38:48Z flobee $
 */


/**
 * Class to generate a xml from a multidimensional array which can be in n depth
 *
 * Hints:
 *      NN = Node Name
 *      NV = Node Value
 *      NA = Node Attribute
 *
 * Example:
 * <code>
 * <?php
 * $arr = array();
 * $arr['nodeName'] = 'root';
 * $arr['nodeAttr']['version'] = 'flobee v 0.1';
 * $arr['nodeAttr']['name'] = 'Array2Xml Creator';
 *
 * // first main tree
 * $arr['NV'][0]['nodeName'] = 'album';
 * $arr['NV'][0]['nodeAttr']['id'] = 123;
 * $arr['NV'][0]['nodeAttr']['code'] = 'First Attribute Value';
 * // new node
 * $arr['NV'][0]['NV'][0]['nodeName'] = 'tracks';
 * $arr['NV'][0]['NV'][0]['nodeAttr']['key'] = '456';
 * // new sub node
 * $arr['NV'][0]['NV'][0]['NV'][0]['nodeName'] = 'track';
 * $arr['NV'][0]['NV'][0]['NV'][0]['nodeAttr']['id'] = '10001';
 * $arr['NV'][0]['NV'][0]['NV'][0]['NV'][0]['nodeName'] = 'name';
 * $arr['NV'][0]['NV'][0]['NV'][0]['NV'][0]['NV'] = 'A name';
 * $arr['NV'][0]['NV'][0]['NV'][0]['NV'][1]['nodeName'] = 'file';
 * $arr['NV'][0]['NV'][0]['NV'][0]['NV'][1]['NV'] = '/some/file/file.mp3';
 * // new sub node
 * $arr['NV'][0]['NV'][0]['NV'][1]['nodeName'] = 'track';
 * $arr['NV'][0]['NV'][0]['NV'][1]['nodeAttr']['id'] = '10002';
 * $arr['NV'][0]['NV'][0]['NV'][1]['NV'][0]['nodeName'] = 'name';
 * $arr['NV'][0]['NV'][0]['NV'][1]['NV'][0]['NV'] = 'A name 2';
 * $arr['NV'][0]['NV'][0]['NV'][1]['NV'][1]['nodeName'] = 'file';
 * $arr['NV'][0]['NV'][0]['NV'][1]['NV'][1]['NV'] = '/some/file/file2.mp3';
 *
 * $options = array(
 *      'data'=>$arr,
 *      'charset_from'=>'iso-8859-1',
 *      'charset_to'=>'iso-8859-1',
 *      'cdata_escape'=>true,
 *      'debug'=>true,
 *      'tag_case'=>-1,
 *      'spacer' => "\t",
 *      'linebreak' => "\n",
 *      'cache'=>false,
 * );
 * $obj = new Mumsys_Array2Xml_Default($options);
 *
 * // if you want to change array keys
 * // $obj->setIdentifier(array('NN'=>'nodeName','NV'=>'NV','NA'=>'nodeAttr'));
 *
 * // default; not needed to set this
 * // $obj->setIdentifier(array('NN'=>'nodeName','NV'=>'nodeValues','NA'=>'nodeAttr'));
 *
 * $obj->echoXML();
 * // $xmlString = $obj->getXML();
 * ?>
 * </code>
 * @example docs/
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Array2Xml
 */
class Mumsys_Array2Xml_Default
    extends Mumsys_Array2Xml_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.3';

    /**
     * Alias method of getXML().
     *
     * @return string Returns the generated XML String
     */
    public function __toString()
    {
        return $this->getXML();
    }


    /**
     * Returns the status if file logging is enabled or not.
     *
     * @return boolean Returns true for cache enabled or false
     */
    public function getCache()
    {
        return $this->_cfg['cache'];
    }


    /**
     * Sets the flag to enable or disable file logging.
     *
     * @param boolean $flag Flag to enable (true) or disable (false) file logging
     */
    public function setCache( $flag ): void
    {
        $this->_cfg['cache'] = (bool) $flag;
    }


    /**
     * Returns the location of the cache file.
     *
     * @return string Returns the location of the cache file
     */
    public function getCacheFile()
    {
        return $this->_cfg['cachefile'];
    }


    /**
     * Sets the location of the cache file.
     *
     * @param string $location Location to the cahce file
     */
    public function setCacheFile( $location ): void
    {
        $this->_cfg['cachefile'] = (string) $location;
    }


    /**
     * Get identifier configuration.
     *
     * @return array Returns the identifier, configuration of data array for
     * node-value, node-name and node-attributes
     */
    public function getIdentifier()
    {
        return $this->_cfg['ID'];
    }


    /**
     * Changing the Identifier of your incomming data array.
     * @param array $ids
     */
    public function setIdentifier( $ids )
    {
        if ( $ids && is_array( $ids ) ) {
            foreach ( $ids as $key => $value ) {
                if ( $value === '' || !isset( $this->_cfg['ID'][$key] ) ) {
                    $this->_error[] = sprintf(
                        'Error setIdentifier! Empty value or ID not found/'
                        . 'wrong: $key: "%1$s", v: "%2$s"', $key, $value
                    );
                } else {
                    $this->_cfg['ID'][$key] = $value;
                }
            }
        }
    }


    /**
     * Returns the charset_from and charset_to parameter as key/value pair.
     *
     * @return array Key/value pair of charset_from and charset_to
     */
    public function getEncoding(): array
    {
        return array(
            'charset_from' => $this->_cfg['charset_from'],
            'charset_to' => $this->_cfg['charset_to']
        );
    }


    /**
     * Sets the character encoding for the data in- and output.
     *
     * @param string $encodingFrom Encoding to transform from; default: iso-8859-1
     * @param string $encodingTo Encoding to transform to; default: utf-8
     */
    public function setEncoding( $encodingFrom = 'iso-8859-1', $encodingTo = 'utf-8' )
    {
        $this->_cfg['charset_from'] = strtolower( $encodingFrom );
        $this->_cfg['charset_to'] = strtolower( $encodingTo );
    }


    /**
     * Get the root elements.
     *
     * @return array returns the root node 0 header/openig tag, 1 footer/
     * closing tag including the doctype
     */
    public function getRoot()
    {
        return $this->_root;
    }


    /**
     * Set the root element.
     *
     * if the data array contains content the content will be kept as is
     * else $opts is ment for the hole data tree
     * @see See _mkRoot() methode
     *
     * @param  array $options Options to be set
     * @return void
     */
    public function setRoot( array $options = array() ): void
    {
        $this->_mkRoot( $options );
    }


    /**
     * Return the root data node value (NV).
     *
     * @return array Returns the root data node value (NV)
     */
    public function getData(): array
    {
        if ( !isset( $this->_data[$this->_cfg['ID']['NV']] ) ) {
            return array();
        }

        return $this->_data[$this->_cfg['ID']['NV']];
    }


    /**
     * Sets the data array if you do not insert it when calling the object
     * it should not contain the root elements.
     *
     * @param  array $data
     */
    public function setData( array $data ): void
    {
        if ( $data ) {
            $this->_data[$this->_cfg['ID']['NV']] = $data;
        } else {
            throw new Mumsys_Array2Xml_Exception( _( 'No data given to be set.' ) );
        }
    }


    /**
     * Building the xml doctype element.
     *
     * @return string Returns the xml doctype element
     */
    public function getDoctype()
    {
        $tmp = '';
        if ( isset( $this->_cfg['version'] ) ) {
            $tmp .= ' version="' . $this->_cfg['version'] . '"';
        }
        if ( isset( $this->_cfg['charset_to'] ) ) {
            $tmp .= ' encoding="' . $this->_cfg['charset_to'] . '"';
        }

        return '<' . '?xml' . $tmp . ' ?>' . $this->_cfg['linebreak'];
    }


    /**
     * Building Node Elements.
     *
     * @example Example for the function parameter $elements:
     * if it contain just a string all is fine. when using an array then two
     * options are allowed: only one array value contains the value and all
     * other parts have array keys so that they will be used as attributes.:
     * array('value', 'attribute'=>'attributevalue'..);
     * Second way: array('value', array('attr1'=>'attr1val' ...);
     *
     * @param string $nodeName Name of the node to be created
     * @param array|string $elements Array with value and or list with attributes
     *
     * @return string Returns a xml element: <some>..something more..</some>
     */
    public function createElements( $nodeName, $elements )
    {
        $i = 0;
        $ret = '';
        $attr = '';
        $desc = '';
        $numNumeric = 0;
        $newNodeName = $nodeName;

        if ( is_array( $elements ) ) {
            foreach ( $elements as $numElem => $values ) {
                // foreach ($elements AS $numElem => $values) {
                if ( is_array( $values ) ) {
                    $attr = $this->getAttributes( $values );
                } else {
                    //if ( is_string( $numElem ) ) {
                    /** @todo just ignore ? */
                    /** @todo overwrite incomming $nodeName ? */
                    //$newNodeName = $numElem;
                    /** @todo overwrite throw exception ? */
                    //$errorMsg = sprintf(
                    //    'Unknown type for array key "%1$s" found.',
                    //    $numElem
                    //);
                    //throw new Mumsys_Array2Xml_Exception($errorMsg);
                    //}
                    if ( is_string( $values ) ) {
                        $desc = $values;
                    }
                    if ( is_numeric( $numElem ) ) {
                        $numNumeric += 1;
                        if ( $numNumeric > 1 ) {
                            $errorMsg = sprintf(
                                'Unknown array configuration for key: "%1$s" value: "%2$s" found.',
                                $numElem, $values
                            );
                            throw new Mumsys_Array2Xml_Exception( $errorMsg );
                        }
                    }
                }
                $i++;
            }

            $ret .= '<' . $this->getCase( $newNodeName ) . $attr;

            if ( !empty( $desc ) ) {
                $ret .= '>' . $this->validate( $desc ) . '</'
                    . $this->getCase( $newNodeName ) . '>';
            } else {
                $ret .= ' />';
            }
        } else {
            $val = '';
            if ( !empty( $elements ) ) {
                $val = $this->validate( $elements );
                $ret = '<' . $this->getCase( $newNodeName ) . '>'
                    . $val
                    . '</' . $this->getCase( $newNodeName ) . '>';
            } else {
                $ret = '<' . $this->getCase( $newNodeName ) . ' />';
            }
        }
        $ret .= $this->_cfg['linebreak'];
        return $ret;
    }


    /**
     * Handle the attributes for an element.
     *
     * @param array $array Array of attributes to be created in a key-value pair
     *
     * @return string Returns the attriutes in a formatted string
     */
    public function getAttributes( array $array )
    {
        $string = '';
        if ( $array && is_array( $array ) ) {
            foreach ( $array as $key => $val ) {
                if ( is_int( $key ) ) {
                    $errorMsg = sprintf(
                        'Numeric attribute key not allowed. '
                        . 'key: "%1$s", value: "%2$s".',
                        $key, $val
                    );
                    throw new Mumsys_Array2Xml_Exception( $errorMsg );
                }
                $string .= ' '
                    . $this->validate( $this->getCase( $key ), false )
                    . '="' . $this->validate( $val, false ) . '"';
            }
        }

        return $string;
    }


    /**
     * Generate the xml and return the string.
     *
     * @return string Returns the generated XML String
     */
    public function getXML(): string
    {
        $xml = '';
        if ( empty( $this->_root ) ) {
            $this->_mkRoot( $this->_data );
        }

        if ( $this->_data ) {
            // init the whole tree
            if ( is_array( $this->_data[$this->_cfg['ID']['NV']] ) ) {
                foreach ( $this->_data[$this->_cfg['ID']['NV']] as $i => $children ) {
                    if ( is_array( $children ) ) {
                        $this->_parse( $children );
                    } else {
                        // is error?

                        $this->buffer( $this->validate( $children ) );
                    }
                }
            } else {
                // is error?
                $this->buffer( $this->validate( $this->_data[$this->_cfg['ID']['NV']] ) );
            }
            $this->buffer( $this->_root[1] );
            $xml = $this->_xml;
        } else {
            if ( $this->_xml || $this->_cfg['cache'] ) {
                $this->buffer( $this->_root[1] );
                if ( $this->_xml ) {
                    $xml .= $this->_xml;
                }
                // $xml .= ($this->_root[1]);
                // root footer element
            } else {
                $this->_error[] = 'No data found';
            }
        }

        return $xml;
    }


    /**
     * Adding a tree by a given array to the buffer.
     *
     * This is helpfule in interations only! otherwise it can break your xml
     * tree e.g.:
     * <code>
     * $this->setRoot( [your root config] );
     * while( [conditions] ) {
     *     $this->addElementTree( [current node] );
     * }
     * ...
     * </code>
     *
     * @param array $array
     *
     * @return void
     */
    public function addElementTree( array $array = array() ): void
    {
        if ( empty( $array ) || !isset( $array[$this->_cfg['ID']['NV']] ) ) {
            return;
        }

        $xml = '';
        // init the whole tree
        if ( is_array( $array[$this->_cfg['ID']['NV']] ) ) {
            foreach ( $array[$this->_cfg['ID']['NV']] as $i => $children ) {
                if ( is_array( $children ) ) {
                    $xml .= $this->_parse( $children );
                } else {
                    $xml .= $this->validate( $children );
                }
            }
        }
        // does not makes sence...
        //else {
        //    $xml .= $this->validate( $array[$this->_cfg['ID']['NV']] );
        //}

        $this->buffer( $xml );
    }


    /**
     * Returns a validated node value.
     *
     * Converts/ encodes the entities if needed base on setting (cdata_escape).
     *
     * @param  string $value Value to validate
     * @param boolean $native Flag to decide to encode entitys (true) and if
     * cdata_escape=true or not (false), default: true
     *
     * @return string Returns the validated string
     */
    public function validate( $value, $native = true ): string
    {
        if ( preg_match( '/[<>&\'"]+/', $value ) ) {
            if ( $this->_cfg['cdata_escape'] && $native === true ) {
                $value = sprintf(
                    "<![CDATA[%s]]>",
                    str_replace( '<![CDATA[', '<![ATADC[', $value )
                );
            } else {
                $search = '/(&[a-zA-Z]{2,7};)/';
                $replace = "'&#'.ord(html_entity_decode('$1',ENT_QUOTES)).';'";
                $value = preg_replace( $search, $replace, htmlentities( $value ) );
                if ( $value === null ) {
                    throw new Mumsys_Array2Xml_Exception( 'Regex error' );
                }
            }
        }
        // TBA !!! $value = $this->encode($value);
        return $value;
    }


    /**
     * Returns the encoded and transformed string if needed.
     *
     * @uses iconv php extension
     *
     * @param string $value String to be transformed to "charset_to" encoding
     *
     * @return string|false Encoded string or false; @see charset_to in setup
     */
    public function encode( $value )
    {
        $php_errormsg = null;
        $encFrom = $this->_cfg['charset_from'];
        $encTo = $this->_cfg['charset_to'];

        if ( ( $value = iconv( $encFrom, $encTo, $value ) ) === false ) {
            $errormsg = 'iconv error, turn on "track_errors" to get more details';
            $this->_error[] = $errormsg;
        }

        return $value;
    }


    /**
     * Check if any error was detected.
     *
     * @return boolean Returns true on error of false for no error
     */
    public function isError()
    {
        if ( $this->_error ) {
            return true;
        }

        return false;
    }


    /**
     * Return error messages.
     *
     * @return array Returns the error messages or empty array for no errors
     */
    public function getError()
    {
        return $this->_error;
    }


    /**
     * Free/ empty all generated data and errors.
     *
     * @return void
     */
    public function free()
    {
        $this->_error = array();
        $this->_root = array();
        $this->_data = array();
        $this->_curNodeName = '';
        $this->_xml = '';
    }


    /**
     * Generate the root element for the xml.
     *
     * @todo root without a node name does not make sence. throw exception then.
     * Like getXml() already does when parsing the data
     *
     * @param array $data Optional; The configuration to set the root element.
     * See examles of the data array
     *
     * @return void
     * @throws Mumsys_Array2Xml_Exception If the $data parameter is empty.
     */
    private function _mkRoot( array $data = array() ): void
    {
        if ( empty( $data ) ) {
            $mesg = 'No data to create a root element';
            throw new Mumsys_Array2Xml_Exception( $mesg );
        }

        if ( empty( $this->_root[0] ) ) {
            $this->_root[0] = $this->getDoctype();

            if ( isset( $data[$this->_cfg['ID']['NA']] ) ) {
                $attr = $this->getAttributes( $data[$this->_cfg['ID']['NA']] );
            } else {
                $attr = '';
            }

            if ( isset( $data[$this->_cfg['ID']['NN']] ) ) {
                $nodeName = $data[$this->_cfg['ID']['NN']];
                $this->_curNodeName = $nodeName;
                $this->_root[0] .= sprintf(
                    '<%1$s%2$s>%3$s',
                    $this->getCase( $nodeName ),
                    $attr,
                    $this->_cfg['linebreak']
                );
                $this->_root[1] = '</' . $this->getCase( $nodeName ) . '>'
                    . $this->_cfg['linebreak'];
            } else {
                $this->_root = array('', '');
            }

            $this->buffer( $this->_root[0] );
        }
    }


    /**
     * Calculate the number of spaces
     *
     * @staticvar array $mem Memmory for already calculated spacers
     * @param integer $num Numer of spaser repeats
     *
     * @return string Returns number of calculated spaces from given number
     */
    private function _sp( $num )
    {
        static $mem = array();

        if ( $num < 0 ) {
            $num = 0;
        }

        if ( !isset( $mem[$num] ) ) {
            $mem[$num] = str_repeat( $this->_cfg['spacer'], $num );
        }

        return $mem[$num];
    }


    /**
     * Parsing the data array and returning the xml tree.
     *
     * @param array $a Array of data to parse
     * @param integer $numSp Number of spaces from Spacer
     *
     * @return string Returns empty string ToDo: To Check
     */
    protected function _parse( array $a, $numSp = 0 )
    {
        if ( !isset( $a[$this->_cfg['ID']['NN']] ) || empty( $a[$this->_cfg['ID']['NN']] ) ) {
            // there must be elements in current node
            return $this->createElements( $this->_curNodeName, $a );
        } else {
            $this->_curNodeName = $a[$this->_cfg['ID']['NN']];
            //$element =  $this->_cfg['linebreak'] . $sp . '<'. $this->getCase($a[$this->_cfg['ID']['NN']]);
            $element = $this->_sp( $numSp ) . '<' . $this->getCase( $a[$this->_cfg['ID']['NN']] );
        }

        // check available Attributes
        if ( isset( $a[$this->_cfg['ID']['NA']] )
            && is_array( $a[$this->_cfg['ID']['NA']] )
            && !empty( $a[$this->_cfg['ID']['NA']] ) ) {
            $element.= $this->getAttributes( $a[$this->_cfg['ID']['NA']] );
        }

        // Bestimmung des Inhalts
        if ( isset( $a[$this->_cfg['ID']['NV']] ) && $a[$this->_cfg['ID']['NV']] !== false ) {
            // Bestimmung Welche Art Inhalt vorliegt
            if ( is_array( $a[$this->_cfg['ID']['NV']] ) ) {
                $element .= '>' . $this->_cfg['linebreak'] . $this->_sp( $numSp );
                $this->buffer( $element );
                $element = '';
                // Aufbau der Kindelemente
                foreach ( $a[$this->_cfg['ID']['NV']] as $_none => $childs ) {
                    // Rekursion auf die Kindelemente
                    if ( is_array( $childs ) ) {
                        $element .= $this->_parse( $childs, ++$numSp );
                        --$numSp;
                    } else {
                        // what is it? error? node? element?
                        // takeing as element value
                        $this->_curNodeName = $childs;
                        $element .= $this->_cfg['linebreak'] . $this->createElements( $this->_curNodeName, $childs );
                    }
                }
                $element .= '</' . $this->getCase( $a[$this->_cfg['ID']['NN']] ) . '>';
            } else {
                // Textnode
                $element .= '>' . $this->validate( $a[$this->_cfg['ID']['NV']] );
                $element .= '</' . $this->getCase( $a[$this->_cfg['ID']['NN']] ) . '>';
            }
        } else {
            // Element ist leer
            $element .= ' />';
        }

        $element .= $this->_cfg['linebreak'] . $this->_sp( --$numSp );
        $this->buffer( $element );
        $element = '';

        return $element;
    }

}
