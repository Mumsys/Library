<?php declare(strict_types=1);

/**
 * Mumsys_ShellTools_Adapter_ResizeImages
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2023 by Florian Blasel
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  ShellTools
 * Created: 2023-08-09
 */


/**
 * ResizeImages incl. keep ratio using 'convert' of imagemagick package
 */
class Mumsys_ShellTools_Adapter_ResizeImages
    extends Mumsys_ShellTools_Adapter_Abstract
{
    /**
     * Version ID information.
     */
    public const VERSION = '2.0.0';

    /**
     * Mixed cli tools to be required for this adapter and on the executing system/os.
     *
     * If you need several cli tools for different tasks/actions: split them to
     * simple tasks: Eg: action1 needs 'df' and action2 needs a special package:
     * then it is time to split things.
     *
     * @var array{cli: array{linux: array{'imagemagick:convert': array<string>}}}
     */
    private array $_requires = array(
        // [PHP_SAPI][strtolower( PHP_OS_FAMILY )]
        'cli' => array(
            'linux' => array(
                'imagemagick:convert' => array('convert' => '') // no global params
            ),
        ),
    );

    /**
     * Options (a Mumsys_Getopts config needs) for the actions this
     * class should share, handle and use.
     * E.g:
     * <code>
     * return array(
     *      'action1' => array(... Mumsys_GetOps option config for action1)
     *      'action2' => array(... getops option config for action2)
     *      'action3' => 'Description for action3 w/o parameters'
     * </code>
     * @var array<string, scalar|array<string|int, scalar>>
     */
    private array $_options = array(
        'Action "resizeimages"' => 'Resize images and keep ratio (dimensions) '
            . 'using imagemagick "convert" command.' . PHP_EOL
            . 'Suffix and size will be merged.' . PHP_EOL
            . 'Eg: suffix: _x, size: 1600 will create a file from source to '
            . '[filename]_x1600[.ext] => photo_x1600.jpg' . PHP_EOL
            . 'If (optional) --target (path!) given that path would be used to '
            . 'store resized images. Default: source path = target path.' . PHP_EOL
            . PHP_EOL
            . 'Currently limitations: Problems with network drives found '
            . 'combined with utf8 characters in the location string.'
            ,
        'resizeimages' => array(
            '--source:' => 'The directory or location to the file to use',
            '--size:' => 'Size in pixel. Default 1600',
            '--suffix:' => 'Default: "_x". Suffix for resized files. ',
            '--target:' => 'Optional; Target path to store resized images. By '
                . 'default it would use the path from --source',
            '--help|-h' => 'Show the help for this action',
        ),
    );

    /**
     * Default values to be used if options not given.
     *
     * Warning: Make sure they are valid or can be used without any problem or
     * leave out here! A structure like Mumsys_Getopts->getResults() to return
     *
     * @var array{
     *  resizeimages: array{size:string, suffix:string}
     * }
     */
    private array $_optionDefaults = array(
        'resizeimages' => array(
            'size' => '1600',
            'suffix' => '_x',
        )
    );

    /**
     * Results mem keeper from _validate().
     *
     * @var array{
     *  source:string, size:string, suffix:string, target:string
     * }|array<string>
     */
    private array $_results = array();

    /**
     * Flag if validation was successful.
     * @var bool
     */
    private bool $_isValid = false;

    /**
     * Initialise the adapter object.
     *
     * @param Mumsys_Logger_Interface $logger
     */
    public function __construct( Mumsys_Logger_Interface $logger )
    {
//clearstatcache();
//echo getcwd() .PHP_EOL;
//echo getcwd() .PHP_EOL;
//echo __METHOD__ . ':' . __LINE__ . ':$_SERVER: ' . PHP_EOL;
//print_r($_SERVER);
//exit(1);
        parent::__construct(
            $logger, $this->_requires, $this->_options, $this->_optionDefaults
        );

        $this->_logger->log( __METHOD__, 7 );
    }


    /**
     * Validates all results of a Mumsys_GetOps->getResult() return.
     *
     * @param array{resizeimages: array{
     *  source:string, size:string, suffix:string, target:string
     * }}|array<string> $input Results from a Mumsys_GetOpts->getResult() to check
     * to be valid as good as possible in this case
     *
     * @return bool|null Returns true on success or null for not relevant
     * @throws Mumsys_ShellTools_Adapter_Exception Throws last detected error
     */
    public function validate( array $input ): ?bool
    {
        $this->_logger->log( __METHOD__, 7 );

        $this->_results = array();

        $action = 'resizeimages';

        if ( !isset( $input[$action] ) || !is_array( $input[$action] ) ) {
            // not of this part
            return null;
        }

        // param: source: path | file
        $curKey = 'source';
        $test = $this->_checkVarExistsWithDefaultsLocationExists(
            'source', $input[$action], $action, $this->_optionDefaults
        );
        if ( $test !== null ) {
            $this->_results['source'] = $test;
        }

        // param: size
        $curKey = 'size';
        $test = $this->_checkVarExistsWithDefaults(
            'size', $input[$action], $action, $this->_optionDefaults
        );
        if ( $test !== null ) {
            $this->_results['size'] = $test;
        }

        // param: suffix
        $curKey = 'suffix';
        $test = $this->_checkVarExistsWithDefaults(
            'suffix', $input[$action], $action, $this->_optionDefaults
        );
        if ( $test !== null ) {
            $this->_results['suffix'] = $test;
        }

        // param: target (optional)
        $curKey = 'target';
        $test = $this->_checkVarExistsNoDefaultsNotRequired(
            'target', $input[$action], $action
        );
        if ( $test !== null ) {
            $this->_results['target'] = $test;
        }

        $this->_isValid = true;
        return true;
    }


    /**
     * Executes a command.
     *
     * Checks first if validation was made, prepares the command and executes it
     * if $realExecution is not false
     *
     * @param bool $realExecution Flag to disable real execution (false) true by default.
     *
     * @return bool True on success
     * @throws Exception|Mumsys_ShellTools_Adapter_Exception On errors
     */
    public function execute( bool $realExecution = true ): bool
    {
        $this->_logger->log( __METHOD__, 7 );

        if ( parent::_prepareExecution( $this->_isValid ) === false ) {
            return false;
        }

        try {
            $cmdList = $this->_prepareCommands();
            if ( $realExecution === true ) {
                foreach ( $cmdList as $cmd ) {
                    $results = $this->_execCommand( $cmd, false );
                    // do something with $results?
                }
            } else {
                $this->_logger->log( 'Test mode. No real execution', 6 );
            }

            return true;

        } catch ( Throwable $thex ) {
            $this->_logger->log( 'Error in execution. Check the logs!!!', 3 );
            throw $thex;
        }
    }


    /**
     * Returns prepared list of commands to be executed.
     *
     * @return array<string> List of commands (each per file if a directory was given)
     * @throws Mumsys_ShellTools_Adapter_Exception On errors
     */
    private function _prepareCommands(): array
    {
        // $this->_logger->log( __METHOD__, Mumsys_Logger_Abstract::DEBUG );

        $cmdList = array();

        // source validation was made in validate() in shelltools.php
        if ( is_dir( $this->_results['source'] . DIRECTORY_SEPARATOR ) ) {
            /** @var array<string> $fileList 4SCA */
            $fileList = (array) scandir( $this->_results['source'] );
            $workingDir = $this->_results['source'];

        } else {
            // expect file location because of prev. validiation test
            $fileList = array( basename( $this->_results['source'] ) );
            $workingDir = dirname( $this->_results['source'] );
        }

        if ( !isset( $this->_results['target'] ) ) {
            $this->_results['target'] = $workingDir;
        } else if ( !is_dir( $this->_results['target'] . DIRECTORY_SEPARATOR ) ) {
            $mesg = sprintf(
                'Target dir not exists "%1$s"', $this->_results['target']
            );
            throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
        }

        //

        $size = (int)$this->_results['size'];
        if ( $size <= 0 ) {
            $mesg = sprintf(
                'Invalid value for --size "%1$s" given. Not a number (0-9)',
                $this->_results['size']
            );
            throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
        }

        if ( isset( $this->_results['suffix'] ) ) {
            $regexTargetsuffix = '/^([0-9a-zA-Z_.-])+$/i';
            $this->_checkValueRegexMatchRequired(
                $this->_results['suffix'], $regexTargetsuffix,
                'suffix', 'a-Z _ - .'
            );
        } else {
            // @codeCoverageIgnoreStart
            // required or by default: this will never happen unitl default changes
            $mesg = 'Value for --suffix missing';
            throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
            // @codeCoverageIgnoreEnd
        }

        foreach ( $fileList as $filename ) {
            $currentFileLocation = $workingDir . DIRECTORY_SEPARATOR . $filename;
            // dots or not a file
            if ( $filename[0] === '.' || !is_file( $currentFileLocation ) ) {
                continue;
            }

            $srcFileName = Mumsys_FileSystem_Default::nameGet( $filename );
            $srcFileExt = Mumsys_FileSystem_Default::extGet( $filename );

            //
            // base cmd (cmd + global params)
            $binParts = $this->_getBinaryParts( 'imagemagick:convert' );
            // expect only one: foreach ( $binParts as $command => $globalParams ) {}
            $cmdBase = key( $binParts ) . ' ' . current( $binParts );
            //

            $target = sprintf(
                '%1$s%2$s%3$s%4$s%5$s%6$s',
                $this->_results['target'], DIRECTORY_SEPARATOR, $srcFileName,
                $this->_results['suffix'], $this->_results['size'],
                ( $srcFileExt ? ( '.' . $srcFileExt ) : '' )
            );

            //  convert "$src" -resize "$format" "$target/$src";

            $cmd = sprintf(
                '%1$s "%2$s" -resize "%3$s" "%4$s"',
                $cmdBase,
                $workingDir . '/' . $filename,
                $this->_results['size'],
                $target
            );
            $cmdList[] = $cmd;

            $this->_logger->log( 'Command for "' . $cmdBase . '"', 7 );
            $this->_logger->log( 'cmd build: ' . $cmd, 6 );
        } // end foreach ( $fileList as $filename ) {

        if ( $cmdList === array() ) {
            $mesg = sprintf(
                'No files found to handle in --source "%1$s"',
                $this->_results['source']
            );
            $this->_logger->log( $mesg, 7 );
            throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
        }

        return $cmdList;
    }

}
