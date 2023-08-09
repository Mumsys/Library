<?php

/**
 * Mumsys_Context Test
 */
class Mumsys_Context_ItemTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Context_Item
     */
    private $_object;

    /**
     * @var string
     */
    private $_version;

    /**
     * @var array
     */
    private $_versions;

    /**
     * @var string
     */
    private $_logfile;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '3.0.0';
        $this->_versions = array(
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
            'Mumsys_Context_Item' => $this->_version,
        );
        $this->_logfile = '/tmp/' . basename( __FILE__ ) . '.log';
        $this->_object = new Mumsys_Context_Item();
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        @unlink( $this->_logfile );
    }


    /**
     * @covers Mumsys_Context_Item::_get
     */
    public function test_get()
    {
        $this->expectingExceptionMessageRegex( '/("Mumsys_Config_Interface" not set)/i' );
        $this->expectingException( 'Mumsys_Context_Exception' );
        $this->_object->getConfig();
    }


    /**
     * @covers Mumsys_Context_Item::getConfig
     * @covers Mumsys_Context_Item::registerConfig
     */
    public function testGetSetConfig()
    {
        $config = new Mumsys_Config( array('x' => 'y') );
        $this->_object->registerConfig( $config );

        $this->assertingInstanceOf( 'Mumsys_Config_Interface', $this->_object->getConfig() );
    }

    /**
     * @covers Mumsys_Context_Item::getPermissions
     * @covers Mumsys_Context_Item::registerPermissions
     */
//    public function testGetSetPermissions()
//    {
//        $oPerms = new Mumsys_Permissions_Console($this->_object, array('x' => 'y'));
//        $this->_object->registerPermissions($oPerms);
//
//        $this->assertingInstanceOf('Mumsys_Permissions_Console', $this->_object->getPermissions());
//        $this->assertingInstanceOf('Mumsys_Permissions_Interface', $this->_object->getPermissions());
//    }


    /**
     * @covers Mumsys_Context_Item::getSession
     * @covers Mumsys_Context_Item::registerSession
     * @covers Mumsys_Context_Item::_get
     * @covers Mumsys_Context_Item::_register
     * @runInSeparateProcess
     */
    public function testGetSetSession()
    {
        $session = new Mumsys_Session_Default();
        $this->_object->registerSession( $session );

        $this->assertingInstanceOf( 'Mumsys_Session_Interface', $this->_object->getSession() );
        $regex = '/("Mumsys_Session_Interface" already set)/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Context_Exception' );
        $this->_object->registerSession( $session );
    }


    /**
     * @covers Mumsys_Context_Item::getDatabase
     * @covers Mumsys_Context_Item::registerDatabase
     * @covers Mumsys_Context_Item::replaceDatabase
     * @covers Mumsys_Context_Item::_replace
     */
    public function testGetSetDatabase()
    {
        $options = array(
            'type' => 'None:None',
        );
        $odb = Mumsys_Db_Factory::getInstance( $this->_object, $options );
        $this->_object->registerDatabase( $odb );
        $this->_object->replaceDatabase( $odb );

        $this->assertingInstanceOf( 'Mumsys_Db_Driver_Interface', $this->_object->getDatabase() );
    }


    /** registerControllerFrontend???
     *
     * @covers Mumsys_Context_Item::getDisplay
     * @ co vers Mumsys_Context_Item::registerDisplay
     * @covers Mumsys_Context_Item::replaceDisplay
     * @covers Mumsys_Context_Item::_replace
     */
    public function testGetSetDisplay()
    {
        $displayA = new Mumsys_Mvc_Templates_Text_Default( $this->_object );
        $this->_object->replaceDisplay( $displayA );
        $actualA = $this->_object->getDisplay();

        $factory = new Mumsys_Mvc_Display_Factory( $this->_object );
        $displayB = $factory->load( array(), 'Text', 'Default' );
        $this->_object->replaceDisplay( $displayB );
        $actualB = $this->_object->getDisplay();

        $this->assertingInstanceOf( 'Mumsys_Mvc_Templates_Text_Default', $actualA );
        $this->assertingInstanceOf( 'Mumsys_Mvc_Display_Control_Abstract', $actualA );
        $this->assertingInstanceOf( 'Mumsys_Mvc_Templates_Text_Default', $actualB );
        $this->assertingInstanceOf( 'Mumsys_Mvc_Display_Control_Abstract', $actualB );
//
//        $this->expectingException( 'Mumsys_Context_Exception' );
//        $this->expectingExceptionMessageRegex( '/("Mumsys_Mvc_Display_Control_Interface" already set)/i' );
//        $this->_object->registerDisplay( $displayB );
    }


    /**
     * @covers Mumsys_Context_Item::getControllerFrontend
     * @covers Mumsys_Context_Item::registerControllerFrontend
     */
//    public function testGetSetControllerFrontend()
//    {
//        $obj = new Mumsys_Mvc_Templates_Text_Default($this->_object);
//        $this->_object->registerControllerFrontend($obj);
//        $actual1 = $this->_object->getControllerFrontend();
//
//        $this->assertingEquals($obj, $actual1);
//    }


    /**
     * @covers Mumsys_Context_Item::getTranslation
     * @covers Mumsys_Context_Item::registerTranslation
     */
    public function testGetSetTranslation()
    {
        $expected1 = new Mumsys_I18n_Default( 'de' );
        $this->_object->registerTranslation( $expected1 );
        $actual1 = $this->_object->getTranslation();

        $this->assertingEquals( $expected1, $actual1 );
    }


    /**
     * @covers Mumsys_Context_Item::getLogger
     * @covers Mumsys_Context_Item::registerLogger
     * @covers Mumsys_Context_Item::_get
     * @covers Mumsys_Context_Item::_register
     */
    public function testGetSetLogger()
    {
        $logger = new Mumsys_Logger_File( array('logfile' => $this->_logfile) );
        $this->_object->registerLogger( $logger );

        $this->assertingInstanceOf( 'Mumsys_Logger_Interface', $this->_object->getLogger() );

        $this->expectingExceptionMessageRegex( '/("Mumsys_Logger_Interface" already set)/' );
        $this->expectingException( 'Mumsys_Context_Exception' );
        $this->_object->registerLogger( $logger );
    }


    /**
     * @covers Mumsys_Context_Item::getRequest
     * @covers Mumsys_Context_Item::registerRequest
     * @covers Mumsys_Context_Item::replaceRequest
     * @covers Mumsys_Context_Item::_get
     * @covers Mumsys_Context_Item::_register
     */
    public function testGetSetRequest()
    {
        $object = new Mumsys_Request_Console();
        $this->_object->registerRequest( $object ); //4CC
        $this->_object->replaceRequest( $object ); //4CC

        $this->assertingInstanceOf( 'Mumsys_Request_Interface', $this->_object->getRequest() );

        $this->expectingException( 'Mumsys_Context_Exception' );
        $this->expectingExceptionMessageRegex( '/("Mumsys_Request_Interface" already set)/' );
        $this->_object->registerRequest( $object );
    }


    /**
     * @covers Mumsys_Context_Item::getGeneric
     * @covers Mumsys_Context_Item::registerGeneric
     */
    public function testGetSetGeneric()
    {
        $interface = 'stdClass';
        $value = new stdClass();
        $this->_object->registerGeneric( $interface, $value );

        $actual1 = $this->_object->getGeneric( $interface, false );
        $actual2 = $this->_object->getGeneric( 'notExists', false );

        $this->assertingEquals( $value, $actual1 );
        $this->assertingFalse( $actual2 );

        $regex = '/(Generic interface "imNotSetInterface" not found)/i';
        $this->expectingExceptionMessageRegex( $regex );
        $this->expectingException( 'Mumsys_Context_Exception' );
        $this->_object->getGeneric( 'imNotSetInterface', null );
    }


    /**
     * @covers Mumsys_Context_Item::getGeneric
     * @covers Mumsys_Context_Item::registerGeneric
     */
    public function testRegisterGerneicException()
    {
        $interface = 'badInterface';
        $value = new stdClass();

        $this->expectingExceptionMessageRegex( '/(Value does not implement the interface "badInterface")/i' );
        $this->expectingException( 'Mumsys_Context_Exception' );
        $this->_object->registerGeneric( $interface, $value );
    }


    /**
     * @covers Mumsys_Context_Item::__clone
     * @covers Mumsys_Context_Item::__destruct
     */
    public function test__clone()
    {
        $object = new Mumsys_Request_Console();
        $this->_object->registerRequest( $object ); //4CC
        $this->_object->replaceRequest( $object ); //4CC

        $cloneA = clone $this->_object;

        $this->assertingInstanceOf( 'Mumsys_Request_Interface', $this->_object->getRequest() );
        $this->assertingFalse( $this->_object === $cloneA );
    }


    /**
     * Version checks
     */
    public function testVersions()
    {
        $this->assertingEquals( $this->_version, $this->_object::VERSION );
        $this->checkVersionList( $this->_object->getVersions(), $this->_versions );
    }

//
//    /** Old?
//     * Test Versions
//     *
//     * @covers Mumsys_Context_Item::getVersion
//     * @covers Mumsys_Context_Item::getVersionID
//     * @covers Mumsys_Context_Item::getVersions
//     */
//    public function testGetVersion()
//    {
//        $message = 'A new version exists. You should have a look at '
//            . 'the code coverage to verify all code was tested and not only '
//            . 'all existing tests where checked!';
//        $this->assertingEquals( $this->_version, Mumsys_Config_File::VERSION, $message );
//
//        $this->assertingEquals( 'Mumsys_Context_Item ' . $this->_version, $this->_object->getVersion() );
//        $this->assertingEquals( $this->_version, $this->_object->getVersionID() );
//
//        $possible = $this->_object->getVersions();
//
//        foreach ( $this->_versions as $must => $value ) {
//            $message = 'Invalid: ' . $must . '::' . $value;
//            $this->assertingTrue( isset( $possible[$must] ), $message );
//            $this->assertingTrue( ( $possible[$must] == $value ), $message );
//        }
//    }

}
