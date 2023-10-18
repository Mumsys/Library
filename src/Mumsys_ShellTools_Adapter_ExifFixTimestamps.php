<?php declare(strict_types=1);

/**
 * Mumsys_ShellTools_Adapter_ExifFixTimestamps
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
 * ExifFixTimestamps fixes/corrects timestamps/ datetime values in the metadata
 * of a media file (mostly photos) by given parameters.
 *
 * Fixable values are DateTimeOriginal, CreateDate, ModifyDate, AllDates.
 *
 * In detail:
 *
 * Calculates time differences to fix photos later using `exiftool`.
 *
 * **Situation:** You forgot to set the time of your digital camara because the
 * batteries were empty. New batteries are now in and you started makeing photos.
 *
 * Later you see the dates of your photos are too old. Maybe in the filename and
 * probably in the exif/ metadata. If so, here you get help.
 *
 * **Solution**: Make a photo now from a computer where the date/time (including
 * seconds) is displayed. Make sure the time is correct.
 *
 * Take the photo an look for the DateTime (e.g. DateTimeOriginal or the time of
 * photo on the SD-Card) the file was created/ made.
 *
 * Write it down to `--datetimeValueOld`
 * (Note: The format may be a bit different. By default somting like this is
 * expected: '2023-12-31 23:58:59'.
 *
 * Now write down the datetime of the photo/ picture you made from the computer
 * to the --datetimeValueNew in the same format.
 *
 * This program calculates the difference and shows you/ or execute the command
 * you need for a fix.
 *
 * E.g.
 *  php [scriptname].php exiffixtimestamps --datetimeValueOld="2009-01-31 00:42:54" \
 *      --datetimeValueNew="2023-07-17 22:56:08" #(... futher options)
 */
class Mumsys_ShellTools_Adapter_ExifFixTimestamps
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
        'Action "exiffixtimestamps"' => 'Can fix the datetime value in the exif '
            . 'metadata if you have two '
            . 'values. The old date (inside a photo) and the datetime this photo '
            . 'was really made. Check the README for details.',

        'exiffixtimestamps' => array(
            '--location:' => 'The path or location to the file to correct your photo/s',

            '--datetimeValueOld:' => 'A value of Y-m-d H:i:s (e.g: \'2009-01-31 '
                . '00:42:54\') is reqired by default',

            '--datetimeValueNew:' => 'A value of Y-m-d H:i:s (e.g: \'2023-07-17 '
                . '22:56:08\') is reqired by default',

            '--fix:' => 'Default: AllDates; Possible values: AllDates, '
                . 'DateTimeOriginal, CreateDate or ModifyDate',

            '--datetimeFormatOld:' => 'Datetime format of the --datetimeValueOld '
                . 'to be used. Default: \'Y-m-d H:i:s\'',

            '--datetimeFormatNew:' => 'Datetime format of the --datetimeValueNew '
            . 'to be used. Default: \'Y-m-d H:i:s\'',

            'Hint for \'DateTime\' formats' =>
                'See: https://www.php.net/manual/en/datetime.format.php',

            '--help|-h' => 'Show the help for this action',
        ),
    );

    /**
     * Default values to be used if options not given.
     *
     * Warning: Make sure they are valid or can be used without any problem or
     * leave out here! A structure like Mumsys_Getopts->getResults() to return
     * @var array{exiffixtimestamps: array{
     *  location: string, datetimeValueOld: string, datetimeValueNew: string,
     *  fix: string, datetimeFormatOld: string, datetimeFormatNew: string
     * }}
     */
    private array $_optionDefaults = array(
        'exiffixtimestamps' => array(
            'location' => '/tmp/my/picture/s',
            'datetimeValueOld' => '2009-01-31 00:42:54',
            'datetimeValueNew' => '2023-07-17 22:56:08',
            'fix' => 'AllDates',
            'datetimeFormatOld' => 'Y-m-d H:i:s',
            'datetimeFormatNew' => 'Y-m-d H:i:s',
        )
    );

    /**
     * Results mem keeper from _validate().
     *
     * @var array{
     *  location: string, datetimeValueOld: string, datetimeValueNew: string,
     *  fix: string, datetimeFormatOld: string, datetimeFormatNew: string
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
     * @param array{exiffixtimestamps: array{
     *  location: string, datetimeValueOld: string, datetimeValueNew: string,
     *  fix:string,datetimeFormatOld:string,datetimeFormatNew:string}} $input Results
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

        $action = 'exiffixtimestamps';

        if ( !isset( $input[$action] ) || !is_array( $input[$action] ) ) {
            // not of this part
            return null;
        }

        // param: location: path | file
        $test = $this->_checkVarExistsWithDefaultsLocationExists(
            'location', $input[$action], $action, $this->_optionDefaults
        );
        if ( $test !== null ) {
            $this->_results['location'] = $test;
        }

        // param: datetimeValueOld
        $test = $this->_checkVarExistsWithDefaults(
            'datetimeValueOld', $input[$action], $action, $this->_optionDefaults
        );
        if ( $test !== null ) {
            $this->_results['datetimeValueOld'] = $test;
        }

        // param: datetimeValueNew
        $test = $this->_checkVarExistsWithDefaults(
            'datetimeValueNew', $input[$action], $action, $this->_optionDefaults
        );
        if ( $test !== null ) {
            $this->_results['datetimeValueNew'] = $test;
        }

        // param: fix
        $allowListFix = array('AllDates', 'DateTimeOriginal', 'CreateDate', 'ModifyDate');
        $test = $this->_checkVarExistsWithDefaultsCheckAllowListMustHave(
            'fix', $input[$action], $action, $this->_optionDefaults, $allowListFix
        );
        if ( $test !== null ) {
            $this->_results['fix'] = $test;
        }

        // param: datetimeFormatOld
        $test = $this->_checkVarExistsWithDefaults(
            'datetimeFormatOld', $input[$action], $action, $this->_optionDefaults
        );
        if ( $test !== null ) {
            $this->_results['datetimeFormatOld'] = $test;
        }

        // param: datetimeFormatNew
        $test = $this->_checkVarExistsWithDefaults(
            'datetimeFormatNew', $input[$action], $action, $this->_optionDefaults
        );
        if ( $test !== null ) {
            $this->_results['datetimeFormatNew'] = $test;
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
            $this->_logger->log( 'Error in execution. Check the log!!!', 3 );
            throw $thex;
        }
    }


    /**
     * Prepare the command.
     *
     * Build, check subparts to be valid and create a command to be executed.
     *
     * @return string Command line sting to be executed
     * @throws Mumsys_ShellTools_Adapter_Exception On errors
     */
    private function _prepareCommand(): string
    {
//        $this->_logger->log( __METHOD__, 7 );

        // calculate, create cmd line/s
        $oDateTimeNew = DateTimeImmutable::createFromFormat(
            $this->_results['datetimeFormatNew'], $this->_results['datetimeValueNew']
        );
        if ( $oDateTimeNew === false ) {
            $mesg = sprintf(
                'Error in --datetimeValueNew: "%1$s" or --datetimeFormatNew: "%2$s"',
                $this->_results['datetimeValueNew'],
                $this->_results['datetimeFormatNew']
            );
            $this->_logger->log( $mesg, 3 );
            $this->_logger->log( (array)DateTimeImmutable::getLastErrors(), 3 );

            throw new Mumsys_ShellTools_Adapter_Exception(
                'Error in DateTimeImmutable: datetimeValueNew,datetimeFormatNew'
            );
        }

        $oDateTimeOld = DateTimeImmutable::createFromFormat(
            $this->_results['datetimeFormatOld'], $this->_results['datetimeValueOld']
        );
        if ( $oDateTimeOld === false ) {
            $mesg = sprintf(
                'Error in --datetimeValueOld: "%1$s" or --datetimeFormatOld: "%2$s"',
                $this->_results['datetimeValueOld'],
                $this->_results['datetimeFormatOld']
            );
            $this->_logger->log( $mesg, Mumsys_Logger_Abstract::ERR );
            $this->_logger->log( (array)DateTimeImmutable::getLastErrors(), 3 );

            throw new Mumsys_ShellTools_Adapter_Exception(
                'Error in DateTimeImmutable: datetimeValueOld,datetimeFormatOld'
            );
        }

        // direction + or -
        if ( $oDateTimeNew->getTimestamp() >= $oDateTimeOld->getTimestamp() ) {
            $shiftPrefix = '+';
        } else {
            $shiftPrefix = '-';
        }
        $interval = $oDateTimeNew->diff( $oDateTimeOld );
        $elapsed = $interval->format(
            '%y years %m months %d days (%a total days) %h hours %i minutes %s seconds'
        );
        /** @var int $year 4SCA */
        $year = (int) $interval->format( '%y' );
        $month = (int) $interval->format( '%m' );
        $day = (int) $interval->format( '%d' );
        $hour = (int) $interval->format( '%h' );
        $min = (int) $interval->format( '%i' );
        $sec = (int) $interval->format( '%s' );

        $this->_logger->log(
            'Diff, elapsed time (direction: ' . $shiftPrefix . '): ' . $elapsed, 6
        );
        if ( $shiftPrefix === '-' ) {
            $this->_logger->log( '("-" = newer date is older than given old datetime)', 6 );
        } else {
             $this->_logger->log( '("+" = older date is older than given new datetime)', 6 );
        }

        //
        // base cmd (cmd + global params)
        $binParts = $this->_getBinaryParts( 'exiftool' );
        // expect only one: foreach ( $binParts as $command => $globalParams ) {}
        $cmdBase = key( $binParts ) . ' ' . current( $binParts );
        //

        // exiftool "-AllDates+=14:5:16 22:13:14" /path
        $cmdMetaDatimeFix = sprintf(
            '%1$s \'-%2$s%3$s=%4$s:%5$s:%6$s %7$s:%8$s:%9$s\' "%10$s"',
            $cmdBase,
            $this->_results['fix'], $shiftPrefix, $year, $month, $day, $hour,
            $min, $sec, $this->_results['location']
        );

        $this->_logger->log( 'Command for exiftool: ', 7 );
        $this->_logger->log( "\t" . $cmdMetaDatimeFix, 6 );

        return $cmdMetaDatimeFix;
    }

}
