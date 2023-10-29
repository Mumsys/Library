<?php

/**
 * Mumsys_Serializer_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2021 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Serializer
 * @version 1.0.0
 * @since 7.0.0 Php 7.0++ implementation
 * Created: 2021-02-06
 */


/**
 * Serializer interface for un|serialize().
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Serializer
 * @since 7.0.0 Php 7.0++ implementations
 */
interface Mumsys_Serializer_Interface
{
    /**
     * Returns a serialize value.
     *
     * @param mixed $value The value to be serialized.
     *
     * @return mixed|string Returns a string containing a byte-stream representation
     * of value that can be stored anywhere.
     * Note that this is a binary string which may include null bytes, and needs
     * to be stored and handled as such. For example, serialize() output should
     * generally be stored in a BLOB field in a database, rather than a CHAR or
     * TEXT field.
     * @throws Mumsys_Serializer_Exception On serialization error
     */
    public function serialize( $value );


    /**
     * Unserialize a value to a php value.
     *
     *
     *
     * @param string $serializedValue Value from the serialize() methode
     * @param array $options Mixed options to be provided to unserialize().
     *
     * @return mixed|false Unserialized value. The converted value is returned
     * and can be a boolean, integer, float, string, array or object.
     * @throws Mumsys_Serializer_Exception on unserialize error
     */
    public function unserialize( $serializedValue, array $options = null );

}
