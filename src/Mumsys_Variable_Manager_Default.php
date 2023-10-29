<?php declare (strict_types=1);

/**
 * Mumsys_Variable_Manager_Default
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Variable
 * Created: 2006 based on Mumsys_Field, renew 2016
 */


/**
 * Default variable item manager to handle variable items.
 *
 * This class hold your variables as objects and is able to show specific
 * informations (if set) or does the validation after a user input.
 * Example:
 * <code>
 * $config = array(
 *     'username' => array(
 *         'minlen' => 1,
 *         'maxlen' => 250,
 *         'required' => true,
 *         'allowEmpty' => false,
 *         'label' => 'Username',
 *         'desc' => 'You username/ alias name',
 *         'info' => 'Required, min 1, max 250 chars',
 *     )
 * );
 * $object = new Mumsys_Variable_Manager_Default($config, $_POST);
 * // in edit mode
 * $usernameObject = $object->getItem('username');
 * // in template/ output case, e.g.:
 * if ($usernameObject->getDescription()) echo $usernameObject->getDescription();
 * if ($usernameObject->getInformation()) echo $usernameObject->getInformation();
 * echo $usernameObject->getLabel() . ': ' . $usernameObject->getValue()
 * // in save mode:
 * if ($object->validate()) { save data }
 * </code>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Variable
 */
class Mumsys_Variable_Manager_Default
    extends Mumsys_Variable_Manager_Abstract
    implements Mumsys_Variable_Manager_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '2.3.9';

    /**
     * List key/validation items.
     * @var array
     */
    private $_items = array();

    /**
     * List of error messages used in this manager
     *
     * @todo generalize order of error parameters! E.g:
     *      "subject name",
     *      "current value",
     *      "expected value"
     *      "current value type"
     *
     * @var array
     */
    private $_messageTemplates = array(
        // basic checks
        self::REQUIRED_MISSING => 'A required value is missing',
        self::ALLOWEMPTY_ERROR => 'Missing value',

        //regex checks
        self::REGEX_FAILURE => 'Value "%1$s" (type: "%3$s") does not match the regular '
            . 'expression/s (json): "%2$s"',
        self::REGEX_ERROR => 'Error in regular expression. Check syntax!',

        // type checks
        self::TYPE_INVALID_STRING => 'Value (json): "%1$s" is not a "string"',
        self::TYPE_INVALID_ARRAY => 'Value (json): "%1$s" is not an "array"',
        self::TYPE_INVALID_EMAIL => 'Value "%1$s" is not a valid "email" address',
        self::TYPE_INVALID_NUMERIC => 'Value (json): "%1$s" is not a "numeric" value',
        self::TYPE_INVALID_FLOAT => 'Value (json): "%1$s" is not a "float" value',
        self::TYPE_INVALID_INT => 'Value (json): "%1$s" is not an "integer"',
        self::TYPE_INVALID_DATE => 'Value (json): "%1$s" is not of type "date"',
        self::TYPE_INVALID_DATETIME => 'Value (json): "%1$s" is not of type "datetime"',
        self::TYPE_INVALID_IPV4 => 'Value (json):"%1$s" is not an "ipv4" address',
        self::TYPE_INVALID_IPV6 => 'Value (json):"%1$s" is not an "ipv6" address',
        self::TYPE_INVALID_UNIXTIME => 'Value (json):"%1$s" is not a "unixtime"',
        //min max checks
        self::MINMAX_TOO_SHORT_STR => 'Value "%1$s" must contain at least '
            . '"%2$s" characters',
        self::MINMAX_TOO_LONG_STR => 'Value "%1$s" must contain maximum of '
            . '"%2$s" characters, "%3$s" given',
        self::MINMAX_TOO_SHORT_NUM => 'Value "%1$s" must be minimum "%2$s"',
        self::MINMAX_TOO_LONG_NUM => 'Value "%1$s" can be maximum "%2$s"',
        self::MINMAX_TOO_SHORT_ARRAY => 'Found "%1$s" values, minimum "%2$s" values',
        self::MINMAX_TOO_LONG_ARRAY => 'Found "%1$s" values, maximum "%2$s" values',
        self::MINMAX_TOO_INVALID_VALUE => 'Value is not of type: "%1$s"',
        // types
        self::MINMAX_TYPE_ERROR => 'Min/max type error "%1$s". Must be '
            . '"string", "integer", "numeric", "float" or "double"',
        // filter
        self::FILTER_ERROR => 'Filter "%1$s" failt for label/name: "%2$s"',
        self::FILTER_NOTFOUND => 'Filter function "%1$s" not found for item: "%2$s"',
        // callbacks
        self::CALLBACK_ERROR => 'Callback "%1$s" for "%2$s" failt for value (json): "%3$s"',
        self::CALLBACK_NOTFOUND => 'Callback function "%1$s" not found for item: "%2$s"',
    );


    /**
     * Initialises the default manager and variable item objects.
     *
     * Example:
     * <pre>
     * $config = array(
     *  'user.name' => array(  // address/ name of the item to work with
     *                         // within the manager
     *      'name' => 'name',  // real name of the item; optional if the address
     *                         // contains the same name otherwise a MUST HAVE
     *      'label' => 'User name',
     *      'desc' => 'A description. E.g: User/ Login name',
     *      'info' => "Info about the value. E.g: Min. 5 chars max. 45 chars.",
     *      'type' => 'string',
     *      'minlen' => 5,
     *      'maxlen' => 45,
     *      'allowEmpty' => false,
     *      'required' => true,
     *      'regex' => array('/^([a-zA-Z0-9-_]{4,45})*$/i'),
     *      'default' => '',
     *      'filters' => array(
     *          'onSave' => array(
     *              'trim',
     *              'substr' => array('%value%', 5, 45)
     *          )
     * ), ...
     * $values = $_REQUEST;
     * $validator = new Mumsys_Variable_Manager_Default($config, $values);
     *
     * // Sets the state and applys it to the items so that filters are ready
     * // befor validatation. Default is "onView";
     * // For maximum performance use the "state" on construction, this is just
     * // a helper to force the state for all reqistered items.
     * $validator->setAttributes( array('state' => 'onSave') );
     * $validator->filtersApply()
     * $success = $validator->validate();
     *
     * $userItem = $validator->getItem('user.name');
     * $userItem->setValue('I\'m your user name');
     * echo $userItem->getLabel();
     * echo $userItem->getDescription();
     * echo $userItem->getInformation();
     * print_r($userItem->getErrorMessages());
     * $itemSuccess = $validator->isValid($userItem);
     * </pre>
     *
     * @param array $config List of key/value configuration pairs containing
     * item properties for the item construction
     * @param array $values List of key/value pairs to set/bind to the item
     * values e.g: the post parameters
     */
    public function __construct( array $config = array(),
        array $values = array() )
    {
        foreach ( $config as $itemKey => $properties ) {
            if ( !isset( $properties['name'] ) ) {
                $properties['name'] = $itemKey;
            }

            if ( $properties['name'] !== $itemKey ) {
                $message = sprintf(
                    'Item name "%1$s" and item address "%2$s" are not '
                    . 'identical. Drop item "name" or "address" in config',
                    $properties['name'], $itemKey
                );

                throw new Mumsys_Variable_Manager_Exception( $message );
            }

            $internalKey = $properties['name'];

            if ( isset( $values[$internalKey] ) ) {
                $properties['value'] = $values[$internalKey];
            }

            $this->_items[$itemKey] = $this->createItem( $properties );
        }
    }


    /**
     * Wrapper for validate() and externalsApply() in default order.
     *
     * @see externalsApply() for details.
     *
     * @param mixed $callbackData Mixed data to pipe to the "callback" function (not filter
     * function) as second parameter
     *
     * @return boolean True on success or false on error
     */
    public function validateAll( $callbackData = null ): bool
    {
        $status = false;
        if ( $this->validate() && $this->externalsApply( $callbackData ) ) {
            $status = true;
        }

        return $status;
    }


    /**
     * Validate registered variable items.
     *
     * @return boolean True on success or false on error
     */
    public function validate(): bool
    {
        $status = true;
        foreach ( $this->_items as $item ) {
            if ( !$this->isValid( $item ) ) {
                $status = false;
            }
        }

        return $status;
    }


    /**
     * Item type validation.
     *
     * If the test fails an error message will be set at the item.
     *
     * @param Mumsys_Variable_Item_Interface $item Variable item interface
     *
     * @return boolean True on success otherwise false.
     */
    public function validateType( Mumsys_Variable_Item_Interface $item ): bool
    {
        $return = true;

        $type = $item->getType();
        $value = $item->getValue();

        $errorKey = false;
        $errorMessage = false;

        switch ( $type )
        {
            case 'string':
            case 'char':
            case 'varchar':
            case 'text':
            case 'tinytext':
            case 'longtext':
                if ( !is_string( $value ) ) {
                    $errorKey = self::TYPE_INVALID_STRING;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['TYPE_INVALID_STRING'],
                        json_encode( $value )
                    );
                }
                break;

            case 'array':
                if ( !is_array( $value ) ) {
                    $errorKey = self::TYPE_INVALID_ARRAY;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['TYPE_INVALID_ARRAY'],
                        json_encode( $value )
                    );
                }
                break;

            case 'email':
                $email = trim( $value );
                //$regex = '/^[a-z0-9_\.-]+@[a-z0-9_\.-]+\.[a-z]{2,6}$/i';
                //if ( !preg_match( $regex, $email ) ) {
                if ( ( (bool) filter_var( $email, FILTER_VALIDATE_EMAIL ) ) === false ) {
                    $errorKey = self::TYPE_INVALID_EMAIL;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['TYPE_INVALID_EMAIL'], $email
                    );
                }
                break;

            case 'numeric':
                if ( !is_numeric( $value ) ) {
                    $errorKey = self::TYPE_INVALID_NUMERIC;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['TYPE_INVALID_NUMERIC'],
                        json_encode( $value )
                    );
                }
                break;

            case 'float':
            case 'double':
                $value = is_numeric( $value ) ? (float) $value : $value;
                if ( !is_float( $value ) ) {
                    $errorKey = self::TYPE_INVALID_FLOAT;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['TYPE_INVALID_FLOAT'],
                        json_encode( $value )
                    );
                }
                break;

            case 'int':
            case 'integer':
            case 'smallint':
                $value = is_numeric( $value ) ? (int) $value : $value;
                if ( !is_int( $value ) ) {
                    $errorKey = self::TYPE_INVALID_INT;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['TYPE_INVALID_INT'],
                        json_encode( $value )
                    );
                }
                break;

            case 'date':
                $regex = '/^(\d{4})-(\d{2})-(\d{2})/i';
                if ( $value !== null && strlen( $value ) != 10 && !preg_match( $regex, $value ) ) {
                    $errorKey = self::TYPE_INVALID_DATE;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['TYPE_INVALID_DATE'],
                        json_encode( $value )
                    );
                }
                break;

            case 'datetime':
            case 'timestamp':
                $regex = '/^(\d{4})-(\d{2})-(\d{2}) (\d{1,2}):(\d{1,2}):(\d{1,2})/i';
                if ( $value !== null && strlen( $value ) != 19 && !preg_match( $regex, $value ) ) {
                    $errorKey = self::TYPE_INVALID_DATETIME;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['TYPE_INVALID_DATETIME'],
                        json_encode( $value )
                    );
                }
                break;

            case 'ipv4':
                $return = $this->validateIPv4( $item );
                break;

            case 'ipv6':
                $return = $this->validateIPv6( $item );
                break;

            case 'unixtime':
                $value = is_numeric( $value ) ? (int) $value : $value;
                if ( is_int( $value ) === false || $value < 0 ) {
                    $errorKey = self::TYPE_INVALID_UNIXTIME;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['TYPE_INVALID_UNIXTIME'],
                        json_encode( $value )
                    );
                }
                break;

            default:
                $message = sprintf( 'Type "%1$s" not implemented', $type );
                throw new Mumsys_Variable_Manager_Exception( $message );
        }

        if ( $errorKey && $errorMessage ) {
            $item->setErrorMessage( $errorKey, $errorMessage );
            $return = false;
        }

        return $return;
    }


    /**
     * Item validation for min and/or max item values.
     *
     * Hint: if item type is string min/max values will be testes by string
     * length. If the type is an integer it will be tested against greater/
     * lower the current value.
     *
     * @param Mumsys_Variable_Item_Interface $item
     * @return boolean True on success otherwise false
     */
    public function validateMinMax( Mumsys_Variable_Item_Interface $item ): bool
    {
        $return = true;
        $min = $item->getMinLength();
        $max = $item->getMaxLength();

        if ( $min === null && $max === null ) {
            return $return;
        }

        $type = $item->getType();
        $value = $item->getValue();

        $errorKey = false;
        $errorMessage = false;

        switch ( $type )
        {
            case 'array':
                if ( !is_array( $value ) ) {
                    $errorKey = self::MINMAX_TOO_INVALID_VALUE;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['MINMAX_TOO_INVALID_VALUE'],
                        $type
                    );

                    break;
                }

                if ( isset( $min ) && count( $value ) < $min ) {
                    $errorKey = self::MINMAX_TOO_SHORT_ARRAY;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['MINMAX_TOO_SHORT_ARRAY'],
                        count( $value ), $min
                    );
                }

                if ( isset( $max ) && count( $value ) > $max ) {
                    $errorKey = self::MINMAX_TOO_LONG_ARRAY;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['MINMAX_TOO_LONG_ARRAY'],
                        count( $value ), $min
                    );
                }
                break;

            case 'string':
            case 'char':
            case 'varchar':
            case 'text':
            case 'tinytext':
            case 'longtext':
            case 'email':
            case 'date':
            case 'datetime':
            case 'unixtime':
                $strlen = strlen( (string)$value );
                if ( isset( $min ) && $strlen < $min ) {
                    $errorKey = self::MINMAX_TOO_SHORT_STR;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['MINMAX_TOO_SHORT_STR'],
                        $value, $min
                    );
                }

                if ( isset( $max ) && $strlen > $max ) {
                    $errorKey = self::MINMAX_TOO_LONG_STR;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['MINMAX_TOO_LONG_STR'], $value,
                        $max, $strlen
                    );
                }
                break;

            case 'int':
            case 'integer':
            case 'smallint':
            case 'float':
            case 'double':
            case 'numeric':
                if ( isset( $min ) && $value < $min ) {
                    $errorKey = self::MINMAX_TOO_SHORT_NUM;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['MINMAX_TOO_SHORT_NUM'],
                        $value, $min
                    );
                }

                if ( isset( $max ) && $value > $max ) {
                    $errorKey = self::MINMAX_TOO_LONG_NUM;
                    $errorMessage = sprintf(
                        $this->_messageTemplates['MINMAX_TOO_LONG_NUM'], $value,
                        $max
                    );
                }
                break;

            default:
                $errorKey = self::MINMAX_TYPE_ERROR;
                $errorMessage = sprintf(
                    $this->_messageTemplates['MINMAX_TYPE_ERROR'], $type
                );
        }

        if ( $errorKey && $errorMessage ) {
            $item->setErrorMessage( $errorKey, $errorMessage );
            $return = false;
        }

        return $return;
    }


    /**
     * Item validation agains regular expressions.
     *
     * @param Mumsys_Variable_Item_Interface $item Validate item object
     *
     * @return boolean True on success or if no regex was set or false on error
     */
    public function validateRegex( Mumsys_Variable_Item_Interface $item ): bool
    {
        $return = true;

        if ( ( $expr = $item->getRegex() ) && ( $value = $item->getValue() ) ) {
            // php > 8: if item type is numeric but should also match to a
            // specific regex eg: 123 but not 10123..., we need to cast the
            // value to string. For instance: disable this to detect configs
            // incl regex which may not really needed.
            $type = $item->getType();
            switch ( $type ) {
                case 'int':
                case 'integer':
                case 'float':
                case 'double':
                case 'numeric':
                    $value = (string) $value;
                    break;
            }

            foreach ( $expr as $regex ) {
                // fallback for php8 error exception eg in invalid value
                // with a TypeError: ... must be of type string, int given
                try {
                    $match = preg_match( $regex, $value );
                }
                catch ( Error | Exception $exc ) {
                    $match = 0;
                }

                $errorKey = false;
                $errorMessage = false;

                if ( $match === 0 ) {
                    $errorKey = self::REGEX_FAILURE;
                    $errorMessage = sprintf(
                        $this->_messageTemplates[self::REGEX_FAILURE],
                        $value,
                        json_encode( $regex ),
                        gettype( $value )
                    );
                }

                if ( $match === false ) {
                    $errorKey = self::REGEX_ERROR;
                    $errorMessage = sprintf(
                        $this->_messageTemplates[self::REGEX_ERROR],
                        $value,
                        $regex
                    );
                }
//
//                switch ( $match ) {
//                    case ( $match === 0 ):
//                        $errorKey = self::REGEX_FAILURE;
//                        $errorMessage = sprintf(
//                            $this->_messageTemplates[self::REGEX_FAILURE],
//                            $value, $regex
//                        );
//                        break;
//
//                    case ( $match === false ):
//                        $errorKey = self::REGEX_ERROR;
//                        $errorMessage = sprintf(
//                            $this->_messageTemplates[self::REGEX_ERROR], $value,
//                            $regex
//                        );
//                        break;
//                }
//
                if ( $errorKey && $errorMessage ) {
                    $item->setErrorMessage( $errorKey, $errorMessage );
                    $return = false;
                }
            }
        }

        return $return;
    }


    /**
     * Item validation for an ipv4 address.
     *
     * @todo implement checks for FILTER_FLAG_NO_PRIV_RANGE
     * (also: return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
     * ? $value : (((ip2long($value) & 0xff000000) == 0x7f000000) ? FALSE :
     * $value); and FILTER_FLAG_NO_RES_RANGE
     * this may need type attributes!?
     *
     * @param Mumsys_Variable_Item_Interface $item Validate item object
     *
     * @return boolean True on success or false for invalid ip format
     */
    public function validateIPv4( Mumsys_Variable_Item_Interface $item ): bool
    {
        $test = filter_var( $item->getValue(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
        if ( $test === false ) {
            $message = sprintf(
                $this->_messageTemplates[self::TYPE_INVALID_IPV4],
                $item->getValue()
            );
            $item->setErrorMessage( self::TYPE_INVALID_IPV4, $message );

            return false;
        }

        return true;
    }


    /**
     * Item validation for an ipv4 address.
     *
     * @param Mumsys_Variable_Item_Interface $item Validate item object
     *
     * @return boolean True on success or false for invalid ip format
     */
    public function validateIPv6( Mumsys_Variable_Item_Interface $item ): bool
    {
        $test = filter_var( $item->getValue(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 );
        if ( $test === false ) {
            $message = sprintf(
                $this->_messageTemplates[self::TYPE_INVALID_IPV6],
                $item->getValue()
            );
            $item->setErrorMessage( self::TYPE_INVALID_IPV6, $message );

            return false;
        }

        return true;
    }


    /**
     * Checks the variable item to be valid.
     *
     * Returns true if and only if $value matches the requirements.
     *
     * If $value fails the validation tests false will return and
     * getErrorMessages() contains a list of reasons to explain what was wrong.
     *
     * @param Mumsys_Variable_Item_Interface $item Variable item
     *
     * @return boolean Returns true on success otherwise false
     */
    public function isValid( Mumsys_Variable_Item_Interface $item ): bool
    {
        $status = true;
        $value = $item->getValue();
        $allowEmpty = $item->getAllowEmpty();
        $required = $item->getRequired();

        if ( ( $required == false && $allowEmpty === true && empty( $value ) ) ||
            ( $required == true && $allowEmpty === true && $value !== null && empty( $value ) )
        ) {
            return true;
        }

        if ( ( $value === null || $value === '' ) && ( $required || ( $allowEmpty === false ) ) ) {
            if ( $required ) {
                $item->setErrorMessage(
                    self::REQUIRED_MISSING,
                    $this->_messageTemplates['REQUIRED_MISSING']
                );
            } else {
                $item->setErrorMessage(
                    self::ALLOWEMPTY_ERROR,
                    $this->_messageTemplates['ALLOWEMPTY_ERROR']
                );
            }
            $status = false;
        }

        if ( $this->validateType( $item ) === false ) {
            $status = false;
        }

        if ( $this->validateMinMax( $item ) === false ) {
            $status = false;
        }

        if ( $this->validateRegex( $item ) === false ) {
            $status = false;
        }

        $item->setValidated( $status );

        return $status;
    }


    /**
     * Returns all variable items.
     *
     * @return array<string,Mumsys_Variable_Item_Interface> List of key/variable
     * items implementing Mumsys_Variable_Item_Interface where key is the
     * identifier of the item
     */
    public function getItems(): array
    {
        return $this->_items;
    }


    /**
     * Returns a variable item by given key.
     *
     * @param string $key Key/ identifier of the variable item
     *
     * @return Mumsys_Variable_Item_Interface|null Variable item or null if not available
     */
    public function getItem( $key ): ?Mumsys_Variable_Item_Interface
    {
        if ( isset( $this->_items[$key] ) ) {
            return $this->_items[$key];
        }

        return null;
    }


    /**
     * Register a variable item object.
     *
     * If the item 'name' does not exists $key will be used for the 'name' property.
     *
     * @param string $key Key/ identifier of the item
     * @param Mumsys_Variable_Item_Interface $item Variable item object
     *
     * @throws Mumsys_Variable_Manager_Exception If key already exists
     */
    public function registerItem( string $key,
        Mumsys_Variable_Item_Interface $item ): void
    {
        if ( isset( $this->_items[$key] ) ) {
            $message = sprintf( 'Item "%1$s" already set', $key );
            throw new Mumsys_Variable_Manager_Exception( $message );
        }

        $itemName = $item->getName();
        if ( $itemName === null ) {
            $item->setName( $key );
            $itemName = $key;
        }

        if ( $key !== $itemName ) {
            $message = sprintf(
                'Item name "%1$s" and item address/key "%2$s" are not '
                . 'identical. Change item "name" or "$key"',
                $itemName, $key
            );

            throw new Mumsys_Variable_Manager_Exception( $message );
        }

        $this->_items[$key] = $item;
    }


    /**
     * Creates a new variable item object.
     *
     * @see Mumsys_Variable_Item_Default
     *
     * @param array $props List of key/value pairs to initialize the variable
     * item object
     *
     * @return Mumsys_Variable_Item_Default
     */
    public function createItem( array $props = array() ): Mumsys_Variable_Item_Interface
    {
        return new Mumsys_Variable_Item_Default( $props );
    }


    /**
     * Returns the list of all error messages from all variable items.
     *
     * Identified by the item key/ID as array index. E.g:
     * array('variablename' => array('errorID' => 'errorMessage', ...);
     *
     * @return array Returns the list of errors or empty array for no errors
     */
    public function getErrorMessages(): array
    {
        $messages = array();

        foreach ( $this->_items as $key => $item ) {
            if ( ( $errors = $item->getErrorMessages() ) ) {
                $messages[$key] = $errors;
            }
        }

        return $messages;
    }


    /**
     * Returns the message templates.
     *
     * @return array List of error message templates
     */
    public function getMessageTemplates(): array
    {
        return $this->_messageTemplates;
    }


    /**
     * Set/ replaces the message templates.
     *
     * Hint: The key is the message identifier to easyly find it where the
     * massage can vari depending on the values.
     *
     * @param array $templates List of key/value pairs for the message templates.
     */
    public function setMessageTemplates( array $templates ): void
    {
        $this->_messageTemplates = $templates;
    }


    /**
     * Sets/ replaces a message template by given key, and value.
     *
     * @param string $key Message key/ID
     * @param string $value The message
     */
    public function setMessageTemplate( string $key, string $value ): void
    {
        $this->_messageTemplates[(string) $key] = (string) $value;
    }


    /**
     * Sets attributes/ propertys to all or selected items.
     *
     * You can set attributes like the value or state for all items like
     *      array('value' => 'this value in all items')
     *      array('state' => 'onSave')
     *
     * Additional and possible attributes to set in $attr are: "value", "values",
     * "labels" an "state" where you can set some of the items and set an
     * individual value.
     * E.g: Item A gets value 1, item B gets value 2:
     * <pre>
     *  // 'values':
     *  // 'labels':
     *  // for some item values
     *  $attr = array('values' => array(itemKeyA => '1', itemKeyB => 2, .... );
     *  // pl. value*s* dont work this way:
     *  $attr = array('values' => 'new value');
     *  // sing. for all
     *  $attr = array('value' => 'new value');
     * </pre>
     * Hint:
     *  - "value" for all items managed in this manager.
     *  - "values" must include a list of key/value pairs to set specific item
     *    values.
     *
     * @param array $attr List of key/value pairs to be set
     *  - 'state' string set state to all existing items
     *  - 'value' string Value to set to all items
     *  - 'values' array<string,string> Values to set to specific items
     *  - 'labels' array<string,string> Labels to set to specific items
     *
     * @throw Mumsys_Variable_Manager_Exception If not implemented
     */
    public function setAttributes( array $attr = array() ): void
    {
        foreach ( $attr as $fieldKey => $value ) {
            foreach ( $this->_items as $item ) {
                $fieldName = $item->getName();

                switch ( $fieldKey ) {
                    // all items
                    case 'value':
                        $item->setValue( $value );
                        break;

                    case 'values':
                        if ( isset( $value[$fieldName] ) ) {
                            $item->setValue( $value[$fieldName] );
                        }
                        break;

                    case 'labels':
                        if ( isset( $value[$fieldName] ) ) {
                            $item->setLabel( $value[$fieldName] );
                        }
                        break;

                    case 'state':
                        $item->stateSet( $value );
                        break;

                    default:
                        $msg = sprintf(
                            'Set item attributes for "%1$s" not implemented',
                            $fieldKey
                        );
                        throw new Mumsys_Variable_Manager_Exception( $msg );
                }
            }
        }
    }


    /**
     * Returns the list of key/value pairs for all items.
     *
     * @param string $strip Strip part of the address to get other array indexes
     * as the default. E.g: "user.id" is the address but you want the "id"
     * @param string $add Optional replacement string for the strip. Default: ""
     *
     * @return array List of key/value pairs
     */
    public function toArray( string $strip = '', string $add = '' ): array
    {
        $list = array();
        foreach ( $this->_items as $address => $item ) {

            if ( $strip && is_string( $strip ) ) {
                $list[str_replace( $strip, $add, $address )] = $item->getValue();
            } else {
                $list[$address] = $item->getValue();
            }
        }

        return $list;
    }


    /**
     * Apply filters and callbacks in this order.
     *
     * You may want to use a different order of validate() filter*() and
     * callback*() of items. This applys only filtersApply() and if successful
     * callbacksApply() and returns the status
     *
     * @param mixed|null $data Mixed data to pipe to the "callback" function (not filter
     * function) as second parameter
     *
     * @return boolean Status, true for success otherwise false
     */
    public function externalsApply( $data = null ): bool
    {
        $status = false;
        if ( $this->filtersApply() && $this->callbacksApply( $data ) ) {
            $status = true;
        }

        return $status;
    }


    /**
     * Apply/ execute all filters of the current state.
     *
     * @return boolean Returns true on success or false on failure
     */
    public function filtersApply(): bool
    {
        $status = true;

        foreach ( $this->_items as $item ) {
            $this->filterItem( $item );
            if ( $item->isValid() === false ) {
                $status = false;
            }
        }

        return $status;
    }


    /**
     * Apply filters of the given item.
     *
     * @param Mumsys_Variable_Item_Interface $item Validate item
     *
     * @return boolean Returns true on success or false on failure
     */
    public function filterItem( Mumsys_Variable_Item_Interface $item ): bool
    {
        $filters = $item->filtersGet( null );
        $state = $item->stateGet();
        $status = true;

        if ( empty( $filters[$state] ) ) {
            $item->setValidated( $status );
            return $status;
        }

        $value = $item->getValue();
        $itemName = ( ( $a = $item->getLabel() ) ? $a : $item->getName() );

        $toReplace = $params = $x = null;

        $_filters = $filters[$state];

        foreach ( $_filters as $opts ) {
            $parameters = $opts['params'];
            /** @var string $cmd 4sca */
            $cmd = $opts['cmd'];

            if ( is_callable( $cmd ) ) {
                if ( $parameters !== null ) {
                    if ( is_array( $parameters ) ) {
                        $params = array();
                        foreach ( $parameters as $tmp => &$toReplace ) {
                            if ( $toReplace === '%value%' ) {
                                $params[$tmp] = $value;
                            } else {
                                $params[$tmp] = $toReplace;
                            }
                        }

                        // $x = $this->_execExternal($cmd, $params, 'array');
                        $x = call_user_func_array( $cmd, $params );
                    } else {
                        if ( $parameters === '%value%' ) {
                            $params = $value;
                        } else {
                            $params = $parameters;
                        }

                        $x = $this->_execExternal( $cmd, $params );
                    }
                } else {
                    $x = $this->_execExternal( $cmd, $value );
                }

                if ( $x === false ) {
                    $status = false;
                    /* false as return or false of the callback ?
                     * boolean values should not be filtered! */
                    $message = sprintf(
                        $this->_messageTemplates['FILTER_ERROR'], $cmd, $itemName
                    );
                    $item->setErrorMessage( self::FILTER_ERROR, $message );
                } else {
                    $item->setValue( $x );
                    $value = $x;
                }
            } else {
                $status = false;
                $message = sprintf(
                    $this->_messageTemplates['FILTER_NOTFOUND'], $cmd, $itemName
                );
                $item->setErrorMessage( self::FILTER_NOTFOUND, $message );
            }
        }

        $item->setValidated( $status );

        return $status;
    }


    /**
     * Apply/ execute all callbacks.
     *
     * @param array|string|null $data Mixed data to pipe to the callback function
     *
     * @return boolean Returns true on success or false on failure
     */
    public function callbacksApply( $data = null ): bool
    {
        $status = true;

        foreach ( $this->_items as $item ) {
            $this->callbackItem( $item, $data );
            if ( $item->isValid() === false ) {
                $status = false;
            }
        }

        return $status;
    }


    /**
     * Apply callbacks of the given item based on the current state.
     *
     * Callback function signature is:
     * <code>
     * functionName( Mumsys_Variable_Item_Interface object,
     *      mixed $dataFromExtenrnalCallerFunc = null
     * );
     * </code>
     *
     * @param Mumsys_Variable_Item_Interface $item Validate item
     * @param array|string|null $data Mixed data to pipe to the callback function
     *
     * @return boolean Returns true on success or false on failure
     */
    public function callbackItem( Mumsys_Variable_Item_Interface $item,
        $data = null ): bool
    {
        $callbacks = $item->callbacksGet( null );
        $state = $item->stateGet();
        $status = true;

        if ( empty( $callbacks[$state] ) ) {
            $item->setValidated( $status );
            return $status;
        }

        $value = $item->getValue();
        $itemName = ( ( $a = $item->getLabel() ) ? $a : $item->getName() );

        $_callbacks = $callbacks[$state];

        foreach ( $_callbacks as $opts ) {
            $parameters = $opts['params'];
            $cmd = $opts['cmd'];

            if ( is_callable( $cmd ) ) {
                if ( $parameters !== null ) {
                    if ( is_array( $parameters ) ) {
                        $params = array();
                        foreach ( $parameters as $tmp => &$toReplace ) {
                            if ( $toReplace === '%value%' ) {
                                $params[$tmp] = $value;
                            } else {
                                $params[$tmp] = $toReplace;
                            }
                        }

                        $x = call_user_func( $cmd, $item, $data, $params );
                    } else {
                        if ( $parameters === '%value%' ) {
                            $params = $value;
                        } else {
                            $params = $parameters;
                        }

                        $x = call_user_func( $cmd, $item, $data, $params );
                    }
                } else {
                    $x = call_user_func( $cmd, $item, $data );
                }

                if ( $x === false ) {
                    $status = false;
                    /* false as return or false of the callback ?
                     * boolean values should not be filtered! */
                    $message = sprintf(
                        $this->_messageTemplates['CALLBACK_ERROR'],
                        $cmd,
                        $itemName,
                        json_encode( $value )
                    );
                    $item->setErrorMessage( self::CALLBACK_ERROR, $message );
                } else {
                    $item->setValue( $x );
                    $value = $x;
                }
            } else {
                $status = false;
                $message = sprintf(
                    $this->_messageTemplates['CALLBACK_NOTFOUND'], $cmd, $itemName
                );
                $item->setErrorMessage( self::CALLBACK_NOTFOUND, $message );
            }
        }

        $item->setValidated( $status );

        return $status;
    }


    /**
     * Compare two item values if the items types are equal.
     *
     * A copy from old Mumsys_Fields
     *
     * @param Mumsys_Variable_Item_Interface $oItemA Base item
     * @param Mumsys_Variable_Item_Interface $oItemB Item to compare its value
     * to item A based on the type
     * @param string $op Operator to use for comparison:
     *  'eq', '==',
     *  'type_eq', '==='
     *  'neq','!=',
     *  'type_neq' = '!==',
     *  'gt' '>', 'gte', '>=',
     *  'lt', '<', 'lte', '<='
     *
     * @return boolean Returns false on error or true on success
     */
    public function compare( Mumsys_Variable_Item_Interface $oItemA,
        Mumsys_Variable_Item_Interface $oItemB, string $op = '===' ): bool
    {
        $typeA = $oItemA->getType();
        $typeB = $oItemB->getType();
        if ( $typeA !== $typeB ) {
            $mesg = sprintf(
                'Invalid types. Type of item A: "%1$s", item B "%2$s"',
                $typeA,
                $typeB
            );
            throw new Mumsys_Variable_Manager_Exception( $mesg );
        }

        $valueA = $oItemA->getValue();
        $valueB = $oItemB->getValue();

        // todo: more operators
        $_operators = array(
            'eq' => '==',
            'type_eq' => '===', // exact test
            'neq' => '!=',
            'type_neq' => '!==', // exact test
            'gt' => '>',
            'gte' => '>=',
            'lt' => '<',
            'lte' => '<='
        );

        // find operator
        if ( in_array( $op, $_operators ) ) {
            $op = $op;
        } elseif ( isset( $_operators[$op] ) ) {
            $op = $_operators[$op];
        }

        switch ( $op ) {
            case '==':
                $result = ( $valueA == $valueB );
                break;

            case '===':
                $result = ( $valueA === $valueB );
                break;

            case '!=':
                $result = ( $valueA != $valueB );
                break;

            case '!==':
                $result = ( $valueA !== $valueB );
                break;

            case '>':
                $result = ( $valueA > $valueB );
                break;

            case '>=':
                $result = ( $valueA >= $valueB );
                break;

            case '<':
                $result = ( $valueA < $valueB );
                break;

            case '<=':
                $result = ( $valueA <= $valueB );
                break;

            default:
                $mesg = sprintf( 'Operator "%1$s" not implemented', $op );
                throw new Mumsys_Variable_Manager_Exception( $mesg );
        }

        return $result;
    }


    /**
     * Execute and return external filter or callback result.
     *
     * @param string $cmd Function/method to be called
     * @param string|array|null $params Parameters to pipe to the function
     * @param string $ptype Type of the parameters, eg: empty-string|string|array
     *
     * @return mixed|false Returns the result of the callback or false on errors
     */
    private function _execExternal( string $cmd, $params, $ptype = 'string' )
    {
        /* future for callbacks:
          if ( $ptype === 'array' ) {
          return call_user_func_array($cmd, $params);
          } */

        $value = false;

        if ( $params === null ) {
            return $value;
        }

        /* switches to improve performance */
        switch ( $cmd )
        {
            case 'trim':
                $value = trim( strval( $params ) );
                break;

            case 'htmlspecialchars':
                $value = htmlspecialchars( strval( $params ) );
                break;

            case 'htmlentities':
                $value = htmlentities( strval( $params ) );
                break;

            default:
                $value = call_user_func( $cmd, $params );
        }

        return $value;
    }

}
