<?php

/**
 * Mumsys_Logger_Writer_Mock
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2011 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 * @version     1.0.0
 * Created 2017/12
 */


/**
 * Mock writer for the logger object.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 */
class Mumsys_Logger_Writer_Mock
    extends Mumsys_Abstract
    implements Mumsys_Logger_Writer_Interface
{

    /**
     * Dummy: Flag to check if a filepointer was opened or not.
     * This flag will be set to true when open() methode was called successfully
     * or set to false when close() method was called.
     * @var boolean
     */
    private $_isOpen = true;

    /**
     * Flag to set if writeabiliy is possible or not
     * @var boolean
     */
    private $_isWriteable = true;


    /**
     * Set isOpen flag to true.
     */
    public function open()
    {
        $this->_isOpen = true;
    }


    /**
     * Set isOpen flag to false.
     */
    public function close()
    {
        $this->_isOpen = false;
    }


    /**
     * Set writeability.
     *
     * @param boolean $bool True for writeable, false for no writeability
     */
    public function setWriteable( bool $bool ): void
    {
        $this->_isWriteable = true;
    }


    /**
     * Write given content to the writer
     *
     * @param string $content String to save
     *
     * @return boolean Returns true on success
     * @throws Exception on errors.
     */
    public function write( $content )
    {
        if ( !$this->_isOpen ) {
            $message = 'File not open. Can not write to file';
            throw new Mumsys_Logger_Exception( $message );
        }

        if ( !$this->_isWriteable ) {
            throw new Mumsys_Logger_Exception( 'File not writeable' );
        }

        return true;
    }


    /**
     * Truncate storage
     *
     * This will clean a file to zero byte or truncate the database table
     *
     * @return boolean true on success or false on failure
     * @throws Mumsys_Logger_Exception If file not opened before
     */
    public function truncate()
    {
        if ( !$this->_isOpen ) {
            $message = 'Can not truncate file. File not open';
            throw new Mumsys_File_Exception( $message );
        }

        return true;
    }

}
