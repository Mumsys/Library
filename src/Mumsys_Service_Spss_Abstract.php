<?php

/**
 * Mumsys_Service_Spss_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2015 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
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
    const VERSION = '2.0.1';

    /**
     * SPSS Reader|Writer adapter/interface
     * @var \SPSS\Sav\Reader|\SPSS\Sav\Writer
     */
    protected $_spss;


    /**
     * Initialise the object.
     *
     * @param \SPSS\Sav\Reader|\SPSS\Sav\Writer|mixed $iface Reader|Writer
     * interface to be used
     */
    public function __construct( $iface )
    {
        if ( !( $iface instanceof \SPSS\Sav\Reader )
            && !( $iface instanceof \SPSS\Sav\Writer )
        ) {
            $mesg = 'Invalid Reader/Writer instance';
            throw new Mumsys_Service_Spss_Exception( $mesg );
        }

        $this->_spss = $iface;
    }


    /**
     * Returns the Reader or Writer adapter/interface.
     *
     * @return \SPSS\Sav\Reader|\SPSS\Sav\Writer interface based on construction
     */
    public function getAdapter()
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
        if ( $this->_spss instanceof \SPSS\Sav\Reader ) {
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
        if ( $this->_spss instanceof \SPSS\Sav\Writer ) {
            return true;
        }

        return false;
    }

}
