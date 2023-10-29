<?php declare(strict_types=1);

/**
 * Mumsys_Serializer_Default
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2021 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Serializer
 * @since 7.0.0 Php 7.0++ implementation
 * Created: 2021-02-06
 */


/**
 * Default php un|serialize handler for the Serializer interface.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Serializer
 * @since 7.0.0 Php 7.0++ implementation
 */
class Mumsys_Serializer_Default
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';


    /**
     * Serialize a value using php serialize() function.
     *
     * @param mixed $value The value to be serialized. serialize() handles all
     * types, except the resource-type and some objects (see note below). You
     * can even serialize() arrays that contain references to itself. Circular
     * references inside the array/object you are serializing will also be
     * stored. Any other reference will be lost.
     * When serializing objects, PHP will attempt to call the member function
     * __sleep() prior to serialization. This is to allow the object to do any
     * last minute clean-up, etc. prior to being serialized. Likewise, when the
     * object is restored using unserialize() the __wakeup() member function is
     * called.
     * Note:
     * Object's private members have the class name prepended to the member
     * name; protected members have a '*' prepended to the member name. These
     * prepended values have null bytes on either side.
     *
     * @return string Returns a string containing a byte-stream representation
     * of value that can be stored anywhere.
     * Note that this is a binary string which may include null bytes, and needs
     * to be stored and handled as such. For example, serialize() output should
     * generally be stored in a BLOB field in a database, rather than a CHAR or
     * TEXT field.
     * @throws Mumsys_Serializer_Exception On serialize error
     */
    public function serialize( $value )
    {
        return serialize( $value );
    }


    /**
     * Unserialize a value using php unserialize() function if matches a php
     * serialize()'ed string.
     *
     * Do not pass untrusted user input to unserialize() regardless of the
     * options value of allowed_classes. Unserialization can result in code
     * being loaded and executed due to object instantiation and autoloading,
     * and a malicious user may be able to exploit this. Use a safe, standard
     * data interchange format such as JSON (via json_decode() and
     * json_encode()) if you need to pass serialized data to the user.
     *
     * If you need to unserialize externally-stored serialized data, consider
     * using hash_hmac() for data validation. Make sure data is not modified by
     * anyone but you.
     *
     * @param string|mixed $serializedValue Value from a php serialize() function: If
     * the variable being unserialized is an object, after successfully
     * reconstructing the object PHP will automatically attempt to call the
     * __wakeup() member function (if it exists).
     * Note: unserialize_callback_func directive
     * It's possible to set a callback-function which will be called, if an
     * undefined class should be instantiated during unserializing. (to prevent
     * getting an incomplete object "__PHP_Incomplete_Class".) Use your php.ini,
     * ini_set() or .htaccess to define unserialize_callback_func. Everytime an
     * undefined class should be instantiated, it'll be called. To disable this
     * feature just empty this setting.
     *
     * @param array $options Any options to be provided to unserialize(), as an
     * associative array.
     * Valid options Name Type Description allowed_classes mixed Either an array
     * of class names which should be accepted, FALSE to accept no classes, or
     * TRUE to accept all classes. If this option is defined and unserialize()
     * encounters an object of a class that isn't to be accepted, then the
     * object will be instantiated as __PHP_Incomplete_Class instead. Omitting
     * this option is the same as defining it as TRUE: PHP will attempt to
     * instantiate objects of any class.
     *
     * @return mixed|false Unserialized value. The converted value is returned,
     * and can be a boolean, integer, float, string, array or object.
     * In case the passed string is not unserializeable, --FALSE is returned and
     * E_NOTICE is issued.-- Exception will be thrown then!
     * @throws Mumsys_Serializer_Exception on unserialize error
     */
    public function unserialize( $serializedValue, array $options = array() )
    {
        // pre checks
        if ( !is_string( $serializedValue ) || !preg_match( '/^((s|i|d|b|a|O|C):|N;)/', $serializedValue ) ) {
            $value = $serializedValue;

            if ( is_object( $value ) ) {
                $value = get_class( $value );
            } elseif ( !is_string( $value ) ) {
                $value = gettype( $value );
            }

            $mesg = sprintf(
                'Serialized value must be a php serialized string. Value: "%1$s"',
                $value
            );
            throw new Mumsys_Serializer_Exception( $mesg );
        }

        return unserialize( $serializedValue, $options );
    }

}
