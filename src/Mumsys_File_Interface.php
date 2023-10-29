<?php

/**
 * Mumsys_File_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2011 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  File
 * @version     3.0.0
 * 0.1 - Created on 2011/02
 */
/* }}} */


/**
 * Interface for file object
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  File
 */
interface Mumsys_File_Interface
{
    /**
     * Initialise the File object.
     *
     * If a file and mode is given the open() methode will be called
     * automaticly.
     *
     * @param array $params Params to set on initialisation the object
     *  [file] Location to the file to read or to write
     *  [way] Kind/ mode of to read/write the file
     *  [buffer] Number of bytes to be read from the file. @see setBuffer()
     *
     * @return void
     */
    public function __construct( array $params = array() );


    /**
     * Open connection to read or write to a file.
     *
     * @return bool Returns true on succsess or fals on failure
     * @throws Mumsys_File_Exception If file can not be opened
     */
    public function open();


    /**
     * Close current file connection if exists.
     *
     * @return boolean Returns the status of the close() command. True on
     * success or true by default even if no connection was made
     */
    public function close();


    /**
     * Write given content to the file.
     *
     * @param mixed $content Content to write to the file
     *
     * @return bool Returns true on success.
     * @throws Mumsys_File_Exception Throws exception if writing to file is
     * impossible
     */
    public function write( $content );


    /**
     * Read from file or number of bytes set in setBuffer().
     *
     * @return string|boolean Returns file contents or false on errors
     * @throws Mumsys_File_Exception Throws exception if reading fails
     */
    public function read();


    /**
     * Truncate storrage
     *
     * This will clean a file to zero byte or truncate the database table
     *
     * @return boolean true on success or false on failure
     *
     * @throws Mumsys_File_Exception If file not opened before
     */
    public function truncate();


    /**
     * Set write or read mode.
     *
     *  - 'r'   Open for reading only; place the file pointer at the beginning
     *          of the file.
     *  - 'r+'  Open for reading and writing; place the file pointer at the
     *          beginning of the file.
     * - 'w'    Open for writing only; place the file pointer at the beginning
     *          of the file and truncate the file to zero length. If the file
     *          does not exist, attempt to create it.
     *  - 'w+'  Open for reading and writing; place the file pointer at the
     *          beginning of the file and truncate the file to zero length. If
     *          the file does not exist, attempt to create it.
     *  - 'a'   Open for writing only; place the file pointer at the end of the
     *          file. If the file does not exist, attempt to create it.
     *  - 'a+'  Open for reading and writing; place the file pointer at the end
     *          of the file. If the file does not exist, attempt to create it.
     *  - 'x'   Create and open for writing only; place the file pointer at the
     *          beginning of the file. If the file already exists, the fopen()
     *          call will fail by returning FALSE and generating an error of
     *          level E_WARNING. If the file does not exist, attempt to create
     *          it. This is equivalent to specifying O_EXCL|O_CREAT flags for
     *          the underlying open(2) system call.
     *  - 'x+'  Create and open for reading and writing; place the file pointer
     *          at the beginning of the file. If the file already exists, the
     *          fopen() call will fail by returning FALSE and generating an
     *          error of level E_WARNING. If the file does not exist, attempt
     *          to create it. This is equivalent to specifying O_EXCL|O_CREAT
     *          flags for the underlying open(2) system call.
     *
     * @param string $string Return the mode for the file operation
     */
    public function setMode( $string );


    /**
     * Sets the name and location of the file to read or write from.
     * If connection was already open and fiel differs from file which was set
     * befor the file connection will be closed and not re-opened with the new
     * file location.
     *
     * @param string $file File to read or write from
     */
    public function setFile( $file );


    /**
     * Returns the location of the file.
     *
     * @return string Returns the location of the file.
     */
    public function getFile();


    /**
     * Set the buffer.
     * @param integer $n Set number of bytes to fetch when reading a file.
     * Set to 0 will read the entire file
     */
    public function setBuffer( $n );


    /**
     * Get writable status.
     * The flag will be set when using open() method.
     *
     * @return boolean Return if file is writable (true) or not (false)
     */
    public function isWriteable();


    /**
     * Get readable status.
     * The flag will be set when using open() method
     *
     * @return boolean Return if file is readable (true) or not (false)
     */
    public function isReadable();


    /**
     * Test if open() was called successfully.
     *
     * @return boolean Returns true if file connection was opend successfully or
     * false on failure or the connection was closed
     */
    public function isOpen();
}
