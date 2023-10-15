#!/usr/bin/env php
<?php declare(strict_types=1);

//chdir( __DIR__ );
require_once __DIR__ . '/bootstrap.php';
//echo 'xx:'.print_r($_SERVER, true).':xx'; -> PWD
$denyList = array('root', 'admin', 'administrator', 'sysadmin');
if ( in_array( strtolower( @$_SERVER['USER'] ), $denyList ) ) {
    $userList = '\'' . implode( '\', ', $denyList ) . '\'';
    $mesg = 'Something belongs to ' . $userList
        . ' Use a different user! Security exit.' . PHP_EOL;
    exit( $mesg );
}


//
// app config
$options = array(
    'debug' => false,// e.g: sets all loggers to max level, never checks maxfilesize
    'verbose' => true,
);

//$oContext = new Mumsys_Context_Item();

$loggerOpts = array(
    'logfile' => __DIR__ . '/../logs/' . basename( __FILE__ ) . '.log',
    'logLevel' => 7,
    'msglogLevel' => 7,
    'way' => 'a', // w=log only last run; def: a=append
    'maxfilesize' => (1024 * 1000 * 10),
    'debug' => $options['debug'],
    // for Mumsys_Logger_Decorator_Messages
    'msgLineFormat' => '%3$s' . "\t" . '%5$s', //'%5$s', //
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

// list of programs/ adapter this tools should use
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

    if ( $cliOptsResult === array()
        || isset( $cliOptsResult['help'] )
        || isset( $cliOptsResult['helplong'] ) ) {

        if ( $cliOptsResult === array() || isset( $cliOptsResult['help'] ) ) {
            echo $cliOpts->getHelp() . PHP_EOL;
        }

            if ( isset( $cliOptsResult['helplong'] ) ) {
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
