<?php

/**
 * Mumsys_Config_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2009 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Config
 * @version     3.0.0
 */


/**
 * Mumsys config class
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Config
 */
interface Mumsys_Config_Interface
{
    /**
     * Returns all config parameters
     *
     * @return array All existing config parameters
     */
    public function getAll();


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
    public function addPath( $path );


    /**
     * Get config parameter/s by given path.
     *
     * If key can not be found the $default will return.
     *
     * @param string|array $key Path to the config to get config value/s from
     * e.g. frontend/pageTitle or array('frontend', 'pageTitle)
     * @param mixed|null $default Expectd value or the default value to return
     * if key does not exists
     *
     * @return mixed Value/s of the requested key or the $default will return.
     */
    public function get( $key, $default = null );


    /**
     * Register a configuration parameter if not exists.
     *
     * @param string $key Path including the key to be registered. E.g.: frontend/pageTitle
     * @param mixed $value Mixed value to be set.
     *
     * @throws Mumsys_Config_Exception If key exists
     */
    public function register( $key, $value = null );


    /**
     * Replace/ sets a configuration parameter.
     *
     * @param string $key Path to the value e.g: frontend/pageTitle
     * @param mixed $value Value to be set
     */
    public function replace( $key, $value = null );
}
