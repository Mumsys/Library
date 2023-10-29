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
 * Created: 2005-01-01
 */


/**
 * Demo file with errors to compare the codeing standard fixes.
 *
 * @category    Mumsys
 * @package     CodingStyle
 * @subpackage  DemoClass
 */
class Mumsys_CodingStyle_DemoClass_DemoWithErrors extends Mumsys_Abstract implements Mumsys_Config_Interface
{
    /**
     * Version ID information<missing dot at end of headline>
     */
    const VERSION = '1.0.0';

    /**
     * Check if WrongConstantToBeChanged will be WRONGCONSTANTTOBECHANGED in sniffs.
     */
    private const WrongConstantToBeChanged = 'must be WRONGCONSTANTTOBECHANGED';

    /**
     * @var stdClass
     */
    private $_item;


    /**
     * headline, short desc
     *
     * @param string|null $a A demo
     * @param string $b B demo w/o string type hint
     * @param integer $c C If 'int' is required in function signature than it make no sence to allow 'integer'
     * @param integer $d D
     */
    public function __construct(
        $a = null,
        $b = 'mumsys',
        int $c = null,
        int $d = null
    )
    {
        if ( defined( '_CMS_AND' ) ) {
            // constant exists
        }

        $this->_item = new stdClass();
        $this->_item->a = $a;
        $this->_item->b = $b;
        $this->_item->c = $c;
        $this->_item->d = $d;

        $this->_item->a = $a;
        $this->_item->b = $b; // should fail  (use single lines)
        $x = 1;
        $y = 2; // should fail (use single lines)
        $this->_item->e = $x + $y;
    }


    /**
     * Stores session informations managed by this object.
     *
     * <no long desc not free space lines>
     *
     */
    public function __destruct()
    {
        unset( $this->_item );
    }


    /**
     * <headline> Method, function decription
     *
     * <description> Long desc if needed, optional
     *
     * @uses optional informations, annotations block
     * @todo optional informations, annotations block
     *
     * @param string $key Parameter block
     * @param mixed|null $default Parameter block
     *
     * @return string Return and exception block
     *
     * @throws Exception Return and exception block
     */
    public function get( $key, $default = null )
    {
        if ( $key ) {
            return $key;
        } else {
            return $default;
        }

        throw new Exception( 'unreachable code' );
    }


    /**
     * headline, short desc
     *
     * missing return annotation
     *
     * @param integer $a
     */
    public function a( int $a )
    {
        // to be fixed to 'bool' keyword and in lower case
        $bool = (bool)$a;
        $int = (int)$a; // to be fixed to 'int' keyword

        // 'as' not 'AS'
        foreach ( array(1,2,3) as $c ) {
            $c += $c;
        }

        return $bool;
    }


    // missing doc block warning
    public function addPath( $path )
    {
        return array(
            $path => $path,
            'a' => 'a',
            'b' => array(1, 2, 3),
            'c' => array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 'd', 'e' => 'd', 'f' => 'd', 'g' => 'd', 'h' => 'd', 'i' => 'd', 'j' => 'd'),
            'd' => array(
                'a' => 1,
                'b' => 2,
                'c' => 3
            ),
        );
    }


    public function getAll(): array
    {
    }


    public function register( $key, $value = null )
    {
    }


    public function replace( $key, $value = null )
    {
    }
}
