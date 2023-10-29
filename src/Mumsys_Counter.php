<?php

/*{{{*/
/**
 * Mumsys_Counter
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2006 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Counter
 * @version     3.1.0
 * Created: 2006-01-12
 * @filesource
 */
/*}}}*/


/**
 * Operation counter/ calculator
 *
 * @category Mumsys
 * @package Mumsys_Library
 * @subpackage Mumsys_Counter
 */
class Mumsys_Counter extends Mumsys_Abstract
{
    /**
     * Version ID information
     */
    const VERSION = '3.1.0';

    /**
     * Result of counts
     * @var float|double
     */
    private $_counts = 0;


    /**
     * Initialize the object.
     *
     * @param boolean $startCount Start flag. If true counter will start now and
     * counts the first occurrence
     */
    public function __construct( $startCount = false )
    {
        if ( $startCount ) {
            $this->_counts += 1;
        }
    }


    /**
     * Adds given quantity to the counter.
     *
     * @param float $count Positiv number/ quantity to add to the counter.
     * A negative value will converted to be a positiv number
     */
    public function add( $count )
    {
        if ( $count <= 0 ) {
            $count *= -1;
        }

        $this->_counts += (float)$count;
    }


    /**
     * Subtract given quantity to the counter.
     *
     * @param float $count Positiv number/ quantity to subtract from the counter.
     * A negative value will be converted to be a positiv number
     */
    public function sub( $count )
    {
        if ( $count <= 0 ) {
            $count *= -1;
        }

        $this->_counts -= (float)$count;
    }


    /**
     * Sets the current count + 1
     */
    public function count()
    {
        $this->_counts += 1;
    }


    /**
     * Returns the number of counts/result of operation.
     *
     * @return int|float Returns the number of counts.
     */
    public function result()
    {
        return $this->_counts;
    }


    /**
     * Returns the number of counts for the output in string representation
     *
     * @return string Number of counts
     */
    public function __toString()
    {
        return (string) $this->result();
    }

}
