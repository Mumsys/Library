<?php

/**
 * Mumsys_Upload_File
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
 * Upload File for handling file uploads and beeing able to mock in test enviroment.
 *
 * Alternativly namespaces would help which this implementation doesnt use currently.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Upload
 */
class Mumsys_Upload_File
    implements Mumsys_Upload_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';


    /**
     * Initialize the upload object and checks for validity with an existing
     * upload process
     *
     * @param array $fileInput PHP's $_FILES array containing only one item
     * with the following properties:
     *  - 'name'
     *  - 'tmp_name'
     *  - 'size'
     *  - 'error'
     *  - 'type' detected mimetype
     *
     * @throws Mumsys_Upload_Exception
     * @throws Exception
     */
    public function __construct( $fileInput )
    {
        if ( !is_array( $fileInput ) || empty( $fileInput['tmp_name'] ) ) {
            throw new Mumsys_Upload_Exception( 'Invalid file' );
        }

        if ( !isset( $fileInput['name'] ) ) {
            throw new Mumsys_Upload_Exception( 'No upload proccess detected' );
        }

        if ( is_array( $fileInput['name'] ) ) {
            throw new Mumsys_Upload_Exception( 'Multiple uploads not implemented' );
        }

        if ( !isset( $fileInput['error'] ) ) {
            throw new Mumsys_Upload_Exception( 'Invalid file.' );
        }

        if ( $fileInput['error'] !== UPLOAD_ERR_OK ) {
            $uploaderrors = array(
                0 => 'There is no error, the file uploaded with success',
                1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                3 => 'The uploaded file was only partially uploaded',
                4 => 'No file was uploaded',
                6 => 'Missing a temporary folder',
                7 => 'Failed to write file to disk.',
                8 => 'A PHP extension stopped the file upload.',
            );

            $errCode = $fileInput['error'];

            $errMsg = ( isset( $uploaderrors[(int) $errCode] )
                ? $uploaderrors[$errCode]
                : 'Unknown error, not part of php upload error' );
            $mesg = sprintf(
                'Upload error! Code: "%1$s", Message: "%2$s"', $errCode, $errMsg
            );
            throw new Mumsys_Upload_Exception( $mesg );
        }

        if ( !isset( $fileInput['size'] ) || $fileInput['size'] == 0 ) {
            $mesg = sprintf(
                'Empty file uploads prohabited for: "%1$s"', $fileInput['name']
            );
            throw new Mumsys_Upload_Exception( $mesg );
        }

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

        if ( move_uploaded_file( $from, $to ) === false ) {
            throw new Mumsys_Upload_Exception( 'Upload error.' );
        }

        // @codeCoverageIgnoreStart
        return true; // not testable that easy, we the mock in tests
        // @codeCoverageIgnoreEnd
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
        if ( is_uploaded_file( $tmpfile ) === false ) {
            throw new Mumsys_Upload_Exception( 'Upload error' );
        }

        // @codeCoverageIgnoreStart
        return true; // not testable that easy, we use the mock in tests
        // @codeCoverageIgnoreEnd
    }

}
