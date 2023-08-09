<?php

/**
 * Mumsys_Logger_Decorator_Messages Test
 */
class Mumsys_Logger_Decorator_MessagesTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Logger_Decorator_Messages
     */
    private $_object;

    /**
     * @var Mumsys_Logger_File
     */
    private $_logger;

    /**
     * @var string
     */
    private $_testsDir;

    /**
     * @var string
     */
    private $_logfile;

    /**
     * Logger options
     * @var array
     */
    private $_opts;

    /**
     * Version string.
     * @var string
     */
    private $_version;

    /**
     * @var array
     */
    private $_versions;


    protected function setUp(): void
    {
        $this->_version = '3.0.0';
        $this->_versions = array(
            'Mumsys_Logger_Decorator_Messages' => $this->_version,
            'Mumsys_Logger_Decorator_Abstract' => '3.0.0',
        );

        $this->_testsDir = MumsysTestHelper::getTestsBaseDir();
        $this->_logfile = $this->_testsDir . '/tmp/' . basename( __FILE__ ) . '.test';

        $this->_opts = $opts = array(
            'logfile' => $this->_logfile,
            'way' => 'a',
            'logLevel' => 7,
            'msglogLevel' => 999,
            'maxfilesize' => 1024 * 2,
            'msgLineFormat' => '%5$s',
        );
        $this->_logger = new Mumsys_Logger_File( $this->_opts );
        $this->_object = new Mumsys_Logger_Decorator_Messages( $this->_logger, $this->_opts );
    }


    protected function tearDown(): void
    {
        //@unlink($this->_logfile);
        unset( $this->_logger, $this->_object );
    }


    /**
     * Just for code coverge.
     * @covers Mumsys_Logger_Decorator_Messages::__construct
     */
    public function test_construct()
    {
        $object1 = new Mumsys_Logger_Decorator_Messages( $this->_logger, $this->_opts );

        $this->_opts['username'] = 'flobeeunit';
        $this->_opts['msgDatetimeFormat'] = 'H:i:s';
        $this->_opts['msgLineFormat'] = '[%3$s] %5$s';
        $this->_opts['debug'] = true;
        $this->_opts['verbose'] = true;
        $this->_opts['lf'] = " end\n";

        $object2 = new Mumsys_Logger_Decorator_Messages( $this->_logger, $this->_opts );

        $this->assertingInstanceOf( 'Mumsys_Logger_Interface', $object1 );
        $this->assertingInstanceOf( 'Mumsys_Logger_Decorator_Abstract', $object1 );
        $this->assertingInstanceOf( 'Mumsys_Logger_Decorator_Messages', $object1 );
        $this->assertingInstanceOf( 'Mumsys_Logger_Decorator_Interface', $object1 );
    }


    /**
     * @covers Mumsys_Logger_Decorator_Messages::__clone
     */
    public function test__clone()
    {
        $obj = clone $this->_object;
        $this->assertingInstanceOf( 'Mumsys_Logger_Decorator_Interface', $obj );
        $this->assertingInstanceOf( 'Mumsys_Logger_Decorator_Interface', $this->_object );
        $this->assertingNotSame( $obj, $this->_object );
    }


    /**
     * @covers Mumsys_Logger_Decorator_Messages::log
     * @covers Mumsys_Logger_Decorator_Messages::getMessageColored
     */
    public function testLog()
    {
        $this->_opts['username'] = 'flobeeunit';
        $this->_opts['msgDatetimeFormat'] = 'H:i';
        $this->_opts['msgLineFormat'] = '%1$s %2$s [%3$s] %5$s';
        $this->_opts['lf'] = " end\n";
        $this->_opts['msgColors'] = true;
        $object = new Mumsys_Logger_Decorator_Messages( $this->_logger, $this->_opts );

        $object->setMessageLoglevel( 1 );
        ob_start();
        $baseExpected = $object->log( 'bam', Mumsys_Logger_Abstract::ALERT );
        $actual = ob_get_clean();
        $expected = chr( 27 ) . '[41m'
            . date( 'H:i', time() ) . ' flobeeunit [ALERT] bam'
            . chr( 27 ) . '[0m'
            . ' end' . "\n";

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_Logger_Decorator_Messages::log
     */
    public function testLogException()
    {
        $this->_opts['username'] = 'flobeeunit';
        $this->_opts['msgDatetimeFormat'] = 'H:i';
        $this->_opts['msgLineFormat'] = '%1$s %2$s [%3$s] %5$s';
        $this->_opts['lf'] = " end\n";

        $object = new Mumsys_Logger_Decorator_Messages( $this->_logger, $this->_opts );
        ob_start();
        $mesg = new stdClass();
        $mesg->prop = 1;
        $code = Mumsys_Logger_Abstract::ALERT;
        $baseExpected = $object->log( $mesg, $code );
        $actual = ob_get_clean();

        $expected = date( 'H:i', time() ) . ' flobeeunit [ALERT] {"prop":1} end' . "\n";

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_Logger_Decorator_Messages::setMessageLoglevel
     */
    public function testSetMessageLoglevel()
    {
        $this->_opts['username'] = 'flobeeunit';
        $this->_opts['msgDatetimeFormat'] = 'H:i';
        $this->_opts['msgLineFormat'] = '%1$s %2$s [%3$s] %5$s';
        $this->_opts['lf'] = " end\n";
        $object = new Mumsys_Logger_Decorator_Messages( $this->_logger, $this->_opts );

        $object->setMessageLoglevel( 1 );
        ob_start();
        $baseExpected = $object->log( 'bam', Mumsys_Logger_Abstract::ALERT );
        $actual = ob_get_clean();
        $expected = date( 'H:i', time() ) . ' flobeeunit [ALERT] bam end' . "\n";

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_Logger_Decorator_Messages::setMessageLoglevel
     */
    public function testSetMessageLoglevelException()
    {
        $object = new Mumsys_Logger_Decorator_Messages( $this->_logger, $this->_opts );

        $regex = '/(Level "99" unknown to set the message log level)/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Logger_Exception' );
        $object->setMessageLoglevel( 99 );
    }


    /**
     * @covers Mumsys_Logger_Decorator_Messages::__construct
     * @covers Mumsys_Logger_Decorator_Messages::setMessageLogFormat
     * @covers Mumsys_Logger_Decorator_Messages::getColors
     * @covers Mumsys_Logger_Decorator_Messages::setColors
     * @covers Mumsys_Logger_Decorator_Messages::log
     * @covers Mumsys_Logger_Decorator_Messages::getMessageColored
     */
    public function testLogColored()
    {
        $this->_opts['username'] = 'flobeeunit';
        $this->_opts['msgColors'] = true;
        $decorator = new Mumsys_Logger_Decorator_Messages( $this->_logger, $this->_opts );

        $colorTemplate = chr( 27 ) . '%1$s%2$s' . chr( 27 ) . '[0m' . "\n";

        $this->_logger->setLogFormat( '%5$s' );
        $decorator->setMessageLogFormat( '%5$s' );

        $colors = $decorator->getColors();
        $colors[99] = '[47m';
        $decorator->setColors( $colors );
        $colors[98] = '[7m';
        foreach ( $colors as $level => $color ) {
            $message = 'level ' . $level;
            ob_start();
            $decorator->log( $message, $level );
            $actual = ob_get_clean();

            $expected = sprintf( $colorTemplate, $color, $message );
            $this->assertingEquals( $expected, $actual, 'error with level: ' . $level );
        }
    }


    /**
     * @covers Mumsys_Logger_Decorator_Messages::getColors
     * @covers Mumsys_Logger_Decorator_Messages::setColors
     */
    public function testGetSetColors()
    {
        $expected = $this->_object->getColors();
        $expected[99] = '[49m';
        $this->_object->setColors( $expected );

        $this->assertingEquals( $expected, $this->_object->getColors() );
    }


    /**
     * Version check
     */
    public function testVersion()
    {
        $this->assertingEquals(
            $this->_version, Mumsys_Logger_Decorator_Messages::VERSION
        );
        $this->assertingEquals(
            $this->_versions['Mumsys_Logger_Decorator_Abstract'],
            Mumsys_Logger_Decorator_Abstract::VERSION
        );
    }

}
