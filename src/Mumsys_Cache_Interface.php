<?php

/**
 * Mumsys_Cache_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2013 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Cache
 * @version     2.3.1
 * Created: 2013-12-10
 */
/* }}} */


/**
 * Caching interface to cache data for a while to a specific location.
 */
interface Mumsys_Cache_Interface
{
    /**
     * Initialize the cache object and sets group and id to store it.
     *
     * The cache filename will be created like: path/ + prefix + group + _ + id
     *
     * @param string $group Groupname
     * @param string $id Unique ID e.g. requested area + userid
     */
    public function __construct( string $group, string $id );


    /**
     * Cache the content.
     *
     * @param int $ttl Time to live in seconds
     * @param mixed $data Content to be cached
     */
    public function write( $ttl, $data ): void;


    /**
     * Returns the cached content.
     *
     * @return mixed Contens of the cache file
     */
    public function read();


    /**
     * Checks if an entry is cached.
     *
     * @return boolean True if cache exists or false
     */
    public function isCached(): bool;


    /**
     * Removes the cache file.
     *
     * @return boolean True on success, false if cache can not be deleted
     */
    public function removeCache(): bool;


    /**
     * Sets the filename prefix.
     *
     * @param string $prefix Filename prefix for the cache filename
     */
    public function setPrefix( string $prefix ): void;


    /**
     * Returns the filename prefix.
     *
     * @return string Prefix of the cache filename default: "cache_"
     */
    public function getPrefix(): string;


    /**
     * Sets the path for cache files.
     *
     * @param string $path The dir where to store the cache files
     */
    public function setPath( $path ): void;


    /**
     * Returns the path for cache files.
     *
     * @return string Path of the cache files
     */
    public function getPath(): string;


    /**
     * Sets caching mode to enabled or not.
     *
     * @param boolean|int $flag True|1 to enable the cache, false|0 to disable
     */
    public function setEnable( $flag );

}
