<?php declare(strict_types=1);

/**
 * Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2023 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  ShellTools
 * Created: 2023-08-04
 */


/**
 * Cut/trim a video using ffmpeg.
 *
 * Helper to get parts of a video without using complex documentation from ffmpeg.
 *
 * Example: You made a video with you Smartphone. But the beginn and/or maybe
 * the end you want to cut for better presentation or for the next step editing
 * that video.
 *
 * E.g:
 *
 *  Cut from start to stop value: Video: 4.50min long
 *
 *    ffmpeg -i input.mp4 -ss 00:05:10 -to 00:10:00 -c:v copy -c:a copy output2.mp4
 *                                     ^^^ range  (start(-ss) until here = 4min50sec)
 *  Cut 10min from start on: Video: 10min long
 *
 *    ffmpeg -i input.mp4 -ss 00:05:20 -t 00:10:00 -c:v copy -c:a copy output1.mp4
 *                                     ^^ duration
 *  Cut the last 5min.
 *
 *    ffmpeg -sseof -00:10:00.000 -i input.mp4 -c copy output5.mp4
 *                  ^ negativ value!
 *           ^^^^^^ reverse
 *    ffmpeg -sseof -300 -i input.mp4 -c copy output4.mp4
 */
class Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo
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
     * @var array{cli: array{linux: array{ffmpeg: array<string>}}}
     */
    private array $_requires = array(
        // [PHP_SAPI][strtolower( PHP_OS_FAMILY )]
        'cli' => array(
            'linux' => array(
                'ffmpeg' => array('ffmpeg' => ' -y -hide_banner') // global params
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
        'Action "ffmpegcuttrimvideo"' => 'Cut/ trim a video. ' . PHP_EOL
            . 'In shot: A very very limited ffmpeg wrapper to cut/trim a video with '
            . 'short examples here.' . PHP_EOL
            . '-------------------------------------------------' . PHP_EOL
            . 'WARNING: Make backups of your files before use!!!' . PHP_EOL
            . '-------------------------------------------------' . PHP_EOL
            ,
        'ffmpegcuttrimvideo' => array(
            '--location:' => 'Location to the file to cut/trim.' . PHP_EOL
                . 'Or a directory to cut/trim all videos inside the directory '
                . 'with the same settings. But: Only good if you have e.g: avi, '
                . 'mpg, mp4... of the same video and want to cut them all the '
                . 'same way. ' . PHP_EOL
                . 'Warning: Qverwrites existing targets! Make Backups first!'
            ,
            '--targetsuffix:' => 'Default: "_cut"; Suffix filename of the '
                . 'cutted video. E.g: video.mp4 becomes video_cut.mp4. '
                . 'Warning: Qverwrites existing targets! Make Backups first!'
            ,
            '--timeStart:' => 'The time as start point ("HH:MM:SS.MILLISECONDS" '
                . 'or "HH:MM:SS" )' . PHP_EOL
                . 'Default: "00:00:00";  Optional: MILLISECONDS e.g: '
                . '"00:00:02.459" otherwise it will be ignored when using just '
                . 'HH:MM:SS',

            '--wayofcut:' => 'Way of the cut: ' . PHP_EOL
                . '- "range": Default value; Cut a video from timeStart to '
                . 'timeEnd value.' . PHP_EOL

                . '- "duration": Cut fixed n seconds begining from --timeStart '
                . 'value.' . PHP_EOL
                . "\t" . 'E.g: Cut 5 min. and begin at the first minute: ' . PHP_EOL
                . "\t" . './script --wayofcut=duration --timeStart="00:01:00" '
                . '--timeEnd="00:05:00"' . PHP_EOL
                . "\t" . 'A cut of minute 1-6 in result. A video of 5 minutes.'
                . PHP_EOL

                 . '- "reverse" : Cut n seconds from the end of the video. '
                . '--timeStart must be' . PHP_EOL
                . "\t" . 'a negativ value. E.g: Cut the last minute from a '
                . 'video: ' . PHP_EOL
                . "\t" . 'Use: ./thisscript --timeStart="-00:01:00" '
                . '--wayofcut=reverse --location...'
            ,
            '--timeEnd:' => 'The time for the cut end: "HH:MM:SS.MILLISECONDS" '
                . 'or "HH:MM:SS". ' . PHP_EOL
                . 'See --wayofcut if mandatory. Dont use for "reverse" wayofcut.',

            '--allowext' => 'File extensions to be allowed when using a '
                . 'directory for "--location".' . PHP_EOL
                . 'Default: mp4,mpg,avi,mpeg',
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
     * dev annotation hint: all of defaults for @ var annotation 4SCA
     *
     * @var array{
     *  ffmpegcuttrimvideo:array{
     *      location: string, targetsuffix:string, timeStart:string,
     *      wayofcut:string, allowext:string
     *  }}
     */
    private array $_optionDefaults = array(
        'ffmpegcuttrimvideo' => array(
            'location' => '/tmp/my/video/s',
            'targetsuffix' => 'cut',
            'timeStart' => '00:00:00',
            'wayofcut' => 'range',
            'allowext' => 'mp4,mpg,avi,mpeg'
        )
    );

    /**
     * Results mem keeper from _validate().
     *
     * dev annotation hint: all of options w/o actions for @ var annotation 4SCA
     *
     * @var array{
     *  location: string,
     *  targetsuffix: string,
     *  timeStart: string,
     *  wayofcut: string,
     *  timeEnd: string,
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
     * @param array{ffmpegcuttrimvideo: string, array{
     *  location:string, targetsuffix:string, timeStart:string,
     *  wayofcut:string, timeEnd:string, allowext:string
     * }} $input Results from a Mumsys_GetOpts->getResult() to check to be valid
     * as good as possible in this case (first step)
     *
     * @return bool|null Returns true on success or null for not relevant
     * @throws Mumsys_ShellTools_Adapter_Exception Throws last detected error
     */
    public function validate( array $input ): ?bool
    {
        $this->_logger->log( __METHOD__, 7 );

        $this->_results = array();

        $action = 'ffmpegcuttrimvideo';

        if ( !isset( $input[$action] ) || !is_array( $input[$action] ) ) {
            // not of this part
            return null;
        }

        //
        // // param: location: path | file
        $test = $this->_checkVarExistsWithDefaultsLocationExists(
            'location', $input[$action], $action, $this->_optionDefaults
        );
        if ( $test !== null ) {
            $this->_results['location'] = $test;
        }

        //
        // // param: targetsuffix: string
        $test = $this->_checkVarExistsWithDefaults(
            'targetsuffix', $input[$action], $action, $this->_optionDefaults
        );
        if ( $test !== null ) {
            $this->_results['targetsuffix'] = $test;
        }

        //
        // // param: timeStart: string
        $test = $this->_checkVarExistsWithDefaults(
            'timeStart', $input[$action], $action, $this->_optionDefaults
        );
        if ( $test !== null ) {
            $this->_results['timeStart'] = $test;
        }

        //
        // param: wayofcut
        $allowList = array('range', 'duration', 'reverse');
        $test = $this->_checkVarExistsWithDefaultsCheckAllowListMustHave(
            'wayofcut', $input[$action], $action, $this->_optionDefaults,
            $allowList
        );
        if ( $test !== null ) {
            $this->_results['wayofcut'] = $test;
        }

        //
        // // param: timeEnd: string
        $test = $this->_checkVarExistsNoDefaultsNotRequired(
            'timeEnd', $input[$action], $action
        );
        if ( $test !== null ) {
            $this->_results['timeEnd'] = $test;
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
            $cmdList = $this->_prepareCommands();
            if ( $realExecution === true ) {
                foreach ( $cmdList as $cmd ) {
                    $results = $this->_execCommand( $cmd, true );
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
     * @uses Mumsys_FileSystem_Default
     *
     * @return array<string> List of commands (each per file if a directory was given)
     * @throws Mumsys_ShellTools_Adapter_Exception On errors
     */
    private function _prepareCommands(): array
    {
        //  $this->_logger->log( __METHOD__, Mumsys_Logger_Abstract::DEBUG );

        $cmdList = array();
        try {
            // check value targetsuffix to alnum for a dir/file
            if ( isset( $this->_results['targetsuffix'] ) ) {
                $regexTargetsuffix = '/^([0-9a-zA-Z_.-])+$/i';
                $this->_checkValueRegexMatchRequired(
                    $this->_results['targetsuffix'], $regexTargetsuffix,
                    'targetsuffix', 'a-Z _ - .'
                );
            } else {
                // @codeCoverageIgnoreStart
                // required or by default: this will never happen unitl default changes
                $mesg = 'Value for --targetsuffix missing';
                throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
                // @codeCoverageIgnoreEnd
            }

            //
            // check value wayofcut to know which times we need
            $allowListWayofcut = array();
            if ( isset( $this->_results['wayofcut'] ) ) {
                $allowListWayofcut = array('range', 'duration', 'reverse');
                if ( ! $this->_checkValueInList( $this->_results['wayofcut'], $allowListWayofcut ) ) {
                    // @codeCoverageIgnoreStart
                    // this will never happen unitl an invalid default is set
                    $mesg = sprintf(
                        'Value for --wayofcut unknown: "%1$s"', $this->_results['wayofcut']
                    );
                    throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
                    // @codeCoverageIgnoreEnd
                }
            } else {
                // @codeCoverageIgnoreStart
                // required or by default: this will never happen unitl default changes
                $mesg = 'Value for --wayofcut missing';
                throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
                // @codeCoverageIgnoreEnd
            }

            // regex for timeStart, timeEnd
            $regexTimeCheck = '/(((-)*\d{2}:\d{2}:\d{2})+(\.\d{3})*)$/';

            // check value timeStart and is negativ if way=reverse
            if ( isset( $this->_results['timeStart'] ) ) {
                // time or int positiv or negativ?
                $regexTimeCheck = '/(((-)*\d{2}:\d{2}:\d{2})+(\.\d{3})*)$/';
                $this->_checkValueRegexMatchRequired(
                    $this->_results['timeStart'], $regexTimeCheck, 'timeStart',
                    'e.g: 01:23:45 or 01:23:45.975'
                );

                if ( $this->_results['wayofcut'] === 'reverse'
                    && $this->_results['timeStart'][0] !== '-' ) {

                    $mesg = sprintf(
                        'Value for --timeStart is not a negative value: "%1$s"',
                        $this->_results['timeStart']
                    );
                    throw new Mumsys_ShellTools_Adapter_Exception( $mesg );

                } else if ( $this->_results['wayofcut'] !== 'reverse'
                    && $this->_results['timeStart'][0] === '-' ) {

                    $mesg = sprintf(
                        'Value for --timeStart is a negative value: "%1$s"',
                        $this->_results['timeStart']
                    );
                    throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
                }
            } else {
                // @codeCoverageIgnoreStart
                // required or by default: this will never happen unitl default changes
                $mesg = 'Value for --timeStart missing';
                throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
                // @codeCoverageIgnoreEnd
            }

            //
            // check value timeEnd if way not reverse
            if ( !isset( $this->_results['timeEnd'] )
                && $this->_results['wayofcut'] === 'reverse' ) {

                // ok, not given, go ahead

            } else if ( isset( $this->_results['timeEnd'] )
                && $this->_results['wayofcut'] !== 'reverse' ) {

                // time or int positiv or negativ?
                $regexTimeCheck = '/(((-)*\d{2}:\d{2}:\d{2})+(\.\d{3})*)$/';
                $this->_checkValueRegexMatchRequired(
                    $this->_results['timeEnd'], $regexTimeCheck, 'timeEnd', 'e.g: 01:23:45 or 01:23:45.975'
                );
                // ok, valid, go ahead

            } else if ( isset( $this->_results['timeEnd'] )
                && $this->_results['wayofcut'] === 'reverse' ) {

                $mesg = '"timeEnd" is not allowed when using --wayofcut "reverse"';
                throw new Mumsys_ShellTools_Adapter_Exception( $mesg );

            } else {
                $mesg = 'Value for --timeEnd missing';
                throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
            }

            //
            // location validation was made in validate()
            if ( is_dir( $this->_results['location'] ) ) {
                /** @var array<string> $fileList 4SCA */
                $fileList = (array) scandir( $this->_results['location'] );
                $workingDir = $this->_results['location'];
            } else {
                // expect file location because of prev. validiation test
                $fileList = array( basename( $this->_results['location'] ) );
                $workingDir = dirname( $this->_results['location'] );
            }

            //
            // base cmd (cmd + global params)
            $binParts = $this->_getBinaryParts( 'ffmpeg' );
            // expect only one: foreach ( $binParts as $command => $globalParams ) {}
            // ffmpeg -y
            $cmdBase = key( $binParts ) . ' ' . current( $binParts );
            //

            foreach ( $fileList as $filename ) {
                // exclude checks
                if ( $filename[0] === '.' ) {
                    continue;
                }

                $srcFileName = Mumsys_FileSystem_Default::nameGet( $filename );
                $srcFileExt = Mumsys_FileSystem_Default::extGet( $filename );

                // check if extension match: mp4,avi,mpeg,mpg,mkv
                $allowListExtensions = array('mp4', 'mpg', 'avi', 'mpeg');
                if ( false === in_array( strtolower( $srcFileExt ), $allowListExtensions ) ) {
                    continue;
                }

                //
                // build cmd line

                $outputFile = sprintf(
                    '%1$s%2$s%3$s',
                    $srcFileName,
                    $this->_results['targetsuffix'],
                    ( $srcFileExt ? '.' . $srcFileExt : '' )
                );

                $cmd = 'test -d /tmp/';
                switch( $this->_results['wayofcut'] ) {
                    case 'range';
                        $cmd = sprintf(
                            '%1$s -i "%2$s" -ss %3$s -to %4$s -c copy "%5$s"',
                            $cmdBase,
                            $workingDir . '/' . $filename,
                            $this->_results['timeStart'],
                            $this->_results['timeEnd'],
                            $workingDir . '/' . $outputFile
                        );
                        break;

                    case 'duration';
                        $cmd = sprintf(
                            '%1$s -i "%2$s" -ss %3$s -t %4$s -c copy "%5$s"',
                            $cmdBase,
                            $workingDir . '/' . $filename,
                            $this->_results['timeStart'],
                            $this->_results['timeEnd'],
                            $workingDir . '/' . $outputFile
                        );
                        break;

                    case 'reverse';
                        $cmd = sprintf(
                            '%1$s -sseof %2$s -i "%3$s" -c copy "%4$s"',
                            $cmdBase,
                            $this->_results['timeStart'],
                            $workingDir . '/' . $filename,
                            $workingDir . '/' . $outputFile
                        );
                        break;
                }
                $cmdList[] = $cmd;

                // $this->_logger->log( 'Command for ffmpeg: ', 7 );
                $this->_logger->log( 'cmd build: ' . $cmd, 6 );

            } // end of fileList

        } catch ( Exception $ex ) {
            $this->_logger->log( $ex->getMessage(), 3 );
            throw $ex;
        }

        return $cmdList;
    }

}
