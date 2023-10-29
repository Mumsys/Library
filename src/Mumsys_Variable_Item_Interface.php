<?php

/**
 * Mumsys_Variable_Item_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2006 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Variable
 * @verion      2.3.4
 * Created: 2006 based on Mumsys_Field, renew 2016
 */


/**
 * Default variable item interface.
 *
 * Default item implementation as variable item interface for general web
 * related tasks like create/edit/save variables.
 * Each variable should be an object with a standard set of methodes which are
 * needed for these tasks. This is the standard set of methodes as interface
 */
interface Mumsys_Variable_Item_Interface
{
    /**
     * Initialisation of the variable item object.
     *
     * @see $_properties
     *
     * @param array $props List of key/value config parameters to be set.
     * Config values MUST NOT be null!
     */
    public function __construct( array $props = array() );


    /**
     * Returns the registered item input properties available.
     *
     * @return array List of key/value pairs of the item
     */
    public function getItemValues(): array;


    /**
     * Returns the list of possible properties and activation flag.
     *
     * @return array List of property-name/flag-available pairs
     */
    public function getProperties(): array;


    /**
     * Returns the item key/identifier name.
     * Note: From a list of key/value pairs: this is the key used as name.
     *
     * @param string $default Default (null) return value if name is not available
     *
     * @return string|null Item name key/identifier or null or $default value
         */
    public function getName( string $default = null );


    /**
     * Sets the item key name/ identifier.
     *
     * If value exists and is the same than the current one null is returned.
     *
     * @param string $value Item key/itenifier
     */
    public function setName( string $value ): void;


    /**
     * Returs the item value or null if not set;
     *
     * @param mixed $default Default return value if value not exists
     *
     * @return mixed|null Returns the item value or $default
     */
    public function getValue( $default = null );


    /**
     * Sets the item value.
     *
     * If value exists and is the same than the current one null is returned.
     *
     * @param mixed $value Item value to be set
     */
    public function setValue( $value ): void;

    /**
     * Returns all error messages of this item if any exists.
     *
     * @return array<string,mixed> List of key/value pairs of error messages
     * where 'value's are bounded to the item properties
     */
    public function getErrorMessages(): array;


    /**
     * Sets/ replace an error message by given key and message value.
     *
     * @param string $key Internal ID of the error (e.g: TOO_LONG, TOO_SHORT message)
     * @param string $value Error message value
     */
    public function setErrorMessage( string $key, string $value ): void;


    /**
     * Set/ replaces the list of error messages.
     *
     * @param array<string,mixed> $list List of key/value pairs of error messages
     */
    public function setErrorMessages( array $list ): void;


    /**
     * Removes all detected errors.
     *
     * What setErrorMessages() in version 1.0 does. Now splitted; Hacking helper.
     * Note: This is not the standard use case this tool was made for (all
     * values to be reported, not to change them. Claning them will end in
     * more complex debuging) but make thing comfortable e.g. for translation
     */
    public function clearErrorMessages(): void;

    /**
     * Returns the item validation status.
     *
     * @return boolean Returns true on success otherwise false
     */
    public function isValid();


    /**
     * Sets the validation status.
     *
     * @param boolean|int $success True|1 for success otherwise false|0
     * Default: false
     */
    public function setValidated( $success = false ): void;


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
    public function filterAdd( $state, $cmd, array $parameters = null ): void;


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
    public function filtersGet( string $state = null ): ?array;


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
        $params = null ): void;


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
    public function callbacksGet( string $state = null ): ?array;


    /**
     * Tests if the Item was modified or not.
     *
     * @return boolean True if modified otherwise false
     */
    public function isModified(): bool;


    /**
     * Sets the modified flag of the object.
     */
    public function setModified(): void;


    /**
     * Sets the current state for filters and callbacks.
     *
     * @param string $state State to be set: 'onEdit', default: 'onView',
     * 'onSave', 'before', 'after'
     *
     * @throws Mumsys_Variable_Item_Exception If state not part of {@see $_states}
     */
    public function stateSet( string $state = 'onView' ): void;


    /**
     * Returns the current state.
     *
     * @return string Current state
     */
    public function stateGet(): string;


    /**
     * Returns the list of possible states.
     *
     * @return array List of states
     */
    public function statesGet(): array;


    //
    // --- /end abstract methodes
    //

    /**
     * Returns the item type.
     *
     * Hint: Mumsys_Variable_Abstract::$_types to see internal handling
     * by the manager.
     *
     * @return string|null Item type or null if not available
     */
    public function getType(): ?string;


    /**
     * Sets the item type.
     *
     * If value exists and is the same than the current one null is returned.
     *
     * Types are php types and optional types like email, date or datetime from
     * mysql which can and will be handled as types in this class. For more
     * @see Mumsys_Variable_Abstract::$_types for a complete list.
     *
     * @param string $value Type to be set
     *
     * @return void
     */
    public function setType( string $value ): void;


    /**
     * Returns the minimum item value length (number or string length).
     *
     * @return float|null Minimum length or null if not available
     */
    public function getMinLength(): ?float;


    /**
     * Sets the minimum item value length (number or string length).
     *
     * @param float $value Minimum item value length
     */
    public function setMinLength( float $value ): void;


    /**
     * Returns the maximum item value length (number or string length).
     *
     * @return float|null Maximum item value length or null if not available
     */
    public function getMaxLength(): ?float;


    /**
     * Sets the maximum item value length (number or string length).
     *
     * @param float $value Maximum item value length
     */
    public function setMaxLength( float $value ): void;


    /**
     * Returns the list of regular expressions.
     *
     * @return array List of regular expression or empty array for none
     */
    public function getRegex(): array;


    /**
     * Sets/ replaces a list of regular expressions.
     *
     * @param array $value Regular expressions to set/replace
     */
    public function setRegex( array $value ): void;


    /**
     * Adds a new regular expressions to the list of regular expressions.
     *
     * @param string $value Regular expression
     */
    public function addRegex( string $value ): void;


    /**
     * Returns the allow empty flag of the item.
     *
     * @return boolean Allow empty flag or null if not available
     */
    public function getAllowEmpty(): ?bool;


    /**
     * Sets the allow empty flag of the item.
     *
     * @param boolean $value True to allow empty values or false for not allow
     * empty values
     */
    public function setAllowEmpty( bool $value ): void;


    /**
     * Returns the required status of the item.
     *
     * @return boolean Required status or null if not available
     */
    public function getRequired(): ?bool;


    /**
     * Sets required flag of the item.
     *
     * @param boolean $value Required flag
     */
    public function setRequired( bool $value ): void;


    /**
     * Returns the item label (or item "name" if label was not set or 'default'
     * if both not set).
     *
     * @param string $altnKey Alternativ property key to get if label not
     * exists (default: "name" for getName().
     * @param string $default Default value to return if the property not set
     *
     * @return string Item/ variable label
     */
    public function getLabel( string $altnKey = 'name', string $default = '' ): string;


    /**
     * Sets the item label.
     *
     * @param string $value Label to set
     */
    public function setLabel( string $value ): void;


    /**
     * Returns the item description.
     *
     * @param string|null $default Default value to return if the property was not set
     *
     * @return string|null Item description, null or string $default if not available
     */
    public function getDescription( string $default = null ): ?string;


    /**
     * Sets the item description.
     *
     * Note: Description of what kind of value will be expected e.g. in a form.
     * E.g: "Enter your email address"
     *
     * @param string $value Description to set
     */
    public function setDescription( string $value ): void;


    /**
     * Returns the item additional information value.
     *
     * @param string|null $default Default value to return if the property was
     * not set
     *
     * @return string|null Item information, null or string $default if not
     * available
     */
    public function getInformation( string $default = null ): ?string;


    /**
     * Sets the item additional information value.
     * Note: Information about the item of what kind of value will be expected
     * or how things will go.
     *
     * @param string $value Additional information value
     */
    public function setInformation( string $value ): void;



    /**
     * Returns the default item value.
     *
     * @param mixed|null $default Default value to return if the property not set
     *
     * @return mixed|null Item "default" value or null or $default if not available
     */
    public function getDefault( $default = null );


    /**
     * Sets a default value for the item.
     *
     * @param mixed $value Default value to set
     */
    public function setDefault( $value ): void;

}
