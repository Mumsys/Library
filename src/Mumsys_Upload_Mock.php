<?php

/**
 * Mumsys_Upload_Mock
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2018 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Upload
 * Created: 2018-12
 */

/**
 * Upload File for handling file uploads and beeing able to mock in test
 * enviroment.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Upload
 */
class Mumsys_Upload_Mock
    implements Mumsys_Upload_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';


    /**
     * Initialize the upload object and checks for validity with an existing
     * upload process. (default/ File implementation)
     *
     * @ param array $fileInput PHP's $_FILES array containing only one item with
     * the following properties:
     *  - 'name'
     *  - 'tmp_name'
     *  - 'size'
     *  - 'error'
     *  - 'type' detected mimetype
     *
     * @throws Mumsys_Upload_Exception
     * @throws Exception
     */
    public function __construct()
    {
    }


    /**
     * Alias of PHP's move_uploaded_file
     *
     * @param string $from File location from (probably: _FILES["tmp_name"])
     * @param string $to File location where the file should go to
     *
     * @return boolean TRUE on success. If filename is not a valid upload file,
     * then no action will occur, and move_uploaded_file will throw an exception.
     * If filename is a valid upload file, but cannot be moved for some reason,
     * no action will occur, and move_uploaded_file will throw an exception.
     * Additionally, a warning will be issued.
     *
     * @throws Mumsys_Upload_Exception If target path is not writeable or
     * PHP's move_uploaded_file() returns false
     */
    public function move_uploaded_file( string $from, string $to ): bool
    {
        $dirTo = dirname( $to );
        if ( !is_writeable( $dirTo ) ) {
            $mesg = sprintf( 'Target path "%1$s" not writeable', $dirTo );
            throw new Mumsys_Upload_Exception( $mesg );
        }

        if ( false === file_exists( $from ) ) {
            throw new Mumsys_Upload_Exception( 'Upload file not found/ exists' );
        }

        if ( rename( $from, $to ) === false ) {
            throw new Mumsys_Upload_Exception( 'Upload error.' );
        }

        return true;
    }


    /**
     * Alias of PHP's is_uploaded_file
     *
     * @param string $tmpfile File location from (probably: _FILES["tmp_name"])
     *
     * @return boolean TRUE on success
     * @throws Mumsys_Upload_Exception If PHP's is_uploaded_file() returns false
     */
    public function is_uploaded_file( string $tmpfile ): bool
    {
        if ( file_exists( $tmpfile ) === false ) {
            throw new Mumsys_Upload_Exception( 'Upload error' );
        }

        return true;
    }

}
