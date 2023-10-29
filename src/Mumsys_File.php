<?php

/**
 * Mumsys_File
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2007 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  File
 * @version     3.1.0
 */


/**
 * Class for standard File handling open close read and write a file.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  File
 */
class Mumsys_File
    extends Mumsys_Abstract
    implements Mumsys_File_Interface, Mumsys_Logger_Writer_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '3.2.0';

    /**
     * File handle
     * @var resource|false
     */
    private $_fh = false;

    /**
     * Location of the file
     * @var string
     */
    protected $_file;

    /**
     * Flag to set if writeabiliy is possible or not
     * @var bool|null
     */
    private $_isWriteable = null;

    /**
     * Flag to set if readabiliy is possible or not
     * This flag will be set when open() methode will be called
     * @var bool|null
     */
    private $_isReadable = null;

    /**
     * Flag to check if a filepointer was opened or not.
     * This flag will be set to true when open() methode was called successfully
     * or set to false when close() method was called.
     * @var boolean
     */
    private $_isOpen = false;

    /**
     * Way/ mode/ kind of reading or writing the file
     * @var string
     */
    private $_way;

    /**
     * Number of bytes to be used when reading a file.
     * By default (0 zero) the hole file will be read
     *
     * @var integer Number of bytes to read
     */
    private $_buffer = 0;


    /**
     * Initialise the File object.
     * If a file and mode is given the open() methode will be called
     * automaticly.
     *
     * @param array $params Params to set on initialisation the object
     *  - [file] Location to the file to read or to write
     *  - [way] Kind/ mode of to read/write the file
     *  - [buffer] Number of bytes to be read from the file when reading from;
     *    see setBuffer()
     *
     * @return void
     */
    public function __construct( array $params = array() )
    {
        if ( isset( $params['file'] ) ) {
            $this->_file = $params['file'];
        }

        if ( isset( $params['way'] ) ) {
            $this->setMode( $params['way'] );
        }
        if ( isset( $params['buffer'] ) ) {
            $this->_buffer = intval( $params['buffer'] );
        }

        if ( $this->_file && $this->_way ) {
            $this->open();
        }
    }


    /**
     * Destructor.
     * File connection, if open, will be closed by php internally befor the
     * dectuction take effect. But needed for other side effects.
     */
    public function __destruct()
    {
        $this->close();
    }


    /**
     * Open connection to read or write to a file.
     *
     * @return boolean Returns true on succsess
     * @throws Mumsys_File_Exception If file can not be opened
     */
    public function open()
    {
        if ( ( $this->_fh = @fopen( $this->_file, $this->_way ) ) === false ) {
            $msg = sprintf(
                'Can not open file "%1$s" with mode "%2$s". Directory is '
                . 'writeable: "%3$s", readable: "%4$s".',
                $this->_file,
                $this->_way,
                ( self::bool2str( $this->isWriteable() ) ),
                ( self::bool2str( $this->isReadable() ) )
            );

            throw new Mumsys_File_Exception( $msg );
        }

        $this->_isOpen = true;

        $this->isWriteable();

        $this->isReadable();

        return true;
    }


    /**
     * Close current file connection if exists.
     *
     * @return boolean Returns the status of the close() command. True on
     * success or true by default even if no connection was made
     */
    public function close()
    {
        $this->_isOpen = false;
        $this->_isWriteable = null;
        $this->_isReadable = null;
        $return = true;

        if ( is_resource( $this->_fh ) ) {
            $return = @fclose( $this->_fh );
        }

        return $return;
    }


    /**
     * Write given content to the file.
     *
     * @param string|mixed $content Content to write to the file
     *
     * @return true Returns true on success.
     *
     * @throws Mumsys_File_Exception If writing to file is impossible
     */
    public function write( $content )
    {
        if ( !$this->_isOpen ) {
            $message = sprintf(
                'File not open. Can not write to file: "%1$s".', $this->_file
            );
            throw new Mumsys_File_Exception( $message );
        }

        if ( !$this->_isWriteable ) {
            $message = sprintf( 'File not writeable: "%1$s".', $this->_file );
            throw new Mumsys_File_Exception( $message );
        }

        if ( ( $numBytes = fwrite( $this->_fh, $content ) ) === false ) {
            $message = sprintf(
                'Can not write to file: "%1$s". IsOpen: "%2$s", Is writeable: "%3$s".',
                $this->_file,
                ( self::bool2str( $this->_isOpen ) ),
                ( self::bool2str( $this->isWriteable() ) )
            );
            throw new Mumsys_File_Exception( $message );
        }

        return true;
    }


    /**
     * Read from file or number of bytes set in setBuffer().
     *
     * @return string|mixed Returns the file contents
     *
     * @throws Mumsys_File_Exception Throws exception if reading fails
     */
    public function read()
    {
        if ( !$this->_isOpen ) {
            $mesg = sprintf(
                'File not open. Can not read from file: "%1$s".', $this->_file
            );
            throw new Mumsys_File_Exception( $mesg );
        }

        if ( !$this->isReadable() ) {
            $mesg = sprintf(
                'File "%1$s" not readable with mode "%2$s". Is writeable '
                . '"%3$s", readable: "%4$s".',
                $this->_file, $this->_way,
                self::bool2str( $this->isWriteable() ),
                self::bool2str( $this->isReadable() )
            );
            throw new Mumsys_File_Exception( $mesg );
        }

        if ( empty( $this->_buffer ) ) {
            // entire file
            $buf = filesize( $this->_file );
        } else {
            $buf = $this->_buffer;
        }

        if ( $buf === 0 ) {
            return '';
        }

        if ( ( $result = fread( $this->_fh, $buf ) ) === false ) {
            $mesg = sprintf(
                'Error when reading the file: "%1$s". IsOpen: "%2$s".',
                $this->_file,
                ( self::bool2str( $this->_isOpen ) )
            );
            throw new Mumsys_File_Exception( $mesg );
        }

        return $result;
    }


    /**
     * Truncate storrage
     *
     * This will clean a file to zero byte or truncate the database table
     *
     * @return boolean true on success or false on failure
     *
     * @throws Mumsys_File_Exception If file not opened before
     */
    public function truncate()
    {
        if ( !$this->_isOpen ) {
            $message = sprintf(
                'Can not truncate file "%1$s". File not open', $this->_file
            );
            throw new Mumsys_File_Exception( $message );
        }

        return ftruncate( $this->_fh, 0 );
    }


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
    public function setMode( $string )
    {
        $modes = array('r', 'r+', 'w', 'w+', 'a', 'a+', 'x', 'x+');

        if ( in_array( $string, $modes ) ) {
            $this->_way = (string) $string;
        } else {
            throw new Mumsys_File_Exception( 'Invalid mode' );
        }
    }


    /**
     * Sets the location of the file to read from or to write to.
     * If connection was already open and file differs from file which was set
     * befor the file connection will be closed and not re-opened with the new
     * file location.
     *
     * @param string $file File to read or write from
     */
    public function setFile( $file )
    {
        if ( $this->_isOpen && $this->_file != $file ) {
            $this->close();
        }

        $this->_file = (string) $file;
    }


    /**
     * Returns the location of the file.
     *
     * @return string Returns the location of the file.
     */
    public function getFile()
    {
        return $this->_file;
    }


    /**
     * Set the buffer.
     *
     * @param integer $n Set number of bytes to fetch when reading a file.
     * Set to 0 will read the entire file
     */
    public function setBuffer( $n )
    {
        $this->_buffer = (int) $n;
    }


    /**
     * Get writable status.
     * The flag will be set when using open() method.
     *
     * @return boolean Return if file is writable (true) or not (false)
     */
    public function isWriteable()
    {
        if ( $this->_isWriteable === null ) {
            if ( $this->isOpen() ) {
                // test if file is writeable
                if ( is_writeable( $this->_file ) ) {
                    $this->_isWriteable = true;
                } else {
                    $this->_isWriteable = false;
                }
            } else {
                // test if writing/creating to write would be possible
                if ( is_writeable( dirname( $this->_file ) )
                    && is_dir( dirname( $this->_file ) )
                ) {
                    $this->_isWriteable = true;
                } else {
                    $this->_isWriteable = false;
                }
            }
        }

        return $this->_isWriteable;
    }


    /**
     * Get readable status.
     * The flag will be set when using open() method
     *
     * @return boolean Return if file is readable (true) or not (false)
     */
    public function isReadable()
    {
        if ( $this->_isReadable === null ) {
            if ( $this->_isOpen ) {
                if ( is_readable( $this->_file ) ) {
                    $this->_isReadable = true;
                } else {
                    $this->_isReadable = false;
                }
            } else {
                // test if reading file would be possible eg when opening/
                // creating a file in read/write mode
                if ( is_readable( dirname( $this->_file ) ) ) {
                    $this->_isReadable = true;
                } else {
                    $this->_isReadable = false;
                }
            }
        }

        return $this->_isReadable;
    }


    /**
     * Test if open() was called successfully.
     *
     * @return boolean Returns true if file connection was opend successfully
     * or false on failure or the connection was closed
     */
    public function isOpen()
    {
        return $this->_isOpen;
    }


    /**
     * Returns the boolean value in human readable way (Yes/No).
     *
     * @param boolean $str Value that match in php for true or false
     *
     * @return string Returns the string for the current bool value: Yes for
     * true, No for false
     */
    private static function bool2str( $str )
    {
        return ( $str ? 'Yes' : 'No' );
    }

}
