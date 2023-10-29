<?php

/**
 * Mumsys_Permissions_Shell
 * for MUMSYS (Multi User Management System)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Permissions
 */


/**
 * Class to deal with the permissions (acl) and custom setting per user
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Permissions
 */
class Mumsys_Permissions_Console
    extends Mumsys_Permissions_Abstract
{
    /**
     * Version ID information
     */
    const VERSION = '3.0.4';


    /**
     * Initialisation of the permissions object
     */
    public function __construct()
    {
    }


    /**
     * Set user configuration infomation for a current program/sub-programm
     *
     * @param array $values array mixd config values
     * @param string $program string modul/program name
     * @param string $controller Controller/ Sub- programm name
     */
    public function progCfgSet( $values, $program, $controller )
    {
        $mesg = ' Not implemented yet';
        throw new Mumsys_Permissions_Exception( __METHOD__ . $mesg );
    }


    /**
     * Get user configuration infomation for a current program/sub-programm
     *
     * @param string $program string modul/program name
     * @param string $controller Controller/ Sub- programm name
     * @param string $param Parameter to return in the user-program-config
     *
     * @return mixed the values which was set in progCfgSet()
     */
    public function progCfgGet( $program, $controller, $param = '' )
    {
        $mesg = ' Not implemented yet';
        throw new Mumsys_Permissions_Exception( __METHOD__ . $mesg );
    }


    /**
     * Delete a user configuration infomation of a current program/sub-programm
     *
     * @param string $key Key to remove from user-program-config
     * @param string $program string modul/program name
     * @param string $controller Controller/ Sub- programm name
     *
     * @return boolean True on success or false on error
     */
    public function progCfgRm( $key, $program, $controller )
    {
        $mesg = ' Not implemented yet';
        throw new Mumsys_Permissions_Exception( __METHOD__ . $mesg );
    }


    /**
     * Get name of given module and or Submodule name if $m is false the current
     * module name will be returned
     *
     * @param string|false $program string modul/program name
     * @param string|false $controller Controller/ Sub- programm name
     *
     * @return string the name of the program
     */
    public function progNameGet( $program = false, $controller = false )
    {
        $mesg = ' Not implemented yet';
        throw new Mumsys_Permissions_Exception( __METHOD__ . $mesg );
    }


    /**
     * Load defaults for the user session eg: acls, language, access, template,
     * module/program-info.
     * old function name: check_permission()
     */
    public function loadDefault()
    {
        $mesg = ' Not implemented yet';
        throw new Mumsys_Permissions_Exception( __METHOD__ . $mesg );
    }


    /**
     * Get name of the remote user
     *
     * @return string|boolean Returns the remote username or false by default
     */
    public function getRemoteUser()
    {
        $mesg = ' Not implemented yet';
        throw new Mumsys_Permissions_Exception( __METHOD__ . $mesg );
    }


    /**
     * Get the password of the current user (remote user).
     * Note: this depens on PHP_SAPI value. apache handle will return the
     * password, cli/cgi will return true.
     *
     * @return string|boolean Returns the password of the current user or true
     * for: authentication was set or false if authentification was not set.
     */
    public function getRemotePass()
    {
        $mesg = ' Not implemented yet';
        throw new Mumsys_Permissions_Exception( __METHOD__ . $mesg );
    }

    /**
     * Tests if the user has admin privileges.
     *
     * @todo superuser ?
     *
     * @return boolean Returns true on success or false on failure
     */
    public function isAdmin()
    {
        return true;
    }


    /**
     * Test if the user has moderator privileges.
     *
     * @return bool true on success or fals on failure
     */
    public function isModerator()
    {
        return true;
    }

    /**
     * Test if the user has super-moderator privileges.
     * Super moderators can control moderators.
     *
     * @return bool true on success or fals on failure
     */
    public function isSupermoderator()
    {
        return true;
    }


    /**
     * Test if the user has user privileges and not default user privileges.
     *
     * @return bool true on success or fals on failure
     */
    public function isUser()
    {
        return true;
    }


    /**
     * Check for access on user level.
     * old methode name has_permission().
     *
     * @todo _isUser() check is min access is userr ? or session good ennugh?
     *
     * @param string|false $program string modul/program name
     * @param string|false $controller Controller/ Sub- programm name
     * @param string|false $action Action to check access for
     *
     * @return boolean Returns true on access or false for no access
     */
    public function hasAccess( $program = false, $controller = false,
        $action = false )
    {
        return true;
    }


    /**
     * get accesslevels based on a setted level or by its own usergrouplevel
     * old: get_auth_levels()
     *
     * @param integer $level optional the highes level to find lower levels (0-5)
     *
     * @return array returns a list of levels: integer level => string level-name
     */
    public function getAuthLevels( $level = null )
    {
        $mesg = ' Not implemented yet';
        throw new Mumsys_Permissions_Exception( __METHOD__ . $mesg );
    }

    /**
     * Get label/ name of a level.
     * @param integer $level Level to get the label/name from
     * @return string Return the label/name for the requested level
     */
    public function getAuthName( $level )
    {
        $mesg = ' Not implemented yet';
        throw new Mumsys_Permissions_Exception( __METHOD__ . $mesg );
    }


    /**
     * Logout the logged in user.
     *
     * This will turncate the session and loads the default session.
     *
     * @return boolean true on success or false if something fails
     */
    public function logout()
    {
        return true;
    }


    /**
     * Login a user.
     *
     * @param string $username Name of the login user
     * @param string $password Password of the login user
     *
     * @return boolean Returns true on success or fals if authentication fails
     */
    public function login( $username = '', $password = '' )
    {
        return true;
    }


    /**
     * get/set language (get_language)
     * load basic language and program language
     *
     * @param string|false $language Languge to load otherwise the default
     * language will be loaded
     *
     * @return boolean true on success of false on failure
     * @throws Mumsys_Permissions_Exception Throws exception on errors
     */
    public function languageLoad( $language = false )
    {
        return true;
    }

}
