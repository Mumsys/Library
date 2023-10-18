<?php declare(strict_types=1);

/**
 * Mumsys_ShellTools_Adapter_ExifFilename2Meta
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
 * ExifFilename2Meta Set a datetime based filename to Exif metadata (if
 * different of not exists).
 */
class Mumsys_ShellTools_Adapter_ExifFilename2Meta
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
        'Action "exiffilename2meta"' => 'Takes datetime values of a filename and set it to'
            . ' the exif metadata of images or other supported files ("man 1 exiftool"). '
            . 'The format of the filename can be set to take the file, grap the datetime '
            . 'values and sets them to the exif metadata. A lot of old digital cameras '
            . 'don\'t have exif metadata but store the datetime as the filename it was created. '
            . 'Why doing it?: If you rename the file you can still get datetime infomations.' . PHP_EOL
            . 'Example: ' . PHP_EOL
            . 'A: format  : \'%pr%Y-%m-%d_%T%rp\' ' . PHP_EOL
            . 'A: filename: \'IMG-2023-07-31_192919_gopro.jpg\'' . PHP_EOL
            . 'B: format  : \'%pr%D_%T%rp\' ' . PHP_EOL
            . 'B: filename: \'IMG-20230802_192919_gopro.jpg\'' . PHP_EOL
            . 'Check the README for details.'
            ,
        'exiffilename2meta' => array(
            '--location:' => 'The directory or location to the file to use',
            '--locationFormat:' => 'Format of the location. ' . PHP_EOL
                . 'If a directory is given: ALL FILES MUST BE of the same format!!!' . PHP_EOL
                . 'Example for a file: "2023-07-31_192919_gopro.jpg"' . PHP_EOL
                . 'The format is "%Y-%m-%d_%T%rp"' . PHP_EOL
                . 'Format aliases available:' . PHP_EOL
                . '    %D = for date e.g: 20230216' . PHP_EOL
                . '    %T = for time in 24h format! e.g: 235859' . PHP_EOL
                . '    %Y = for year e.g: 2023' . PHP_EOL
                . '    %m = for month e.g: 12 (December)' . PHP_EOL
                . '    %d = for day e.g: 01 (January)' . PHP_EOL
                . '    %H = for hour (24h) e.g: 23' . PHP_EOL
                . '    %i = for minute e.g: 02' . PHP_EOL
                . '    %s = for seconds e.g: 01' . PHP_EOL
                . '    %pr = for prefix e.g: \'IMG-\' mixed chars' . PHP_EOL
                . '    %rp = for suffix e.g: \'_gopro.jpg\' mixed chars' . PHP_EOL
                . '    Hint: %pr = prefix, reverse %pr = %rp'
                ,
            '--set' => 'Default "AllDates". Key of the metadata to set: Possible values: '
                . 'AllDates, DateTimeOriginal, CreateDate or ModifyDate',
            '--locationFilter:' => 'Allow only this extension/s. E.g: "jpg". Default: "jpg,png",'
                . ' All supported extensions by exiftool: "*"',
//            . 'Default: DateTimeOriginal; Possible values: DateTimeOriginal, CreateDate or ModifyDate',
//            '--run-compare' => 'Compare the file/s and the found datetime value by hand',
//            '--run-filename2meta' => 'Execute the given command. Rename the files! Make backups first!',

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
     *  exiffilename2meta: array{location:string, set:string, locationFilter:string}
     * }
     */
    private array $_optionDefaults = array(
        'exiffilename2meta' => array(
            'location' => '/tmp/my/picture/s',
            'set' => 'AllDates',
            // list of extension to allow, comma seperated
            'locationFilter' => 'jpg,png',
        )
    );

    /**
     * Results mem keeper from _validate().
     *
     * @var array{
     *  location:string, locationFormat:string, set:string, locationFilter:string
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
     * @todo more --set, eg: --set=DateTimeOriginal,CreateDate,ModifyDate
     *
     * @param array{exiffilename2meta: array{
     *  location:string, locationFormat:string, set:string, locationFilter:string
     * }} $input Results from a Mumsys_GetOpts->getResult() to check
     * to be valid as good as possible in this case
     *
     * @return bool|null Returns true on success or null for not relevant
     * @throws Mumsys_ShellTools_Adapter_Exception Throws last detected error
     */
    public function validate( array $input ): ?bool
    {
        $this->_logger->log( __METHOD__, 7 );

        $this->_results = array();

        $action = 'exiffilename2meta';

        if ( !isset( $input[$action] ) || !is_array( $input[$action] ) ) {
            // not of this part
            return null;
        }

        // param: location: path | file
        $curKey = 'location';
        $test = $this->_checkVarExistsWithDefaultsLocationExists(
            'location', $input[$action], $action, $this->_optionDefaults
        );
        if ( $test !== null ) {
            $this->_results['location'] = $test;
        }

        // param: locationFormat
        $curKey = 'locationFormat';
        $test = $this->_checkVarExistsNoDefaultsButRequired(
            'locationFormat', $input[$action], $action
        );
        if ( $test !== null ) {
            $this->_results['locationFormat'] = $test;
        }

        // param: set
        $curKey = 'set';
        $allowListSet = array('AllDates', 'DateTimeOriginal', 'CreateDate', 'ModifyDate');
        $test = $this->_checkVarExistsWithDefaultsCheckAllowListMustHave(
            'set', $input[$action], $action, $this->_optionDefaults, $allowListSet
        );
        if ( $test !== null ) {
            $this->_results['set'] = $test;
        }

        // param: locationFilter required or default
        $test = $this->_checkVarExistsWithDefaults(
            'locationFilter', $input[$action], $action, $this->_optionDefaults
        );
        if ( $test !== null ) {
            $this->_results['locationFilter'] = $test;
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
     * @return array<string> List of commands (each per file if a directory was given)
     * @throws Mumsys_ShellTools_Adapter_Exception On errors
     */
    private function _prepareCommands(): array
    {
        // $this->_logger->log( __METHOD__, Mumsys_Logger_Abstract::DEBUG );

        $cmdList = array();

        // 20230216_183105-1.jpg
        // 20230216_183105-2.jpg
        // IMG-20230216_183105.jpg
        // 2023-08-02_192919_gopro.jpg
        // 2023-08-02_192919
        //$filename = '2023-08-02_192919_gopro.jpg';

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

        // $format = '%pr%Y-%m-%d_%T%rp'; // IMG-2023-08-02_192919_gopro.jpg
        // $format = '%pr%D_%T%rp'; // IMG-20230802_192919_gopro.jpg
        $format = $this->_results['locationFormat'];
        $patterns = array(
            // simple date & time
            '%D' => '(?P<date>\d{8})',    // D for date e.g: 20231231
            '%T' => '(?P<time>\d{6})',    // T for time in 24h format! e.g: 235859
            // splited date
            '%Y' => '(?P<year>\d{4})',  // Y for year e.g: 2023
            '%m' => '(?P<month>\d{2})', // m for month e.g: 12 (December)
            '%d' => '(?P<day>\d{2})',   // d for day e.g: 01 (January)
            // splited time
            '%H' => '(?P<hour>\d{2})',  // H for hour (24h) e.g: 23
            '%i' => '(?P<minute>\d{2})',// i for minute e.g: 58
            '%s' => '(?P<seconds>\d{2})',//s for seconds e.g: 59
            // extras: prefix, suffix
            '%pr' => '(?P<prefix>.*)',  // pr for prefix e.g: 'IMG-' mixed chars
            '%rp' => '(?P<suffix>.*)',  // re for suffix e.g: '_gopro.jpg' mixed chars
        );

        $oParser = new Mumsys_Parser_Logline( $format, $patterns );

        $filterString = $this->_results['locationFilter'];
        $filterParts = explode( ',', $filterString );

        foreach ( $fileList as $filename ) {
            $currentFileLocation = $workingDir . '/' . $filename;
            // dots or not file
            if ( $filename[0] === '.' || !is_file( $currentFileLocation ) ) {
                continue;
            }
            // filter
            if ( false === in_array( strtolower( substr( $filename, -3 ) ), $filterParts )
                && ( isset( $filterParts[0] ) && $filterParts[0] !== '*' )
            ) {
                //$this->_logger->log( 'Filter, skip: "' . $filename . '"', 7 );
                continue;
            }

            //$this->_logger->log( 'Parse file: ' . $filename, 7 );
            /** @var array<string> $parserResults 4SCA */
            $parserResults = $oParser->parse( $filename, false );

            if ( ! $parserResults ) {
                $mesg = sprintf( 'String of filename seems invalid "%1$s"', $filename );
                throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
            }

            // date checks
            if ( isset( $parserResults['date'] ) ) {
                // simple date handling
                if ( strlen( $parserResults['date'] ) !== 8 ) {
                    // @codeCoverageIgnoreStart
                    // difficult to test
                    throw new Mumsys_ShellTools_Adapter_Exception( 'Date handling fails: Not of YYYYMMDD' );
                    // @codeCoverageIgnoreEnd
                }
                $exifDate = sprintf(
                    '%1$s:%2$s:%3$s',
                    substr( $parserResults['date'], 0, 4 ),
                    substr( $parserResults['date'], 4, 2 ),
                    substr( $parserResults['date'], 6, 2 )
                );
            } else {
                // spec. date handling
                if ( ! isset( $parserResults['year'], $parserResults['month'], $parserResults['day'] ) ) {
                    // @codeCoverageIgnoreStart
                    // difficult to test
                    throw new Mumsys_ShellTools_Adapter_Exception( 'Date handling fails: Parts invalid' );
                    // @codeCoverageIgnoreEnd
                }
                $exifDate = sprintf(
                    '%1$s:%2$s:%3$s',
                    $parserResults['year'], $parserResults['month'], $parserResults['day']
                );
            }

            // time checks
            if ( isset( $parserResults['time'] ) ) {
                // simple time handling
                if ( strlen( $parserResults['time'] ) !== 6 ) {
                    // @codeCoverageIgnoreStart
                    // difficult to test
                    throw new Mumsys_ShellTools_Adapter_Exception( 'Time handling fails: Not of HHiiSS' );
                    // @codeCoverageIgnoreEnd
                }
                $exifTime = sprintf(
                    '%1$s:%2$s:%3$s',
                    substr( $parserResults['time'], 0, 2 ),
                    substr( $parserResults['time'], 2, 2 ),
                    substr( $parserResults['time'], 4, 2 )
                );
            } else {
                // spec. time handling
                if ( !isset( $parserResults['hour'], $parserResults['minute'], $parserResults['seconds'] ) ) {
                    // @codeCoverageIgnoreStart
                    // difficult to test
                    throw new Mumsys_ShellTools_Adapter_Exception( 'Time handling fails: Parts invalid' );
                    // @codeCoverageIgnoreEnd
                }
                $exifTime = sprintf(
                    '%1$s:%2$s:%3$s',
                    $parserResults['hour'], $parserResults['minute'], $parserResults['seconds']
                );
            }

            //
            // base cmd (cmd + global params)
            $binParts = $this->_getBinaryParts( 'exiftool' );
            // expect only one: foreach ( $binParts as $command => $globalParams ) {}
            $cmdBase = key( $binParts ) . ' ' . current( $binParts );
            //

            // Examples:
            // exiftool "-AllDates=2011:08:02 19:29:19" /tmp/gopro/20230216_183105-1.jpg
            // exiftool "-DateTimeOriginal=2023:08:02 19:29:19" \
            //      "-CreateDate=2023:08:02 19:29:19" \
            //      "-ModifyDate=2023:08:02 19:29:19" 2023-08-02_192919_gopro.jpg

            $exifDatetimeString = $exifDate . ' ' . $exifTime;
            //$this->_logger->log( 'Exif dateTime string: "' . $exifDatetimeString . '"', 7 );
            $cmd = sprintf(
                '%1$s "-%2$s=%3$s" "%4$s"',
                $cmdBase,
                $this->_results['set'], $exifDatetimeString, $currentFileLocation
            );
            $cmdList[] = $cmd;

            // $this->_logger->log( 'Command for exiftool: ', 7 );
            $this->_logger->log( 'cmd build: ' . $cmd, 6 );
        }

        if ( $cmdList === array() ) {
            $mesg = sprintf(
                'No files found to handle in --location "%1$s"',
                $this->_results['location']
            );
            $this->_logger->log( $mesg, 7 );
            throw new Mumsys_ShellTools_Adapter_Exception( $mesg );
        }

        return $cmdList;
    }

}
