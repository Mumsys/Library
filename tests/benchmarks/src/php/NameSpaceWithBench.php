<?php declare( strict_types=1 );

/**
 * NameSpaceWithBench
 * for MUMSYS / Multi User Management System (MUMSYS)
 *
 * @license GPL Version 3 http://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright Copyright (c) 2019 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Benchmarks
 */

namespace NameSpaceWithBench;

/**
 * Namespace test using namespaces Benchmark
 *
 * @BeforeMethods({"beforeBenchmark"})
 * @AfterMethods({"afterBenchmark"})
 *
 * @Iterations(3)
 * @ Iterations({1,5,10})
 */
class NameSpaceWithBench
    extends \Mumsys_Benchmarks_Testcase
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
    public function namespaceSimpleWith()
    {
        new \stdClass();
    }

}
