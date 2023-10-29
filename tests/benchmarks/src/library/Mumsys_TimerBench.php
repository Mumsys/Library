<?php declare( strict_types=1 );

/**
 * Mumsys_TimerBench
 * for MUMSYS / Multi User Management System (MUMSYS)
 *
 * @license GPL Version 3 http://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright Copyright (c) 2019 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Timer
 */


/**
 * Mumsys_TimerBench Benchmarks
 *
 * @BeforeMethods({"beforeBenchmark"})
 * @AfterMethods({"afterBenchmark"})
 *
 * @Iterations(3)
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Timer
 */
class Mumsys_TimerBench
    extends Mumsys_Benchmarks_Testcase
{
    /**
     * @var Mumsys_Timer
     */
    private $_object;


    public function beforeBenchmark(): void
    {
        $this->_object = new Mumsys_Timer( true );
    }


    public function afterBenchmark(): void
    {
        unset( $this->_object );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Timer::__construct
     * @covers Mumsys_Timer::start
     */
    public function initAndStart()
    {
        new Mumsys_Timer( true );
    }


    /**
     * @Subject
     *
     * @Revs(10000)
     *
     * @covers Mumsys_Timer::__construct
     * @covers Mumsys_Timer::start
     */
    public function initThanStart()
    {
        $object = new Mumsys_Timer( false );
        // not really fair because at a later time you want to start collecting
        // the time using start()
        $object->start();
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Timer::__construct
     * @covers Mumsys_Timer::start
     */
    public function initAndStartWithTime()
    {
        new Mumsys_Timer( (float)$_SERVER['REQUEST_TIME_FLOAT'] );
    }


    /**
     * @Subject
     *
     * @Revs(10000)
     *
     * @covers Mumsys_Timer::__construct
     * @covers Mumsys_Timer::startTimeSet
     */
    public function initThanStartWithTime()
    {
        $object = new Mumsys_Timer( false );
        $object->startTimeSet( (float) $_SERVER['REQUEST_TIME_FLOAT'] );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Timer::__construct
     * @covers Mumsys_Timer::start
     * @covers Mumsys_Timer::stop
     */
    public function initAndStartStop()
    {
        $object = new Mumsys_Timer( true );
        $object->stop();
    }


    /**
     * @Subject
     *
     * @Revs(10000)
     *
     * @covers Mumsys_Timer::__construct
     * @covers Mumsys_Timer::start
     * @covers Mumsys_Timer::stop
     */
    public function initThanStartStop()
    {
        $object = new Mumsys_Timer( false );
        $object->start();
        $object->stop();
    }


    /**
     * @Subject
     *
     * @Revs(10000)
     *
     * @covers Mumsys_Timer::__construct
     * @covers Mumsys_Timer::startTimeSet
     * @covers Mumsys_Timer::stop
     */
    public function initStartWithTimeStop()
    {
        $object = new Mumsys_Timer( (float) $_SERVER['REQUEST_TIME_FLOAT'] );
        $object->stop();
    }

}
