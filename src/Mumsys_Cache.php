<?php

/* {{{ */
/**
 * ----------------------------------------------------------------------------
 * Mumsys_Cache
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 * ----------------------------------------------------------------------------
 * @author Florian Blasel <flobee.code@gmail.com>
 * ----------------------------------------------------------------------------
 * @copyright Copyright (c) 2013 by Florian Blasel for FloWorks Company
 * ----------------------------------------------------------------------------
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * ----------------------------------------------------------------------------
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Cache
 * Created: 2013-12-10
 */
/* }}} */

/**
 * Class for standard file caching
 *
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Cache
 */
class Mumsys_Cache
    extends Mumsys_Abstract
{
    /**
     * Version ID information
     */
    const VERSION = '1.1.1';

    /**
     * Flag if caching is enabled or not
     * @var bool
     */
    protected $_enabled = true;

    /**
     * Path to store the cache file/s
     * @var string
     */
    protected $_path = '/tmp/';

    /**
     * Prefix for cache files
     * @var string
     */
    protected $_prefix = 'cache_';

    /**
     * Initialize the cache object and sets group and id to store it.
     *
     * The cache filename will be created like: path/ + prefix + group + _ + id
     *
     * @param string $group Groupname
     * @param string $id Unique ID e.g. requested area + userid
     */
    public function __construct($group, $id)
    {
        $this->_id = md5((string) $id);
        $this->_group = (string) $group;
    }

    /**
     * Cache the content.
     *
     * @param int $ttl Time to live in seconds
     * @param mixed $data Content to be cached. You may serialise it before.
     */
    public function write($ttl, $data)
    {
        $filename = $this->_getFilename();

        if ($fp = fopen($filename, 'wb')) {
            if (flock($fp, LOCK_EX)) {
                fwrite($fp, $data);
            }
            fclose($fp);

            // Set filemtime
            touch($filename, time() + (int) $ttl);
        }
    }

    /**
     * Returns the cached content.
     */
    public function read()
    {
        $filename = $this->_getFilename();

        return file_get_contents($filename);
    }

    /**
     * Checks if an entry is cached.
     *
     * @param string $group Groupname
     * @param string $id Unique ID
     *
     * @return boolean True if cache exists or false
     */
    public function isCached()
    {
        if ($this->_enabled) {
            $filename = $this->_getFilename();

            if (file_exists($filename) && filemtime($filename) > time()) {
                return true;
            }

            unlink($filename);
        }

        return false;
    }

    /**
     * Removes the specific cache file.
     *
     * @return boolean True on success
     */
    public function removeCache()
    {
        $filename = $this->_getFilename();

        if (file_exists($filename)) {
            @unlink($filename);
        }

        return true;
    }

    /**
     * Sets the filename prefix.
     *
     * @param string $prefix Filename Prefix to use
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = (string) $prefix;
    }

    /**
     * Returns the filename prefix.
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Sets the path for cache files.
     *
     * @param string $store The dir where to store the cache files
     */
    public function setPath($path)
    {
        $this->_path = rtrim((string) $path, '/') . '/';
    }

    /**
     * Returns the path for cache files.
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Sets caching mode to enabled or not.
     *
     * @param boolean $flag True to enable the cache, false to disable
     */
    public function setEnable($flag)
    {
        $this->_enabled = (bool) $flag;
    }

    /**
     * Builds a filename/path from group, id and path.
     */
    protected function _getFilename()
    {
        return $this->_path . $this->_prefix . $this->_group . '_' . $this->_id;
    }

}
