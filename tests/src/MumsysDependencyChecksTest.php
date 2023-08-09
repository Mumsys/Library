<?php

/**
 * MumsysDependencyChecks Test
 */
class MumsysDependencyChecksTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * List of required extensions.
     * @var array
     */
    private $_requiredExtensions = array();

    /**
     * List of php.ini default values to be expected.
     * key/value pairs like phpini setting/ expected value/s
     * @var array
     */
    private $_requiredPhpiniSetup = array();


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $production = 'production';
        $testing = 'testing/ unit tests';

        $this->_requiredExtensions = array(
            'ctype' => $production,
            'curl' => $production,
            'date' => $production,
            'exif' => $production,
            'fileinfo' => $production,
            'filter' => $production,
            'gd' => $production,
            'pdo' => $production,
            'pdo_mysql' => $production,
            'pdo_sqlite' => $production,
            'sqlite3' => $production,
            'json' => $production,
            'iconv' => $production,
            'mbstring' => $testing,
            'session' => $production,
            'SPL' => $production,
        );

        $this->_requiredPhpiniSetup = array(
            /** @see http://php.net/manual/en/ini.core.php#default_charset */
            /** @see http://php.net/manual/en/function.htmlspecialchars.php */
            'default_charset' => array(
                // defaults
                'UTF-8', 'utf-8', 'ISO-8859-1', 'ISO-8859-5', 'ISO-8859-15', 'cp866',
                'cp1251', 'cp1252', 'KOI8-R', 'BIG5', 'GB2312', "BIG5-HKSCS",
                "Shift_JIS", 'EUC-JP', 'MacRoman',
                // aliases
                'ISO8859-1', 'ISO8859-5', 'ISO8859-15', 'ibm866', '866',
                'Windows-1251', 'win-1251', '1251', 'Windows-1252', '1252',
                'koi8-ru', 'koi8r', '950', '936', 'SJIS', 'SJIS-win', 'cp932',
                '932', 'EUCJP', 'eucJP-win'
            )
        );
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {

    }


    public function test_CheckPhpExtensions()
    {
        foreach ( $this->_requiredExtensions as $ext => $usage ) {
            $mesg = sprintf(
                '"%1$s" extension not installed/ found for: %2$s',
                $ext,
                $usage
            );
            $this->assertingTrue( extension_loaded( $ext ), $mesg );
        }
    }


    public function test_CheckPhpIniSettings()
    {
        $actual = $expected = null;

        foreach ( $this->_requiredPhpiniSetup as $iniValue => $possible ) {
            if ( !is_array( $possible ) ) {
                $possible = array($possible);
            }

            // once must fix
            foreach ( $possible as $expected ) {
                $actual = ini_get( $iniValue );

                if ( $actual == $expected ) {
                    break;
                }
            }

            $mesg = sprintf(
                'php.ini: "%1$s"; Expect one of "%2$s"; Found: "%3$s"',
                $iniValue,
                '"' . implode( '"|"', $possible ) . '"',
                $actual
            );
            $this->assertingEquals( $expected, $actual, $mesg );
        }
    }

}
