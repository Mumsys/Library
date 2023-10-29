<?php

/**
 * Mumsys_Weather_Item_Unit_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2013 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 * @verion      1.0.0
 * Created: 2013, renew 2018
 */


/**
 * Interface for the weather unit items.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 */
interface Mumsys_Weather_Item_Unit_Interface
{
    /**
     * Initialize the unit item.
     *
     * @param array $input List of input parametery as follow:
     *  - 'key' (string) Name of the unit eg: 'fahrenheit'
     *  - 'label' (string) Label for the 'key' e.g. for translation like
     * 'Degrees Fahrenheit'
     *  - 'sign' (string) Sign/ short symbol like: '°'|'°F'
     *  - 'code' => 'F',
     */
    public function __construct( array $input = array() );


    /**
     * Returns the item key/identifier name.
     *
     * @param mixed $default Default (null) return value if key was not set
     *
     * @return string Item name key/identifier
     */
    public function getKey( $default = null );


    /**
     * Sets the item key name/ identifier.
     *
     * @param string $value Item key/itenifier
     *
     * @return void
     */
    public function setKey( string $value ): void;


    /**
     * Returns the item label or $default if not set
     *
     * @param mixed $default Default return value if label not exists
     *
     * @return string|mixed Returns the item label or $default
     */
    public function getLabel( $default = null );


    /**
     * Sets the item label.
     *
     * @param string $value Item label to be set
     *
     * @return void
     */
    public function setLabel( string $value ): void;


    /**
     * Returns the item sign/symbol or $default if not set
     *
     * @param mixed $default Default return value if sign not exists
     *
     * @return string|mixed Returns the item sign or $default
     */
    public function getSign( $default = null );


    /**
     * Sets the item sign/ symbol sign.
     *
     * @param string $value Item label to be set
     *
     * @return void
     */
    public function setSign( string $value ): void;


    /**
     * Returns the item code (short code of the key name) or $default.
     *
     * @param mixed $default Default return value if code not exists
     *
     * @return string|mixed Returns the item sign or $default
     */
    public function getCode( $default = null );


    /**
     * Sets the item code (short code of the key name).
     *
     * @param string $value Item code to be set
     *
     * @return void
     */
    public function setCode( string $value ): void;


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
     * Returns the list of key/values pairs of item properties HTML encoded.
     *
     * Formats item values HTML compilant e.g: & goes &amp; , " goes &quot; ...
     * Array keys does NOT includes the domain prefix.
     *
     * @return array Returns item properties as key/value pairs
     */
    public function toRawArrayHtml(): array;


    /**
     * Returns the list of key/values pairs of item properties.
     *
     * @return array Returns item properties as key/value pairs
     */
    public function toArray(): array;


    /**
     * Returns the list of key/values pairs of item properties.
     *
     * Array keys does NOT includes the domain prefix.
     *
     * @return array Returns item properties as key/value pairs
     */
    public function toRawArray(): array;

}
