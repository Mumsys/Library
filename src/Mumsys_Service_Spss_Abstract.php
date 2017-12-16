<?php

/**
 * Mumsys_Service_Spss_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2015 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 * @version     1.0.0
 * Created: 2017-11-30
 */


/**
 * Abstract class for SPSS reader/writer.
 *
 * @see https://github.com/flobee/spss
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 */
abstract class Mumsys_Service_Spss_Abstract
    extends Mumsys_Service_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * SPSS Reader|Writer interface
     * @var \SPSS\Sav\Reader|\SPSS\Sav\Writer
     */
    protected $_spss;


    /**
     * Initialise the object.
     *
     * @param \SPSS\Sav\Reader|\SPSS\Sav\Writer $iface Reader|Writer interface
     * to be used
     */
    public function __construct( $iface )
    {
        if (
            !( $iface instanceof \SPSS\Sav\Reader)
            && !($iface instanceof \SPSS\Sav\Writer)
        ) {
            $mesg = 'Invalid Reader/Writer instance';
            throw new Mumsys_Service_Spss_Exception( $mesg );
        }

        $this->_spss = $iface;
    }


    /**
     * Returns the Reader or Writer interface.
     *
     * @return Reader|Writer interface based on construction
     */
    public function getInterface()
    {
        return $this->_spss;
    }


    /**
     * Returns the status of the curent used reader interface.
     *
     * @return boolean True if spss instance is a "reader" otherwise false
     */
    public function isReader(): bool
    {
        if ( $this->_spss instanceof \SPSS\Sav\Reader) {
            return true;
        }

        return false;
    }


    /**
     * Returns the status of the curent used writer interface.
     *
     * @return boolean True if spss instance is a "writer" otherwise false
     */
    public function isWriter(): bool
    {
        if ( $this->_spss instanceof \SPSS\Sav\Writer) {
            return true;
        }

        return false;
    }

}
