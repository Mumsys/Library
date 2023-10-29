<?php

/**
 * Mumsys_Unittest_Testcase_Interface
 * for MUMSYS / Multi User Management System (MUMSYS)
 *
 * @license GPL Version 3 http://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright Copyright (c) 2021 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Unittest
 */


/**
 * PhpUnit test case interface as wrapper for PHPUnit_Framework_TestCase.
 *
 * Helper for deprecated or removed methodes to keep you informed and to manage
 * changes here instead in all of your code.
 * More lasy restrictions for lower phpunit/ php versions.
 *
 * Wrapper methodes are important to be used if phpunit changes methodes again
 * and again because of version/ incompatibility changes. Use these methodes
 * and keep attention to this interface to only use these methodes to avoid
 * future conflicts or to solve them more quickly.
 * One does not need all the features of phpunit. So keep the use of method set
 * as small as possible to reduce future conflicts, be more on the php level to
 * reduce maintainance and bugfixing time over the time.
 *
 * eg:
    possible: $this->assertArrayHasKey( $key, $array )
    php way : $this->assertTrue( array_key_exists( $key, $array ) , 'Key not found');
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Unittest
 */
interface Mumsys_Unittest_Testcase_Interface
{
    //
    // Wrapper methods on top of phpunit
    //


    /**
     * Alias of assertEquals() phpunit 8, 9
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     * @param float $delta
     * @param int $maxDepth
     * @param bool $canonicalize
     * @param bool $ignoreCase
     */
    public function assertingEquals( $expected, $actual, $message = '',
        $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false );


    /**
     * Alias of assertNotEquals() phpunit 8, 9
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     * @param float $delta
     * @param int $maxDepth
     * @param bool $canonicalize
     * @param bool $ignoreCase
     */
    public function assertingNotEquals( $expected, $actual, $message = '',
        $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false );


    /**
     * Alias of assertTrue() phpunit 8, 9
     *
     * @param mixed $condition
     * @param string $message
     * @return void
     */
    public function assertingTrue( $condition, $message = '' );


    /**
     * Alias of assertFalse() phpunit 8, 9
     *
     * @param mixed $condition
     * @param string $message
     * @return void
     */
    public function assertingFalse( $condition, $message = '' );


    /**
     * Alias of assertEmpty() phpunit 8, 9
     *
     * @param mixed $actual
     * @param string $message
     * @return void
     */
    public function assertingEmpty( $actual, $message = '' );


    /**
     * Alias of assertNull() phpunit <7, 8, 9
     * @param mixed $actual
     * @param string $message Optional error message if test fails
     */
    public function assertingNull( $actual, $message = '' );


    /**
     * Alias of assertRegExp(), assertMatchesRegularExpression() phpunit <7, 8, 9.
     * @param string $pattern
     * @param string $string
     * @param string $message
     */
    public function assertingRegExp( $pattern, $string, $message = '' );


    /**
     * Check multiple regular expressions in a string.
     *
     * @param list $patternList List of key/ pattern pairs where key/index can contain an
     * identifier/ hint for debuging output
     * @param string $content Content/ string to test for a match
     * @param string $message Message to output on failure. Use %1$ for the array index/
     * identifier and/or %2$s for the regex to get them in the error string
     */
    public function assertingRegExpPlural( array $patternList, $content, $message = '' );


    /**
     * Alias of assertInstanceOf() phpunit 7, 8, 9
     * @param string $expected
     * @param mixed $actual
     * @param string $message
     */
    public function assertingInstanceOf( $expected, $actual, $message = '' );


    /**
     * Alias of phpunit 8,9 expectException()
     *
     * @param string $exception
     */
    public function expectingException( $exception );


    /**
     * Alias of expectExceptionMessage() phpunit 8, 9
     *
     * @param string $exceptionMessage
     */
    public function expectingExceptionMessage( $exceptionMessage );


    /**
     * - Alias of expectExceptionMessageRegExp() phpunit 8
     * - Alias of expectExceptionMessageMatches() phpunit 9
     *
     * @param string $regex
     */
    public function expectingExceptionMessageRegex( $regex );


    //
    // test helper for the mumsys project
    //


    /**
     * Checks for available class versions.
     *
     * Check for Mumsys_Abstract::getVersions()
     *
     * @param array $allList List of loaded class versions @see
     * Mumsys_Abstract::getVersions()
     * @param array $myList List of expected versions
     *
     * @return boolean Returns true on success
     */
    public function checkVersionList( $allList, $myList );


    /**
     * Checks for available classes.
     *
     * @param array $list List of classes, interfaces, abstract classes to be
     * checked
     * @param array $myList List of expected classes, interfaces...
     * @return boolean True on success
     */
    public function checkClassList( $list, $myList );


    /**
     * Helper to test methods behind the scene (private, protected methods)
     *
     * @param string $className Name of the class
     * @param string $methodName Name of the method to access
     *
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    public function getReflectionMethod( string $className, string $methodName = 'Unknown' );

}
