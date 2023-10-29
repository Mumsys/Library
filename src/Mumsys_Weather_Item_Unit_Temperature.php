<?php

/**
 * Mumsys_Weather_Item_Unit_Temperature
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
 * Temperature unit item for weather unit properties.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 */
class Mumsys_Weather_Item_Unit_Temperature
    extends Mumsys_Weather_Item_Unit_Abstract
    implements Mumsys_Weather_Item_Unit_Interface, Mumsys_Weather_Item_Unit_Convert_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';


    /**
     * Initialize the unit temperature item.
     *
     * To improve: Only the 'key' needs to be set to get default values. If none
     * of the following keys is set an exception will be thrown because you may
     * just want the default unit item.
     * This item contain also some extra methodes for data conversions like the
     * convert() method which is not needed by default.
     *
     * Possible keys for celsius (e.g: $input['key'] = 'celsius'):
     *  - 'metric' | 'celsius' | 'c'
     *
     * Possible keys for fahrenheit:
     *  - 'imperial' | 'fahrenheit' | 'f'
     *
     * Possible keys for kelvin:
     *  - 'internal' | 'kelvin' | 'k'
     *
     * All other properties can be set individually if given.
     *
     * Example:
     * <code>
     * $obj = new Mumsys_Weather_Item_Unit_Factory::createItem(
     *      'Temperature',  array('key'=> 'c')
     * );
     * // $obj->toRawArray() will return:
     * array(
     *      'key' => 'celsius',
     *      'label' => 'Celsius',
     *      'sign' => '°C',
     *      'code' => 'C',
     * );
     * </code>
     *
     * @param array $input Possible list of input parameters as follow:
     *  - 'key' (string) Required; Name of the unit eg: 'fahrenheit','kelvin'|
     * 'celsius'
     *  - 'label' (string) Optional; Label for the 'key' e.g. for translation
     * like 'Degrees fahrenheit' otherwise a default value will be set
     *  - 'sign' (string) Optional; Sign/ short symbol like: '°' Defaults:
     * '° C' | '° F' | null (for kelvin)
     * if key=fahrenheit)
     *  - 'code' (string) Optional; Code of the unit e.g: 'F'|'C'|'K'
     *
     * @throws Mumsys_Weather_Item_Unit_Exception If 'key' is invalid or not set
     */
    public function __construct( array $input = array() )
    {
        if ( !isset( $input['key'] ) ) {
            $mesg = '"Key" must be set to initialise the object';
            throw new Mumsys_Weather_Item_Unit_Exception( $mesg );
        }

        $parts = $this->_initInputDefaults( $input );

        $this->_domainPrefix = 'weather.item.unit.temperature.';

        parent::__construct( $parts );
    }


    /**
     * Returns converted temperature value for celsius, fahrenheit or kelvin.
     *
     * @todo Future: add methode like $unit->getFormated()?
     *
     * @param float $value Temperature value to convert
     * @param string $unitTo Unit to convert to. Possible values are: "celsius"
     * "fahrenheit" | "kelvin" (default)
     *
     * @return float New calculated value for the target unit.
     * @throws Mumsys_Weather_Item_Unit_Exception If unit parameter can not be used
     */
    public function convert( $value, string $unitTo = 'kelvin' )
    {
        $unitFrom = $this->getKey();
        if ( $unitFrom === $unitTo ) {
            return $value;
        }

        if ( !is_float( $value ) ) {
            $value = (float) $value;
        }

        $units = array(
            'celsius' => array(
                'fahrenheit' => ( $value * 9 / 5 + 32 ),
                'kelvin' => ( $value + 273.15 ),
            ),
            'fahrenheit' => array(
                'celsius' => ( ( $value - 32 ) / 1.8 ),
                'kelvin' => ( ( $value + 459.67 ) * 5 / 9 ),
            ),
            'kelvin' => array(
                'celsius' => ( $value - 273.15 ),
                'fahrenheit' => ( ( $value - 459.67 ) * 5 / 9 ),
            ),
        );

        if ( !isset( $units[$unitFrom] ) || !isset( $units[$unitTo] ) ) {
            $msg = sprintf(
                'Invalid unit to convert temperature from: "%1$s" to: "%2$s"',
                $unitFrom, $unitTo
            );
            throw new Mumsys_Weather_Item_Unit_Exception( $msg );
        }

        return $units[$unitFrom][$unitTo];
    }


    /**
     * Sets incoming defaults.
     *
     * @link https://de.wikipedia.org/wiki/Kelvin
     *
     * @param array $params Like in constuctor
     *
     * @return array Item properties as mix os defauls or custom if the were set.
     * @throws Mumsys_Weather_Item_Unit_Exception
     */
    private function _initInputDefaults( array $params ): array
    {
        $return = array();
        switch ( $params['key'] )
        {
            case 'metric':
            case 'celsius':
            case 'c':
                $return = array(
                    'key' => 'celsius',
                    'label' => 'Celsius',
                    'sign' => '° C',
                    'code' => 'C',
                );
                break;

            case 'imperial':
            case 'fahrenheit':
            case 'f':
                $return = array(
                    'key' => 'fahrenheit',
                    'label' => 'Fahrenheit',
                    'sign' => '° F',
                    'code' => 'F',
                );
                break;

            case 'internal':
            case 'kelvin':
            case 'k':
                $return = array(
                    'key' => 'kelvin',
                    'label' => 'Kelvin',
                    'sign' => null,
                    'code' => 'K',
                );
                break;

            default:
                $mesg = sprintf(
                    'Invalid "key" to get a temperature unit item: "%1$s"',
                    $params['key']
                );
                throw new Mumsys_Weather_Item_Unit_Exception( $mesg );
        }

        if ( isset( $params['label'] ) ) {
            $return['label'] = $params['label'];
        }

        if ( isset( $params['sign'] ) ) {
            $return['sign'] = $params['sign'];
        }

        if ( isset( $params['code'] ) ) {
            $return['code'] = $params['code'];
        }

        unset( $params );

        return $return;
    }

}
