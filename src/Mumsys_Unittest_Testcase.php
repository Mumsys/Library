<?php declare( strict_types=1 );

/**
 * Mumsys_Unittest_Testcase
 * for MUMSYS / Multi User Management System (MUMSYS)
 *
 * @license GPL Version 3 http://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright Copyright (c) 2015 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Unittest
 */


/**
 * PhpUnit test case class as wrapper for PHPUnit_Framework_TestCase.
 *
 * Helper for deprecated or removed methods to keep you informed.
 *
 * Wrapper methods are important to be used if phpunit changes methodes again
 * and again because of version/ incompatibility changes. Use these methodes
 * and keep attention to Mumsys_Unittest_Testcase_Interface to only use these
 * methods to avoid future conflicts. One does not need all the features
 * phpunit offers so keep the used method set small as possible to reduce
 * maintainance time.
 *
 * This interface has its own method set to be used instead of phpunit methods
 * to be more independent.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Unittest
 */
class Mumsys_Unittest_Testcase
    extends PHPUnit\Framework\TestCase
    implements Mumsys_Unittest_Testcase_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '3.13.2';

    /**
     * Methods memory container
     * @var array
     */
    private static $_methods = array();

    /**
     * PHPUNIT Version ID.
     * @var string
     */
    private $_phpunitVersionID = '';


    /**
     * Initialize the object and set some custom requirements to wrap around
     * different phpunit / php versions or methods.
     *
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct( $name = null, $data = array(), $dataName = '' )
    {
        parent::__construct( $name, $data, $dataName );

        $semver = new Mumsys_Semver( PHPUnit\Runner\Version::id() );

        $this->_phpunitVersionID = $semver->getMajorID() . '.' . $semver->getMinorID() . '.' . $semver->getPatchID();

        unset( $semver );
    }


    //
    // Wrapper/ fallback methods on top of phpunit
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
        $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false )
    {
        parent::assertEquals( $expected, $actual, $message, $delta, $maxDepth, $canonicalize, $ignoreCase );
    }


    /**
     * Alias of assertTrue() phpunit 8, 9
     *
     * @param mixed $condition
     * @param string $message
     * @return void
     */
    public function assertingTrue( $condition, $message = '' )
    {
        parent::assertTrue( $condition, $message );
    }


    /**
     * Alias of assertFalse() phpunit 8, 9
     *
     * @param mixed $condition
     * @param string $message
     * @return void
     */
    public function assertingFalse( $condition, $message = '' )
    {
        parent::assertFalse( $condition, $message );
    }


    /**
     * Alias of assertEmpty() phpunit 8, 9
     *
     * @param mixed $actual
     * @param string $message
     * @return void
     */
    public function assertingEmpty( $actual, $message = '' )
    {
        parent::assertEmpty( $actual, $message );
    }

    /**
     * Alias of assertNull() phpunit <7, 8, 9
     * @param mixed $actual
     * @param string $message Optional error message if test fails
     */
    public function assertingNull( $actual, $message = '' )
    {
        parent::assertNull( $actual, $message );
    }

    /**
     * Alias of assertRegExp(), assertMatchesRegularExpression() phpunit <7, 8, 9.
     * @param string $pattern
     * @param string $string
     * @param string $message
     */
    public function assertingRegExp( $pattern, $string, $message = '' )
    {
        parent::assertMatchesRegularExpression( $pattern, $string, $message );
    }


    /**
     * Check multiple regular expressions on a string.
     *
     * @param list $patternList List of key/ string pattern pairs where
     * key/index can contain an identifier/ hint for debuging output
     * @param string $content Content to test
     * @param string $message Message to output on failure. Use %1$ for the
     * array index/identifier and/or %2$s for the regex to get them in the error
     * string
     */
    public function assertingRegExpPlural( array $patternList, $content, $message = '' )
    {
        if ( empty( $message ) ) {
            $message = 'Content not found for case/index "%1$s" regex:"%2$s"';
        }

        foreach ( $patternList as $case => $regex ) {
            $this->assertTrue( ( preg_match( $regex, $content ) === 1 ), sprintf( $message, $case, $regex ) );
        }
    }


    /**
     * Alias of assertInstanceOf() phpunit 7, 8, 9
     * @param string $expected
     * @param mixed $actual
     * @param string $message
     */
    public function assertingInstanceOf( $expected, $actual, $message = '' )
    {
        parent::assertInstanceOf( $expected, $actual, $message );
    }


    /**
     * Alias of expectException() phpunit 8,9
     *
     * @param string $exception
     */
    public function expectingException( $exception )
    {
        parent::expectException( $exception );
    }


    /**
     * Alias of expectExceptionMessage() phpunit 8,9
     *
     * @param string $regex
     */
    public function expectingExceptionMessage( $regex )
    {
        parent::expectExceptionMessage( $regex );
    }


    /**
     * - Alias of expectExceptionMessageRegExp() phpunit 8
     * - Alias of expectExceptionMessageMatches() phpunit 9
     *
     * @param string $regex
     */
    public function expectingExceptionMessageRegex( $regex )
    {
        // A >= B
        if ( version_compare( $this->_phpunitVersionID, '9.0.0', '>=' ) ) {
            $this->expectExceptionMessageMatches( $regex );
        } else {
            parent::expectExceptionMessageRegExp( $regex );
        }
    }


    //
    // test helper: methods names start with a '_' eg.: _methodName()
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
    public function checkVersionList( $allList, $myList )
    {
        foreach ( $myList as $className => $version ) {
            $test = ( $allList[$className] === $version );
            $message = 'Failure: ' . $className . ':' . $version . ' !== ' . $allList[$className];
            $this->assertTrue( $test, $message );
        }

        return true;
    }


    /**
     * Checks for available classes.
     *
     * @param array $list List of classes, interfaces, abstract classes to be
     * checked
     * @param array $myList List of expected classes, interfaces...
     * @return boolean True on success
     */
    public function checkClassList( $list, $myList )
    {
        foreach ( $myList as $className ) {
            $this->assertTrue(
                isset( $list[$className] ), $className . ' not found/ exists'
            );
        }

        return true;
    }


    /**
     * Helper to test methods behind the scene (private, protected methods)
     *
     * Makes accessibility on to be testable.
     *
     * @param string $class Name of the class
     * @param string $method Name of the method to access
     *
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    public function getReflectionMethod( string $class, string $method = 'Unknown' )
    {
        $object = new ReflectionClass( $class );
        $action = $object->getMethod( $method );
        $action->setAccessible( true );

        return $method;
    }

}
