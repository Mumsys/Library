<?php

/**
 * Php_Globals
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Php
 * @subpackage  Globals
 */


/**
 * @deprecated since version 1.0.0 Use \Mumsys_Php_Globals
 *
 * Nice interface for php's GLOBAL VARIABLES.
 *
 * Wraper for $GLOBALS, $_SERVER, $_GET, $_POST, $_FILES, $_COOKIE, $_SESSION,
 * $_REQUEST and $_ENV and getenv().
 *
 * When ever using the server or env variables and your are bored about testing
 * if an array key exists and/or has a value you may find this class useful to
 * always have a default value if something NOT EXISTS. eg.: When switching to
 * shell, something is not available. This will solve some or more overhead
 * implementing things but brings more memory usage.
 * If you dont really need some of the methodes: don't use them! As long the
 * initialisation of the super globals is not needed you are in a good
 * performace way. With or without this class.
 *
 * @category    Mumsys
 * @package     Php
 * @subpackage  Globals
 */
class Php_Globals
    extends Mumsys_Php_Globals
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

}
