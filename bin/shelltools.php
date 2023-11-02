#!/usr/bin/env php
<?php declare(strict_types=1);

/**
 * shelltools.php Runner for mixed adapters of the ShellTools domain.
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2023 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  ShellTools
 * Created: 2023-08-10
 */

require_once __DIR__ . '/bootstrap.php';

$denyList = array('root', 'admin', 'administrator', 'sysadmin');
if ( in_array( strtolower( @$_SERVER['USER'] ), $denyList ) ) {
    $userList = '\'' . implode( '\', ', $denyList ) . '\'';
    $mesg = 'Something belongs to ' . $userList . PHP_EOL
        . ' Use a different user! Security exit.' . PHP_EOL;
    exit( $mesg );
}

//
// app config
$options = array(
    'debug' => false,// e.g: sets all loggers to max level, never checks maxfilesize
    'verbose' => true,
);

$loggerOpts = array(
    'logfile' => __DIR__ . '/../logs/' . basename( __FILE__ ) . '.log',
    'logLevel' => 7,
    'msglogLevel' => 7,
    'way' => 'a', // w=log only last run; def: a=append
    'maxfilesize' => ( 1024 * 1000 * 10 ),
    'debug' => $options['debug'],
    // for Mumsys_Logger_Decorator_Messages
    'msgLineFormat' => '%3$s' . "\t" . '%5$s',
    'msgColors' => false,
);

$oLoggerFile = new Mumsys_Logger_File( $loggerOpts );
$oLogger = new Mumsys_Logger_Decorator_Messages( $oLoggerFile, $loggerOpts );

if ( isset( $_SERVER['argv'][0] ) ) {
    $currentScript = (string) $_SERVER['argv'][0];
} else {
    $currentScript = basename( __FILE__ );
}
$oLogger->log( '--- Script start: "' . $currentScript . '" ---------', 7 );
$oLogger->log( 'Logfile goes to: ' . $loggerOpts['logfile'], 6 );
//$oLogger->log( 'Argv:', 7 );
//$oLogger->log( $_SERVER['argv'], 7 );

// list of programs/ adapter this tool should use
$adapterList = array(
    new Mumsys_ShellTools_Adapter_ExifFixTimestamps( $oLogger ), // exiftool
    new Mumsys_ShellTools_Adapter_ExifMeta2Filename( $oLogger ), // exiftool
    new Mumsys_ShellTools_Adapter_ExifFilename2Meta( $oLogger ), // exiftool
    new Mumsys_ShellTools_Adapter_FfmpegCutTrimVideo( $oLogger ), // ffmpeg
    new Mumsys_ShellTools_Adapter_ResizeImages( $oLogger ), // imagemagick: convert
    new Mumsys_ShellTools_Adapter_ResizeVideos( $oLogger ), // ffmpeg
//    new Mumsys_ShellTools_Adapter_ColorImagesToGrayscale( $oLogger ), // imagemagick, exiftool 4 thmbs
    //new Mumsys_ShellTools_Adapter_Demo( $oLogger ),
);
$oConfig = new Mumsys_Config_Default();
$oShellTools = new Mumsys_ShellTools_Default( $adapterList, $oLogger, $oConfig );

try {
    $cliOpts = new Mumsys_GetOpts( $oConfig->get( 'getopts' ) );
    $cliOptsResult = $cliOpts->getResult();

    // check if a global or local (action) help is requested
    $helpToShow = $cliOpts->getHelpCheckGlobalOrLocal();

    if ( $cliOptsResult === array()
        || isset( $cliOptsResult['help'] )
        || isset( $cliOptsResult['helplong'] )
        || $helpToShow !== null ) {

        $doHelp = ( $helpToShow || isset( $cliOptsResult['help'] ) );

        if ( $cliOptsResult === array() || $doHelp === true ) {
            echo $cliOpts->getHelp( $helpToShow ) . PHP_EOL;
        }

        if ( isset( $cliOptsResult['helplong'] ) ) {
            // $helpToShow --helplong need to be set in adapter options
            echo $cliOpts->getHelpLong() . PHP_EOL;
        }

        exit( 0 );

    } else {
        $oShellTools->validate( $cliOptsResult );
        if ( isset( $cliOptsResult['test'] ) ) {
            $oShellTools->execute( false );
        } else {
            $oShellTools->execute( true );
        }
    }
} catch ( Throwable $thex ) {
    throw $thex;
}
