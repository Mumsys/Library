<?php declare( strict_types=1 );

/**
 * Mumsys_Variable_Manager_DefaultBench
 * for MUMSYS / Multi User Management System (MUMSYS)
 *
 * @license GPL Version 3 http://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright Copyright (c) 2019 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Variable
 */

/**
 * Mumsys_Variable_Manager_Default Benchmarks
 *
 * @BeforeMethods({"beforeBenchmark"})
 * @AfterMethods({"afterBenchmark"})
 *
 * @Iterations(3)
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Variable
 */
class Mumsys_Variable_Manager_DefaultBench
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Variable_Manager_Default
     */
    protected $_object;

    /**
     * @var string
     */
    protected $_version = '1.3.3';

    /**
     * @var array
     */
    protected $_values = array();

    /**
     * @var array
     */
    protected $_config = array();


    protected function setUp(): void
    {
        $this->_config = array(
            'username' => array(
                'label' => 'Username',
                //type: string, array (list), email, numeric, float, integer, date, datetime, ipv4, ipv6
                'type' => 'string',
                'minlen' => 1,
                'maxlen' => 45,
                'allowEmpty' => false,
                'required' => true,
                'regex' => array(),
                'default' => '',
                'value' => null,
                'errors' => array(),
            ),
        );
        $this->_values = array('username' => 'unittest');

        $this->_object = new Mumsys_Variable_Manager_Default( $this->_config, $this->_values );
    }


    protected function tearDown(): void
    {
        $this->_object = null;
        $this->_config =null;
    }


    public function beforeBenchmark(): void
    {
        $this->setUp();
    }


    public function afterBenchmark(): void
    {
        $this->tearDown();
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::__construct
     */
    public function constructA()
    {
        new Mumsys_Variable_Manager_Default( $this->_config, $this->_values );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::__construct
     */
    public function constructException()
    {
        try {
            $this->_config['username']['name'] = 'user';
            new Mumsys_Variable_Manager_Default( $this->_config, $this->_values );
        }
        catch ( Exception $ex ) {
            ;
        }
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::validate
     */
    public function validate()
    {
        $this->_object->validate();
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::validateType
     */
    public function validateTypeTrue()
    {
        $_types = array(
            'string' => 'test string',
            'array' => array('unittest'),
            'email' => 'thisisallowed@host.tld',
            'numeric' => '1234',
            'float' => 1.234,
            'integer' => 12345,
            'date' => '2000-12-31',
            'datetime' => '2000-12-31 23:58:59',
            'ipv4' => '127.0.0.1',
            'ipv6' => '::1',
            'unixtime' => '1234567890',
        );
        $item = $this->_object->getItem( 'username' );

        foreach ( $_types as $type => $value ) {
            $item->setType( $type );
            $item->setValue( $value );
            // true
            $this->_object->validateType( $item );
        }
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::validateType
     */
    public function validateTypeFalse()
    {
        $_types = array(
            'string' => array('value'),
            'array' => 'unittest',
            'email' => 'unittest@3',
            'numeric' => 'unittest',
            'float' => 'unittest',
            'integer' => 'unittest',
            'date' => 'unittest',
            'datetime' => 'unittest',
            'ipv4' => 'localhost',
            'ipv6' => 'localhost',
            'unixtime' => 'string',
        );
        $item = $this->_object->getItem( 'username' );

        foreach ( $_types as $type => $value ) {
            $item->setType( $type );
            $item->setValue( $value );
            // false
            $this->_object->validateType( $item );
        }
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::validateType
     */
    public function validateTypeException()
    {
        $item = $this->_object->getItem( 'username' );
        try {
            $item->setType( 'unittest' );
            $this->_object->validateType( $item );
        }
        catch ( Exception $ex ) {
            ;
        }
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::validateMinMax
     */
    public function validateMinMaxTrue()
    {
        $_types = array(
            'string' => 'test',
            'integer' => 4,
            'float' => 4.123,
            'numeric' => '4.123',
        );

        $item = $this->_object->getItem( 'username' );
        $item->setMinLength( 1 );
        $item->setMaxLength( 4.123 );

        foreach ( $_types as $type => $value ) {
            $item->setType( $type );
            $item->setValue( $value );
            // true
            $this->_object->validateMinMax( $item );
        }
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::validateMinMax
     */
    public function validateMinMaxFalse()
    {
        $_types = array(
            'string' => 'test',
            'integer' => 4,
            'float' => 4.123,
            'numeric' => '4.123',
        );
        $item = $this->_object->getItem( 'username' );
        $item->setMinLength( 5 );
        $item->setMaxLength( 1 );

        foreach ( $_types as $type => $value ) {
            $item->setType( $type );
            $item->setValue( $value );
            // false
            $this->_object->validateMinMax( $item );
        }
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::validateMinMax
     */
    public function validateMinMaxComplex()
    {
        // for code coverage
        $itemC = $this->_object->createItem( array('value' => array('unittest', 'a'=>'b', 'c'=>'d')) );
        $this->_object->validateMinMax( $itemC );

        $itemC->setType( 'array' );
        $itemC->setMinLength( 4 );
        $itemC->setMaxLength( 1 );
        $this->_object->validateMinMax( $itemC );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::validateRegex
     */
    public function validateRegexTrue()
    {
        $item = $this->_object->getItem( 'username' );
        $item->setValue( 'uNiTtEsT' );
        $item->setRegex( array('/^(unittest)$/i') );

        $this->_object->validateRegex( $item );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::validateRegex
     */
    public function validateRegexFalse()
    {
        $item = $this->_object->getItem( 'username' );
        $item->setValue( 'uNiTtEsT' );
        $item->setRegex( array('/^(somtest)$/i') );
        $this->_object->validateRegex( $item );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::validateRegex
     */
    public function validateRegexInvalid()
    {
        $item = $this->_object->getItem( 'username' );
        $item->setValue( 'uNiTtEsT' );
        $item->setRegex( array('\d') ); // invalid regex / syntax error will perform

        $displayErrors = ini_get( 'display_errors' );
        $errorReporting = error_reporting();

        ini_set( 'display_errors', '0' );
        error_reporting( 0 );

        try {
            $this->_object->validateRegex( $item );
        } catch ( Exception $ex ) {
            ;
        }

        ini_set( 'display_errors', $displayErrors );
        error_reporting( $errorReporting );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::validateIpv4
     */
    public function validateIPv4True()
    {
        $item = $this->_object->getItem( 'username' );
        $item->setType( 'ipv4' );
        $item->setValue( '11.22.33.44' );
        $this->_object->validateIpv4( $item );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::validateIpv4
     */
    public function validateIPv4False()
    {
        $item = $this->_object->getItem( 'username' );
        $item->setType( 'ipv4' );
        $item->setValue( 'noipv4' );
        $this->_object->validateIpv4( $item );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::validateIpv6
     */
    public function validateIPv6True()
    {
        $item = $this->_object->getItem( 'username' );
        $item->setType( 'ipv6' );
        $item->setValue( '::1' );
        $this->_object->validateIpv6( $item );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::validateIpv6
     */
    public function validateIPv6False()
    {
        $item = $this->_object->getItem( 'username' );
        $item->setType( 'ipv6' );
        $item->setValue( 'noipv6' );
        $this->_object->validateIpv6( $item );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::isValid
     */
    public function isValidTrue()
    {
        $item = $this->_object->getItem( 'username' );
        $this->_object->isValid( $item );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::isValid
     */
    public function isValidFalse()
    {
        $item = $this->_object->getItem( 'username' );
        $item->setValue( '' );
        $item->setAllowEmpty( true );
        $this->_object->isValid( $item );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::getItems
     */
    public function getItems()
    {
        $this->_object->getItems();
    }

    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::registerItem
     */
    public function registerItem()
    {
        $object = clone $this->_object;
        unset( $this->_config['userX'] );
        $item = $object->getItem( 'username' );
        $item->setName( 'userX' );

        $object->registerItem( 'userX', $item );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::registerItem
     */
    public function registerItemException()
    {
        $item = $this->_object->getItem( 'username' );
        try {
            $this->_object->registerItem( 'username', $item );
        } catch ( Exception $ex ) {
            ;
        }

    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::createItem
     */
    public function createItem()
    {
        $this->_object->createItem( $this->_config['username'] );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::getErrorMessages
     */
    public function getErrormessages()
    {
        $this->_config['username']['errors'] = array('REQUIRED_MISSING' => 'Missing required value');
        new Mumsys_Variable_Item_Default( $this->_config['username'] );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::getMessageTemplates
     */
    public function getMessageTemplates()
    {
        $this->_object->getMessageTemplates();
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::setMessageTemplates
     */
    public function setMessageTemplates()
    {
        $expected = array(
            'REQUIRED_MISSING' => 'Missing required value',
            'ALLOWEMPTY_ERROR' => 'Missing value',
            'REGEX_FAILURE' => 'Value "%1$s" does not match the regex rule: "%2$s"',
            'REGEX_ERROR' => 'Error in regular expression',
            'TYPE_INVALID_STRING' => 'Value (json):"%1$s" is not a "string"',
            'TYPE_INVALID_ARRAY' => 'Value (json):"%1$s" is not an "array"',
            'TYPE_INVALID_EMAIL' => 'Value "%1$s" is not a valid value for type "email"',
            'TYPE_INVALID_NUMERIC' => 'Value (json):"%1$s" is not a "numeric" type',
            'TYPE_INVALID_FLOAT' => 'Value (json):"%1$s" is not a "float" type',
            'TYPE_INVALID_INT' => 'Value (json):"%1$s" is not an "integer" type',
            'TYPE_INVALID_DATE' => 'Value (json):"%1$s" is not a "date" type',
            'TYPE_INVALID_DATETIME' => 'Value (json):"%1$s" is not a "datetime" type',
            'TYPE_INVALID_UNIXTIME' => 'Value (json):"%1$s" is not a "unixtime"',
            'MINMAX_TOO_SHORT_STR' => 'Value "%1$s" must contain at least "%2$s" characters',
            'MINMAX_TOO_LONG_STR' => 'Value "%1$s" must contain maximum of "%2$s" characters, "%3$s" given',
            'MINMAX_TOO_SHORT_NUM' => 'Value "%1$s" must be minimum "%2$s"',
            'MINMAX_TOO_LONG_NUM' => 'Value "%1$s" can be maximum "%2$s"',
            'MINMAX_TYPE_ERROR' => 'Min/max type error "%1$s". Must be "string", "integer", '
            . '"numeric", "float" or "double"',
        );

        $this->_object->setMessageTemplates( $expected );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::setMessageTemplate
     */
    public function setMessageTemplate()
    {
        $this->_object->setMessageTemplate( 'unittest', 'Unittest template message' );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::setAttributes
     */
    public function setAttributesValues()
    {
        // value for some items
        $attributesA = array('values' => array('username' => 'unittest value'));
        $this->_object->setAttributes( $attributesA );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::setAttributes
     */
    public function setAttributesLabels()
    {
        // labels for some items
        $attributes = array('labels' => array('username' => 'unittest label'));
        $this->_object->setAttributes( $attributes );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::setAttributes
     */
    public function setAttributesState()
    {
        // "state" for all items
        $attributes = array('state' => 'onSave');
        $this->_object->setAttributes( $attributes );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::setAttributes
     */
    public function setAttributesException()
    {
        try {
            $attributes = array('unittest' => 'throw an exception');
            $this->_object->setAttributes( $attributes );
        } catch ( Exception $ex ) {
            ;
        }
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::toArray
     */
    public function toArray()
    {
        $this->_object->toArray();
    }



    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::externalsApply
     */
    public function externalsApply()
    {
        $this->_object->externalsApply();
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::filtersApply
     */
    public function filtersApply()
    {
        $this->_object->filtersApply();
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::filtersApply
     */
    public function filtersApplyError()
    {
        $this->_config['username']['filters'] = array(
            'onView' => array(
                'is_array',
            )
        );

        $object = new Mumsys_Variable_Manager_Default( $this->_config, $this->_values );
        $object->filtersApply();
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::callbacksApply
     */
    public function callbacksApply()
    {
        $this->_object->callbacksApply();
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::callbackItem
     */
    public function callbackItem()
    {
        $object = new Mumsys_Variable_Manager_Default( $this->_config, $this->_values );

        $itmProps = array(
            'name' => 'unittest',
            'value' => ' unittest & test value ',
            'callbacks' => array(
                'onView' => array(
                    'Mumsys_Variable_Manager_DefaultTest_CBFunc' => array('%value%', 12, 34, 56),
                    'Mumsys_Variable_Manager_DefaultTest_CBClass::runA' => '%value%',
                    'Mumsys_Variable_Manager_DefaultTest_CBClass::runB' => '123',
                    'Mumsys_Variable_Manager_DefaultTest_CBClass::runC',
                    'callbackNotExists',
                    'Mumsys_Variable_Manager_DefaultTest_CBClass::runFalse',
                ),
            )
        );
        $newItem = $object->createItem( $itmProps );
        $object->registerItem( 'unittest', $newItem );
        $object->callbackItem( $newItem );
    }


    /**
     * @Subject
     *
     * @Warmup(5)
     * @Revs(10000)
     *
     * @covers Mumsys_Variable_Manager_Default::compare
     */
    public function compare()
    {
        $oItemA = $this->_object->getItem( 'username' );
        $oItemB = clone $oItemA;

        $this->_object->compare( $oItemA, $oItemB, '==' );
    }

}
