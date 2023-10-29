<?php

/**
 * Mumsys_Variable_Manager_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Variable
 * @version     2.1.1
 * Created: 2006 based on Mumsys_Field, renew 2016
 */


/**
 * Variable item manager interface.
 */
interface Mumsys_Variable_Manager_Interface
{
    /**
     * Initialises the default manager and variable item objects.
     *
     * @param array $config List of key/value configuration pairs containing
     * item properties for the item construction
     * @param array $values List of key/value pairs to set/bind to the item
     * values e.g: the post parameters
     */
    public function __construct( array $config, array $values );


    /**
     * Validate registered variable items.
     *
     * @return boolean True on success or false on error
     */
    public function validate(): bool;


    /**
     * Item type validation.
     *
     * If the test fails an error message will set at the item.
     *
     * @param Mumsys_Variable_Item_Interface $item Variable item interface
     *
     * @return boolean True on success otherwise false.
     */
    public function validateType( Mumsys_Variable_Item_Interface $item ): bool;


    /**
     * Item validation for min and/or max item values.
     *
     * Hint: if item type is string min/max values will be testes by string
     * length. If the type is an integer it will be tested against greater/
     * lower the current value.
     *
     * @param Mumsys_Variable_Item_Interface $item
     *
     * @return boolean True on success otherwise false
     */
    public function validateMinMax( Mumsys_Variable_Item_Interface $item ): bool;


    /**
     * Item validation agains regular expressions.
     *
     * @param Mumsys_Variable_Item_Interface $item Validate item object
     *
     * @return boolean True on success or if no regex was set or false on error
     */
    public function validateRegex( Mumsys_Variable_Item_Interface $item ): bool;


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
    public function isValid( Mumsys_Variable_Item_Interface $item ): bool;


    /**
     * Returns all variable items.
     *
     * @return array<string,Mumsys_Variable_Item_Interface> List of key/variable
     * items implementing Mumsys_Variable_Item_Interface where the key is the
     * identifier of the item
     */
    public function getItems(): array;


    /**
     * Returns a variable item by given key.
     *
     * @param string $key Key/ identifier of the variable item
     *
     * @return Mumsys_Variable_Item_Interface|null Variable item or null if not available
     */
    public function getItem( $key ): ?Mumsys_Variable_Item_Interface;


    /**
     * Register a variable item object.
     *
     * @param string $key Key/ identifier of the item
     * @param Mumsys_Variable_Item_Interface $item Variable item object
     *
     * @throws Mumsys_Variable_Manager_Exception If key already exists
     */
    public function registerItem( string $key,
        Mumsys_Variable_Item_Interface $item ): void;


    /**
     * Creates a new variable item object.
     *
     * @see Mumsys_Variable_Item_Default
     *
     * @param array $properties List of key/value pairs to initialize the
     * variable item object
     *
     * @return Mumsys_Variable_Item_Interface
     */
    public function createItem( array $properties = array() ): Mumsys_Variable_Item_Interface;


    /**
     * Returns the list of all error messages from all variable items.
     *
     * Identified by the item key/ID as array index. E.g:
     * array('variablename' => array('errorID' => 'errorMessage', ...);
     *
     * @return array Returns the list of errors or empty array for no errors
     */
    public function getErrorMessages(): array;


    /**
     * Returns the message templates.
     *
     * @return array List of error message templates
     */
    public function getMessageTemplates(): array;


    /**
     * Set/ replaces the message templates.
     *
     * Hint: The key is the message identifier to easyly find it where the
     * massage can vari depending on the values.
     *
     * @param array $templates List of key/value pairs for the message
     * templates.
     */
    public function setMessageTemplates( array $templates ): void;

}
