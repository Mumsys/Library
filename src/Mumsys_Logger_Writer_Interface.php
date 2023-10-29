<?php

/**
 * Mumsys_Logger_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2011 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 * @version     1.1.0
 * Created on 2011/02
 */


/**
 * Writer interface for the logger object.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Logger
 */
interface Mumsys_Logger_Writer_Interface
{
    /**
     * Write given content to the writer
     *
     * @param string $content String to save
     *
     * @return boolean Returns true on success
     *
     * @throws Exception on errors.
     */
    public function write( $content );


    /**
     * Truncate storage
     *
     * This will clean a file to zero byte or truncate the database table
     */
    public function truncate();
}
