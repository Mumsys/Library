<?php

/**
 * Mumsys_Weather_Item_Unit_Direction
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
 * Direction unit item in degrees.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 */
class Mumsys_Weather_Item_Unit_Direction
    extends Mumsys_Weather_Item_Unit_Abstract
    implements Mumsys_Weather_Item_Unit_Interface,
        Mumsys_Weather_Item_Unit_Direction_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';


    /**
     * Initialize the direction unit item.
     *
     * The input 'key' will be set to 'degrees'; All options are optional.
     *
     * Example:
     * <code>
     * $obj = new Mumsys_Weather_Item_Unit_Factory::createItem('Direction');
     * // $obj->toRawArray() will return:
     * array(
     *      'key' => 'degrees',
     *      'label' => 'Degrees',
     *      'sign' => '°',
     *      'code' => null,
     * );
     * </code>
     *
     * @param array $input Possible list of input parameters as follow:
     *  - 'key' (string) Will be set to 'degrees' not needed as parameter
     *  - 'label' (string) Optional; E.g. for translation like 'Degrees'
     *  - 'code' (string) Optional; Default: null
     *  - 'sign' (string) Optional; Default: '°'
     */
    public function __construct( array $input = array() )
    {
        $parts = array(
            'key' => 'degrees',
            'name' => 'Degrees',
            'sign' => '°',
            'code' => null,
        );

        if ( isset( $input['label'] ) ) {
            $parts['label'] = $input['label'];
        }

        if ( isset( $input['sign'] ) ) {
            $parts['sign'] = $input['sign'];
        }

        if ( isset( $input['code'] ) ) {
            $parts['code'] = $input['code'];
        }

        $this->_domainPrefix = 'weather.item.unit.direction.';

        parent::__construct( $parts );
    }


    /**
     * Returns the code of a direction (e.g: Wind direction) to be represented
     * as value.
     *
     * E.g. SW = South west, SSW = South south west.
     *
     * @param integer $degrees Direction in degrees
     * @param integer $precision Precision of the wind direction to return.
     * 0 = 45° steps (8 steps) (default),
     * 1 = 22.5° steps (16 steps)
     *
     * @return string Code of the wind direction
     * @throws Mumsys_Weather_Item_Unit_Exception
     */
    public function getDirectionCode( $degrees = 0, $precision = 0 )
    {
        if ( $degrees > 360 || $degrees < 0 ) {
            $mesg = sprintf( 'Invalid value for degrees: "%1$s"', $degrees );
            throw new Mumsys_Weather_Item_Unit_Exception( $mesg );
        }

        if ( (int) $precision ) {
            $steps = 16;
            $codes = array(
                'N', 'NNO', 'NO', 'ONO', 'O', 'OOS', 'SO', 'SSO',
                'S', 'SSW', 'SW', 'WSW', 'W', 'WWN', 'NW', 'NNW', 'N'
            );
        } else {
            $steps = 8;
            $codes = array('N', 'NO', 'O', 'SO', 'S', 'SW', 'W', 'NW', 'N');
        }

        $div = ( 360 / $steps ); //45, 22.5

        $codeIndex = ( ( ( $degrees + ( $div / 2 ) ) / 360 * $steps ) );

        return $codes[round( $codeIndex, 0 ) - 1];
    }


    /**
     * Returns an alternativ representation value by given degrees value.
     *
     * Implemented code's (codeTo):
     *  - 'clock' E.g. 0 degrees = 12 o'clock
     *
     * @param integer|float $value Value in degrees
     * @param string $codeTo Code to convert to. e.g: 'clock'
     *
     * @return integer|float Converted value for the new presentation.
     * @throws Mumsys_Weather_Item_Unit_Exception If conversion code not
     * implemented
     */
    public function convert( $value, string $codeTo )
    {
        if ( $value > 360 || $value < 0 ) {
            $mesg = sprintf( 'Invalid value for degrees: "%1$s"', $value );
            throw new Mumsys_Weather_Item_Unit_Exception( $mesg );
        }

        switch ( $codeTo ) {
            case 'clock':
                $result = round( (int) $value / 30 );
                if ( $result == 0 ) {
                    $result = 12;
                }
                break;

            case 'degrees':
                $result = $value;
                break;

            default:
                $mesg = 'Direction conversion to "' . $codeTo . '" not implemented';
                throw new Mumsys_Weather_Item_Unit_Exception( $mesg );
        }

        return $result;
    }

}
