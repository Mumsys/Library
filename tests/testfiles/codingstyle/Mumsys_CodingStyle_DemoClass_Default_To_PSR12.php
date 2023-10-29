<?php

/**
 * File header headline
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2019 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     CodingStyle
 * @subpackage  DemoClass
 * Created: 2019-10-30
 */

/**
 * Class headline block Mumsys default coding style (hand made for maximum readability)
 *
 * Class detail desc block Currently it doesnt use namespaces because there is
 * no real need to do so.
 * Also: Imagine having only Interface.php or Default.php in your editor tabs
 * wont solve readability.
 *
 * @category    Mumsys
 * @package     CodingStyle
 * @subpackage  DemoClass
 */
class Mumsys_CodingStyle_DemoClass_Default_To_PSR12
    extends Mumsys_Abstract
    implements Mumsys_Logger_Interface
{
    /**
     * Version ID information.
     */
    public const VERSION = '1.0.0';

    /**
     * Check if WrongConstantToBeChanged will be WRONGCONSTANTTOBECHANGED
     */
    public const WRONGCONSTANTTOBECHANGED = 'must be WRONGCONSTANTTOBECHANGED';

    /**
     * @var stdClass
     */
    private $_item;


    /**
     * Headline, short desc
     *
     * @param string|null $a A demo
     * @param string      $b B demo w/o string type hint
     * @param integer     $c C If 'int' is required in function signature than
     *                         it make no sence to allow 'integer'
     * @param integer     $d D
     */
    public function __construct( string $a = null, string $b = 'mumsys', int $c = null, int $d = null )
    {
        if ( defined( '_CMS_AND' ) ) {
            // constant exists
        }

        /*
         * some inline docblock
         * with params
         *
         * @var $_item stdClass Some std class
         */

        $this->_item = new stdClass();
        $this->_item->a = $a;
        $this->_item->b = $b;
        $this->_item->c = $c;
        $this->_item->d = $d;

        $this->_item->a = $a;
        $this->_item->b = $b;
        $x = 1;
        $y = 2;
        $this->_item->e = $x + $y;
    }


    /**
     * destructs
     */
    public function __destruct()
    {
        unset( $this->_item );
    }


    /**
     * Headline Method, function decription
     *
     * Description Long desc if needed, optional
     *
     * @uses optional informations, annotations block
     * @todo optional informations, annotations block
     *
     * @param string     $key     Parameter block
     * @param mixed|null $default Parameter block
     *
     * @return string Return and exception block
     * @throws Exception Return and exception block
     */
    public function get( string $key, $default = null )
    {
        if ( $key ) {
            return $key;
        } else {
            return $default;
        }

        throw new Exception( 'unreachable code' );
    }


    /**
     * Headline, short desc
     *
     * @param integer $a A parameter
     *
     * @return boolean Boolean
     */
    public function a( int $a ): bool
    {
        $bool = (bool) $a;
        $int = (int) $a;

        foreach ( array(1, 2, 3) as $c ) {
            $c += $c;
        }

        return $bool;
    }


    /**
     * Headline
     *
     * @param string $path Path
     *
     * @return array
     */
    public function addPath( string $path )
    {
        return array(
            $path => $path,
            'a' => 'a',
            'b' => array(1, 2, 3),
            'c' => array(
                'a' => 1, 'b' => 2, 'c' => 3, 'd' => 'd', 'e' => 'd', 'f' => 'd',
                'g' => 'd', 'h' => 'd', 'i' => 'd', 'j' => 'd'
            ),
            'd' => array(
                'a' => 1,
                'b' => 2,
                'c' => 3
            ),
        );
    }


    /**
     * Headline.
     *
     * @param string|array $message Message
     * @param integer      $level   Int
     * @param string       $tag     String
     *
     * @return string The message (converted/ formatted to string if input was an array)
     * @throws Exception Not inplemented yet
     */
    public function log( $message = '', $level = 0, string $tag = 'message' )
    {
        // log message

        throw new Exception( 'not in' );

        $result = '';
        // eg convert array to a valid string
        if ( is_array( $message ) ) {
            foreach ( $message as $key => $value ) {
                $result .= "$key $value";
            }
        } else {
            $result = $message;
        }

        return $result;
    }


    /**
     *
     * @param integer $int  Integervalue required
     * @param boolean $bool Booleanvalue required
     */
    public function methodParamsIntAndBool( int $int, bool $bool ): void
    {
    }


    /**
     * Private method
     *
     * @return boolean
     */
    private function _privateUnderscored()
    {
        return false;
    }
}
