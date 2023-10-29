<?php // declare iface

/**
 * Mumsys_ShellTools_Adapter_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2023 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  ShellTools
 * @version 1.0.0
 * Created: 2023-07-27
 */


/**
 * ShellTools adapter interface for adding adapter/ implementations of a task
 * you want to manage/ handled with other implementations.
 *
 * The sum of each adapter can grow to a huge application.
 *
 * All is based on Mumsys_GetOpts to combine cli/shell input and execute jobs an
 * adapter/ implementation can offer.
 * Mostly a wrapper around shell tool inside your linux OS with limited options
 * but a helper for concrete common tasks.
 */
interface Mumsys_ShellTools_Adapter_Interface
{
    /**
     * Retruns the _requires config.
     *
     * Commands must be in $PATH.
     *
     * E.g:
     * $_requires = array(
     *  array( PHP_SAPI => array( strtolower(PHP_OS_FAMILY)] => array(
     *      'command alias' => array( cmd => global cmd params)
     *  'cli' => array(
     *      'linux' => array(
     *         'test' => array('test' => ' -h'), //
     *                                     ^^ global option -h
     *                          ^^^^ command (may be OS dependend)
     *          ^^^^ command alias
     *      ),
     *      // demo: cross OS not implemented yet
     *      //'windows' => array(
     *      //    'test' => array('test.exe' => ' -h'), ...
     *
     * @return array<string, array<string, array<string, array<string>>>> List of
     * key/value pairs of the _requires config
     */
    public function getRequirementConfig(): array;

    /**
     * Sets the requirement config.
     *
     * [PHP_SAPI][strtolower( PHP_OS_FAMILY )][ programID/alias ] [ key => mixed ] pairs
     *
     * programID/alias: eg. for /path/xy and c:\path\xy.exe. @see getRequirementConfig()
     *
     * @param array<string, array<string, array<string, array<string>>>> $config List of
     * key/value pairs of the _requires config
     */
    public function setRequirementConfig( array $config = array() ): void;

    /**
     * Returns the config (a Mumsys_Getopts config needs) for the actions this
     * program should share to be used.
     *
     * E.g:
     * <code>
     * return array(
     *      'action1' => array(... Mumsys_GetOps option config for action1)
     *      'action2' => array(... getops option config for action2)
     *      'action3' => 'Description for action3 w/o parameters'
     * </code>
     *
     * @return array<string, scalar|array<string|int, scalar>> Cli options
     */
    public function getCliOptions(): array;

    /**
     * Returns option default values to be available to outer world.
     *
     * Default options can be used to pipe that values to the adapters validation
     * to be used if no other value is given e.g. by shell command to limit parameters.
     *
     * @return array<string, scalar|array<string|int, scalar>> Optional option defaults
     */
    public function getCliOptionsDefaults(): array;

    /**
     * Validates results of a Mumsys_GetOps->getResult() return.
     *
     * Checks only the incomming params (if given) to let an adapter work.
     * Not the values and if they are correct. This should be done inside
     * _prepareCommnd/s(). There are still ideas to extend the parser of GetOpts
     * to automate the validation process for incomming params.
     *
     * @param array<string, scalar|array<string, scalar>> $input Results
     * from a Mumsys_GetOpts->getResult() to check to be valid as good as
     * possible in this case (first step)
     *
     * @return bool|null Returns true on success or null for not relevant here
     * @throws Mumsys_ShellTools_Adapter_Exception Throws last detected error
     */
    public function validate( array $input ): ?bool;

    /**
     * Executes a command.
     *
     * @param bool $realExecution Flag to disable real execution (false) true
     * by default.
     *
     * @return bool True on success
     * @throws Mumsys_ShellTools_Adapter_Exception On errors
     */
    public function execute( bool $realExecution = true ): bool;

}
