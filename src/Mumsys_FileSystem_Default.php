<?php

/**
 * Mumsys_FileSystem_Default
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright (c) 2006 by Florian Blasel
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  FileSystem
 */


/**
 * Class for the file system to handle files or directories. Default version
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  FileSystem
 */
class Mumsys_FileSystem_Default
    extends Mumsys_FileSystem_Common_Abstract
    implements Mumsys_FileSystem_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '3.1.0';

    /**
     * Buffer/ memory keeper of scanned directories.
     *
     * Note: Each object ha its own buffer! scanning several times you may have
     * dublicate records which is not a fault!
     *
     * @var array
     */
    private $_dirInfo;


    /**
     * Initialise the object.
     *
     * @param array $options Not implemented yet.
     */
    public function __construct( array $options = array() )
    {
        unset( $options ); // currently unused here
        $this->_dirInfo = array();
    }


    /**
     * Scan for files and directorys in given path.
     *
     * Important: If using this methode more often: All results will be held in
     * $this->_dirinfo and all of it will be returned! Dont be confused if you
     * think records are scanned twice or you think you have dublicate records.
     *
     * Links if can be detected will be ignored. Depending on start path.
     *
     * @todo follow symlinks?
     *
     * @param string $dir Directory/ Path to start the scan
     * @param boolean $hideHidden Flag to decide to skip hidden files or directories
     * @param boolean $recursive Flag to deside to scan recursive or not
     * @param array $filters List of regular expressions to look for a match
     * (the list will used AND conditions)
     * @param integer $offset Optional; Start point to collect data
     * @param integer $limit Optional; Limit of results. 0 (zero): no limit, Max 500 default: 0
     *
     * @return array|false Returns list of file/link/directory details like path, name, size, type
     */
    public function scanDirInfo( $dir, $hideHidden = true, $recursive = false,
        array $filters = array(), $offset = 0, $limit = 0 )
    {
        if ( $offset < 0 ) {
            $offset = 0;
        }

        if ( $limit && $limit > 1000 ) {
            $limit = 500;
        }

        $ds = DIRECTORY_SEPARATOR;

        if ( @is_dir( $dir ) && is_readable( $dir ) && !is_link( $dir ) ) {
            $cnt = 0;
            if ( $dh = @opendir( $dir ) ) {
                while ( ( $file = readdir( $dh ) ) !== false ) {
                    if ( $file == '.' || $file == '..' ) {
                        continue;
                    }

                    // ...an than came polly.avi detection
                    $noPolly = preg_match( '/^(\.\w+|\.$|\.\.$)/i', $file );
                    if ( $hideHidden && $file[0] == '.' && $noPolly ) {
                        continue;
                    }

                    $test = $dir . $ds . $file;
                    if ( $recursive && is_dir( $test . $ds ) ) {
                        $newdir = $dir . $ds . $file;
                        $this->_dirInfo[$newdir] = $this->getFileDetails( $newdir );
                        $this->scanDirInfo(
                            $newdir, $hideHidden, $recursive, $filters, $offset,
                            $limit
                        );
                    } else {
                        $this->_dirInfo[$test] = $this->getFileDetails(
                            $dir, $file
                        );
                    }
                }
            }
            @closedir( $dh );
        } else {
            return false;
        }

        if ( $filters ) {
            foreach ( $this->_dirInfo as $location => & $tmp ) {
                foreach ( $filters as $regex ) {
                    if ( !preg_match( $regex, $location ) ) {
                        unset( $this->_dirInfo[$location] );
                    }
                }
            }
        }

        if ( $limit ) {
            $this->_dirInfo = array_slice( $this->_dirInfo, $offset, $limit, true );
        }

        return $this->_dirInfo;
    }


    /**
     * Prepare incomming file informations to get file details.
     *
     * @param string $fileOrPath Location of the file including the filename or
     * the path (if it is a directory) or the path of a file but then the
     * filename will be required as second parameter.
     * @param string|false $filename Name of the file without the path
     *
     * @return array Returns an array containing the "filename", "path" and
     * "file" as the hole location of the file or directory.
     *
     * @throws Exception Throws exception if file, link or directory could not be found
     */
    private function _getFileDetailsPrepare( $fileOrPath, $filename = false )
    {
        $result = array();
        if ( $filename && is_dir( $fileOrPath ) ) {
            $filepath = $fileOrPath . '/' . $filename;
            $path = $fileOrPath;
        } else {
            $filepath = $fileOrPath;
            $path = false;
            $filename = false;
        }
        if ( file_exists( $filepath ) || is_link( $filepath ) ) {
            if ( !$filename ) {
                $filename = basename( $filepath );
            }
            if ( !$path ) {
                $path = substr( $filepath, 0, strrpos( $filepath, '/' ) );
            }
        } else {
            throw new Mumsys_FileSystem_Exception( 'File "' . $filepath . '" not found' );
        }
        return array('filename' => $filename, 'file' => $filepath, 'path' => $path);
    }


    /**
     * Returns simple file details of a file, link or directory.
     * Best usage for a directory scan: The first parameter contains the path,
     * the second parameter contains the filename.
     *
     * @todo implement tests
     *
     * @param string $fileOrPath Location of the file including the filename or the
     * path (if it is a directory) or the path of a file but then the filename
     * will be required as second parameter.
     * @param string|false $filename Optional; Name of the file without the path
     *
     * @return array Returns an array containing the filename "name", "path", "filesize"
     * and the filetype "type" (like: "link", file", "dir")
     *
     * @throws Exception Throws exception if file, link or directory could not be found
     */
    public function getFileDetails( $fileOrPath, $filename = false )
    {
        $prepared = $this->_getFileDetailsPrepare( $fileOrPath, $filename );
        return array(
            'file' => $prepared['file'],
            'name' => $prepared['filename'],
            'path' => $prepared['path'],
            'size' => is_link( $prepared['file'] ) ? 0 : @filesize( $prepared['file'] ),
            'type' => filetype( $prepared['file'] )
        );
    }


    /**
     * Returns extended file details of a file, link or directory.
     * Best usage for a directory scan: The first parameter contains the path,
     * the second parameter contains the file or link name for an optimal usage.
     *
     * Note: This methode is made for scaning for files in cli enviroment to feed
     * a media database. Use it only if know what you are doing. Things can
     * run in a timeout when using in web enviroment.
     *
     * @param string $fileOrPath Location of the file including the filename or the
     * path (if it is a directory) or the path of a file but then the filename
     * will be required as second parameter.
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
     * @throws Exception Throws exception if file, link or directory not exists.
     */
    public function getFileDetailsExtended( $fileOrPath, $filename = false )
    {
        $prepared = $this->_getFileDetailsPrepare( $fileOrPath, $filename );
        $path = $prepared['path'];
        $filename = $prepared['filename'];
        $fileOrPath = $prepared['file'];
        $info = array();
        if ( $stat = @lstat( $path . '/' . $filename ) ) {
            $info = array(
                'file' => $prepared['file'],
                'type' => filetype( $fileOrPath ),
                'name' => $filename,
                'size' => $stat['size'],
                'is_file' => @is_file( $fileOrPath ),
                'is_dir' => @is_dir( $fileOrPath ),
                'is_link' => @is_link( $fileOrPath ),
                'is_readable' => @is_readable( $fileOrPath ),
                'is_writable' => @is_writable( $fileOrPath ),
                'path' => $path,
                'permission' => $stat['mode'],
                'owner' => $stat['uid'],
                'group' => $stat['gid'],
                'mtime' => $stat['mtime'],
                'atime' => $stat['atime'],
                'ctime' => $stat['ctime'],
                'filetype' => $this->getFileType( $fileOrPath ), // unix 'file ./file.ext'/mimetype?
            );
            if ( $info['type'] == 'dir' ) {
                $info['is_executable'] = true;
                $info['ext'] = false;
            } else {
                $info['is_executable'] = @is_executable( $path );
                $info['ext'] = $this->extGet( $filename );
                if ( function_exists( 'mime_content_type' ) && $info['is_readable'] ) {
                    $info['mimetype'] = mime_content_type( $path . '/' . $filename );
                }
            }
            if ( $info['is_link'] ) {
                $info['target'] = @readlink( $fileOrPath );
            }

            if ( function_exists( 'posix_getpwuid' ) ) {
                $info['owner_name'] = @reset( posix_getpwuid( $info['owner'] ) );
            }
            if ( function_exists( 'posix_getgrgid' ) ) {
                $info['group_name'] = @reset( posix_getgrgid( $info['group'] ) );
            }
        }
        return $info;
    }


    /**
     * Returns the content file type of a file.
     *
     * It uses fileinfo extension first or the shell command "file" to get
     * the information.
     * Returning examples: "UTF-8 Unicode text", "ASCII Text"
     *
     * @param string $file Location of the file
     *
     * @return string Returns the content file type or an empty string
     */
    public function getFileType( $file )
    {
        $info = '';

        if ( class_exists( 'finfo' ) ) {
            $finfo = new finfo( FILEINFO_PRESERVE_ATIME );
            $info = $finfo->file( $file, FILEINFO_DEVICES );
        } else if ( function_exists( 'mime_content_type' ) ) {
            $info = mime_content_type( $file );
        } else if ( PHP_SHLIB_SUFFIX != 'dll' ) {
            $info = shell_exec( 'file -b -p "' . $file . '";' );
        }

        return $info;
    }


    /**
     * Copy a file to a destination and return the new location on success.
     * If needed: Keep a copy if exists.
     *
     *
     * @param string $fileSource Location of the source file
     * @param string $fileTarget Target path or file location
     * @param boolean $keepCopy Flag to keep copys if target exists
     * @param integer $tries Internal counter as suffix if keepcopy set to true
     *
     * @return string Returns the new/target filename
     *
     * @throws Mumsys_FileSystem_Exception Throws exception on error
     */
    public function copy( $fileSource, $fileTarget, $keepCopy = false,
        $tries = 0 )
    {
        try {
            if ( @is_dir( $fileSource ) ) {
                $msg = 'Source file: A directory was found. only file copying is implemented';
                throw new Mumsys_FileSystem_Exception( $msg );
            }
            if ( @is_dir( $fileTarget ) ) {
                $fileTarget = $fileTarget . DIRECTORY_SEPARATOR . basename( $fileSource );
            }
            if ( $keepCopy && file_exists( $fileTarget ) ) {
                $tries++;
                return $this->copy(
                    $fileSource, $fileTarget . '.' . $tries, $keepCopy,
                    $tries
                );
            } else {
                if ( @copy( $fileSource, $fileTarget ) ) {
                    return $fileTarget;
                } else {
                    $mesg = 'copy (to: ' . $fileTarget . ') fails';
                    throw new Mumsys_FileSystem_Exception( $mesg );
                }
            }
        }
        catch ( Exception $e ) {
            $mesg = 'Copy error for: "' . $fileSource . '" ' . $e->getMessage();
            throw new Mumsys_FileSystem_Exception( $mesg );
        }
    }


    /**
     * Rename a file or directory.
     *
     * @todo rename for dirs!
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
        $streamContext = null )
    {
        $rename = false;
        try {
            // test type of source and destionation?
            if ( !file_exists( $source ) || empty( $source ) ) {
                $message = 'Source "' . $source . '" is no directory and no file';
                throw new Mumsys_FileSystem_Exception(
                    $message, Mumsys_Exception::ERRCODE_DEFAULT
                );
            }
//    if ( is_dir($source . '/') ) {
//          if ($keepCopy && is_dir($destination)) {
//              /*
//              static $i = 0;
//              if (substr($destination, -1) == '/' || substr($destination, -1) == DIRECTORY_SEPARATOR) {
//                  $destination = substr($destination, 0, -1);
//              }
//              $r = $this->rename($source, $destination . '_' . ++$i, $stream_context, $keepCopy);
//              */
//          } else {
//              if ($stream_context) {
//                  $rename = rename($source, $destination, $stream_context);
//              } else {
//                  $rename = rename($source, $destination);
//              }
//              if ($rename) {
//                  $r = false;
//              } else {
//                  $this->error[] = 'Rename failt: src: "' . $source . '"; target: "' . $destination . '"';
//                  $r = true;
//              }
//          }
//      }
            if ( is_file( $source ) ) {
                if ( $keepCopy && file_exists( $destination ) ) {
                    $destination = $this->copy( $source, $destination, $keepCopy );
                }

                if ( $streamContext ) {
                    $rename = rename( $source, $destination, $streamContext );
                } else {
                    $rename = rename( $source, $destination );
                }

                // if false exception must be thrown, but if false do the right here
                if ( $rename ) {
                    $rename = $destination;
                }
            }
        }
        catch ( Exception $e ) {
            throw new Mumsys_FileSystem_Exception( 'Rename failt for reason: ' . $e->getMessage() );
        }

        return $rename;
    }


    /**
     * Creates a link (hard or soft) and returns the link location.
     *
     * @todo check how to remove realpath function or what is important in controller?
     *
     * @param string $file Absolut location to the file
     * @param string $to Absolut location of the link name
     * @param string $type Link type to be created "soft" for symlinks, "hard" for hardlinks
     * @param string $way Setter to create absolut or relative links (rel|abs)
     * @param boolean $keepCopy Flag to keep existing files or links
     *
     * @return string Returns the link name
     *
     * @throws Exception Throws exception on errors. Eg: if link type is invalid
     */
    public function link( $file, $to, $type = 'soft', $way = 'rel',
        $keepCopy = false )
    {
        try {
            $toExists = file_exists( $to );
            $toIsLink = is_link( $to );

            if ( $keepCopy && ( $toExists || $toIsLink ) ) {
                return $this->link( $file, $to . '.lnk', $type, $keepCopy );
            }

            if ( $way == 'rel' ) {
                $dirFrom = realpath( dirname( $file ) );
                $linkName = basename( $to );

                $dirTo = realpath( dirname( $to ) );
                if ( $dirTo === false ) {
                    $message = 'Real path not found for "' . dirname( $to ) . '"';
                    throw new Mumsys_FileSystem_Exception( $message );
                }
                chdir( $dirTo );

                // from and to in reverse as parameter ? why?
                if ( ( $relDir = $this->getRelativeDir( $dirTo, $dirFrom ) ) > '' ) {
                    $srcFile = $relDir . '/' . basename( $file );
                } else {
                    $srcFile = basename( $file );
                }
            } else {
                $way = 'abs';
                $linkName = $to;
                $srcFile = $file;
            }

            if ( $toIsLink ) {
                return $to;
            }

            switch ( $type )
            {
                case 'soft':
                    $res = symlink( $srcFile, $linkName );
                    break;
                case 'hard':
                    $res = link( $srcFile, $linkName );
                    break;
                default:
                    $msg = 'Invalid link type "' . $type . '" (Use soft|hard)';
                    throw new Mumsys_FileSystem_Exception( $msg );
            }
        }
        catch ( Exception $e ) {
            $msg = 'Linking failt for source: "' . $file . '"; target: "' . $to . '". ' . $e->getMessage();
            throw new Mumsys_FileSystem_Exception(
                $msg, $e->getCode(), $e->getPrevious()
            );
        }
        return $to;
    }


    /**
     * Removes/ unlinks a file.
     *
     * @param string $file Location to the file to be deleted
     *
     * @return bool TRUE on success.
     *
     * @throws Mumsys_FileSystem_Exception
     */
    public function unlink( $file )
    {
        if ( !is_file( $file ) && !is_link( $file ) ) {
            return true;
        }

        if ( unlink( $file ) === false ) {
            $message = sprintf( 'Can not delete file "%1$s"', $file );
            throw new Mumsys_FileSystem_Exception( $message );
        }

        return true;
    }


    /**
     * Alias methode for unlink().
     *
     * @param string $file Location to the file to be deleted
     *
     * @return boolean TRUE on success.
     *
     * @throws Mumsys_FileSystem_Exception
     */
    public function rmFile( $file )
    {
        return $this->unlink( $file );
    }


    /**
     * Creates a directory if not exists.
     *
     * @param string $dir Directory to be created
     * @param int $perm Octal permission mode of directory to be chmod eg.: 755
     *
     * @return boolean True on success or false if directory exists.
     * @throws Mumsys_FileSystem_Exception Throws exception on any other error
     */
    public function mkdir( $dir, $perm = 0755 )
    {
        if ( is_dir( $dir . DIRECTORY_SEPARATOR ) ) {
            return false;
        }

        if ( ( $result = mkdir( $dir, $perm ) ) === false ) {
            $mesg = 'Unknown';
            if ( ( $err = error_get_last() ) ) {
                $mesg = $err['message'];
            }
            $message = 'Can not create dir: "' . $dir . '" mode: "'
                . decoct( $perm ) . '". Message: ' . $mesg;
            throw new Mumsys_FileSystem_Exception( $message );
        }

        return $result;
    }


    /**
     * Create a given directory recursivly like "mkdir -p".
     *
     * @param string $dir Directory to be created
     * @param integer $perm Octal! permission mode of directory to be chmod eg.: 755
     *
     * @return boolean true on success or false on failure
     *
     * @throws Mumsys_FileSystem_Exception Throws exception if the real path
     * could not be determined or the creation of a directory fails
     */
    public function mkdirs( $dir, $perm = 0755 )
    {
        if ( is_dir( $dir ) ) {
            return true;
        }
        $stack = array(basename( $dir ));
        $path = null;
        while ( ( $d = dirname( $dir ) ) ) {
            if ( !is_dir( $d ) ) {
                $stack[] = basename( $d );
                $dir = $d;
            } else {
                $path = $d;
                break;
            }
        }

        /** @todo test this exception */
        if ( $path && ( $path = realpath( $path ) ) === false ) {
            $message = 'Can not determine realpath("' . $path . '".)';
            throw new Mumsys_FileSystem_Exception( $message );
        }

        $created = array();
        for ( $n = count( $stack ) - 1; $n >= 0; $n-- ) {
            $s = $path . '/' . $stack[$n];
            if ( !$this->mkdir( $s, $perm ) ) {
                for ( $m = count( $created ) - 1; $m >= 0; $m-- ) {
                    rmdir( $created[$m] );
                }

                return false;
            }
            $created[] = $s;
            $path = $s;
        }

        return true;
    }


    /**
     * Removes a path.
     *
     * @todo What about symlinks ?
     *
     * @param string $path Path to be deleted
     *
     * @return boolean TRUE on success.
     * @throws Mumsys_FileSystem_Exception
     */
    public function rmdir( $path )
    {
        if ( !is_dir( $path ) ) {
            return true;
        }

        if ( @rmdir( $path ) === false ) {
            $message = sprintf( 'Can not delete directory "%1$s"', $path );
            throw new Mumsys_FileSystem_Exception( $message );
        }

        return true;
    }


    /**
     * Removes a path recursivly.
     *
     * @param string $basePath Path to be deleted
     *
     * @throws Mumsys_FileSystem_Exception
     */
    public function rmdirs( $basePath )
    {
        $basePath = (string) $basePath;
        if ( !is_dir( $basePath ) ) {
            return true;
        }

        try
        {
            $iterator = new DirectoryIterator( $basePath );
            foreach ( $iterator as $fileinfo ) {
                if ( $fileinfo->isDot() ) {
                    continue;
                }

                if ( $fileinfo->isDir() ) {
                    if ( $this->rmdirs( $fileinfo->getPathname() ) ) {
                        $this->rmdir( $fileinfo->getPathname() );
                    }
                }

                if ( $fileinfo->isFile() ) {
                    $this->unlink( $fileinfo->getPathname() );
                }
            }

            $this->rmdir( $basePath );
        }
        catch ( Exception $e ) {
            throw $e;
        }

        return true;
    }


    /**
     * Returns the relative path for the target.
     * Note: This method can not decide between path and a file e.g: /myfile
     * It will be handled as directory /myfile/.
     *
     * @param string $from Path location from
     * @param string $to Path location to
     *
     * @return string Returns the relative path
     */
    public function getRelativeDir( $from, $to )
    {
        $sep = DIRECTORY_SEPARATOR;
        $search = $sep . $sep;

        $from = trim( str_replace( $search, $sep, $from ), '/' ) . '/';
        $from = explode( $sep, $from );

        $to = trim( str_replace( $search, $sep, $to ), '/' ) . '/';
        $to = $resultParts = explode( $sep, $to );

        $cntFrom = count( $from );

        foreach ( $from as $key => $pathPart ) {
            if ( isset( $to[$key] ) && $pathPart === $to[$key] ) {
                array_shift( $resultParts );
            } else {
                $rest = $cntFrom - $key;
                if ( $rest > 1 ) {
                    $padLength = ( count( $resultParts ) + $rest - 1 ) * -1;
                    $resultParts = array_pad( $resultParts, $padLength, '..' );
                    break;
                } else {
                    $resultParts[0] = '.' . $sep . $resultParts[0];
                }
            }
        }

        return implode( $sep, $resultParts );
    }


    /**
     * Returns the cool filesize. A formatted string like eg.: "124.89 KB"
     *
     * @param integer $size number of bytes
     * @param integer $digits number of fractional digits
     *
     * @return string The formated string of size
     */
    public static function coolfilesize( $size, $digits = 2 )
    {
        for ( $n = 0; $size >= 1024; $n++ ) {
            $size /= 1000;
        }

        switch ( $n )
        {
            case 0:
                $txt = 'Bytes';
                break;

            case ( $n === 1 ):
                $txt = 'KB';
                break;

            case ( $n === 2 ):
                $txt = 'MB';
                break;

            case ( $n === 3 ):
                $txt = 'GB';
                break;

            case ( $n === 4 ):
            default:
                $txt = 'TB';
        }

        return round( $size, $digits ) . ' ' . $txt;
    }

}
