<?php declare( strict_types=1 );

/**
 * NameSpaceWithoutBench
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
 * Namespace test using NO namespaces Benchmark
 *
 * @BeforeMethods({"beforeBenchmark"})
 * @AfterMethods({"afterBenchmark"})
 *
 * @Iterations(3)
 */
class NameSpaceWithoutBench
    extends Mumsys_Benchmarks_Testcase
{
    public function beforeBenchmark(): void
    {
    }

    public function afterBenchmark(): void
    {
    }


    /**
     * Your benchmark: init a new stdClass
     *
     * @Subject
     *
     * @Warmup(1500)
     * @Revs(50000)
     */
    public function namespaceSimpleWithout()
    {
        new stdClass();
    }

}
