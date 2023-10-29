<?php

/**
 * Mumsys_Html_Table
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Html
 */


/**
 * CHANGELOG:
 * 2016
 *      Merge to git, fixes code, improves code, also for php 7, drops php4 support
 *
 * 2007-08-18
 *      Added global row and col attributes:
 *      $table->setRowAttributes('_', array('style'=>'color:red;', 'nowrap'=>'nowrap'));
 *      $table->setColAttributes('_', 4, array('align'=>'right'));
 *
 * ToDo:
 * Colspan, Rowspan
 */
// ================================================================== //
// USAGE:
//
// $tableAttributes = array('border'=>0,'cellpadding'=>2);
// $objTable = new Mumsys_Html_Table($tableAttributes);
// $headlines = array('USERNAME', 'WEEK','YEAR','DATE', 'OPTIONS' );
// $objTable->setHeadlines( $headlines );
//
// $objTable->setAltRowColor( array('#000000','#FFFFFF') );
//
// if the value in this col (columne 0) has changed than the row befor, then
// the color will change
// $objTable->setAltRowColor($arrayColors, $colKeyChange=0);
//
// $objTable->setRowAttributes($row, array('class'=>'rowclass'))
// $objTable->setColAttributes($row,$col, array('width'=>'100'))
// $tabledata = array(0=>array('col1'=>'val1','col2'=>'val3','col3'=>'val3...'));
// $objTable->setContent($tabledata);
//
// echo $objTable->toHTML();
// $html =  $objTable->getHtml();
// ================================================================== //


/**
 * Class for generating a Html-Table
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Html
 */
class Mumsys_Html_Table
    extends Mumsys_Xml_Abstract
{
    /**
     * Version ID information
     */
    const VERSION = '3.2.3';

    /**
     * Array containing the table attributes used in the constructor
     * @var array
     */
    protected $_tblProps = array();

    /**
     * Array containing the content of headlines
     * @var array
     */
    private $_headlines = array();

    /**
     * Array containing your data
     * @var array
     */
    private $_content = array();

    /**
     * Array containing number of Cols
     * @var integer
     */
    private $_numCols = 0;

    /**
     * Array containing number of Rows
     * @var integer
     */
    private $_numRows = 0;

    /**
     * String containing the html code of your table
     * @var string
     */
    private $_html = '';

    /**
     * Array containing alternative row colors for listing presentation
     * E.g.: ['#CCCCCC', '#EFEFEF', '#999999', '#666666']
     * @var array
     */
    private $_altRowColor = array();

    /**
     * ID of the column to detect row color changes, if given this activates
     * the feature.
     * @var int|string
     */
    private $_altRowColorKeyChange;

    /**
     * Internal memory keeper to detect color changes
     * @var integer|string
     */
    private $_colorChangeVal;

    /**
     * Array containing the table structure
     * @var array
     */
    private $_structure = array();  // $this->_structure[$row][$col]['attr']

    /**
     * Value to insert into empty cells
     * @var string
     */
    private $_autoFill = '&nbsp;';


    /**
     * Initialize the table object.
     *
     * @param array $attributes List of key/value pairs for the table tag
     * attributes
     */
    public function __construct( array $attributes = array() )
    {
        $this->_tblProps = $attributes;
        $this->_headlines['values'] = false;
        $this->_headlines['attr'] = array();
    }

    /**
     * Returns the html code of the table.
     *
     * This will render the table.
     *
     * @return string Html got of the redered table
     */
    public function __toString()
    {
        return $this->toHtml();
    }

    /**
     * Set attributes for the < table > tag.
     *
     * @param array $attributes List of key/value pairs for the table tag
     * attributes
     */
    public function setTableProps( array $attributes )
    {
        $this->_tblProps = $attributes;
    }


    /**
     * Set headline values for the table
     *
     * @param array|true $values List of values or if set true to use the key of the
     * input data which will expect key/value pairs and the keys will be used
     */
    public function setHeadlines( $values = true )
    {
        $this->_headlines['values'] = $values;
    }


    /**
     * Set attributes for each column at the headlines. Plural, the th-row.
     *
     * @param array $attr List of key/value pais for the attributes to set
     */
    public function setHeadlinesAttributes( $attr = array() )
    {
        $_isAttr = isset( $this->_headlines['attr']['attr'] );
        if ( !$_isAttr || !is_array( $this->_headlines['attr']['attr'] ) ) {
            $this->_headlines['attr']['attr'] = array();
        }

        $this->_headlines['attr']['attr'] = $attr;
    }


    /**
     * Returns headlines attributes (thead section)
     *
     * @return string Attribute/s string for the < thead > tag
     */
    public function getHeadlinesAttributes()
    {
        $attrib = '';
        if ( !empty( $this->_headlines['attr']['attr'] ) ) {
            $attrib = ' ' . parent::attributesCreate(
                $this->_headlines['attr']['attr']
            );
        }

        return $attrib;
    }


    /**
     * Sets headline attributes, singular, for a specified column
     */
    public function setHeadlinesAttribute( $col = 0, $attr = array() )
    {
        $_colAttr = isset( $this->_headlines['attr'][$col] );
        if ( !$_colAttr || !is_array( $this->_headlines['attr'][$col] ) ) {
            $this->_headlines['attr'][$col] = array();
        }

        $this->_headlines['attr'][$col] = $attr;
    }


    /**
     * Returns headlines attributes for the specified column.
     *
     * @param integer $col Column to get the attributes for
     * @return string Attributes for the specified column
     */
    public function getHeadlinesAttribute( $col = 0 )
    {
        $result = '';
        if ( !empty( $this->_headlines['attr'][$col] ) ) {
            $result = ' ' . parent::attributesCreate( $this->_headlines['attr'][$col] );
        }

        return $result;
    }


    /**
     * Adds a new column as headline.
     *
     * @param string $value Name of the new column
     */
    public function addHeadline( $value )
    {
        if ( $this->_headlines['values'] === false ) {
            $this->_headlines['values'] = array();
        }
        $this->_headlines['values'][] = $value;
    }


    /**
     * Sets the auto fill character for empty fields. E.g: n/a or [space]
     *
     * @param string $string String to fill if a value is empty eg.: &nbsp;
     */
    public function setAutoFill( $string )
    {
        $this->_autoFill = $string;
    }


    /**
     * Set the content to fill the table.
     *
     * @param array $data List of records
     */
    public function setContent( array $data )
    {
        $this->_content = $data;
    }


    /**
     * Returns the content by specified IDs if given.
     *
     * @param integer|string $rowId Number/key of the row or -1 for all rows
     *
     * @return array Key/value pairs of selected rowID
     */
    public function getContent( $rowId = -1 )
    {
        if ( $rowId >= 0 ) {
            $result = $this->_content[$rowId];
        } else {
            $result = $this->_content;
        }

        return $result;
    }


    /**
     * Return the number of columns based on the number of elements of the first row.
     *
     * @return integer Number of columns
     */
    public function getNumCols()
    {
        return ( $this->_numCols = count( current( $this->_content ) ) );
    }


    /**
     * Return the number of rows of data. without the headlines.
     *
     * @return integer Number of rows
     */
    public function getNumRows()
    {
        return ( $this->_numRows = count( $this->_content ) );
    }


    /**
     * Set alternativ row colors.
     *
     * If the second argument is given the color for each row will only change
     * if the value of the given key is changed.
     *
     * @param array $list List of colors to set e.g.: array('#FFFFF','#333333'..
     * @param int|false $colKey The column when a color-change should take effect
     */
    public function setAltRowColor( array $list, $colKey = false )
    {
        $this->_altRowColor = $list;

        if ( $colKey !== false ) { // col number
            $this->_altRowColorKeyChange = $colKey;
        }
    }


    /**
     * Returns the alternativ row color if possible/ was set befor.
     *
     * @param integer $num Row number the color should be used for (NOT
     * IMPLEMENTED YET)
     * @param string $val Value to detect a color change
     *
     * @return string|false New color string or false for no change
     */
    public function getAltRowColor( $num, $val = null )
    {
        $color = false;
        if ( $this->_altRowColor ) {
            $_isAltrowKeyChg = is_int( $this->_altRowColorKeyChange );
            if ( $_isAltrowKeyChg && $this->_colorChangeVal == $val ) {
                $color = current( $this->_altRowColor );
            } else {
                if ( ( $c = next( $this->_altRowColor ) ) ) {
                    $color = $c;
                } else {
                    $color = reset( $this->_altRowColor );
                }
            }
        }

        return $color;
    }


    /**
     * Get the html-head of the table including <thead> tags.
     *
     * @return string the html head or empty string if no headlines are given
     */
    public function getHeadlines()
    {
        $html = '';
        if ( $this->_headlines['values'] ) { // true OR list of headlines
            $html .= '<thead>' . _NL . '<tr'
                . $this->getHeadlinesAttributes()
                . '>' . _NL;
            $tmp = false;

            if ( $this->_headlines['values'] === true ) {
                $values = array_keys( $this->_content[0] );
            } else {
                $values = $this->_headlines['values'];
            }

            foreach ( $values as $i => $value ) {
                if ( $this->_headlines['attr'] ) {
                    $attr = $this->getHeadlinesAttribute( $i ); // sing.
                } else {
                    $attr = '';
                }

                if ( $this->_headlines['values'] !== true ) {
                    // creating by content
                    $_test = $this->_headlines['values'][$i];
                    if ( !$_test || is_int( $this->_headlines['values'][$i] ) ) {
                        $html .= '   <th' . $attr . '>' . $this->_autoFill
                            . '</th>' . _NL;
                    } else {
                        $html .= '   <th' . $attr . '>'
                            . $this->_headlines['values'][$i] . '</th>' . _NL;
                    }
                } else {
                    // creating by array indexes
                    if ( !$tmp ) {
                        $tmp = array_keys( $this->_content[0] );
                    }

                    if ( is_int( $tmp[$i] ) ) {
                        $html .= '   <th' . $attr . '>' . $this->_autoFill . '</th>' . _NL;
                    } else {
                        $html .= '   <th' . $attr . '>' . $tmp[$i] . '</th>' . _NL;
                    }
                }
            }
            $html .= '</tr>' . _NL . '</thead>' . _NL;
        }

        return $html;
    }


    /**
     * Sets/ replaces attributes for a specified row or column.
     *
     * @param integer|string $row Number of the row where attributes should be
     * placed or "_" for all rows
     * @param integer $col Number of the column where attributes should be
     * placed
     * @param array $attributes Attributes as key/value pairs
     */
    public function setColAttributes( $row, $col, $attributes = array() )
    {
        if ( !isset( $this->_structure[$row] ) ) {
            $this->_structure[$row] = array();
        }

        $_struct = isset( $this->_structure[$row][$col] );
        if ( !$_struct || !is_array( $this->_structure[$row][$col] ) ) {
            $this->_structure[$row][$col] = array();
        }

        // global attributes for each col
        if ( $row === '_' ) {
            $this->_structure['_'][$col]['attr'] = $attributes;
        } else {
            $this->_structure[$row][$col] = array('attr' => $attributes);
        }
    }


    /**
     * Get the attrutes for a specified row and col.
     *
     * @param integer $row Number of the selected row
     * @param integer $col Number of the selected column
     *
     * @return string attrubutes for the html tag eg.: key="value"
     */
    public function getColAttributes( $row, $col )
    {
        $result = '';
        if ( isset( $this->_structure[$row][$col]['attr'] ) ) {
            $result .= ' ' . parent::attributesCreate(
                $this->_structure[$row][$col]['attr']
            );
        }

        // global attributes for each col
        if ( isset( $this->_structure['_'][$col]['attr'] ) ) {
            $result .= ' ' . parent::attributesCreate(
                $this->_structure['_'][$col]['attr']
            );
        }

        return $result;
    }


    /**
     * Sets/ replaces attributes for a specified row.
     * For all rows you can set '_' as row.
     *
     * @param integer|string $row Number of the row where attributes should be
     * placed or "_" for all rows
     * @param array $attributes List of key/value pairs for the attributes
     */
    public function setRowAttributes( $row, $attributes = array() )
    {
        $_isStruct = isset( $this->_structure[$row] );
        if ( !$_isStruct || !is_array( $this->_structure[$row] ) ) {
            $this->_structure[$row] = array();
        }

        // global attributes
        if ( $row === '_' ) {
            $this->_structure['_']['attr'] = $attributes;
        } else {
            $this->_structure[$row]['attr'] = $attributes;
        }
    }


    /**
     * Return the attributes defined for this row of for all rows.
     *
     * @param integer $row Row number
     * @return string The html/ xml attributes string
     */
    public function getRowAttributes( $row )
    {
        $attributes = false;
        if ( isset( $this->_structure[$row]['attr'] ) ) {
            $attributes .= ' ' . parent::attributesCreate( $this->_structure[$row]['attr'] );
        }

        // global attributes
        if ( isset( $this->_structure['_']['attr'] ) ) {
            $attributes .= ' ' . parent::attributesCreate( $this->_structure['_']['attr'] );
        }

        return $attributes;
    }


    /**
     * Sets values and as replacement for a specified or all field/s.
     *
     * This method is usefult when overwriting content after using setContent()
     * and may be used to replace placeholder inside some fields: E.g. The
     * initial table records are templates with placeholder %s and you are able
     * to replace that items.
     *
     * @param string $str Value/ content to set
     * @param int|string $row Row number or "_" for all rows
     * @param int|string $col Number of the column or "_" for all columns
     */
    public function setColContents( $str, $row, $col )
    {
        try {
            if ( ! is_int( $row ) && $row != '_' ) {
                throw new Exception( 'Invalid row value to set col contents' );
            }

            if ( ! is_int( $col ) && $col != '_' ) {
                throw new Exception( 'Invalid column value to set col contents' );
            }

        } catch ( Exception $e ) {
            throw new Mumsys_Html_Exception( $e->getMessage() );
        }

        $this->_structure[$row][$col]['replacement'] = $str;
    }


    /**
     * Returns the content or a replacement value for the specified field.
     * If field was not set 'n/a' will return.
     *
     * @return string Content text for the requested field
     */
    public function getColContents( $row, $col, $str = 'n/a' )
    {
        if ( isset( $this->_structure[$row][$col]['replacement'] ) ) {
            $text = str_replace(
                '%s',
                $str,
                $this->_structure[$row][$col]['replacement']
            );
        } else {
            $text = $str;
        }

        return $text;
    }


    /**
     * Retuns the html code of the generated table.
     *
     * Parsing/ build the table will be used only once when using this method.
     * You may check toHtml() method to update the contents.
     *
     * @return string HTML code of the table
     */
    public function getHtml()
    {
        if ( empty( $this->_html ) ) {
            $this->toHtml();
        }
        return $this->_html;
    }


    /**
     * Returns the source as html representation.
     * NOT the html code to use!
     *
     * @return string html enterties encoded string of the generated html code
     * of the table
     */
    public function getSource()
    {
        return htmlentities( $this->getHtml() );
    }


    /**
     * Generates/ reders the html code for the table.
     *
     * You may need to generate the code twice. This is the method otherwise
     * use the getHtml() method
     *
     * @return string HTML ode of the table
     */
    public function toHtml()
    {
        if ( !$this->_content ) {
            $mesg = 'No content found to create a table';
            throw new Mumsys_Html_Exception( $mesg );
        } else {
            $records = $this->_content;
        }

        $tmp = false;

        if ( !$this->_numCols ) {
            $this->getNumCols();
        }

        if ( !$this->_numRows ) {
            $this->getNumRows();
        }

        $theHtml = '<table';

        if ( $this->_tblProps ) {
            $theHtml .= ' ' . parent::attributesCreate( $this->_tblProps );
        }
        $theHtml .= '>' . _NL;

        $theHtml .= $this->getHeadlines();

        if ( $this->_numRows ) {
            $theHtml .= '<tbody>' . _NL;
        }

        foreach ( $records as $row => $value ) {
            // col content
            $tmp = array_values( $records[$row] );

            // row begin
            $theHtml .= '<tr';
            if ( $this->_altRowColor ) {
                if ( is_int( $this->_altRowColorKeyChange ) ) {
                    if ( !isset( $tmp[$this->_altRowColorKeyChange] ) ) {
                        $message = 'Column key not exists to change a color for rows: "'
                            . $this->_altRowColorKeyChange . '"';
                        throw new Mumsys_Html_Exception( $message );
                    }
                    $colorKey = $tmp[$this->_altRowColorKeyChange];
                } else {
                    $colorKey = null;
                }

                $theHtml .= ' bgcolor="' . $this->getAltRowColor( $row, $colorKey ) . '"';

                if ( is_int( $this->_altRowColorKeyChange ) ) {
                    // memory
                    $this->_colorChangeVal = $tmp[$this->_altRowColorKeyChange];
                }
            }

            $theHtml .= $this->getRowAttributes( $row );
            $theHtml .= '>' . _NL;

            // col begin
            for ( $col = 0; $col < $this->_numCols; $col++ ) {
                $attr = $this->getColAttributes( $row, $col );

                $theHtml .= '   <td';
                // place attributes
                if ( $attr ) {
                    $theHtml .= $attr;
                }
                $theHtml .= '>';

                // content
                if ( isset( $tmp[$col] ) ) {
                    $theHtml .= $this->getColContents( $row, $col, $tmp[$col] );
                } else {
                    $theHtml .= $this->_autoFill;
                }

                $theHtml .= '</td>' . _NL;
            }
            $theHtml .= '</tr>' . _NL;
        }

        if ( $this->_numRows ) {
            $theHtml .= '</tbody>' . _NL;
        }
        $theHtml .= '</table>' . _NL;

        $this->_html = & $theHtml;

        return $theHtml;
    }

}
