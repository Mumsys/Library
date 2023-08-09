<?php

/**
 * Test class for Mumsys_Array2Xml.
 * $Id: Mumsys_Array2XmlTest.php 3254 2016-02-09 20:57:53Z flobee $
 */
class Mumsys_Array2Xml_DefaultTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Array2Xml_Default
     */
    private $_object;

    /**
     * Test object using cache properties.
     * @var Mumsys_Array2Xml_Default
     */
    private $_object2;


    /**
     * @var string
     */
    private $_version;

    /**
     * @var array
     */
    protected $_versions;

    /**
     * @var string
     */
    private $_refxmldata;

    /**
     * @var array
     */
    private $_xmldata;

    /**
     * @var array
     */
    private $_objectoptions;

    /**
     * @var array
     */
    private $_objectoptions2;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_version = '1.0.3';
        $this->_versions = array(
            'Mumsys_Array2Xml_Default' => $this->_version,
            'Mumsys_Array2Xml_Abstract' => '1.0.0',
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
        );

        $lf = "\n";
        $this->_refxmldata = '<?xml version="1.0" encoding="iso-8859-1" ?>' . $lf
            . '<root version="flobee v 0.1" name="Array2Xml Creator">' . $lf
            . '<album id="123" code="First Attribute Value">' . $lf
            . '<tracks key="456">' . $lf
            . '<track id="10001">' . $lf
            . '<name>A name</name>' . $lf
            . '<file>/some/file/file.mp3</file>' . $lf
            . '</track>' . $lf
            . '<track id="10002">' . $lf
            . '<name>A name 2</name>' . $lf
            . '<file>/some/file/file2.mp3</file>' . $lf
            . '</track>' . $lf
            . '</tracks>' . $lf
            . '</album>' . $lf
            . '</root>' . $lf
        ;
        $arr = array();
        $arr['nodeName'] = 'root';
        $arr['nodeAttr']['version'] = 'flobee v 0.1';
        $arr['nodeAttr']['name'] = 'Array2Xml Creator';
        // first main tree
        $arr['nodeValues'][0]['nodeName'] = 'album';
        $arr['nodeValues'][0]['nodeAttr']['id'] = 123;
        $arr['nodeValues'][0]['nodeAttr']['code'] = 'First Attribute Value';
        // new node
        $arr['nodeValues'][0]['nodeValues'][0]['nodeName'] = 'tracks';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeAttr']['key'] = '456';
        // new sub node
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeName'] = 'track';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeAttr']['id'] = '10001';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeName'] = 'name';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeValues'] = 'A name';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeValues'][1]['nodeName'] = 'file';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeValues'][1]['nodeValues'] = '/some/file/file.mp3';
        // new sub node
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][1]['nodeName'] = 'track';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][1]['nodeAttr']['id'] = '10002';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][1]['nodeValues'][0]['nodeName'] = 'name';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][1]['nodeValues'][0]['nodeValues'] = 'A name 2';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][1]['nodeValues'][1]['nodeName'] = 'file';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][1]['nodeValues'][1]['nodeValues'] = '/some/file/file2.mp3';

        $this->_xmldata = $arr;

        $this->_objectoptions = array(
            'data' => $this->_xmldata,
            'charset_from' => 'iso-8859-1',
            'charset_to' => 'iso-8859-1',
            'version' => '1.0',
            'cdata_escape' => true,
            'debug' => true,
            'tag_case' => Mumsys_Array2Xml_Default::TAG_CASE_LOWER,
            'spacer' => "", // \t
            'linebreak' => "\n", // \n
            'cache' => false,
            'ID' => array(
                'NN' => 'nodeName',
                'NA' => 'nodeAttr',
                'NV' => 'nodeValues'
            ),
        );

        $this->_objectoptions2 = $this->_objectoptions;
        $this->_objectoptions2['cachefile'] = MumsysTestHelper::getTestsBaseDir()
            . '/tmp/cachefile.'
            . basename( __FILE__ ) . '.tmp';
        $this->_objectoptions2['cache'] = true;

        $writerOpts = array(
            'file' => $this->_objectoptions2['cachefile'],
            'way' => 'a+'
        );
        $writer = new Mumsys_File( $writerOpts );
        $this->_object2 = new Mumsys_Array2Xml_Default( $this->_objectoptions2 );
        $this->_object2->setWriter( $writer );
        // final init
        $this->_object = new Mumsys_Array2Xml_Default( $this->_objectoptions );
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        // dont do! see __destruct
        //$this->_object = null;
        //$this->_object2 = null;
    }


    public function __destruct()
    {
        // cleanup cache files
        $list = array($this->_object->getCacheFile(), $this->_object2->getCacheFile());
        foreach ( $list as $file ) {
            if ( file_exists( $file ) ) {
                unlink( $file );
            }
        }
        unset( $this->_object2, $this->_object );
    }


    // --- check defaults


    public function testVersionsAndConstants()
    {
        $this->assertingEquals( 0, Mumsys_Array2Xml_Abstract::TAG_CASE_LOWER );
        $this->assertingEquals( 1, Mumsys_Array2Xml_Abstract::TAG_CASE_UPPER );
        $this->assertingEquals( -1, Mumsys_Array2Xml_Abstract::TAG_CASE_AS_IS );

        $this->assertingEquals( $this->_version, Mumsys_Array2Xml_Default::VERSION );
        $this->checkVersionList(
            $this->_object->getVersions(),
            $this->_versions
        );
    }

    // --- check abstract


    /**
     * @covers Mumsys_Array2Xml_Abstract::__construct
     */
    public function test_construct()
    {
        $this->expectingException( 'Mumsys_Array2Xml_Exception' );
        $this->expectingExceptionMessage( 'Invalid tag case' );
        $this->_objectoptions['tag_case'] = '69';
        new Mumsys_Array2Xml_Default( $this->_objectoptions );
    }


    /**
     * @covers Mumsys_Array2Xml_Abstract::buffer
     */
    public function testBuffer()
    {
        $this->_object->buffer( 'test' );
        $this->_object2->buffer( 'test' );
        $current = file_get_contents( $this->_object2->getCacheFile() );

        /* In some cases 'testtest' come out. Cache was not cleand correctly.
         * This happens if a test ends before __destruct() executed here. E.g:
         * Also if you break/ end tests while running */
        $this->assertingEquals( 'test', $current );
        $this->assertingEquals(
            $this->_objectoptions2['cachefile'], $this->_object2->getCacheFile()
        );

        // test buffer exception
        $this->_object->setCache( true );
        $this->expectingException( 'Mumsys_Array2Xml_Exception' );
        $this->expectingExceptionMessage( 'Can not buffer. Writer not set' );
        $this->_object->buffer( 'test' );
    }


    /**
     * @covers Mumsys_Array2Xml_Abstract::getWriter
     * @covers Mumsys_Array2Xml_Abstract::setWriter
     */
    public function testGetSetWriter()
    {
        $actual1 = $this->_object->getWriter();
        $actual2 = $this->_object2->getWriter();
        $this->_object2->setWriter( $actual2 ); // 4 CC

        $this->assertingNull( $actual1 );
        $this->assertingInstanceOf( 'Mumsys_Logger_Writer_Interface', $actual2 );
        $this->assertingInstanceOf( 'Mumsys_File', $actual2 );
    }


    /**
     * @covers Mumsys_Array2Xml_Abstract::setWriter
     */
    public function testSetWriterException1()
    {
        $this->expectingException( 'Error' );
        $regex = '/((must be of type Mumsys_Logger_Writer_Interface, Mumsys_Array2Xml_Default given)'
            . '|(must implement interface Mumsys_Logger_Writer_Interface))/i';

        $this->expectingExceptionMessageRegex( $regex );

        $this->_object2->setWriter( $this->_object );
    }


    /**
     * @covers Mumsys_Array2Xml_Abstract::setWriter
     */
    public function testSetWriterException2()
    {
        $loc = '/tmp/not-exists/no-nothing';

        $this->_objectoptions2['cachefile'] = $loc;
        $this->_objectoptions2['cache'] = true;

        $this->expectingException( 'Mumsys_File_Exception' );
        $message = 'Can not open file "/tmp/not-exists/no-nothing" with mode '
            . '"w". Directory is writeable: "No", readable: "No".';
        $this->expectingExceptionMessage( $message );

        $this->_object2 = new Mumsys_Array2Xml_Default( $this->_objectoptions2 );
        $writerOpts = array(
            'file' => $this->_objectoptions2['cachefile'],
            'way' => 'w'
        );
        $writer = new Mumsys_File( $writerOpts );
        $this->_object2->setWriter( $writer );
        $this->_object2->buffer( 'flobee' );
    }

    // --- check default class


    /**
     * @covers Mumsys_Array2Xml_Default::__toString
     */
    public function testToString()
    {
        $actual = $this->_object->__toString();
        $this->assertingEquals( $this->_refxmldata, $actual );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::getCache
     * @covers Mumsys_Array2Xml_Default::setCache
     */
    public function testGetSetCache()
    {
        $this->_object->setCache( true );
        $this->_object2->setCache( false );
        $this->assertingFalse( $this->_object2->getCache() );
        $this->assertingTrue( $this->_object->getCache() );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::getCacheFile
     * @covers Mumsys_Array2Xml_Default::setCacheFile
     */
    public function testGetSetCacheFile()
    {
        $loc = '/tmp/flobeewashere';
        $this->_object->setCacheFile( $loc );
        $this->_object2->setCacheFile( $loc );
        $this->assertingEquals( $loc, $this->_object->getCacheFile() );
        $this->assertingEquals( $loc, $this->_object2->getCacheFile() );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::setIdentifier
     * @covers Mumsys_Array2Xml_Default::getIdentifier
     */
    public function testGetSetIdentifier()
    {
        $actual = $this->_object->getIdentifier();
        $expected = $this->_objectoptions['ID'];

        $newcfg = array('NN' => 'nodeName', 'NA' => 'nodeAttr', 'NV' => 'nodeValues');
        $this->_object->setIdentifier( $newcfg );

        // test for error
        $this->_object->setIdentifier( array('bla' => 'bla bla bla') );
        $error = array(
            'Error setIdentifier! Empty value or ID not found/wrong: $key: '
            . '"bla", v: "bla bla bla"'
        );

        $this->assertingEquals( $expected, $actual );
        $this->assertingEquals( $newcfg, $this->_object->getIdentifier() );

        $this->assertingEquals( $error, $this->_object->getError() );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::getEncoding
     * @covers Mumsys_Array2Xml_Default::setEncoding
     */
    public function testGetSetEncoding()
    {
        $oldEnc = $this->_object->getEncoding();
        $this->_object->setEncoding( 'iso-8859-1', 'utf-8' );
        $newEnc = $this->_object->getEncoding();

        $this->assertingEquals( $this->_objectoptions['charset_from'], $oldEnc['charset_from'] );
        $this->assertingEquals( $this->_objectoptions['charset_to'], $oldEnc['charset_to'] );

        $this->assertingEquals( 'iso-8859-1', $newEnc['charset_from'] );
        $this->assertingEquals( 'utf-8', $newEnc['charset_to'] );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::getRoot
     * @covers Mumsys_Array2Xml_Default::setRoot
     * @covers Mumsys_Array2Xml_Default::_mkroot
     */
    public function testGetSetRoot()
    {
        $actual1 = $this->_object->getRoot();
        $expected1 = array();

        $data = array();
        // just with values wich are not needed/handled
        $data['nodeValues'] = array(array('nodeName' => 'music'));
        $this->_object->setRoot( $data );
        $actual2 = $this->_object->getRoot();
        $this->_object->free();
        $expected2 = array('', '');

        //with no node name
        $data['nodeAttr']['version'] = 'V109';
        $data['nodeAttr']['datetime'] = '2010-07-31 23:58:59';
        $data['nodeAttr']['generator'] = 'Array2Xml Creator';
        $this->_object->setRoot( $data );
        $actual3 = $this->_object->getRoot();
        $this->_object->free();
        $expected3 = array('', '');

        // all possible data
        $data['nodeName'] = 'mymusic';
        $this->_object->setRoot( $data );
        $actual4 = $this->_object->getRoot();
        $this->_object->free();
        $expected4 = array();
        $expected4[0] = '<' . '?xml version="1.0" encoding="iso-8859-1" ?'
            . '>' . "\n"
            . '<mymusic version="V109" datetime="2010-07-31 23:58:59" '
            . 'generator="Array2Xml Creator">' . "\n";
        $expected4[1] = '</mymusic>' . "\n";

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );
        $this->assertingEquals( $expected4, $actual4 );

        $this->expectingException( 'Mumsys_Array2Xml_Exception' );
        $this->expectingExceptionMessage( 'No data to create a root element' );
        $this->_object->setRoot( array() );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::getData
     * @covers Mumsys_Array2Xml_Default::setData
     */
    public function testGetSetData()
    {
        $obj = new Mumsys_Array2Xml_Default();
        $actual1 = $obj->getData();

        $arr = array();
        // first main tree
        $arr['nodeValues'][0]['nodeName'] = 'album';
        $arr['nodeValues'][0]['nodeAttr']['id'] = 123;
        $arr['nodeValues'][0]['nodeAttr']['code'] = 'First Attribute Value';
        // new node
        $arr['nodeValues'][0]['nodeValues'][0]['nodeName'] = 'tracks';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeAttr']['key'] = '456';
        // new sub node
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeName'] = 'track';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeAttr']['id'] = '10001';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeName'] = 'name';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeValues'] = 'A name';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeValues'][1]['nodeName'] = 'file';
        $arr['nodeValues'][0]['nodeValues'][0]['nodeValues'][0]['nodeValues'][1]['nodeValues'] = '/some/file/file.mp3';
        $this->_object->setData( $arr['nodeValues'] );
        $actual2 = $this->_object->getData();

        $this->assertingEquals( array(), $actual1 );
        $this->assertingEquals( $arr['nodeValues'], $actual2 );

        $this->expectingException( 'Mumsys_Array2Xml_Exception' );
        $this->expectingExceptionMessage( 'No data given to be set.' );
        $this->_object->setData( array() );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::getCase
     */
    public function testGetCase()
    {
        $opi = $this->_objectoptions;
        $opi['tag_case'] = Mumsys_Array2Xml_Default::TAG_CASE_AS_IS;
        $oA = new Mumsys_Array2Xml_Default( $opi );
        $expected1 = $oA->getXML();

        $oB = $this->_object;
        $expected2 = $oB->getXML();

        $opi['tag_case'] = Mumsys_Array2Xml_Default::TAG_CASE_UPPER;
        $oC = new Mumsys_Array2Xml_Default( $opi );
        $expected3 = $oC->getXML();

        // as of construction: "set to lower": then both should match
        $this->assertingEquals( $expected1, $expected2 );

        // string lenght MUST be the same
        $this->assertingEquals( strlen( $expected3 ), strlen( $expected1 ) );
        // check for upper case parts
        $this->assertingTrue( ( preg_match( '/(ROOT VERSION)/', $expected3 ) === 1 ) );

        $this->expectingException( 'Mumsys_Array2Xml_Exception' );
        $opi['tag_case'] = 'nocase';
        $obj = new Mumsys_Array2Xml_Default( $opi );
        $obj->getCase( 'none' );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::getDoctype
     */
    public function testGetDoctype()
    {
        $doc = $this->_object->getDoctype();
        $ref = '<' . '?xml version="1.0" encoding="iso-8859-1" ?' . '>' . "\n";
        $this->assertingEquals( $ref, $doc );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::createElements
     */
    public function testCreateElements()
    {
        $valueAndAttributes = array(
            '..some more value..',
            array(
                'attr1' => 1,
                'attr2' => 2,
            )
        );

        $valueAndAttributesErr = array(
            'value',
            'attr1' => 1,
            'attr2' => 2,
            'wrong values throws exception'
        );

        $valueAndAttributesAttrErr = array(
            '..some more value..',
            array(
                'attr1' => 1,
                'attr2' => 2,
                'Wrong attribute'
            )
        );

        // test 1
        $actual1 = $this->_object->createElements(
            'element', array(0 => 'elementvalue')
        );
        $expected1 = '<element>elementvalue</element>'
            . $this->_objectoptions['linebreak'];

        // test 2
        $actual2 = $this->_object->createElements( 'node', 'value' );
        $expected2 = '<node>value</node>' . $this->_objectoptions['linebreak'];

        // test 3
        $actual3 = $this->_object->createElements( 'node', $valueAndAttributes );
        $expected3 = '<node attr1="1" attr2="2">..some more value..</node>'
            . $this->_objectoptions['linebreak'];

        //test 4, tag just exists
        $actual4 = $this->_object->createElements( 'node', '0' );
        $expected4 = '<node />' . $this->_objectoptions['linebreak'];

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );
        $this->assertingEquals( $expected4, $actual4 );

        // test 5
        $this->expectingException( 'Mumsys_Array2Xml_Exception' );
        $actual5 = $this->_object->createElements(
            'node', $valueAndAttributesErr
        );

        //test 6
        $this->expectingException( 'Mumsys_Array2Xml_Exception' );
        $actual6 = $this->_object->createElements(
            'node', $valueAndAttributesAttrErr
        );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::getAttributes
     */
    public function testGetAttributes()
    {
        $attr1 = array('AttRiBuTe1' => 'val1', 'AttRiBuTe2' => 'val2');
        $actual1 = $this->_object->getAttributes( $attr1 );
        $expected1 = ' attribute1="val1" attribute2="val2"';

        $this->assertingEquals( $expected1, $actual1 );

        $attr2 = array('AttRiBuTe1' => 'val1', 1 => 'val2');
        $this->expectingException( 'Mumsys_Array2Xml_Exception' );
        $this->expectingExceptionMessage(
            'Numeric attribute key not allowed. key: "1", value: "val2".'
        );
        $actual1 = $this->_object->getAttributes( $attr2 );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::getXML
     * @covers Mumsys_Array2Xml_Default::_sp
     * @covers Mumsys_Array2Xml_Default::_parse
     */
    public function testGetXML()
    {
        $lf = "\n";
        $xml = '<' . '?xml version="1.0" encoding="iso-8859-1" ?' . '>' . $lf
            . '<root version="flobee v 0.1" name="Array2Xml Creator">' . $lf
            . '<album id="123" code="First Attribute Value">' . $lf
            . '<tracks key="456">' . $lf
            . '<track id="10001">' . $lf
            . '<name>A name</name>' . $lf
            . '<file>/some/file/file.mp3</file>' . $lf
            . '</track>' . $lf
            . '<track id="10002">' . $lf
            . '<name>A name 2</name>' . $lf
            . '<file>/some/file/file2.mp3</file>' . $lf
            . '</track>' . $lf
            . '</tracks>' . $lf
            . '</album>' . $lf
            . '</root>' . $lf;

        $this->assertingEquals( $xml, $this->_object->getXML() );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::addElementTree
     * @covers Mumsys_Array2Xml_Default::_sp
     * @covers Mumsys_Array2Xml_Default::_parse
     */
    public function testAddElementTree()
    {
        $opts = $this->_objectoptions;
        $opts['data']['nodeValues'] = array();
        $obj = new Mumsys_Array2Xml_Default( $opts );
        $obj->setRoot( $this->_xmldata );
        //$o->setData( $opts );
        $obj->addElementTree( array() );
        $obj->addElementTree( array('nodeValues' => $this->_xmldata['nodeValues']) );
        $xml = $obj->getXML();

        $this->assertingEquals( $this->_refxmldata, $xml );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::validate
     */
    public function testValidate()
    {
        $actual1 = $this->_object->validate( 'this & that value' );
        $expected1 = '<![CDATA[this & that value]]>';

        $opts['cdata_escape'] = false;
        $obj = new Mumsys_Array2Xml_Default( $opts );
        $actual2 = $obj->validate( 'this & that value' );
        $expected2 = 'this &amp; that value';

        $this->assertingEquals( $expected1, $actual1 );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::encode
     */
    public function testEncode()
    {
        $actual1 = $this->_object->encode( 'äöüß' );
        $testStringUtf8 = 'äöüß';

        $obj2 = new Mumsys_Array2Xml_Default();
        $obj2->setEncoding( 'utf-8', 'iso-8859-15' );
        $actual2 = $obj2->encode( $testStringUtf8 );
        $iso_8859_15 = mb_convert_encoding( $testStringUtf8, 'iso-8859-15', 'utf-8' );

        $obj3 = new Mumsys_Array2Xml_Default();
        $obj3->setEncoding( 'iso-8859-15', 'utf-8' );
        $actual3 = $obj3->encode( $iso_8859_15 );
        $expected3 = $testStringUtf8;

        $errBak = error_reporting();
        error_reporting( 0 );
        $obj4 = new Mumsys_Array2Xml_Default();
        $obj4->setEncoding( 'utf-8', 'iso-8859-1' );
        $testtext4 = "This is the Euro symbol '€'.";
        $actual4 = $obj4->encode( $testtext4 );
        $expected4 = iconv( "UTF-8", "ISO-8859-1", $testtext4 );
        error_reporting( $errBak );

        $this->assertingEquals( $testStringUtf8, $actual1 );
        $this->assertingEquals( $iso_8859_15, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );
        $this->assertingEquals( $expected4, $actual4 );
        $this->assertingFalse( $actual4 );
    }


    /**
     * @covers Mumsys_Array2Xml_Default::isError
     * @covers Mumsys_Array2Xml_Default::getError
     */
    public function testIsGetError()
    {
        $this->assertingFalse( $this->_object->isError() );

        $this->assertingEquals( array(), $this->_object->getError() );
        // create an error
        $this->_object->setIdentifier( array('bla' => 'bla bla bla') );
        $error = array(
            'Error setIdentifier! Empty value or ID not found/wrong: '
            . '$key: "bla", v: "bla bla bla"'
        );
        $this->assertingTrue( $this->_object->isError() );
        $this->assertingEquals( $error, $this->_object->getError() );
    }


    /**
     * Just 4 CC
     * @covers Mumsys_Array2Xml_Default::free
     */
    public function testFree()
    {
        $this->_object->free();

        $this->assertingEquals( array(), $this->_object->getData() );
        $this->assertingEquals( array(), $this->_object->getError() );
        $this->assertingEquals( array(), $this->_object->getRoot() );

        $this->expectingException( 'Mumsys_Array2Xml_Exception' );
        $this->_object->getXML();
    }

}
