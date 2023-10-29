<?php declare (strict_types=1);

/**
 * Mumsys_Variable_Item_Default
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2006 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Variable
 * Created: 2006 based on Mumsys_Field, renew 2016
 */


/**
 * Default item implementation as variable item interface for general web
 * related tasks like create/edit/save variables.
 * Each variable should be an object with a standard set of methodes which are
 * needed for these tasks.
 * This class only keeps minimum getter/setter like get/set name, value and
 * error messages.
 */
abstract class Mumsys_Variable_Item_Abstract
    extends Mumsys_Variable_Abstract
    implements Mumsys_Variable_Item_Interface
{
    /**
     * Version ID information
     */
    public const VERSION = '2.4.2';

    /**
     * List of initial incoming variable properties to be set on construction.
     * @var array
     */
    protected $_input = array();

    /**
     * Flag to set if some properties has changed.
     * @var boolean
     */
    protected $_modified = false;

    /**
     * Flag if value was validated or not.
     * @var boolean
     */
    private $_isValidated = false;

    /**
     * Flag if validation succeed.
     * @var boolean
     */
    private $_isValid = false;

    /**
     * List of possible states to render the value.
     * @var array
     */
    private $_states = array('onEdit', 'onView', 'onSave', 'before', 'after');

    /**
     * Current status to use filters or callbacks for.
     * @var string
     */
    private $_state = 'onView';

    /**
     * Registered filters.
     *
     * @var array
     */
    private $_filters = null;

    /**
     * Registered callbacks.
     * @var array
     */
    private $_callbacks = null;


    /**
     * Returns the item value for output.
     *
     * @return string Converts the value to string if differerent.
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }


    /**
     * Returns the registered item input properties available.
     *
     * @return array List of key/value pairs of the item
     */
    public function getItemValues(): array
    {
        return $this->_input;
    }


    /**
     * Returns the item key/identifier name.
     * Note: From a list of key/value pairs: this is the key used as name.
     *
     * @param string $default Default (null) return value if name is not available
     *
     * @return string|null Item name key/identifier or null or $default value
     */
    public function getName( string $default = null )
    {
        return ( isset( $this->_input['name'] ) ? $this->_input['name'] : $default );
    }


    /**
     * Sets the item key name/ identifier.
     *
     * If value exists and is the same than the current one null is returned.
     *
     * @param string $value Item key/itenifier
     */
    public function setName( string $value ): void
    {
        if ( isset( $this->_input['name'] ) && $value === $this->_input['name'] ) {
            return;
        }

        $this->_input['name'] = $value;
        $this->_modified = true;
    }


    /**
     * Returns the item value or null if not set
     *
     * @param mixed $default Default return value if value not exists
     *
     * @return mixed|null Returns the item value or $default
     */
    public function getValue( $default = null )
    {
        return ( isset( $this->_input['value'] ) ) ? $this->_input['value'] : $default;
    }


    /**
     * Sets the item value.
     *
     * @param mixed $value Item value to be set
     */
    public function setValue( $value ): void
    {
        if ( $value === $this->getValue() ) {
            return;
        }

        $this->_input['value'] = $value;
        $this->_modified = true;
    }


    /**
     * Returns all error messages of this item if any exists.
     *
     * @return array List of key/value pairs of error messages
     */
    public function getErrorMessages(): array
    {
        return ( isset( $this->_input['errors'] ) ? (array)$this->_input['errors'] : array() );
    }


    /**
     * Sets/ replace an error message by given key and message value.
     *
     * @param string $key Internal ID of the error (e.g: TOO_LONG, TOO_SHORT message)
     * @param string $value Error message value
     */
    public function setErrorMessage( string $key, string $value ): void
    {
        $this->_input['errors'][$key] = $value;
    }


    /**
     * Set/ replaces the list of error messages.
     *
     * @param array<string,mixed> $list List of key/value pairs of error messages
     */
    public function setErrorMessages( array $list ): void
    {
        foreach ( $list as $key => $value ) {
            $this->setErrorMessage( $key, $value );
        }
    }


    /**
     * Removes all detected errors.
     *
     * What setErrorMessages() in version 1.0 does. Now splitted; Hacking helper.
     * Note: This is not the standard use case this tool was made for (all
     * values to be reported, not to change them. Claning them will end in
     * more complex debuging) but make thing comfortable e.g. for translation
     */
    public function clearErrorMessages(): void
    {
        $this->_input['errors'] = array();
    }


    /**
     * Returns the item validation status.
     *
     * @return boolean Returns true on success otherwise false
     */
    public function isValid()
    {
        return $this->_isValid;
    }


    /**
     * Sets the validation status.
     *
     * @param boolean|int $success True|1 for success otherwise false|0;
     * Default: false
     */
    public function setValidated( $success = false ): void
    {
        $this->_isValidated = true;
        $this->_isValid = (bool)$success;
    }


    /**
     * Adds a filter for the given state.
     *
     * Filters have a variable signature like php functions have. Filter
     * function signature is: functionName(mixed params)
     * To replace/use the current item value use %value% in the parameters list.
     *
     * Differents between filters and callbacks:
     *  - different function signature
     *  - filters are only for the item object itselves
     *  - callbacks can be used from outside using callbacksGet() methode.
     *
     * Example:
     * <code>
     * // php function substr($value, 0, 150);
     * $item->filterAdd('onSave', 'substr', array('%value%', 0, 150) );
     * // php function str_replace('this', 'by that', $value]);
     * $item->filterAdd('onSave', 'str_replace', array('this', 'by that', '%value%'));
     * // call php's substr and cut the last 3 chars
     * $item->filterAdd('onSave', 'substr', array('%value%', -3) );
     * // cast total to be a float value. Both options are possible:
     * $item->filterAdd('onEdit', 'floatval');
     * $item->filterAdd('onEdit', 'floatval', array('%value%') );
     * </code>
     *
     * @param string $state State to add the filter for {@link $_states}
     * @param string $cmd Function name to call
     * @param array|null $parameters Parameters to be set for 'php' operations.
     *
     * @throws Mumsys_Variable_Item_Exception If state not part of {@link $_states}
     */
    public function filterAdd( $state, $cmd, array $parameters = null ): void
    {
        if ( $this->_initExternalType( 'filters' ) ) {
            $this->_initExternalCalls( 'filters' );
        }

        $this->_filterSet( $state, $cmd, $parameters );
    }


    /**
     * Returns a list of filter configurations.
     *
     * If flag $state is not set (null) all filters will return. Otherwise if
     * string 'current' given: it will return the list of the current state or
     * the list of the selected callbacks will return. {@see $_states}.
     *
     * @param string|null $state State value/s to return, all (null), 'current'
     * (the current state or the selected of eg (onView, onEdit, before, after...).
     *
     * @return array|null List of filter rules or null if not alvailable.
     *
     * @throws Mumsys_Variable_Item_Exception If state not part of {@see $_states}
     */
    public function filtersGet( string $state = null ): ?array
    {
        if ( $this->_initExternalType( 'filters' ) === true ) {
            $this->_initExternalCalls( 'filters' );
        }

        if ( $state === null ) {
            return $this->_filters;
        }

        if ( $state === 'current' ) {
            $_state = $this->_state;
        } else {
            $this->_stateCheck( $state );
            $_state = $state;
        }

        if ( !isset( $this->_filters[$_state] ) ) {
            $return = null;
        } else {
            $return = $this->_filters[$_state];
        }

        return $return;
    }


    /**
     * Adds a callback for the given state.
     *
     * Callbacks have a static function signature:
     *      functionName(Mumsys_Variable_Item $item, array $optionalParams)
     *
     * Differents between filters and callbacks:
     *  - different function signature
     *  - filters are only for the item object itselves
     *  - callbacks can be used from outside using callbacksGet() methode.
     *
     * Example:
     * <code>
     * // To call eg: my_substr(Mumsys_Variable_Item $item, $params=array(0, 150));
     * $item->callbackAdd('onSave', 'my_substr', array(0, 150));
     * </code>
     *
     * @param string $state State to add the filter for {@link $_states} e.g.:
     * 'onEdit', 'onSave', 'onView'
     * @param string $cmd Function name to call
     * @param array|string|null $params Parameters to be set for 'php' operations.
     *
     * @throws Mumsys_Variable_Item_Exception If state not part of {@link $_states}
     */
    public function callbackAdd( string $state, string $cmd,
        $params = null ): void
    {
        if ( $this->_initExternalType( 'callbacks' ) === true ) {
            $this->_initExternalCalls( 'callbacks' );
        }

        $this->_callbackSet( $state, $cmd, $params );
    }


    /**
     * Returns a list of callback configurations.
     *
     * If flag $state is not set (null) all callbacks will return. Otherwise if
     * string 'current' given: it will return the list of the current state or
     * the list of the selected callbacks will return. {@see $_states}.
     *
     * @param string|null $state State value to return, all (null), 'current'
     * (the current state or the selected of eg (onView, onEdit, before, after...).
     *
     * @return array|null List of callbacks rules or null if not alvailable.
     *
     * @throws Mumsys_Variable_Item_Exception If state not part of {@see $_states}
     */
    public function callbacksGet( string $state = null ): ?array
    {
        if ( $this->_initExternalType( 'callbacks' ) === true ) {
            $this->_initExternalCalls( 'callbacks' );
        }

        if ( $state === null ) {
            return $this->_callbacks;
        }

        if ( $state === 'current' ) {
            $_state = $this->_state;
        } else {
            $this->_stateCheck( $state );
            $_state = $state;
        }

        if ( !isset( $this->_callbacks[$_state] ) ) {
            $return = null;
        } else {
            $return = $this->_callbacks[$_state];
        }

        return $return;
    }



    /**
     * Tests if the Item was modified or not.
     *
     * @return boolean True if modified otherwise false
     */
    public function isModified(): bool
    {
        return $this->_modified;
    }


    /**
     * Sets the modified flag of the object.
     */
    public function setModified(): void
    {
        $this->_modified = true;
    }


    /**
     * Sets the current state for filters and callbacks.
     *
     * @param string $state State to be set: 'onEdit', default: 'onView',
     * 'onSave', 'before', 'after'
     *
     * @throws Mumsys_Variable_Item_Exception If state not part of {@see $_states}
     */
    public function stateSet( string $state = 'onView' ): void
    {
        $this->_stateCheck( $state );
        $this->_state = $state;
    }


    /**
     * Returns the current state.
     *
     * @return string Current state
     */
    public function stateGet(): string
    {
        return $this->_state;
    }


    /**
     * Returns the list of possible states.
     *
     * @return array List of states
     */
    public function statesGet(): array
    {
        return $this->_states;
    }


    //
    // --- private methodes ---------------------------------------------------
    //

    /**
     * Sets the filter to the list of item filters.
     *
     * @param string $state State to add the filter for {@see $_states} e.g.:
     * 'onEdit', 'onSave', 'onView'
     * @param string $cmd Function name to call
     * @param array|string|null $parameters Parameters to be set for 'php' operations.
     *
     * @throws Mumsys_Variable_Item_Exception If state not part of {@see $_states of manager}
     */
    private function _filterSet( string $state, string $cmd, $parameters = null ): void
    {
        $this->_stateCheck( $state );
        $this->_filters[$state][] = array(
            'cmd' => $cmd,
            'params' => $parameters,
        );
    }


    /**
     * Sets the callback to the list of item callbacks.
     *
     * @param string $state State to add the filter for {@link $_states} e.g.:
     * 'onEdit', 'onSave', 'onView'
     * @param string $cmd Function name to call
     * @param array|string|null $parameters Parameters to be set for 'php' operations.
     *
     * @throws Mumsys_Variable_Item_Exception If state not part of {@see $_states}
     */
    private function _callbackSet( string $state, string $cmd, $parameters = null ): void
    {
        $this->_stateCheck( $state );
        $this->_callbacks[$state][] = array(
            'cmd' => $cmd,
            'params' => $parameters,
        );
    }


    /**
     * Initialize internal callback variables to be able to fill it with
     * properties when needed and not in construction which may not needed
     * in some cases.
     *
     * @param string $type Type of the variable to init: filters or callbacks
     *
     * @return boolean Returns true to init existing callbacks from construction,
     * false if there are no callbacks or filters set
     */
    private function _initExternalType( string $type ): bool
    {
        $_type = '_' . $type;
        if ( $this->$_type !== null ) {
            return false;
        }

        if ( !isset( $this->_input[$type] ) ) {
            $this->$_type = array();
            return false;
        }

        return true;
    }


    /**
     * Initialize a list of callback/filter rules.
     *
     * Example:
     * <code>
     * $list = array(
     *  'onView' => array(
     *      'substr', array('%value%', 0, 150),
     *      'str_replace', array('this', 'by that', '%value%')),
     *  'onEdit' => array(
     *      'htmlspecialchars', array('%value%', ENT_QUOTES)),
     *  'onSave => array(
     *      'trim',
     *      'json_encode' => array('%value%', true)
     *  ...
     * </code>
     *
     * @param string $type Type to initialise e.g: "filters"|"callbacks"
     */
    private function _initExternalCalls( string $type ): void
    {
        foreach ( $this->_input[$type] as $state => $props ) {
            if ( is_array( $props ) ) {
                foreach ( $props as $cmd => $params ) {
                    if ( is_int( $cmd ) ) {
                        $this->_setExternalCall( $type, $state, $params );
                    } else {
                        $this->_setExternalCall( $type, $state, $cmd, $params );
                    }
                }
            } else if ( is_string( $props ) ) {
                $this->_setExternalCall( $type, $state, $props );
            }
        }
    }


    /**
     * Sets/ adds a callback/filter by given type..
     *
     * @param string $type Type to set e.g.: "filters" | "callbacks"
     * @param string $state State to add the filter for {@link $_states} e.g.:
     * 'onEdit', 'onSave', 'onView'
     * @param string $cmd Function name to call
     * @param array|string|null $params Parameters to be set for 'php' operations.
     *
     * @throws Mumsys_Variable_Item_Exception On errors
     */
    private function _setExternalCall( string $type, string $state, string $cmd,
        $params = null ): void
    {
        switch ( $type )
        {
            case 'filters':
                $this->_filterSet( $state, $cmd, $params );
                break;

            case 'callbacks':
                $this->_callbackSet( $state, $cmd, $params );
                break;
        }
    }


    /**
     * Checks if given state is in the list of allowed/ implemented states.
     *
     * @param string $state State to be set: 'onEdit','onView', 'onSave'...
     *
     * @throws Mumsys_Variable_Item_Exception If state not part of {@link $_states}
     */
    private function _stateCheck( string $state ): void
    {
        if ( !in_array( $state, $this->_states ) ) {
            $message = sprintf( 'State "%1$s" unknown', $state );
            throw new Mumsys_Variable_Item_Exception( $message );
        }
    }

}
