<?php

/**
 * Mumsys_Upload_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2018 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Upload
 * @version     1.0.0
 * Created: 2018-12
 */

/**
 * Upload Interface
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Upload
 */
interface Mumsys_Upload_Interface
{
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
    public function move_uploaded_file( string $from, string $to ): bool;


    /**
     * Alias of PHP's is_uploaded_file in this implementation
     *
     * @param string $tmpfile File location from (probably: _FILES["tmp_name"])
     *
     * @return boolean TRUE on success
     * @throws Mumsys_Upload_Exception If PHP's is_uploaded_file() returns false
     */
    public function is_uploaded_file( string $tmpfile ): bool;
}
