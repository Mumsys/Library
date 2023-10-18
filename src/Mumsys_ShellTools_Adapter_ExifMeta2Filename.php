<?php declare(strict_types=1);

/**
 * Mumsys_ShellTools_Adapter_ExifMeta2Filename
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2023 by Florian Blasel
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  ShellTools
 * Created: 2023-07-27
 */


/**
 * ExifMeta2Filename Exif metadata to filename.
 *
 * Where metadata is ment for the exif datetime values to create a datetime
 * filename to be more save handling files e.g. of photos.
 *
 * The filename format is currently fixed to e.g: 20231231_235859
 */
class Mumsys_ShellTools_Adapter_ExifMeta2Filename
    extends Mumsys_ShellTools_Adapter_Abstract
{
    /**
     * Version ID information.
     */
    public const VERSION = '1.0.0';

    /**
     * Mixed cli tools to be required for this adapter and on the executing system/os.
     *
     * If you need several cli tools for different tasks/actions: split them to
     * simple tasks: Eg: action1 needs 'df' and action2 needs a special package:
     * then it is time to split things.
     *
     * @var array{cli: array{linux: array{exiftool: array<string>}}}
     */
    private array $_requires = array(
        // [PHP_SAPI][strtolower( PHP_OS_FAMILY )]
        'cli' => array(
            'linux' => array(
                'exiftool' => array('exiftool' => '') // no global params
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
        'Action "exifmeta2filename"' => 'Takes one of the datetime tags in the '
            . 'exif metadata and renames the file to a fixed datetime string.' . PHP_EOL
            . 'The filename format is currently fixed to e.g: 20231231_235859.jpg ',

        'exifmeta2filename' => array(
            '--timestampFrom:' => 'Key of the timestamp from exif data. '
                . 'Default: DateTimeOriginal' . PHP_EOL
                . 'Possible values: DateTimeOriginal, CreateDate or ModifyDate',

            '--location:' => 'The path or location to the file to correct your file/s',

            '--run-compare' => 'Compare the file/s and the found datetime value '
                . 'by hand',

            '--run-meta2filename' => 'Execute the given command. Rename the '
                . 'files! Make backups first!',

            '--help|-h' => 'Show the help for this action',
        ),
    );

    /**
     * Default values to be used if options not given.
     *
     * Warning: Make sure they are valid or can be used without any problem or
     * leave out here! A structure like Mumsys_Getopts->getResults() to return
     *
     * sca gen: array<string, scalar|array<string|int, scalar>>
     *
     * @var array{exifmeta2filename: array{timestampFrom: string,location: string}}
     */
    private array $_optionDefaults = array(
        'exifmeta2filename' => array(
            'timestampFrom' => 'DateTimeOriginal',
            'location' => '/tmp/my/picture/s',
        )
    );

    /**
     * Results mem keeper from _validate().
     *
     * @var array{
     *  timestampFrom: string,
     *  location: string,
     *  run-compare: bool,
     *  run-meta2filename: bool,
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
        parent::__construct(
            $logger, $this->_requires, $this->_options, $this->_optionDefaults
        );
        $this->_logger->log( __METHOD__, 7 );
    }


    /**
     * Validates all results of a Mumsys_GetOps->getResult() return.
     *
     * @uses Mumsys_Logger_Interface Logger to log or output informations
     *
     * @param array{exifmeta2filename: string, array{timestampFrom: string}} $input Results
     * from a Mumsys_GetOpts->getResult() to check to be valid as good as
     * possible in this case (first step)
     *
     * @return bool|null Returns true on success or null for not relevant
     * @throws Mumsys_ShellTools_Adapter_Exception Throws last detected error
     */
    public function validate( array $input ): ?bool
    {
        $this->_logger->log( __METHOD__, 7 );

        $this->_results = array();

        $action = 'exifmeta2filename';

        if ( !isset( $input[$action] ) || !is_array( $input[$action] ) ) {
            // not of this part
            return null;
        }

        //
        // param: timestampFrom
        $allowListTimstampFrom = array('DateTimeOriginal', 'CreateDate', 'ModifyDate');
        $test = $this->_checkVarExistsWithDefaultsCheckAllowListMustHave(
            'timestampFrom', $input[$action], $action, $this->_optionDefaults,
            $allowListTimstampFrom
        );
        if ( $test !== null ) {
            $this->_results['timestampFrom'] = $test;
        }

        //
        // param: location: path | file
        $test = $this->_checkVarExistsWithDefaultsLocationExists(
            'location', $input[$action], $action, $this->_optionDefaults
        );
        if ( $test !== null ) {
            $this->_results['location'] = $test;
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
     * @throws Mumsys_ShellTools_Adapter_Exception On errors
     */
    public function execute( bool $realExecution = true ): bool
    {
        $this->_logger->log( __METHOD__, 7 );

        if ( parent::_prepareExecution( $this->_isValid ) === false ) {
            return false;
        }

        try {
            $cmd = $this->_prepareCommand();
            if ( $realExecution === true ) {
                $results = $this->_execCommand( $cmd, true );
                // do something with $results?
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
     * Prepare the command.
     *
     * Build, check subparts to be valid and create a command to be executed.
     *
     * @todo check if target file exists and add suffix (counter)
     *
     * @return string Command line sting to be executed
     * @throws Mumsys_ShellTools_Adapter_Exception On errors
     */
    private function _prepareCommand(): string
    {
        //  $this->_logger->log( __METHOD__, Mumsys_Logger_Abstract::DEBUG );

        //
        // base cmd (cmd + global params)
        $binParts = $this->_getBinaryParts( 'exiftool' );
        // expect only one: foreach ( $binParts as $command => $globalParams ) {}
        $cmdBase = key( $binParts ) . ' ' . current( $binParts );
        //

        // e.g: exiftool '-FileName<DateTimeOriginal' -d %Y%m%d_%H%M%S%%-c.%%e /tmp/my/pictures/

        $cmd = sprintf(
            '%1$s \'-FileName<%2$s\' -d %%Y%%m%%d_%%H%%M%%S%%%%-c.%%%%e %3$s',
            $cmdBase,
            $this->_results['timestampFrom'], //$timestampFrom,
            $this->_results['location'],
        );

        $this->_logger->log( 'Command for exiftool: ', 7 );
        $this->_logger->log( "\t" . $cmd, 6 );
        $this->_logger->log( 'Compare by hand first if you are not sure:', 6 );
        $this->_logger->log(
            "\t" . 'exiftool -d \'%Y%m%d_%H%M%S\' -DateTimeOriginal -S -s '
            . $this->_results['location'],
            6
        );
        $this->_logger->log(
            "\t" . 'This lists the date_time found including the file', 6
        );

        return $cmd;
    }

}
