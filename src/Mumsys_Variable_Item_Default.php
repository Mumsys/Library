<?php

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
 * Default variable item class.
 *
 * Default item implementation as variable item interface for general web
 * related tasks like create/edit/save variables.
 *
 * This class does the following: Each variable should be an object with a
 * standard set of methodes which are needed for these tasks.
 * This class keeps default properties like name, value, type, minlen, maxlen,
 * label, description, additional information, regular expressions for
 * validation checks, filter, callbacks setups and error messages.
 * With this you already have a powerful set to handle and validate variables
 * internally and also for the frontend to show properties around the key/
 * value pairs.
 */
class Mumsys_Variable_Item_Default
    extends Mumsys_Variable_Item_Abstract
    implements Mumsys_Variable_Item_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '3.2.6';

    /**
     * List of key/value pairs (property/[boolean: en|dis-abled] handled by
     * this item as whitelist.
     *
     * @var array
     */
    private $_properties = array(
        'name' => true,
        'value' => true,
        'label' => true,
        'desc' => true,
        'info' => true,
        'default' => true,
        'type' => true,
        'minlen' => true,
        'maxlen' => true,
        'regex' => true,
        'allowEmpty' => true,
        'required' => true,
        'errors' => true,
        'filters' => true,
        'callbacks' => true,
    );


    /**
     * Initialisation of the item object.
     *
     * @see $_properties
     *
     * @param array $props List of key/value config parameters to be set.
     * Config values MUST NOT be null!
     */
    public function __construct( array $props = array() )
    {
        // set allowed whitelist
        foreach ( $this->_properties as $key => $value ) {
            if ( $value === true && isset( $props[$key] ) ) {
                $this->_input[$key] = $props[$key];
            }
        }

        if ( isset( $props['state'] ) ) {
            $this->stateSet( $props['state'] );
        }
    }


    /**
     * Returns the list of possible properties and activation flag.
     *
     * @return array List of property-name/flag-available pairs
     */
    public function getProperties(): array
    {
        return $this->_properties;
    }


    /**
     * Returns the variable item type.
     *
     * Hint: Mumsys_Variable_Abstract::$_types to see internal handling
     * by the manager.
     *
     * @return string|null Item type or null if not available
     */
    public function getType(): ?string
    {
        return ( isset( $this->_input['type'] ) ) ? (string)$this->_input['type'] : null;
    }


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
     * @throws Mumsys_Variable_Item_Exception If type not implemented
     */
    public function setType( string $value ): void
    {
        if ( $value === $this->getType() ) {
            return;
        }

        if ( !in_array( $value, Mumsys_Variable_Abstract::$_types ) ) {
            $message = sprintf( 'Type "%1$s" not implemented', $value );
            throw new Mumsys_Variable_Item_Exception( $message );
        }

        $this->_input['type'] = $value;
        $this->_modified = true;
    }


    /**
     * Returns the minimum item value length (number or string length).
     *
     * @return float|null Minimum length or null if not available
     */
    public function getMinLength(): ?float
    {
        return ( isset( $this->_input['minlen'] ) ) ? (float)$this->_input['minlen'] : null;
    }


    /**
     * Sets the minimum item value length (number or string length).
     *
     * @param float $value Minimum item value length
     */
    public function setMinLength( float $value ): void
    {
        if ( $value === $this->getMinLength() ) {
            return;
        }

        $this->_input['minlen'] = $value;
        $this->_modified = true;
    }


    /**
     * Returns the maximum item value length (number or string length).
     *
     * @return float|null Maximum item value length or null if not available
     */
    public function getMaxLength(): ?float
    {
        return ( isset( $this->_input['maxlen'] ) ) ? (float)$this->_input['maxlen'] : null;
    }


    /**
     * Sets the maximum item value length (number or string length).
     *
     * @param float $value Maximum item value length
     */
    public function setMaxLength( float $value ): void
    {
        if ( $value === $this->getMaxLength() ) {
            return;
        }

        $this->_input['maxlen'] = $value;
        $this->_modified = true;
    }


    /**
     * Returns the list of regular expressions.
     *
     * @return array List of regular expression or empty array for none
     */
    public function getRegex(): array
    {
        return ( isset( $this->_input['regex'] ) ) ? (array)$this->_input['regex'] : array();
    }


    /**
     * Sets/ replaces a list of regular expressions.
     *
     * @param array $value Regular expressions to set/replace
     */
    public function setRegex( array $value ): void
    {
        if ( $value === $this->getRegex() ) {
            return;
        }

        $this->_input['regex'] = $value;
        $this->_modified = true;
    }


    /**
     * Adds a new regular expressions to the list of regular expressions.
     *
     * @param string $value Regular expression
     */
    public function addRegex( string $value ): void
    {
        $this->_input['regex'][] = $value;
        $this->_modified = true;
    }


    /**
     * Returns the allow empty flag of the item.
     *
     * @return boolean|null Allow empty flag or null if not available
     */
    public function getAllowEmpty(): ?bool
    {
        return ( isset( $this->_input['allowEmpty'] ) ? (bool)$this->_input['allowEmpty'] : null );
    }


    /**
     * Sets the allow empty flag of the item.
     *
     * @param boolean $value True to allow empty values or false for not allow
     * empty values
     */
    public function setAllowEmpty( bool $value ): void
    {
        if ( $value === $this->getAllowEmpty() ) {
            return;
        }

        $this->_input['allowEmpty'] = $value;
        $this->_modified = true;
    }


    /**
     * Returns the required status of the item.
     *
     * @return boolean|null Required status or null if not available
     */
    public function getRequired(): ?bool
    {
        return ( isset( $this->_input['required'] ) ? (bool)$this->_input['required'] : null );
    }


    /**
     * Sets required flag of the item.
     *
     * @param boolean $value Required flag
     */
    public function setRequired( bool $value ): void
    {
        if ( $value === $this->getRequired() ) {
            return;
        }

        $this->_input['required'] = $value;
        $this->_modified = true;
    }


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
    public function getLabel( string $altnKey = 'name', string $default = '' ): string
    {
        if ( isset( $this->_input['label'] ) ) {
            $return = (string)$this->_input['label'];
        } else if ( isset( $this->_input[$altnKey] ) ) {
            $return = (string)$this->_input[$altnKey];
        } else {
            $return = $default;
        }

        return $return;
    }


    /**
     * Sets the item label.
     *
     * @param string $value Label to set
     */
    public function setLabel( string $value ): void
    {
        if ( $value === $this->getLabel() ) {
            return;
        }

        $this->_input['label'] = $value;
        $this->_modified = true;
    }


    /**
     * Returns the item description.
     *
     * @param string|null $default Default value to return if the property was not set
     *
     * @return string|null Item description, null or string $default if not available
     */
    public function getDescription( string $default = null ): ?string
    {
        return ( isset( $this->_input['desc'] ) ) ? $this->_input['desc'] : $default;
    }


    /**
     * Sets the item description.
     *
     * Note: Description of what kind of value will be expected e.g. in a form.
     * E.g: "Enter your email address"
     *
     * @param string $value Description to set
     */
    public function setDescription( string $value ): void
    {
        if ( $value === $this->getDescription() ) {
            return;
        }

        $this->_input['desc'] = $value;
        $this->_modified = true;
    }


    /**
     * Returns the item additional information value.
     *
     * @param string|null $default Default value to return if the property was
     * not set
     *
     * @return string|null Item information, null or string $default if not
     * available
     */
    public function getInformation( string $default = null ): ?string
    {
        return ( isset( $this->_input['info'] ) ) ? $this->_input['info'] : $default;
    }


    /**
     * Sets the item additional information value.
     *
     * Note: Information about the item of what kind of value will be expected
     * or how things will go.
     *
     * @param string $value Additional information value
     */
    public function setInformation( string $value ): void
    {
        if ( $value === $this->getInformation() ) {
            return;
        }

        $this->_input['info'] = $value;
        $this->_modified = true;
    }


    /**
     * Returns the default item value.
     *
     * @param mixed $default Default value to return if the property was not set
     *
     * @return mixed|null Item "default" value or null or $default if not available
     */
    public function getDefault( $default = null )
    {
        return ( isset( $this->_input['default'] ) ? $this->_input['default'] : $default );
    }


    /**
     * Sets a default value for the item.
     *
     * @param mixed $value Default value to set
     */
    public function setDefault( $value ): void
    {
        if ( $value === $this->getDefault() ) {
            return;
        }

        $this->_input['default'] = $value;
        $this->_modified = true;
    }

}
