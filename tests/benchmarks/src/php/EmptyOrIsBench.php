<?php declare( strict_types=1 );

/**
 * EmptyOrIsBench
 * for MUMSYS / Multi User Management System (MUMSYS)
 *
 * @license GPL Version 3 http://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright Copyright (c) 2019 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Benchmarks
 */


/**
 * EmptyOrIsBench Benchmarks
 *
 * Compare performance of empty(), isset() and some common is_* functions.
 * A common discussion... to have an answer.
 *
 * empty() vs isset():
 * My result: When expecting something is not set (isset($x) === false): Best
 * way! Empty() takes ~25% more time.
 * Disable other test to compare to what you want to know.
 *
 *
 * @BeforeMethods({"beforeBenchmark"})
 * @AfterMethods({"afterBenchmark"})
 *
 * @Iterations(3)
 */
class EmptyOrIsBench
    extends Mumsys_Benchmarks_Testcase
{
    /**
     * @var int
     */
    private $_numIndexes;


    public function beforeBenchmark(): void
    {
        $this->_numIndexes = 2048;
    }


    public function afterBenchmark(): void
    {

    }


    /**
     * Test empty() is never true
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function emptyNever()
    {
        $result = 0;
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            if ( empty( 0 ) ) {
                $result = 1;
            }
        }

        unset( $result );
    }


    /**
     * Test empty() is always true
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function emptyAlways()
    {
        $result = 0;
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            if ( empty( 0 ) ) {
                $result = 1;
            }
        }

        unset( $result );
    }


    /**
     * Test isset() is never true
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function issetNever()
    {
        $result = null;
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            if ( isset( $result ) ) {
                $result = 1;
            }
        }

        unset( $result );
    }


    /**
     * Test isset() is always true
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function issetAlways()
    {
        $result = 0;
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            if ( isset( $result ) ) {
                $result = 1;
            }
        }

        unset( $result );
    }


    /**
     * Test is_scalar() is never true
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function isScalarNever()
    {
        $result = array();
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            if ( is_scalar( $result ) ) {
                $result = 1;
            }
        }

        unset( $result );
    }


    /**
     * Test is_scalar() is always true
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function isScalarAlways()
    {
        $result = 0;
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            if ( is_scalar( $result ) ) {
                $result = 1;
            }
        }

        unset( $result );
    }


    /**
     * Test is_array() is never true
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function isArrayNever()
    {
        $result = 1;
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            if ( is_array( $result ) ) {
                $result = 1;
            }
        }

        unset( $result );
    }


    /**
     * Test is_array() is always true
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function isArrayAlways()
    {
        $result = array();
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            if ( is_scalar( $result ) ) {
                $result = 1;
            }
        }

        unset( $result );
    }


    /**
     * Test is_string() is never true
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function isStringNever()
    {
        $result = 1;
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            if ( is_string( $result ) ) {
                $result = 1;
            }
        }

        unset( $result );
    }


    /**
     * Test is_string() is always true
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function isStringAlways()
    {
        $result = '1';
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            if ( is_string( $result ) ) {
                $result = 1;
            }
        }

        unset( $result );
    }


    /**
     * Test is_numeric() is never true
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function isNumericNever()
    {
        $result = 'a';
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            if ( is_numeric( $result ) ) {
                $result = 1;
            }
        }

        unset( $result );
    }


    /**
     * Test is_numeric() is always true
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function isNumericAlways()
    {
        $result = '1';
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            if ( is_numeric( $result ) ) {
                $result = 1;
            }
        }

        unset( $result );
    }


    /**
     * Test gettype() for analysis.
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function gettype()
    {
        $array = array(
            true,
            1,
            (double)1.2,
            (float)2.4,
            'a',
            array(),
            new stdClass(),
            null
        );
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            for ( $j = 0; $j < count( $array )-1; $j++ ) {
                if ( gettype( $array[$j] ) ) {
                    $test = 1;
                }
            }
        }

        unset( $array, $test );
    }

}
