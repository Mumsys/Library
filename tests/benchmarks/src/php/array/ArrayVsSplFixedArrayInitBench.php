<?php declare( strict_types=1 );

/**
 * ArrayVsSplFixedArrayInitBench
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
 * ArrayVsSplFixedArrayInitBench Benchmarks
 *
 * The task: Find out how many...
 * - memory takes it to register an array
 * - how fast
 * - try beeing fair to each init
 *
 * @BeforeMethods({"beforeBenchmark"})
 * @AfterMethods({"afterBenchmark"})
 *
 * @Iterations(3)
 */
class ArrayVsSplFixedArrayInitBench
    extends Mumsys_Benchmarks_Testcase
{
    /**
     * @var int
     */
    private $_numIndexes;

    /**
     * @var array
     */
    private $_testArray;


    public function beforeBenchmark(): void
    {
        $this->_numIndexes = 2048;
        $this->_testArray = array_fill( 0, $this->_numIndexes, null );
    }


    public function afterBenchmark(): void
    {
        unset( $this->_numIndexes, $this->_testArray );
    }


    /**
     * Using SplFixedArray
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function arrayFillSplFixedArray()
    {
        $test = new SplFixedArray( $this->_numIndexes );

        unset( $test );
    }


    /**
     * Using array_fill
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function arrayFill()
    {
        $test = array_fill( 0, $this->_numIndexes, null );

        unset( $test );
    }


    /**
     * Using a for loop to register
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function arrayFillFor()
    {
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            $test[$i] = null;
        }

        unset( $test );
    }


    /**
     * Using a foreach loop to register from an existing array
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function arrayFillForeach()
    {
        foreach ( $this->_testArray as $i => &$null ) {
            $test[$i] = null;
        }

        unset( $test );
    }


    /**
     * Using a while loop to register
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function arrayFillWhile()
    {
        $i = 0;
        while ( $i < $this->_numIndexes ) {
            $test[$i] = null;
            $i++;
        }

        unset( $test );
    }

}
