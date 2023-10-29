<?php

/**
 * Mumsys_Config_File
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2009 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Config
 */


/**
 * Mumsys config file driver
 *
 * Handling configurations based on configuration files.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Config
 */
class Mumsys_Config_File
    extends Mumsys_Abstract
    implements Mumsys_Config_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '3.0.0';

    /**
     * Configuration vars in an array container.
     * @var array
     */
    private $_configs = array();

    /**
     * List of file locations containing config parameters.
     * @var array
     */
    private $_paths = array();

    /**
     * Buffer of config file inclusions.
     * @var array
     */
    private $_incCache = array();


    /**
     * Initialize the config object.
     *
     * @param array $config Config parameters to be set
     * @param array $paths List of locations for config files
     */
    public function __construct( array $config = array(), array $paths = array() )
    {
        $this->_configs = $config;
        $this->_paths = $paths;
    }


    /**
     * Get config parameter/s by given path.
     *
     * The path can be a string like: frontend/plugins/jquery-ui or an array
     * defining the path: array('frontend','plugins', 'jquery-ui') (recommend way)
     *
     * @param string|array $key Path to the config to get config value/s from
     * e.g. frontend/pageTitle or array('frontend', 'pageTitle)
     * @param mixed|null $default Expected value or the default value to return
     * if key does not exists
     *
     * @return array|mixed|null Value/s of the requested key or the default will return.
     */
    public function get( $key, $default = null )
    {
        if ( is_array( $key ) ) {
            $parts = $key; // old getsubValues() feature
        } else {
            $parts = explode( '/', trim( $key, '/' ) );
        }

        if ( ( $value = $this->_get( $this->_configs, $parts ) ) !== null ) {
            return $value;
        }

        foreach ( $this->_paths as $path ) {
            $this->_configs = $this->_load( $this->_configs, $path, $parts );
        }

        if ( ( $value = $this->_get( $this->_configs, $parts ) ) !== null ) {
            return $value;
        }

        return $default;
    }


    /**
     * Get all config parameters
     *
     * @return array List of all config parameters
     */
    public function getAll()
    {
        return $this->_configs;
    }


    /**
     * Register a configuration parameter if not exists.
     *
     * @param string|null $key Path including the key to be registered. E.g.: frontend/pageTitle
     * @param mixed $value Mixed value to be set.
     *
     * @throws Mumsys_Config_Exception If key exists
     */
    public function register( $key, $value = null )
    {
        if ( $this->get( $key ) !== null ) {
            $mesg = sprintf( 'Config key "%1$s" already exists', $key );
            throw new Mumsys_Config_Exception( $mesg );
        }

        $this->replace( $key, $value );
    }


    /**
     * Replace/ sets a config parameter.
     *
     * @param string $key Path to the value e.g: frontend/pageTitle
     * @param mixed $value Value to be set
     */
    public function replace( $key, $value = null )
    {
        $parts = explode( '/', trim( $key, '/' ) );
        $this->_configs = $this->_replace( $this->_configs, $parts, $value );
    }


    /**
     * Adds a path to the config.
     *
     * On runtime you may set additional paths to the config object. Note that
     * the configs will be avalivable only since this time you add the path.
     *
     * @param string $path Additionl path where config files exists.
     *
     * @throws Mumsys_Config_Exception If path not exists
     */
    public function addPath( $path )
    {
        if ( !is_dir( $path . '/' ) ) {
            $message = sprintf( 'Path not found: "%1$s"', $path );
            throw new Mumsys_Config_Exception( $message );
        }

        $this->_paths[] = (string) $path;
    }


    /**
     * Returns a config value.
     *
     * @param array $config Config array
     * @param array $parts List of sub paths to look for
     *
     * @return mixed Config value/s or null if config does not exists
     */
    protected function _get( $config, $parts )
    {
        if ( ( $cur = array_shift( $parts ) ) !== null && isset( $config[$cur] ) ) {
            if ( count( $parts ) > 0 ) {
                return $this->_get( $config[$cur], $parts );
            }

            return $config[$cur];
        }

        return null;
    }


    /**
     * Load the config file if possible.
     *
     * @param array $config Current global config
     * @param string $curPath Path of the config file
     * @param array $parts List of config parts to look for
     *
     * @return array The merged confiiguration settings
     */
    protected function _load( array $config, $curPath, array $parts )
    {

        if ( ( $key = array_shift( $parts ) ) !== null ) {
            $newPath = $curPath . DIRECTORY_SEPARATOR . $key;

            if ( is_dir( $newPath ) ) {
                if ( !isset( $config[$key] ) ) {
                    $config[$key] = array();
                }

                $config[$key] = $this->_load( $config[$key], $newPath, $parts );
            }

            if ( file_exists( $newPath . '.php' ) ) {
                if ( !isset( $config[$key] ) ) {
                    $config[$key] = array();
                }

                $config[$key] = $this->_merge(
                    $config[$key], $this->_include( $newPath . '.php' )
                );
            }
        }

        return $config;
    }


    /**
     * Merges the configuration, existing keys will be untouched (FIFO).
     *
     * @param array $left Array to be merged
     * @param array $right Array to be merged
     */
    protected function _merge( array $left, array $right )
    {
        foreach ( $right as $key => $value ) {
            if ( isset( $left[$key] ) && is_array( $left[$key] ) && is_array( $value ) ) {
                $left[$key] = $this->_merge( $left[$key], $value );
            } else {
                $left[$key] = $value;
            }
        }

        return $left;
    }


    /**
     * Include a config file.
     *
     * @param string $file Location of the config file
     *
     * @return array Configs of the requested file
     * */
    protected function _include( $file )
    {
        if ( !isset( $this->_incCache[$file] ) ) {
            $this->_incCache[$file] = include $file;
        }

        return $this->_incCache[$file];
    }


    /**
     * Replaces/ sets a given config to the existing config.
     *
     * @todo array $path in func signature?
     *
     * @param array $config Configuration sub-part
     * @param array $path Parts of the path
     * @param array $value The value to be set
     */
    protected function _replace( $config, $path, $value )
    {
        if ( ( $current = array_shift( $path ) ) !== null ) {
            $_ccur = array();
            if ( isset( $config[$current] ) ) {
                $_ccur = $config[$current];
            }
            $config[$current] = $this->_replace( $_ccur, $path, $value );

            return $config;
        }

        return $value;
    }


    /**
     * Load configuration by a given application config key and merge existing
     * values if exists.
     *
     * @uses Mumsys_Db_Interface Database interface will be used
     *
     * @param string $appKey config-application key to load
     * @return array Returns all configuration parameters
     *
     * @throws Mumsys_Config_Exception If key already registered
     */
    public function load( $appKey = 'mumsys' )
    {
        throw new Mumsys_Config_Exception( 'Not implemented yet.' );
    }

}
