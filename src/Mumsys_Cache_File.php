<?php declare(strict_types=1);

/**
 * Mumsys_Cache_File
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2013 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Cache
 * Created: 2013-12-10
 */


/**
 * Class for file caching
 *
 * Example:
 * <code>
 * $cache = new Mumsys_Cache_File('group-ID', 'individual-ID');
 * $cache->setPath(__DIR__ . '/cache/');
 * if ($cache->isCached()) {
 *      // get the cached data
 *      $data = unserialize( $oCache->read() );
 * } else {
 *      $data = array('a' => 1, 'b' => 2, 'c' => 3);
 *      $cache->write( 3600, serialize($data) );
 * }
 * </code>
 */
class Mumsys_Cache_File
    extends Mumsys_Abstract
{
    /**
     * Version ID information
     */
    public const VERSION = '2.3.2';

    /**
     * Flag if caching is enabled or not
     * @var bool
     */
    private $_enabled = true;

    /**
     * Path to store the cache file/s
     * @var string
     */
    private $_path = '/tmp/';

    /**
     * Prefix for cache files
     * @var string
     */
    private $_prefix = 'cache_';

    /**
     * Individual group name (a-z) to define a cache file to find on the
     * filesystem more easier if needed to look for manually..
     * @var string
     */
    private $_group;

    /**
     * Individual ID to define a cache file which will be md5 encrypted.
     * @var string
     */
    private $_id;


    /**
     * Initialize the cache object and sets group and id to build the cache
     * filename.
     *
     * The cache filename will be created like: path/ + prefix + group + _ + id
     *
     * @param string $group Groupname
     * @param string $id Unique ID e.g. requested area + userid
     */
    public function __construct( string $group, string $id )
    {
        $this->_id = md5( $id );
        $this->_group = $group;
    }


    /**
     * Writes the content to the cache file in exclusive + binay mode.
     *
     * @param int $ttl Time to live in seconds
     * @param mixed $content Content to be cached
     */
    public function write( $ttl, $content ): void
    {
        $filename = $this->getFilename();

        if ( $fp = fopen( $filename, 'wb' ) ) {
            if ( flock( $fp, LOCK_EX ) ) {
                $data = serialize( $content );
                fwrite( $fp, $data );
            }
            fclose( $fp );

            // Set filemtime
            touch( $filename, time() + (int) $ttl );
        }
    }


    /**
     * Returns the cached content.
     *
     * @return mixed Contens of the cache file
     */
    public function read()
    {
        $filename = $this->getFilename();
        if ( ( $data = file_get_contents( $filename ) ) === false ) {
            throw new Mumsys_Cache_Exception( 'Can not read cache. File not found' );
        }

        return unserialize( $data );
    }


    /**
     * Checks if an entry is cached.
     *
     * @return boolean True if cache exists or false
     */
    public function isCached(): bool
    {
        if ( $this->_enabled ) {
            $filename = $this->getFilename();

            if ( ( $exists = file_exists( $filename ) ) && filemtime( $filename ) > time() ) {
                return true;
            }

            if ( $exists ) {
                unlink( $filename );
            }
        }

        return false;
    }


    /**
     * Removes the cache file.
     *
     * @return boolean True on success, false if cache can not be deleted
     */
    public function removeCache(): bool
    {
        $filename = $this->getFilename();
        if ( file_exists( $filename ) === false ) {
            return true;
        }

        return unlink( $filename );
    }


    /**
     * Sets the filename prefix.
     *
     * @param string $prefix Filename prefix for the cache filename
     */
    public function setPrefix( string $prefix ): void
    {
        $this->_prefix = $prefix;
    }


    /**
     * Returns the filename prefix.
     *
     * @return string Prefix of the cache filename default: "cache_"
     */
    public function getPrefix(): string
    {
        return $this->_prefix;
    }


    /**
     * Sets the path for cache files.
     *
     * @param string $path The dir where to store the cache files
     */
    public function setPath( $path ): void
    {
        $this->_path = rtrim( (string) $path, '/' ) . '/';
    }


    /**
     * Returns the path for cache files.
     *
     * @return string Path of the cache files
     */
    public function getPath(): string
    {
        return $this->_path;
    }


    /**
     * Sets caching mode to enabled or not.
     *
     * If set to false it forces the isCached() method to return false.
     *
     * @param boolean|int $flag True|1 to enable the cache, false|0 to disable
     */
    public function setEnable( $flag )
    {
        $this->_enabled = (bool) $flag;
    }


    /**
     * Builds a filename/path from group, id and path.
     *
     * @return string File location of the cache file
     */
    public function getFilename()
    {
        return $this->_path . $this->_prefix . $this->_group . '_' . $this->_id;
    }

}
