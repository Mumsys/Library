<?php declare( strict_types=1 );

/**
 * ConcatOrSprintfBench
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
 * Concat Or Sprintf Benchmarks
 *
 * Just to compare the performance. concatination seems to be speedy but sprintf
 * is more fail save for several options. So now you know: Unknown, dynamic,
 * insceure content: sprintf; Known content, secure: concat stuff. +50% speedup.
 *
 * @BeforeMethods({"beforeBenchmark"})
 * @AfterMethods({"afterBenchmark"})
 *
 * @Iterations(3)
 */
class ConcatOrSprintfBench
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
     * Test concatination of strings.
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function concat()
    {
        $file = __FILE__;
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            $str = 'location/' . $file . '.' . $file . '.' . PATH_SEPARATOR;
        }

        unset( $str );
    }


    /**
     * Test concatination of strings using sprintf.
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function sprintf()
    {
        $file = __FILE__;
        for ( $i = 0; $i < $this->_numIndexes; $i++ ) {
            $str = sprintf(
                'location/%1$s.%2$s.%3$s', $file, $file, PATH_SEPARATOR
            );
        }

        unset( $str );
    }

}
