<?php


/**
 * Mumsys_Service_VdrTest
 */
class Mumsys_Service_VdrTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Service_Vdr
     */
    protected $_object;

    /**
     * Context item with at least the logger object
     * @var Mumsys_Context_Item
     */
    private $_context;
//
//    /**
//     * @var array
//     */
//    private $_options;

    private $_logfile;
    private static $_isAvailable = true;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        if ( self::$_isAvailable !== true ) {
            $mesg = 'Repeated failure: This it not a bug!'
                . 'See previous message!';
            $this->markTestSkipped( $mesg );
        }

        $this->_context = MumsysTestHelper::getContext();
        $this->_logfile = MumsysTestHelper::getTestsBaseDir()
            . '/tmp/service_vdr.log';

        try {
            $this->_context->getLogger();
        }
        catch ( Exception $e ) {
            $logOptions = array('logfile' => $this->_logfile);
            $logger = new Mumsys_Logger_File( $logOptions );
            $this->_context->registerLogger( $logger );
        }

//        $this->_options = array();

        try {
            $this->_object = new Mumsys_Service_Vdr( $this->_context, 'localhost' );
        } catch ( Exception $ex ) {
            self::$_isAvailable = false;
            $message = 'Service error or service not available, skip test. '
                . 'Message: ' . $ex->getMessage();
            $this->markTestSkipped( $message );
        }
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        $this->_object->disconnect();

        if ( file_exists( $this->_logfile ) ) {
            unlink( $this->_logfile );
        }

        unset( $this->_object );
    }


    public function testSetup()
    {
        $this->assertingTrue( self::$_isAvailable );
    }


    /**
     * @covers Mumsys_Service_Vdr::__destruct
     */
    public function test__destruct()
    {
        $this->_object->__destruct();
        $actualA = $this->_object->isOpen();

        $this->assertingFalse( $actualA );
    }

    /**
     * @covers Mumsys_Service_Vdr::connect
     * @covers Mumsys_Service_Vdr::__construct
     */
    public function testConnect()
    {
        $this->_object->disconnect();

         $this->_object = new Mumsys_Service_Vdr( $this->_context );

        $actual1 = $this->_object->connect();
        $actual2 = $this->_object->disconnect();
        $actual3 = $this->_object->connect();

        $this->assertingTrue( $actual1 );
        $this->assertingTrue( $actual2 );
        $this->assertingTrue( $actual3 );
    }


    /**
     * @covers Mumsys_Service_Vdr::connect
     */
    public function testConnectException1()
    {
        $origA = ini_get( 'display_errors' );
        $origB = ini_get( 'error_reporting' );
        ini_set( 'display_errors', false );
        ini_set( 'error_reporting', 0 );

        $this->expectingException( 'Mumsys_Service_Exception' );
        $regex = '/(Connection to server "nohostexist" failt)/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object = new Mumsys_Service_Vdr( $this->_context, 'nohostexist', 666, 5 );

        ini_set( 'display_errors', $origA );
        ini_set( 'error_reporting', $origB );
    }


    /**
     * Get raw exception messages.
     * @covers Mumsys_Service_Vdr::connect
     */
    public function testConnectException2()
    {
        $origA = ini_get( 'display_errors' );
        $origB = ini_get( 'error_reporting' );
        ini_set( 'display_errors', true );
        ini_set( 'error_reporting', -1 );

        $this->expectingException( 'Mumsys_Service_Exception' );
        $regex = '/(fsockopen)(.*)(php_network_getaddresses)(.*)(getaddrinfo failed)(.*)(Name or service not known)/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object = new Mumsys_Service_Vdr( $this->_context, 'nohostexist', 666, 5 );

        ini_set( 'display_errors', $origA );
        ini_set( 'error_reporting', $origB );
    }


    /**
     * @covers Mumsys_Service_Vdr::disconnect
     */
    public function testDisconnect()
    {
        $actual2 = $this->_object->disconnect();
        $this->assertingTrue( $actual2 );
    }


    /**
     * Expect one or more existing channels.
     *
     * @covers Mumsys_Service_Vdr::execute
     */
    public function testExecute()
    {
        // epg data channel 2 (e.g. ZDF)
        $actual2 = $this->_object->execute( 'LSTE', 2 );

        // list some recordings. this can end up in a tomeout first because vdr
        // caches the results which can be a huge list
        $actual3 = $this->_object->execute( 'LSTR', 1 );

        $this->assertingTrue( ( count( $actual2 ) >= 1 ) );
        $this->assertingTrue( ( count( $actual3 ) == 1 ), 'cnt: ' . count( $actual3 ) );
    }


    /**
     * @covers Mumsys_Service_Vdr::execute
     */
    public function testExecuteException1()
    {
        $this->_object->disconnect();
        $regex = '/(Not connected)/i';
        $this->expectingException( 'Mumsys_Service_Exception' );
        $this->expectingExceptionMessageRegex( $regex );

        $this->_object->execute( 'SCAN' );
    }


    /**
     * @covers Mumsys_Service_Vdr::execute
     */
    public function testExecuteException2()
    {
        $regex = '/(Command unknown or not implemented yet. Exiting)/i';
        $this->expectingException( 'Mumsys_Service_Exception' );
        $this->expectingExceptionMessageRegex( $regex );

        $this->_object->execute( 'ImACommandThatNotExists' );
    }


    /**
     * @covers Mumsys_Service_Vdr::channelAdd
     * @covers Mumsys_Service_Vdr::channelDelete
     * @covers Mumsys_Service_Vdr::_channelStringGet
     * @covers Mumsys_Service_Vdr::_channelString2ItemGet
     */
    public function testChannelAdd()
    {
        // $str = 'TestChannel;NDR:000000001:TESTCHANNEL:T:0:1=2:3=deu@3,4=mis@4:5:6:7:8:9:0';
        $actual1 = $this->_object->channelAdd(
            'TestChannel', 'NDR', '000000001', 'TESTCHANNEL', 'T', 0, '1=2',
            '3=deu@3,4=mis@4', '5', '6', '7', '8', '9', '10'
        );

        $expected = array(
            'channel_id' => $actual1['channel_id'],
            'name' => 'TestChannel',
            'bouquet' => 'NDR',
            'frequency' => '1',
            'parameter' => 'TESTCHANNEL',
            'source' => 'T',
            'symbolrate' => '0',
            'VPID' => '1=2',
            'APID' => '3=deu@3,4=mis@4',
            'TPID' => '5',
            'CAID' => '6',
            'SID' => '7',
            'NID' => '8',
            'TID' => '9',
            'RID' => '10',
        );

        $this->assertingEquals( $expected, $actual1 );

        $delete = false;
        try {
            $this->_object->channelAdd(
                'TestChannel', 'NDR', '000000001', 'TESTCHANNEL', 'T', 0, '1=2',
                '3=deu@3,4=mis@4', '5', '6', '7', '8', '9', '10'
            );
        }
        catch ( Exception $exc )
        {
            $delete = $this->_object->channelDelete( $actual1['channel_id'] );

            $this->assertingEquals( '501', $exc->getCode() );
            $this->assertingEquals(
                'Channel settings are not unique', trim( $exc->getMessage() )
            );

        }

        $this->assertingTrue( $delete );
    }


    /**
     * @covers Mumsys_Service_Vdr::channelDelete
     */
    public function testChannelDeleteException()
    {
        $this->expectingException( 'Mumsys_Service_Exception' );
        $regex = '/(Invalid channel ID)/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->channelDelete( 0 );
    }


    /**
     * @covers Mumsys_Service_Vdr::channelsGet
     * @covers Mumsys_Service_Vdr::channelGet
     * @covers Mumsys_Service_Vdr::channelSearch
     * @covers Mumsys_Service_Vdr::_channelsGet
     * @covers Mumsys_Service_Vdr::channelItemCreate
     */
    public function testChannelsGet()
    {
        $channelsList = $this->_object->channelsGet();

        if ( count( $channelsList ) <= 0 ) {
            $this->markTestSkipped( 'No channels found. Pls check your vdr config. Skip test' );
        }
        // reverse checks: list to items
        foreach ( $channelsList as $id => $parts ) {
            $current = $this->_object->channelGet( $id );

            $this->assertingEquals( $current, $parts );
        }

        $chanSearch = $this->_object->channelSearch( 'sat' );
        if ( count( $chanSearch ) <= 0 ) {
            $this->markTestSkipped( 'No channels found. Pls check the search option in ' . __METHOD__ . '' );
        }

        foreach ( $chanSearch as $id => $parts ) {
            $this->assertingEquals( $channelsList[$id], $parts );
        }

        $regex = '/(Invalid channel parameter)/i';
        $this->expectingException( 'Mumsys_Service_Exception' );
        $this->expectingExceptionMessageRegex( $regex );
        $this->_object->channelSearch( 0 );
    }

//
//    /**
//     * @covers Mumsys_Service_Vdr::recordingGet
//     */
//    public function testRecordingGet()
//    {
//        $expectedKeys = array(
//            'channel_key'
//            ,'channel_name'
//            ,'event_id'
//            ,'timestamp'
//            ,'duration'
//            ,'e_tableid'
//            ,'e_version'
//            ,'description'
//            ,'subtitle'
//            ,'title'
//            //optional ,'advisory'
//            //optional ,'vps'
//            ,'genre'
//            //optional ,'extras'
//            ,'framerate'
//            ,'priority'
//            ,'lifetime'
//            //optional ,'notes'
//        );
//
//        $actual1 = $this->_object->recordingGet(1, false);
//
//        foreach ( $expectedKeys as $key ) {
//            if ( $key == 'extras' ) {
//                $extras = array('stream_kind', 'stream_type', 'stream_lang', 'stream_desc');
//                foreach ( $extras as $extr ) {
//                    $acEx = reset($actual1['extras']);
//                    $this->assertingTrue( isset($acEx[$extr]));
//                }
//            }
//
//            $this->assertingTrue( isset($actual1[$key]), 'Error: "'. $key . '" not set' );
//        }
//
//        $actual2 = $this->_object->recordingGet(1, true);
//        $this->assertingTrue(is_dir($actual2 . '/'), 'Directory "' . $actual2 . '" not found');
//
//        $regex = '/(Invalid recording ID)/i';
//        $this->expectingException('Mumsys_Service_Exception');
//        $this->expectingExceptionMessageRegex($regex);
//        $this->_object->recordingGet(0);
//    }
//
//    /**
//     * @covers Mumsys_Service_Vdr::recordingsGet
//     */
//    public function testRecordingsGet()
//    {
//
//    }
//
//
//
//
//    /**
//     * @covers Mumsys_Service_Vdr::timersGet
//     */
//    public function testTimersGet()
//    {
//
//    }
//
//
//    /**
//     * @covers Mumsys_Service_Vdr::timerRecordGet
//     */
//    public function testTimerRecordGet()
//    {
//
//    }
//
//
//    /**
//     * @covers Mumsys_Service_Vdr::_timerStringGet
//     */
//    public function testTimerStringGet()
//    {
//
//    }
//
//
//    /**
//     * @covers Mumsys_Service_Vdr::timerString2RecordGet
//     */
//    public function testTimerString2RecordGet()
//    {
//
//    }
//
//
//    /**
//     * @covers Mumsys_Service_Vdr::timerArray2StringGet
//     */
//    public function testTimerArray2StringGet()
//    {
//
//    }


    /**
     * @covers Mumsys_Service_Vdr::isOpen
     */
    public function testIsOpen()
    {
        $actual1 = $this->_object->isOpen();

        $this->_object->disconnect();
        $actual2 = $this->_object->isOpen();

        $this->assertingTrue( $actual1 );
        $this->assertingFalse( $actual2 );
    }

}
