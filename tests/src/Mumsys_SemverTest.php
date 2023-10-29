<?php

/**
 * Mumsys_SemverTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2019 by Florian Blasel
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Semver
 */

/**
 * Mumsys_Semver Test
 *
 * Generated on 2019-11-10 at 17:27:40.
 */
class Mumsys_SemverTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Semver
     */
    private $_object;

    /**
     * @var array
     */
    private $_testVersions;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_testVersions = array(
            '1.0.0' => '1.2.3',
            '2.0.0' => '1.2.3-unknown-version',
        );
        $this->_object = new Mumsys_Semver( $this->_testVersions['2.0.0'] );
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        unset( $this->_object );
    }


    /**
     * Checks like in https://regex101.com/r/Ly7O1x/3/
     * @covers Mumsys_Semver::__construct
     * @covers Mumsys_Semver::validate
     */
    public function test_construct()
    {
        foreach ( $this->_testVersions as $ver => $testVersion ) {
            $actual = new Mumsys_Semver( $testVersion, $ver );
            $this->assertingInstanceOf( Mumsys_Semver::class, $actual );
        }

        // test exception for validation: invalid semver
        $this->expectingException( 'Mumsys_Exception' );
        $this->expectingExceptionMessage( 'Regex error for "1.2.3" or semver not available' );
        $actual = new Mumsys_Semver( '1.2.3', '0.0.1' );
        //$actual->validate( $listINValid[0] );
    }


    /**
     * Checks like in https://regex101.com/r/Ly7O1x/3/
     * @covers Mumsys_Semver::validate
     */
    public function testValidate()
    {
        /** @var array $listISValid Valid Semantic Versions */
        $listISValid = array(
            '0.0.4',
            '1.2.3',
            '10.20.30',
            '1.1.2-prerelease+meta',
            '1.1.2+meta',
            '1.1.2+meta-valid',
            '1.0.0-alpha',
            '1.0.0-beta',
            '1.0.0-alpha.beta',
            '1.0.0-alpha.beta.1',
            '1.0.0-alpha.1',
            '1.0.0-alpha0.valid',
            '1.0.0-alpha.0valid',
            '1.0.0-alpha-a.b-c-somethinglong+build.1-aef.1-its-okay',
            '1.0.0-rc.1+build.1',
            '2.0.0-rc.1+build.123',
            '1.2.3-beta',
            '10.2.3-DEV-SNAPSHOT',
            '1.2.3-SNAPSHOT-123',
            '1.0.0',
            '2.0.0',
            '1.1.7',
            '2.0.0+build.1848',
            '2.0.1-alpha.1227',
            '1.0.0-alpha+beta',
            '1.2.3----RC-SNAPSHOT.12.9.1--.12+788',
            '1.2.3----R-S.12.9.1--.12+meta',
            '1.2.3----RC-SNAPSHOT.12.9.1--.12',
            '1.0.0+0.build.1-rc.10000aaa-kk-0.1',
            '99999999999999999999999.999999999999999999.99999999999999999',
            '1.0.0-0A.is.legal'
        );
        /** @var array Invalid Semantic Versions */
        $listINValid = array(
            '1',
            '1.2',
            '1.2.3-0123',
            '1.2.3-0123.0123',
            '1.1.2+.123',
            '+invalid',
            '-invalid',
            '-invalid+invalid',
            '-invalid.01',
            'alpha',
            'alpha.beta',
            'alpha.beta.1',
            'alpha.1',
            'alpha+beta',
            'alpha_beta',
            'alpha.',
            'alpha..',
            'beta',
            '1.0.0-alpha_beta',
            '-alpha.',
            '1.0.0-alpha..',
            '1.0.0-alpha..1',
            '1.0.0-alpha...1',
            '1.0.0-alpha....1',
            '1.0.0-alpha.....1',
            '1.0.0-alpha......1',
            '1.0.0-alpha.......1',
            '01.1.1',
            '1.01.1',
            '1.1.01',
            '1.2',
            '1.2.3.DEV',
            '1.2-SNAPSHOT',
            '1.2.31.2.3----RC-SNAPSHOT.12.09.1--..12+788',
            '1.2-RC-SNAPSHOT',
            '-1.0.3-gamma+b7718',
            '+justmeta',
            '9.8.7+meta+meta',
            '9.8.7-whatever+meta+meta',
            '99999999999999999999999.999999999999999999.99999999999999999----'
            . 'RC-SNAPSHOT.12.09.1--------------------------------..12',
        );

        for ( $i = 0; $i < count( $listISValid ) - 1; $i++ ) {
            // valid checks
            if ( isset( $listISValid[$i] ) ) {
                $this->assertingTrue(
                    $this->_object->validate( $listISValid[$i] ),
                    "Valid list: $listISValid[$i] failed"
                );
            }
            // invalid check
            if ( isset( $listINValid[$i] ) ) {
                $this->assertingFalse(
                    $this->_object->validate( $listINValid[$i] ),
                    "Invalid list: $listINValid[$i] failed"
                );
            }
        }
    }


    /**
     * @covers Mumsys_Semver::getMajorID
     */
    public function testGetMajorID()
    {
        $actualA = $this->_object->getMajorID();
        $expectedA = 1;

        $objectB = new Mumsys_Semver( '', '2.0.0' );
        $actualB =  $objectB->getMajorID();
        $expectedB = null;

        $this->assertingEquals( $expectedA, $actualA );
        $this->assertingEquals( $expectedB, $actualB );
    }


    /**
     * @covers Mumsys_Semver::getMinorID
     */
    public function testGetMinorID()
    {
        $actualA = $this->_object->getMinorID();
        $expectedA = 2;

        $objectB = new Mumsys_Semver( '', '2.0.0' );
        $actualB =  $objectB->getMinorID();
        $expectedB = null;

        $this->assertingEquals( $expectedA, $actualA );
        $this->assertingEquals( $expectedB, $actualB );
    }


    /**
     * @covers Mumsys_Semver::getPatchID
     */
    public function testGetPatchID()
    {
        $actualA = $this->_object->getPatchID();
        $expectedA = 3;

        $objectB = new Mumsys_Semver( '', '2.0.0' );
        $actualB =  $objectB->getPatchID();
        $expectedB = null;

        $this->assertingEquals( $expectedA, $actualA );
        $this->assertingEquals( $expectedB, $actualB );
    }


    /**
     * @covers Mumsys_Semver::getPreRelease
     */
    public function testGetPreRelease()
    {
        $actualA = $this->_object->getPreRelease();
        $expectedA = 'unknown-version';

        $objectB = new Mumsys_Semver( '', '2.0.0' );
        $actualB =  $objectB->getPreRelease();
        $expectedB = null;

        $this->assertingEquals( $expectedA, $actualA );
        $this->assertingEquals( $expectedB, $actualB );
    }


    /**
     * @covers Mumsys_Semver::getBuildMetadata
     */
    public function testGetBuildMetadata()
    {
        $objectA = new Mumsys_Semver( '2.0.0-rc.1+build.123', '2.0.0' );
        $actualA = $objectA->getBuildMetadata();
        $expectedA = 'build.123';

        $objectB = new Mumsys_Semver( '', '2.0.0' );
        $actualB =  $objectB->getBuildMetadata();
        $expectedB = null;

        $this->assertingEquals( $expectedA, $actualA );
        $this->assertingEquals( $expectedB, $actualB );
    }


    /**
     * @covers Mumsys_Semver::getRawResult
     */
    public function testGetRawResult()
    {
        $actualA = $actualA = $this->_object->getRawResult();
        $expectedA = array(
            0 => '1.2.3-unknown-version',
            'major' => 1,
            1 => 1,
            'minor' => 2,
            2 => 2,
            'patch' => 3,
            3 => 3,
            'prerelease' => 'unknown-version',
            4 => 'unknown-version',
        );

        $this->assertingEquals( $expectedA, $actualA );
    }
}
