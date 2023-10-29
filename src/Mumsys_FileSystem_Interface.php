<?php

/**
 * Mumsys_FileSystem_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright (c) 2006 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  FileSystem
 * @version 3.0.6
 * Created on 2006-12-01
 */


/**
 * Interface for the filesystem and tools to handle files or directories
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  FileSystem
 */
interface Mumsys_FileSystem_Interface
{
    /**
     * Scan for files and directorys in given path.
     *
     * Important: If using this methode more often: All results will be held in
     * $this->_dirinfo and all of it will be returned! Dont be confused if you
     * think records are scanned twice or you think you have dublicate records.
     *
     * @todo follow symlinks?
     *
     * @param string $dir Directory/ Path to start the scan
     * @param boolean $hideHidden Flag to decide to skip hidden files or
     * directories
     * @param boolean $recursive Flag to deside to scan recursive or not
     * @param array $filters List of regular expressions look for a match (the
     * list will used AND conditions)
     *
     * @return array|false Returns list of file/link/directory details like
     * path, name, size, type
     */
    public function scanDirInfo( $dir, $hideHidden = true, $recursive = false,
        array $filters = array() );


    /**
     * Returns simple file details of a file, link or directory.
     * Best usage for a directory scan: The first parameter contains the path,
     * the second parameter contains the filename.
     *
     * @param string $fileOrPath Location of the file including the filename or
     * the path (if it is a directory) or the path of a file but then the
     * filename will be required as second parameter.
     * @param string|false $filename Optional; Name of the file without the path
     *
     * @return array Returns an array containing the filename "name", "path",
     * "filesize" and the filetype "type" (like: "link", file", "dir")
     *
     * @throws Mumsys_FileSystem_Exception Throws exception if file, link or
     * directory could not be found
     */
    public function getFileDetails( $fileOrPath, $filename = false );


    /**
     * Returns extended file details of a file, link or directory.
     * Best usage for a directory scan: The first parameter contains the path,
     * the second parameter contains the file or link name for an optimal usage.
     *
     * Note: This methode is made for scaning for files in cli enviroment to
     * feed a media database etc. Use it only if know what you are doing. Things
     * can run in a timeout when using in web enviroment.
     *
     * @param string $fileOrPath Location of the file including the filename or
     * the path (if it is a directory) or the path of a file but then the
     * filename will be required as second parameter.
     * @param string|false $filename Optional; Name of the file without the path
     *
     * @return array Returns an array containing the following array keys (if
     * available):
     *  'file' string file location,
     *  'name' string filename
     *  'size' integer filesize in bytes
     *  'type' string filetype: file|dir|link
     *  'path' string path
     *  'is_file', 'is_dir', 'is_link', 'is_readable' 'is_writable', 'is_executable' boolean
     *  'permission' integer permission decimal,
     *  'owner' integer UID
     *  'group' integer GID
     *  'mtime' unix timestamp
     *  'atime' unix timestamp
     *  'ctime' unix timestamp
     *  'filetype' string Mime filetype description
     *  'ext' string extension
     *  'mimetype' string mime type
     *  'target' if a link
     *  'owner_name' string owner name
     *  'group_name' string group name
     *
     * @throws Mumsys_FileSystem_Exception Throws exception if file, link or
     * directory not exists.
     */
    public function getFileDetailsExtended( $fileOrPath, $filename = false );


    /**
     * Returns the content file type of a file.
     * It uses the shell command "file" to get its information.
     * Returning examples: "UTF-8 Unicode text", ASCII Text",,
     *
     * @param string $file Location of the file
     * @return string Returns the content file type or an empty string
     */
    public function getFileType( $file );


    /**
     * Copy a file to a destination and return the new location on success.
     * If needed: Keep a copy if exists.
     *
     * @param string $fileSource Location of the source file
     * @param string $fileTarget Target path or file location
     * @param boolean $keepCopy Flag to keep copys if target exists
     * @param integer $tries Internal counter as suffix if keepcopy set to true
     *
     * @return string Returns the new/target filename
     * @throws Mumsys_FileSystem_Exception Throws exception on error
     */
    public function copy( $fileSource, $fileTarget, $keepCopy = false,
        $tries = 0 );


    /**
     * Rename a file or directory.
     *
     * @param string $source Source file or directory to be renamed
     * @param string $destination Target file or directory name
     * @param boolean $keepCopy Flag to decide what to do if target exists
     * @param mixed|resource $streamContext optional stream functions
     *
     * @return string Returns the new/target filename on success
     * @throws Mumsys_FileSystem_Exception Throws exception on error
     */
    public function rename( $source, $destination, $keepCopy = true,
        $streamContext = null );


    /**
     * Creates a link (hard or soft) and returns the link location.
     *
     * @todo check how to remove realpath function or what is important in
     * controller?
     *
     * @param string $file Absolut location to the file
     * @param string $to Absolut location of the link name
     * @param string $type Link type to be created "soft" for symlinks, "hard"
     * for hardlinks
     * @param string $way Setter to create absolut or relative links (rel|abs)
     * @param boolean $keepCopy Flag to keep existing files or links
     *
     * @return string Returns the link name
     * @throws Mumsys_FileSystem_Exception Throws exception on errors. Eg: if
     * link type is invalid
     */
    public function link( $file, $to, $type = 'soft', $way = 'rel',
        $keepCopy = false );


    /**
     * Creates a directory if not exists.
     *
     * @param string $dir Directory to be created
     * @param int $perm Octal permission mode of directory to be chmod eg.: 0755
     *
     * @return boolean True on success or false if directory exists.
     * @throws Mumsys_FileSystem_Exception Throws exception on any other error
     */
    public function mkdir( $dir, $perm = 0755 );


    /**
     * Create a given directory recursivly like "mkdir -p".
     *
     * @param string $dir Directory to be created
     * @param int $perm Octal permission mode of directory to be chmod eg.: 0755
     *
     * @return boolean true on success or false on failure
     *
     * @throws Mumsys_FileSystem_Exception Throws exception if the real path
     * could not be determined or the creation of a directory fails
     */
    public function mkdirs( $dir, $perm = 0755 );


    /**
     * Returns the relative path for the target.
     * Note: This method can not decide between path and a file e.g: /myfile
     * It will be handled as directory /myfile/.
     *
     * @param string $from Path location from
     * @param string $to Path location to
     * @return string Returns the relative path
     * @throws Mumsys_FileSystem_Exception Throws exception on error.
     */
    public function getRelativeDir( $from, $to );


    /**
     * Returns the cool filesize. A formatted string like eg.: "124.89 KB"
     *
     * @param integer $size number of bytes
     * @param integer $digits number of fractional digits
     *
     * @return string The formated string of size
     */
    public static function coolfilesize( $size, $digits = 2 );

}
