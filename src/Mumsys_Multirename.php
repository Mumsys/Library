<?php declare(strict_types=1);

/**
 * Mumsys_Multirename
 * for MUMSYS Library for Multi User Management System
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright (c) 2015 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Multirename
 * Created on 2015-02-28
 */


/**
 * Class for renaming multiple files
 *
 * @todo optional: Reduce crap index of codecoverage
 * @todo handle errors to stop execution? at the moment all is reported to the log
 *
 * @category Mumsys
 * @package Mumsys_Library
 * @subpackage Mumsys_Multirename
 */
class Mumsys_Multirename
    extends Mumsys_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '1.4.6';

    /**
     * Logger to log and output messages.
     * @var Mumsys_Logger_Interface
     */
    private $_logger;
//
//    /**
//     * @deprecated since version 1.3.3
//     * Current working settings.
//     * @var array
//     */
//    private $_config;

    /**
     * Current list of working settings. Since version 1.3.4++
     * @var array
     */
    private $_configs = array();

    /**
     * Filesystem object to execute rename or link/symlink tasks.
     * @var Mumsys_FileSystem
     */
    private $_oFiles;

    /**
     * List of substitutions.
     * @var array
     */
    private $_substitutions;

    /**
     * List of path substitutions.
     * @var array
     */
    private $_pathSubstitutions = array();

    /**
     * Number of history entrys to save.
     * Note: If you run on much more than tousands of files you may set the
     * memory limit to a higher value and/or reduce this number to 1.
     * This feature may consume much memory. Take care.
     * @var integer
     */
    private $_historySize = 10;

    /**
     * Relativ location for the history file
     * @var string
     */
    private $_historyFile = '/.multirename/lastactions';

    /**
     * Path to the file to collect working directories.
     * For an overview or a possible remove of the program
     * @var string
     */
    private $_collection = '/tmp/.multirename/collection';

    /**
     * Path to the users HOME Directory (Autodetection or this path will be used)
     * @var string
     */
    private $_pathHome = '/tmp/';

    /**
     * Statistics
     * @var array
     */
    private $_counter = array();

    /**
     * Bitmask for json encode options; @see toJson()
     * @var integer
     */
    private $_jsonOptions = JSON_PRETTY_PRINT;


    /**
     * Initialise Multirename object.
     *
     * @param array $config Setup parameters. @see getSetup() for more.
     * @param Mumsys_FileSystem $oFiles Filesystem object for the primary
     * execution.
     * @param Mumsys_Logger_Interface $logger Log object to track the
     * work and/or to show the output when using as shell script or cronjob
     */
    public function __construct( array $config, Mumsys_FileSystem $oFiles,
        Mumsys_Logger_Interface $logger )
    {
        $logger->log( '### multirename (' . self::VERSION . ') starts', 7 );

        // nothing which belongs to root is allowed at the moment!
        if ( PHP_SAPI === 'cli' && in_array( 'root', $_SERVER ) ) {
            $message = 'Something which belongs to "root" is forbidden. '
                . 'Sorry! Use a different user!' . PHP_EOL;
            $logger->log( $message, 4 );
        }

        $serverHome = Mumsys_Php_Globals::getServerVar( 'HOME', '' );
        if ( is_dir( $serverHome . DIRECTORY_SEPARATOR ) ) {
            $this->_pathHome = $serverHome;
        } else {
            $this->_pathHome = $this->_pathHome;
        }

        $this->_collection = $this->_pathHome . '/.multirename/collection';
        $this->_logger = $logger;

        if ( isset( $config['loglevel'] ) ) {
            if ( $this->_logger instanceof Mumsys_Logger_Decorator_Messages ) {
                $this->_logger->setMessageLoglevel( (int) $config['loglevel'] );
            }
        }

        $this->_counter = array(
            'cntMatches' => 0,
            'cntMatchesTotal' => 0,
            'cntMatchesRelevant' => 0,
        );
        $this->_oFiles = $oFiles;

        $this->run( $config );

        $this->_logger->log( '### multirename done.' . PHP_EOL, 7 );
    }


    /**
     * Free temporarily created results or properties on destruction or if the
     * destructor is called.
     */
    public function __destruct()
    {
        $this->_pathSubstitutions = array();
        $this->_substitutions = array();
    }


    /**
     * Run the rename process based on given config.
     *
     * @param array $input Configuration/ setup parameters e.g. from shell
     * input. see initSetup() for more
     */
    public function run( array $input = array() )
    {
        if ( isset( $input['from-config'] ) ) {
            $this->_configs = $this->_mergeConfigs( $input );
        } else {
            $this->_configs = array($input);
        }

        foreach ( $this->_configs as $config ) {
            $config = $this->initSetup( $config );

            $actions = array();

            /** @todo to be replace with new getopts features:
             * action1 options action2 options */
            if ( !empty( $config['undo'] ) ) {
                $actions['undo'] = 'undo';
            } else {
                $actions['run'] = 'run';
            }

            if ( isset( $config['save-config'] ) || isset( $config['set-config'] ) ) {
                $actions['save-config'] = 'save-config';
            }

            if ( isset( $config['del-config'] ) ) {
                $actions['del-config'] = 'del-config';
            }

            if ( isset( $config['show-config'] ) ) {
                $actions['show-config'] = 'show-config';
                unset( $actions['run'] );
            }

            if ( isset( $config['version'] ) ) {
                $actions = array('version' => 'version');
            }

            if ( isset( $config['stats'] ) ) {
                $actions['stats'] = 'stats';
            }

            foreach ( $actions as $action ) {
                $this->_logger->log(
                    'Will perform action "' . $action . '" now', 7
                );

                switch ( $action )
                {
                    case 'save-config':
                        $this->saveConfig( $config['path'] );
                        break;

                    case 'del-config':
                        $this->deleteConfig( $config['path'] );
                        break;

                    case 'show-config':
                        $this->_showConfig( $config );
                        break;

                    case 'undo':
                        $this->_undo( $config );
                        break;

                    case 'version':
                        $this->showVersion();
                        break;

                    case 'stats':
                        $this->stats();
                        break;

                    case 'run':
                    default:
                        $this->_execute( $config );
                }
            }
        }
    }


    /**
     * Initialise config parameters for the current action and returns the new
     * configuration.
     *
     * Parameters will be validated, defaults set and prepares it for the usage
     * internally.
     *
     * @param array $config Configuration/ setup parameters.
     *
     * @return array Returns the new, checked configuration.
     *
     * @throws Mumsys_Exception Throws exception on any error happen with the
     * incoming data.
     */
    public function initSetup( array $config = array() )
    {
        $this->__destruct();

        if ( !isset( $config['path'] ) || !is_dir( $config['path'] ) ) {
            throw new Mumsys_Multirename_Exception( 'Invalid --path <your value>' );
        }

        if ( isset( $config['test'] ) && !is_bool( $config['test'] ) ) {
            throw new Mumsys_Multirename_Exception( 'Invalid --test value' );
        }

        if ( !isset( $config['keepcopy'] ) || $config['keepcopy'] == false ) {
            $config['keepcopy'] = false;
        } else {
            $config['keepcopy'] = true;
        }

        if ( isset( $config['hidden'] ) && $config['hidden'] == true ) {
            $config['hidden'] = true;
        } else {
            $config['hidden'] = false;
        }

        if ( !empty( $config['link'] ) ) {
            $linkParts = explode( ':', $config['link'] );
            $config['link'] = $linkParts[0];
            if ( isset( $linkParts[1] ) ) {
                $config['linkway'] = $linkParts[1];
            } else {
                $config['linkway'] = 'rel';
            }
        }

        if ( !empty( $config['linkway'] ) ) {
            if ( $config['linkway'] != 'abs' ) {
                $config['linkway'] = 'rel';
            }
        }

        if ( empty( $config['fileextensions'] ) && !isset( $config['undo'] ) ) {
            throw new Mumsys_Multirename_Exception( 'Missing --fileextensions "<your value/s>"' );
        }

        if ( !is_array( $config['fileextensions'] ) ) {
            $config['fileextensions'] = explode( ';', $config['fileextensions'] );
        }

        if ( !isset( $config['substitutions'] ) ) {
            throw new Mumsys_Multirename_Exception( 'Missing --substitutions "<your value/s>"' );
        }
        $this->_substitutions = $this->_buildSubstitutions( $config['substitutions'] );

        if ( isset( $config['recursive'] ) && $config['recursive'] === true ) {
            $config['recursive'] = true;
        } else {
            $config['recursive'] = false;
        }

        if ( !isset( $config['sub-paths'] ) ) {
            $config['sub-paths'] = false;
        }

        if ( !isset( $config['find'] ) || $config['find'] == false ) {
            $config['find'] = false;
        } else {
            $config['find'] = explode( ';', $config['find'] );
        }

        if ( !isset( $config['exclude'] ) || $config['exclude'] == false ) {
            $config['exclude'] = false;
        } else {
            $config['exclude'] = explode( ';', $config['exclude'] );
        }

        if ( !isset( $config['history'] ) ) {
            $config['history'] = false;
        }

        if ( isset( $config['history-size'] ) ) {
            $this->_historySize = intval( $config['history-size'] );
        }

        if ( empty( $config['test'] ) || $config['test'] !== true ) {
            $config['test'] = false;
        }

        return $config;
    }


    /**
     * Execute.
     * This method will look for required files and will test, link or rename
     * affected files.
     *
     * @param array $config Configuration to work with for this action.
     */
    private function _execute( array $config )
    {
        $pathAll = array();
        $dirinfo = $this->_getRelevantFiles( $config );
        $this->_logger->log( 'Base-Path: "' . $config['path'] . '"', 7 );

        $history = array();

        foreach ( $dirinfo as $k => $file ) {
            $path = $file['path'];

            $this->_counter['cntMatchesTotal'] += 1;

            if ( $file['ext'] == '' ) {
                $newName = $file['name'];
                $extension = '';
            } else {
                $newName = $this->_oFiles->nameGet( $file['name'] );
                $extension = '.' . $file['ext'];
            }

            // generate %path0% ... %pathN% for substitution
            if ( !isset( $pathAll[$path] ) ) {
                $pathAll[$path] = $this->_buildPathBreadcrumbs( $path, $config['path'] );
            }

            $newName = $this->_substitute(
                $newName, $path, $pathAll[$path], $config['sub-paths']
            );

            $source = $path . '/' . $file['name'];
            $destination = $path . '/' . $newName . $extension;

            if ( !empty( $config['link'] ) ) {
                $txtMode = 'link';
                $mode = 'link';
                if ( $config['link'] == 'soft' ) {
                    $txtMode = 'symlink';
                    $mode = 'symlink';
                }
            } else {
                $txtMode = 'rename';
                $mode = 'rename';
            }

            if ( $config['test'] !== true ) {
                if ( $source != $destination ) {
                    $this->_logger->log(
                        'Will ' . $txtMode . ':' . "\n\t" . $file['name']
                        . ' ...TO: ' . "\n\t" . $newName . $extension, 6
                    );
                    try
                    {
                        if ( !empty( $config['link'] ) ) {
                            $newdest = $this->_oFiles->link(
                                $source, $destination, $config['link'],
                                $config['linkway'], $config['keepcopy']
                            );
                        } else {
                            $newdest = $this->_oFiles->rename(
                                $source, $destination, $config['keepcopy']
                            );
                        }

                        if ( $newdest != $destination ) {
                            $message = 'Target "' . $destination
                                . '" exists. Used "' . $newdest . '"';
                            $this->_logger->log( $message, 5 );
                        }

                        $history[$mode][$source] = $destination = $newdest;
                        $this->_counter['cntMatchesRelevant'] += 1;

                    }
                    catch ( Exception $e ) {
                        $message = $txtMode . ' failt for: "' . $source
                            . '": ' . $e->getMessage();
                        $this->_logger->log( $message, 3 );
                    }
                }
            } else {
                $matches = $this->_executeTest(
                    $config, $source, $destination, $file, $newName, $extension,
                    $txtMode
                );
                $this->_counter['cntMatchesRelevant'] += $matches;
            }
        }

        if ( !empty( $config['history'] ) && empty( $config['test'] ) && $history ) {
            $this->_addActionHistory( $config, $history );
        }
    }


    /**
     * Send statistics to the logger if possible (loglevel >= 6)
     */
    public function stats()
    {
        // stat output
        if ( $this->_counter['cntMatchesTotal'] ) {
            $message = 'Stats:' . PHP_EOL
                . 'Scanned files total: ' . $this->_counter['cntMatchesTotal'] . PHP_EOL
                . 'Files relevant: ' . $this->_counter['cntMatchesRelevant'] . PHP_EOL
                . 'Memory limit: ' . ini_get( 'memory_limit' ) . PHP_EOL
                . 'Memory used: '
                . $this->_oFiles->coolfilesize( memory_get_usage(), 2 ) . PHP_EOL;
            $this->_logger->log( $message, 6 );
        }
    }


    /**
     * Perfom a test rename.
     *
     * @param array $config Current action config
     * @param string $source Source file
     * @param string $destination Target file
     * @param array $file List of key/value pairs of source file properties
     * @param string $newName New file name
     * @param string $extension Extension of the new filename
     * @param string $txtMode Mode in text format to show/output
     *
     * @return integer Number of occurances.
     */
    private function _executeTest( array $config, $source, $destination, $file,
        $newName, $extension, $txtMode )
    {
        $cntMatchesRelevant = 0;

        if ( $source != $destination ) {
            if ( file_exists( $destination ) || is_link( $destination ) ) {
                $message = 'Target exists! Will ';
                if ( $config['keepcopy'] ) {
                    $message .= 'create a copy';
                } else {
                    $message .= 'overwrite target';
                }
                $message .= ': "'
                    . str_replace( $config['path'], '...', $destination ) . "'";
                $this->_logger->log( $message, Mumsys_Logger_Abstract::WARN );
            }

            $this->_logger->log(
                'Test-mode ' . $txtMode . ' (found: ' . $cntMatchesRelevant
                . ' actions):' . PHP_EOL
                . "\t" . $file['name'] . ' ...TO: ' . "\n"
                . "\t" . $newName . $extension . PHP_EOL, 6
            );

            $cntMatchesRelevant += 1;
        } else {
            $message = 'No ' . $txtMode . ', identical for "' . $file['name']
                . '" in "' . $file['path'] . '"';
            $this->_logger->log( $message, 7 );
        }

        return $cntMatchesRelevant;
    }


    /**
     * Scan for relevant files, filter and return found matches.
     *
     * @todo Reduce crap index of codecoverage
     *
     * @param array $config Current action config
     *
     * @return array List of properties from Mumsys_FileSystem::getFileDetails
     * including 'ext' for extension.
     */
    private function _getRelevantFiles( array $config )
    {
        $files = array();

        $dirinfo = $this->_oFiles->scanDirInfo(
            $config['path'], ( $config['hidden'] ? false : true ),
            $config['recursive']
        );

        foreach ( $dirinfo as $file ) {
            if ( $file['type'] != 'file' ) {
                continue;
            }

            $extension = $this->_oFiles->extGet( $file['name'] );
            $file['ext'] = $extension;

            if ( in_array( '*', $config['fileextensions'] )
                || in_array( $extension, $config['fileextensions'] )
            ) {
                // Check in OR condition; continue loop on match
                if ( $config['exclude'] ) {
                    foreach ( $config['exclude'] as $find ) {
                        if ( $this->_relevantFilesCheckMatches( $find, $file['file'] ) ) {
                            continue;
                        }
                    }
                }

                // Check in OR condition; take it on match or continue loop
                if ( $config['find'] ) {
                    foreach ( $config['find'] as $find ) {
                        if ( $this->_relevantFilesCheckMatches( $find, $file['file'] ) ) {
                            $files[] = $file;
                        } else {
                            continue;
                        }
                    }
                } else {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }


    /**
     * Checks for a match.
     *
     * @param string $lookup Keyword to look for in the subject
     * @param string $subject Subject to test for matches
     *
     * @return integer|false Returns 1 for a match, 0 for no match, false for
     * an error
     */
    private function _relevantFilesCheckMatches( $lookup, $subject )
    {
        $check = false;
        if ( preg_match( '/^(regex:)/i', $lookup ) ) {
            $regex = substr( $lookup, 6 );
        } else {
            $regex = '/' . $lookup . '/i';
        }

        return preg_match( $regex, $subject );
    }


    /**
     * Undo last rename action.
     *
     * @param array $config Current action config
     * @param boolean $keepCopy Flag to set to what to do if old file already
     * exists again on undo. On true the existing file will be kept, on false
     * overwriting take affect.
     */
    protected function _undo( array $config, $keepCopy = true )
    {
        $allHistorys = $this->_getActionHistory( $config['path'], -1 );

        if ( $allHistorys ) {
            // takes latest item for the moment
            $history = $allHistorys[count( $allHistorys ) - 1]['history'];

            foreach ( $history as $mode => $lastActions ) {
                if ( $config['test'] ) {
                    $mode = $mode . '-Test';
                }

                switch ( $mode ) {
                    case 'link':
                    case 'symlink':
                        $this->_undoLink( $lastActions, $keepCopy );
                        break;

                    case 'rename':
                        $this->_undoRename( $lastActions, $keepCopy );
                        break;

                    case 'link-Test':
                    case 'symlink-Test':
                    case 'rename-Test':
                        $this->_undoTest( $lastActions, $mode );
                        break;

                    default:
                        $this->_logger->log(
                            'Undo failt. Invalid mode. Weather '
                            . 'a link, symlink nor rename action set', 3
                        );
                        break;
                }

                $this->_logger->log( 'Undo (mode: "' . $mode . '") done', 7 );
            }
        } else {
            $this->_logger->log( 'Undo failt. No action history found', 6 );
        }

        return;
    }


    /**
     * Undo for the testmode to show what it will do.
     *
     * This is not a real test. It yust shows the files which are effeced.
     *
     * @param array $files List of files (orig=>newvalue) to be re-done
     * @param string $mode Type of the undo mode links symlink, rename to show
     * to the output
     */
    private function _undoTest( array $files = array(), $mode = '' )
    {
         // reverse, wording is now correct for this undo case for $to and $from
        foreach ( $files as $to => $from ) {
            if ( preg_match( '/link/i', $mode ) ) {
                $mesg = 'Undo ' . $mode . ': delete: "' . $from . '"';
            } else {
                $mesg = 'Undo ' . $mode . ': "' . $from . '" TO: "' . $to . '"';
            }

            $this->_logger->log( $mesg, 6 );
        }
    }


    /**
     * Undo a rename action.
     *
     * @param array $files List of files from/to pairs to undo/ reverse.
     * @param boolean $keepCopy Flag to set to what to do if old file already
     * exists again on undo. On true the existing file will be kept, on false
     * overwriting take affect.
     *
     * @throws Mumsys_FileSystem_Exception Throws exception on error eg: source
     * not found
     */
    private function _undoRename( array $files, $keepCopy = true )
    {
        // reverse (old to is now from)
        foreach ( $files as $to => $from ) {
            try
            {
                $newTo = $this->_oFiles->rename( $from, $to, $keepCopy );
                $mesg = 'Undo rename ok for: "' . basename( $from ) . '"';
                $this->_logger->log( $mesg, 7 );
                if ( $newTo != $to ) {
                    $mesg = sprintf(
                        'Undo rename to "%1$s" notice: Already exists! '
                        . 'Used "%2$s" instead',
                        $to,
                        basename( $newTo )
                    );
                    $this->_logger->log( $mesg, 5 );
                }
            }
            catch ( Mumsys_FileSystem_Exception $e ) {
                $message = 'Undo rename failt for "' . $from . '" TO: "'
                    . $to . '". Message: ' . $e->getMessage();
                $this->_logger->log( $message, 3 );
            }
        }
    }


    /**
     * Undo a link/symlink action.
     *
     * @param array $files List of files to unlink sysm/hardlinks
     * @param boolean $keepCopy Flag to set to what to do if old link already
     * exists, again, on undo. On true the existing  will be kept, on false the
     * link will be deleted. Default: false.
     */
    private function _undoLink( $files, $keepCopy = false )
    {
        // reverse (old to is now from)
        foreach ( $files as $to => $from ) {
            if ( is_link( $from ) && !@unlink( $from ) ) {
                $mesg = 'Deleting the link failt for "' . $from . '" ';
                $code = 3;
            } else {
                $mesg = 'Undo link ok for: "' . basename( $from ) . '"';
                $code = 7;
            }

            $this->_logger->log( $mesg, $code );
        }
    }


    /**
     * Adds current history to the history log.
     *
     * @param array $config Current action config
     * @param array $current Current created actions to add to the history
     */
    protected function _addActionHistory( array $config, array $current )
    {
        $this->_mkConfigDir( $config['path'] );

        $file = $config['path'] . $this->_historyFile;

        $history = $this->_getActionHistory( $config['path'], -1 );

        if ( count( $history ) > $this->_historySize ) {
            array_shift( $history );
            $this->_logger->log( 'History size exceed. Oldest entry droped', 6 );
        }

        $historyItem = array(
            'name' => 'history ' . date( 'Y-m-d', time() ),
            'date' => date( 'Y-m-d H:i:s', time() ),
            'history' => $current,
        );

        if ( $history && isset( $history[0]['date'] ) ) {
            $history[] = $historyItem;
        } else {
            $history = array($historyItem);
        }

        $data = $this->toJson( $history );
        $result = file_put_contents( $file, $data );

        $mesgA = 'Actions saved. To undo/ reverse use multirename --undo '
            . '--path "' . $config['path'] . '"';
        $mesgB = 'Undo is possible for this path until the next rename action '
            . 'will be performed/ executed';

        $this->_logger->log( $mesgA, 6 );
        $this->_logger->log( $mesgB, 6 );
        unset( $mesgA, $mesgB );

        return $result;
    }


    /**
     * Returns history data.
     * The actions preformed by a rename/symlink action. If the index is given
     * the selected action will be returnd otherwise all history data returns.
     *
     * @param string $path Action/ start directory for renaming
     * @param integer $index History index to return
     *
     * @return array|false Returns a list of action historys or false on error
     */
    protected function _getActionHistory( $path, $index = -1 )
    {
        $result = array();
        $file = $path . $this->_historyFile;

        if ( file_exists( $file ) ) {
            $data = file_get_contents( $file );
            $result = json_decode( $data, true );

            /** @todo future
              if (isset($history[$index])) {
              $result = array($history[$index]);
              } else {
              $result = $history;
              } */
        }

        return $result;
    }


    /**
     * Removes the history
     *
     * @param string $path Action/ start directory for renaming
     *
     * @return boolean Returns true on success or false on error
     *
     * @throws Mumsys_Multirename_Exception On any other errors
     */
    public function removeActionHistory( $path )
    {
        $file = $path . $this->_historyFile;

        if ( !file_exists( $file ) || !unlink( $file ) ) {
            throw new Mumsys_Multirename_Exception( 'Removing history failed' );
        }

        return true;
    }


    /**
     * Creates a config directory to store a config or action history.
     *
     * @param string $path Action/ start directory for renaming files.
     *
     * @return boolean Returns true on success of false if the config dir could
     * not be created
     */
    private function _mkConfigDir( $path )
    {
        $path = $path . '/.multirename/';
        if ( !is_dir( $path ) ) {
            if ( !@mkdir( $path, 0755 ) ) {
                $mesg = 'Can not create directory "' . $path . '/.multirename"';
                $this->_logger->log( $mesg, 3 );
                return false;
            } else {
                $this->_trackConfigDir( $path );
            }
        }

        return true;
    }


    /**
     * Collects action directories.
     * The idea behind: To find where this program was used. Also useful for an
     * uninstall process
     *
     * @param string $path Action/ start directory for renaming files
     */
    private function _trackConfigDir( $path )
    {
        $mesg = 'Will track config directory for collection. Path: "'
            . $path . '"';
        $this->_logger->log( $mesg, 7 );
        $data = $this->_getCollection();
        $this->_setCollection( $data, $path );
    }


    /**
     * Returns a list of action/ start directories.
     *
     * @return array List of start directories
     */
    private function _getCollection()
    {
        $colldata = array();
        if ( file_exists( $this->_collection ) ) {
            $data = file_get_contents( $this->_collection );
            $colldata = json_decode( $data, true );
        }

        return $colldata;
    }


    /**
     * Adds a collection record and saves all collection data.
     *
     * @param array $data List of records of the collection.
     * @param string $path Directory to be added to the collection
     *
     * @return boolean Returns true on success or false on failure
     */
    private function _setCollection( array $data, string $path )
    {
        $data[md5( $path )] = str_replace( '//', '/', $path );
        asort( $data );
        $jdata = $this->toJson( $data );

        return file_put_contents( $this->_collection, $jdata );
    }


    /**
     * Loads and returns a list of configurations from the given start directory.
     *
     * @param string $path Action/ start directory for renaming files
     * @param integer|string $configID Config ID to return
     *
     * @return array|false Returns a list of configurations or false if no
     * configuration exists.
     *
     * @throws Mumsys_Multirename_Exception If config not exists
     */
    public function getConfig( $path = '', $configID = '_' )
    {
        $file = $path . '/.multirename/config';
        if ( file_exists( $file ) ) {
            $data = file_get_contents( $file );
            $allconfig = json_decode( $data, true );

            $mesg = 'loaded config from --from-config "' . $path . '"';
            $this->_logger->log( $mesg, 7 );

            if ( isset( $allconfig[$configID]['configs'] ) ) {
                return $allconfig[$configID]['configs'];
            }

            /** @deprecated since version 1.3.3 */
            $mesg = '--- Old config found. Please UPGRADE using --save-config ---';
            $this->_logger->log( $mesg, 4 );
            $this->_configs = array($allconfig[$configID]['config']);
            return array($allconfig[$configID]['config']);
            /** @deprecated since version 1.3.3 */
        }

        $mesg = 'Could not read config in path: "' . $path . '"';
        throw new Mumsys_Multirename_Exception( $mesg );
    }


    /**
     * Merge config list from loaded config file into config from shell input.
     * Note: shell input overwrites config items from config file.
     *
     * @param array $config Configuration/ setup parameters. see initSetup() for
     * help/ a complete list!
     *
     * @throws Mumsys_Multirename_Exception On errors
     */
    public function _mergeConfigs( array $config = array() )
    {
        if ( is_dir( $config['from-config'] . '/' ) ) {
            $config['path'] = $config['from-config'];
        } else {
            $mesg = 'Invalid --from-config <your value> parameter. Path not found';
            throw new Mumsys_Multirename_Exception( $mesg );
        }

        $newConfigList = $this->getConfig( $config['from-config'] );

        foreach ( $newConfigList as $i => $opts ) {
            foreach ( $config as $key => $val ) {
                $newConfigList[$i][$key] = $val;
            }
        }

        return $newConfigList;
    }


    /**
     * Saves the configuration.
     *
     * Note: configID: '_' will be used for batch processing (When --batch flag
     * is set, currently the only implementation)
     * configID: 0 will be used as default and will be replace and used as
     * latest config if no other ID is given.
     *
     * @param string $path Action/ start directory for renaming files
     * @param integer|string $configID Config ID to set. Optional, for the future.
     *
     * @return integer|false Returns number of bytes written or false on error
     */
    public function saveConfig( $path, $configID = '_' )
    {
        if ( !$this->_mkConfigDir( $path ) ) {
            return false;
        }

        $file = $path . '/.multirename/config';

        $configs = array();

        foreach ( $this->_configs as $i => $values ) {
            unset(
                $values['test'], $values['save-config'], $values['show-config'],
                $values['from-config'], $values['loglevel']
            );
            $configs[$i] = $values;
        }

        $config = array(
            $configID => array(
                'name' => 'config or preset name',
                'date' => date( 'Y-m-d H:i:s', time() ),
                'version' => self::VERSION,
                'path' => $path,
                'configs' => $configs,
            ),
        );

        $data = $this->toJson( $config );

        $result = file_put_contents( $file, $data );
        $this->_logger->log( 'Set config done', 6 );

        return $result;
    }


    /**
     * Removes/ purges the complete configuration file.
     *
     * @param string $path Action/ start directory for renaming files
     *
     * @return boolean Returns true on success or false
     */
    public function deleteConfig( $path = '' )
    {
        $file = $path . '/.multirename/config';
        if ( file_exists( $file ) ) {
            if ( @unlink( $file ) ) {
                $this->_logger->log( 'Config deleted', 6 );
                return true;
            } else {
                $this->_logger->log( 'Could not delete config', 3 );
            }
        } else {
            $this->_logger->log( 'Config not found', 5 );
        }

        return false;
    }


    /**
     * Show /output all existing action configurations.
     */
    public function showConfigs()
    {
        foreach ( $this->_configs as $n => $config ) {
            $this->_logger->log(
                'Configuration number ' . ( $n + 1 ) . ' (index:' . $n . '):', 6
            );
            $this->_showConfig( $config );
        }
    }


    /**
     * Shows the current loaded configuration for the cmd line.
     * Note: This will push the informations to the logger! Enable loglevel 6
     * to show it!
     *
     * @param array $config Current action config
     */
    private function _showConfig( array $config )
    {
        $this->_logger->log( 'Show config:', 6 );
        $all = '';

        foreach ( $config as $key => $value ) {
            if ( is_int( $key ) ) {
                continue;
            }

            $msg = '';
            if ( $value ) {
                $msg .= ' --' . $key . ' ';
            }
            if ( !is_bool( $value ) ) {
                if ( is_array( $value ) ) {
                    $msg .= '\'' . implode( ';', $value ) . '\'';
                } else {
                    $msg .= "'" . $value . "'";
                }
            }
            if ( $msg ) {
                $this->_logger->log( $msg, 6 );
            }

            $all .= $msg;
        }

        $this->_logger->log( 'cmd#> multirename' . $all, 6 );
    }


    /**
     * Create path-breadcrumbs.
     *
     * This will generate %path0% ... %pathN% as array keys and the part of the
     * path as value. %path0% will be incoming path, %path1 .. %pathN% in reverse
     * order.
     * Example: /var/files/records
     * array(
     *  '%path0%'=> '/var/files/records',
     *  '%path1%' = 'records',
     *  '%path2%' = 'files',
     *  '%path3%' = 'var'
     * );
     *
     * @param string $path Path of the current file
     * @param string $configPath Path of the current config, recursiv scans may
     * differ
     *
     * @return array List of path-breadcrumbs of the current file.
     */
    private function _buildPathBreadcrumbs( $path = '', $configPath = '' )
    {
        $pathAll = array('%path0%' => $configPath);
        $pathTmp = explode( '/', $path );

        $j = 1;
        for ( $i = count( $pathTmp ) - 1; $i > 0; $i-- ) {
            if ( $pathTmp[$i] ) {
                $pathAll['%path' . $j . '%'] = $pathTmp[$i];
                $j++;
            }
        }

        return $pathAll;
    }


    /**
     * Build/ prepare the basic substitutions from incoming substitution string.
     *
     * When enabling "--sub-paths" every string, and also regular expressions will
     * be checked and substituted in a second step (see _substitutePaths()). Then
     * things like this will be done: regex:/(%path2%)$/i=%path1%_$1. Be careful
     * using this extra feature! Always use --test and read every test result!
     *
     * @param string $substitutions Semicolon separated list with key value pairs
     * for substitution eg: 'ä=ae;ß=ss;regex:/^(\d{5})$/i=%path1%_number_$1'
     *
     * @return array
     */
    private function _buildSubstitutions( $substitutions = '' )
    {
        $subs = explode( ';', $substitutions );
        $result = array();
        foreach ( $subs as $expr ) {
            $keyVal = explode( '=', $expr );
            if ( !isset( $keyVal[1] ) ) {
                $keyVal[1] = '';
            }

            if ( preg_match( '/^(regex:)/i', $keyVal[0] ) ) {
                $result[] = array(substr( $keyVal[0], 6 ) => $keyVal[1]);
            } else {
                $result[$keyVal[0]] = $keyVal[1];
            }
        }

        return $result;
    }


    /**
     * Replace path informations in substitution configuration
     *
     * @param array $paths List of path-breadcrumbs of the current working file
     * for substitution.
     *
     * @return array Returns the compiled list of substitution to substitude
     */
    private function _substitutePaths( array $substitutions = array(),
        array $paths = array() )
    {
        foreach ( $substitutions as $search => &$replace ) {
            if ( is_numeric( $search ) && is_array( $replace ) ) {
                foreach ( $replace as $key => &$value ) {
                    foreach ( $paths as $pk => &$pv ) {
                        if ( ( $newValue = str_replace( $pk, $pv, $key ) ) != $key ) {
                            $substitutions[$search][$newValue] = $value;
                            unset( $substitutions[$search][$key] );
                        }

                        if ( ( $newValue = str_replace( $pk, $pv, $value ) ) != $value ) {
                            $substitutions[$search][$key] = $newValue;
                        }
                    }
                }
            } else {

                if ( is_int( $search ) ) {
                    $search = (string) $search;
                }

                foreach ( $paths as $pk => $pv ) {
                    $newValue = str_replace( $pk, $pv, $search );
                    if ( $newValue != $search ) {
                        $substitutions[$newValue] = $replace;
                        unset( $substitutions[$search] );
                    }

                    $newValue = str_replace( $pk, $pv, $replace );
                    if ( $newValue != $replace ) {
                        $substitutions[$search] = $newValue;
                    }
                }
            }
        }
        return $substitutions;
    }


    /**
     * Substitute/ replace given string (filename)
     *
     * @param string $name Filename to substitute
     * @param string $curPath Current path of the file
     * @param array $breadcrumbs Replacement breadcrumbs of the current path
     * @param boolean $substitutePaths Flag to enable to sustitude %path%
     * informations or not; Default: false
     *
     * @return string Returns the new substituted filename
     */
    private function _substitute( $name, $curPath, array $breadcrumbs = array(),
        $substitutePaths = false ): string
    {
        if ( $substitutePaths ) {
            if ( !isset( $this->_pathSubstitutions[$curPath] ) ) {
                $substitutions = $this->_substitutePaths( $this->_substitutions, $breadcrumbs );
                $this->_pathSubstitutions[$curPath] = $substitutions;
            } else {
                $substitutions = $this->_pathSubstitutions[$curPath];
            }
        } else {
            $substitutions = $this->_substitutions;
            foreach ( $breadcrumbs as $pKey => $pValue ) {
                $substitutions[$pKey] = $pValue;
            }
        }

        foreach ( $substitutions as $search => $replace ) {
            if ( ( is_array( $search ) && is_array( $replace ) )
                || ( is_scalar( $search ) && is_scalar( $replace ) )
            ) {
                if ( is_int( $search ) ) {
                    $search = (string) $search;
                }
                $name = str_replace( $search, $replace, $name );
            } else {
                /** @todo escape operators? do tests */
                foreach ( $replace as $regex => $repl ) {
                    $name = preg_replace( $regex, $repl, $name, -1 );
                }
            }
        }

        return $name;
    }


    /**
     * Install multirename.
     *
     * @todo complete it, more tests of returen values, do tests
     *
     * This will create some hidden paths and files for a better usage later on
     * and will init the collection of working directories for an overview of a
     * possible remove of the program. Data/ history created by the program can
     * be savely removed.
     */
    public function install()
    {
        try {
            $path = $this->_pathHome . '/.multirename/';
            $this->_oFiles->mkdirs( $path, 0755 );
            $this->_trackConfigDir( $path );
        }
        catch ( Exception $err ) {
            $this->_logger->log( 'Install failure! Reason: "' . $err->getMessage() . '"' );
            throw $err;
        }
    }


    public function upgrade()
    {
        switch ( self::VERSION ) {
            case '1.4.0':
            case '1.4.1':
            default:
//                $list = $this->_getCollection();
//                foreach ($list as $id => $configPath) {
//                    if (is_dir($configPath)) {
//                        $path = $configPath . '/../';
//                        $config = $this->getConfig($path);
//                        print_r($config);
//                    }
//                }
//                echo 'paths where configs exists to upgrade:' . PHP_EOL;
//                print_r($list);
        }

        return true;
    }


    /**
     * Retuns the version of this program.
     *
     * @return string Returns the version string
     */
    public static function getVersionLong()
    {
        $version = self::getVersionShort();
        $versions = parent::getVersions();

        $verGlobal = array(0, 0, 0);
        $verFallback = $verGlobal;
        foreach ( $versions as $class => $ver ) {
            $version .= str_pad( $class, 35, ' ', STR_PAD_RIGHT ) . ' ' . $ver . PHP_EOL;

            $verParts = explode( '.', $ver );
            if ( count( $verParts ) !== 3 ) {
                // Version probably not set or not setable (eg. from generic stdClass()
                $verParts = $verFallback;
            }

            $verGlobal[0] += (int)$verParts[0];
            $verGlobal[1] += (int)$verParts[1];
            $verGlobal[2] += (int)$verParts[2];
        }

        $version .= str_pad( 'Global version ID', 35, ' ', STR_PAD_RIGHT )
            . ' ' . implode( '.', $verGlobal ) . PHP_EOL . PHP_EOL;

        return $version;
    }


    public static function getVersionShort()
    {
        $version = 'multirename %1$s by Florian Blasel' . PHP_EOL . PHP_EOL;
        return sprintf( $version, self::VERSION );
    }


    /**
     * Outputs the version information.
     */
    public static function showVersion()
    {
        echo self::getVersionLong();
    }


    /**
     * Returns all input options for the construction.
     *
     * When using other GetOpt than the current one this is probably the
     * configuration your are looking for.
     * Note: When using your own GetOpt program: The long input values will be
     * used and are required!!! Short options map to the long version.
     *
     * @param boolean $shellOptions Returns shell options if true or array as
     * list of input parameters and their description for the construction.
     *
     * @return array Returns a list of all input options which can be set.
     */
    public static function getSetup( $shellOptions = true )
    {
        $result = array(
            '--test|-t' => 'Flag: test before execute',
            '--path|-p:' => 'Path to scann for files (tailing slash is important!) * Required',
            //
            '--fileextensions|-e:' => 'Semicolon separated list of file extensions to scan for '
            . 'eg. "avi;AVI;mpg;MPG" or "*" (with quotes) for all files * Required',
            //
            '--substitutions|-s:' => 'Semicolon separated list with key value pairs for substitution eg:'
            . ' --substitutions ä=ae;ö=oe;ß=ss; =_;\'regex:/^(\d{5})$/i=x_\$1\'... .'
            . 'As simple feature you can use %path1%...%pathN% parameters to substitute '
            . 'path informations in substitution values the file belongs to. For more '
            . 'information see --sub-paths but only use --sub-paths if you really need '
            . 'it. It can became strange side effects when enabling it. * Required',
            //
            '--sub-paths' => 'Flag; Enable substitution using paths. Feature for the substitution: '
            . 'Breadcrumbs of the --path can be found/ substituted with %path1% - %pathN% '
            . 'in reverse. If you want to rename files and want to add the folder '
            . 'the file belongs to you can use %path1%. One folder above is %path2% '
            . 'and so on until the given root in --path. Example: /var/files/records '
            . '=> %path1% = records, %path2% = files, %path3% = var; With this option '
            . 'you can also replace %pathN% in keys or values and also in regular expressions'
            . 'Use the --test flag and test and check the results carefully! '
            . 'WARNING: Enabling this feature can change the behavior of existing substitutions '
            . ' in your cmd line!',
            //
            '--find|-f:' => 'Find files. Semicolon seperated list of search keywords or '
            . 'regular expressions (starting with "regex:"). The list will be handled in OR conditons.'
            . 'The keyword checks for matches in any string of the file location (path and filename). Optional',
            //
            '--exclude:' => 'Exclude files. Semicolon seperated list of search keywords or regular expressions ('
            . 'starting with "regex:"). The list will be handled in OR conditons.'
            . 'The keyword will be checked for matches in any string of the file location (path and filename). Exclude '
            . 'will also ignore matches from the --find option; Optional',
            //
            '--recursive|-r' => 'Flag, if set read all files under each directory starting from --path recursively',
            '--keepcopy' => 'Flag. If set keep all existing files',
            //
            '--hidden' => 'Include hidden files (dot files)',
            //
            '--link:' => 'Don\'t rename, create symlinks or hardlinks, relativ or absolut to target '
            . '(Values: soft|hard[:rel|abs]). If the second parameter is not given relativ links will be created',
            //
            '--linkway:' => 'Optional for --link: Type of the link to be created: Relative or absolut ("rel"|"abs"), '
            . 'default: "rel". This will be used internally if you use e.g: --link soft:rel or --link soft:abs the '
            . 'linkway will be extracted from that line. Otherwise use --link soft --linkway rel|abs',
            //
            '--history|-h' => 'Flag; If set this will enable the history and tracks all actions for a later undo',
            //
            '--history-size:' => 'Integer; Number of history entrys if --history is enabled; Default: 10; '
            . 'Note: If you run on much more than hundreds of files you may set the memory'
            . ' limit to a higher value and/or reduce this number to 1. This feature may consume much memory. '
            . 'Using the --test mode with loglevel 6 or higher will give you informations about the memory usage.',
            //
            '--batch' => 'Flag; Not implemented yet. Run the job recusiv from given --path as start directory and '
            . 'start renaming. If a new configuration in the sub directories exists it trys to load the '
            . 'configuration for batch-mode and execute it. This enables --recursiv and --history',
            //
            '--plugins' => 'Not implemented yet. Semicolon separated list of plugins to include. Plugins to assist you'
            . 'for the renaming. Eg.: You have a text file including the new name of the file, or parts of it: '
            . 'The pluging gets the content and uses it befor or after the other rules take affect! '
            . 'Example: --plugins \'GetTheTitleFromVDRsInfoFile:before;CutAdvertising:after\'',
            //
            '--undo' => 'Flag; Revers/ undo the last action',
            //
            '--from-config:' => 'Read saved configuration from given path and execute it',
            //
            '--set-config' => 'disabled; see --save-config',
            //
            '--save-config' => 'Flag; Saves the configuration to the --path of the config which adds a new folder '
            . '".multirename" for later use with --from-config',
            //
            '--del-config' => 'Flag; Deletes the config from given --path',
            //
            '--show-config' => 'Flag; Shows the config parameters from a saved config to check or rebuild it. '
            . 'Use it with --from-config=/some/path/movies/ --show-config (see --save-config)',
            //
            '--loglevel|--ll:' => 'Logging level for the output of messages (0=Emerg ... 7=verbose/debug). '
            . 'For testing use 6 or 7; For cronjob etc. do not use lower than 5 to get important messages',
            //
            '--stats' => 'Print some stats after execution',
            //
            '--version|-v' => 'Flag; Return version informations',
        );

        if ( $shellOptions !== true ) {
            $res = array();
            foreach ( $result as $key => $value ) {
                $key = str_replace( ':', '', $key );
                $key = substr( $key, 2 );
                $pos = strpos( $key, '|' );
                if ( $pos ) {
                    $key = substr( $key, 0, $pos );
                }
                $res[$key] = $value;
            }
            $result = $res;
        }

        return $result;
    }


    /**
     * Returns a json encodeded string.
     *
     * @param mixed $content The value being encoded. Can be any type except a
     * resource. All string data must be UTF-8 encoded.
     * PHP implements a superset of JSON as specified in the original RFC 4627 -
     * it will also encode and decode scalar types and NULL. RFC 4627 only
     * supports these values when they are nested inside an array or an object.
     * Although this superset is consistent with the expanded definition of
     * "JSON text" in the newer RFC 7159 (which aims to supersede RFC 4627) and
     * ECMA-404, this may cause interoperability issues with older JSON parsers
     * that adhere strictly to RFC 4627 when encoding a single scalar value.
     * @param int $options [optional] Bitmask consisting of JSON_HEX_QUOT,
     * JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK,
     * JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT,
     * JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE,
     * JSON_PARTIAL_OUTPUT_ON_ERROR. The behaviour of these constants is
     * described on the JSON constants page.
     * @param int $depth [optional] Set the maximum depth. Must be greater than zero.
     *
     * @return string A JSON encoded string on success or FALSE on failure
     */
    public function toJson( $content, $options = null, $depth = 512 )
    {
        if ( empty( $depth ) ) {
            $depth = 512;
        }

        if ( $options ) {
            $jsonOptions = $options;
        } else {
            $jsonOptions = $this->_jsonOptions;
        }

        return json_encode( $content, $jsonOptions, $depth );
    }

}
