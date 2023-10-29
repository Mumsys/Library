<?php declare( strict_types=1 );

/**
 * DemoBench
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
 * DemoBench Benchmarks
 *
 * Skeleton test to add a new features/ benchmark tests. Take care using correct
 * annotations.
 *
 * @BeforeMethods({"beforeBenchmark"})
 * @AfterMethods({"afterBenchmark"})
 *
 * @Iterations(3)
 * @ Iterations({1,5,10})
 */
class DemoBench
    extends Mumsys_Benchmarks_Testcase
{
    /**
     * @var stdClass
     */
    private $_object;


    public function beforeBenchmark(): void
    {
        $this->_object = new stdClass();
    }


    public function afterBenchmark(): void
    {
        unset( $this->_object );
    }


    /**
     * Your benchmark: init a new stdClass
     *
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     */
    public function initPhpStdClass()
    {
        new stdClass();
    }

}
