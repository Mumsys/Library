<?php

/**
 * Mumsys_Weather_Item_Unit_Speed
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
 * Speed unit item for weather units.
 *
 * Data container and conversion for:
 * - 'm/s'     meters per second
 *  - 'mps'     miles per second
 *  - 'mph'     miles per hour
 *  - 'km/h'    kilometers per hour
 *  - 'kn'      knots
 *  - 'nmiph'   nautic sea miles = sea miles = knots
 *  - 'bf'      Beaufort
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 */
class Mumsys_Weather_Item_Unit_Speed
    extends Mumsys_Weather_Item_Unit_Abstract
    implements Mumsys_Weather_Item_Unit_Interface,
        Mumsys_Weather_Item_Unit_Convert_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';


    /**
     * Conversion factor to re-calculate m/s (meter per second) to mps (miles
     * per second).
     */
    const MS_TO_MPS = 2.237; //
    /**
     * Conversion factor to re-calculate m/s (meter per second) to mph (miles
     * per hour).
     */
    const MS_TO_MPH = 2.2369362920544025;
    /**
     * Conversion factor to re-calculate m/s (meter per second) to km/h
     * (kilometer per hour).
     */
    const MS_TO_KMPH = 3.6;
    /**
     * Conversion factor to re-calculate m/s (meter per second) to kn (knots)
     * (nautic sea miles = sea miles = knots).
     */
    const MS_TO_KNOTS = 1.944;
    /**
     * Conversion factor to re-calculate m/s (meter per second) to nmiph
     * (nautic sea miles = sea miles = knots).
     */
    const MS_TO_NMIPH = 1.944;
    /**
     * Conversion factor to re-calculate m/s (meter per second) to bf
     * (Beaufort (Bf)).
     * B = ( v/0.8360m/s )^2/3 ;  v = 0,8360m/s * B^2/3
     */
    const MS_TO_BF = 1.1268405883429;


    /**
     * Conversion factor to re-calculate km/h (kilometers per hour) to m/s (
     * meter per second).
     */
    const KMPH_TO_MS = 0.2777777777777778;
    /**
     * Conversion factor to re-calculate km/h (kilometers per hour) to mph (
     * miles per hour).
     */
    const KMPH_TO_MPH = 0.621371192237334;


    /**
     * Conversion factor to re-calculate mph (miles per hour) to m/s (meter per
     * second).
     */
    const MPH_TO_MS = 0.44704;
    /**
     * Conversion factor to re-calculate mph (miles per hour) to mps (miles per
     * second).
     */
    const MPH_TO_MPS = 0;
    /**
     * Conversion factor to re-calculate mph (miles per hour) to km/h (
     * kilometers per hour).
     */
    const MPH_TO_KMPH = 1.609344;
    /**
     * Conversion factor to re-calculate mph (miles per hour) to knots (Sea
     * miles = nautic sea miles = knots).
     * 1kn = 463m/900s = 0,514444 m/s
     * 1 Knoten = 1 Seemeile/h = 1,852 km/h ≈ 0,514444 m/s
     */
    const MPH_TO_KNOTS = 0.868976242;
    /**
     * Conversion factor to re-calculate mph (miles per hour) to nmiph (nautic
     * sea miles = sea miles = knots).
     */
    const MPH_TO_NMIPH = 0;
    /**
     * Conversion factor to re-calculate mph (miles per hour) to bf (
     * Beaufort (Bf)).
     */
    const MPH_TO_BF = 0;


    /**
     * Initialize the speed unit item.
     *
     * Returns a unit item and gives conversion posibility for possible codes
     * like: 'm/s', 'mps', 'mph', km/h', 'kn', 'nmiph', 'bf'
     *
     * To improve the usage: Only the 'code' needs to be set to get default
     * values. If none of the following keys is set an exception will be thrown
     * because you may just want a default unit item.
     *
     * This item contain also some extra methodes for data conversions like the
     * convert() method which is not needed by default.
     *
     * Possible codes to be set (e.g: $input['code'] = 'm/s'):
     *  - 'm/s'     meters per second
     *  - 'mps'     miles per second
     *  - 'mph'     miles per hour
     *  - 'km/h'    kilometers per hour
     *  - 'kn'      knots
     *  - 'nmiph'   nautic sea miles = sea miles = knots
     *  - 'bf'      Beaufort
     *
     * All other properties can be set individually if given.
     *
     * Example:
     * <code>
     * $obj = new Mumsys_Weather_Item_Unit_Factory::createItem(
     *      'Speed',  array('code'=> 'm/s')
     * );
     * // $obj->toRawArray() will return:
     * array(
     *      'code' => 'm/s',
     *      'key' => 'meters per second',
     *      'label' => 'Meters per second',
     *      'sign' => null,
     * );
     * </code>
     *
     * @param array $input Possible list of input parameters as follow:
     *  - 'code' (string) Required; Code of the unit. One of: 'm/s', 'mps',
     * 'mph', km/h', 'kn', 'nmiph', 'bf' is required
     *  - 'key' (string) Optional; Name of the unit eg: 'miles per hour'
     *  - 'label' (string) Optional; E.g. for translation like 'Miles per hour'
     *  - 'sign' (string) Optional; Sign/ short symbol (if any required, none
     * per default)
     *
     * @throws Mumsys_Weather_Item_Unit_Exception If code is invalid or missing
     */
    public function __construct( array $input = array() )
    {
        if ( !isset( $input['code'] ) ) {
            $mesg = '"Code" must be set to initialise the object';
            throw new Mumsys_Weather_Item_Unit_Exception( $mesg );
        }

        $parts = $this->_initInputDefaults( $input );

        $this->_domainPrefix = 'weather.item.unit.speed.';

        parent::__construct( $parts );
    }


    /**
     * Returns converted speed value.
     *
     * @todo Future: add methode like $unit->getFormated()?
     *
     *
     * http://www.wetterstation-goettingen.de/wind.htm
     *
     * http://de.wikipedia.org/wiki/Windgeschwindigkeit
     * http://en.wikipedia.org/wiki/Knot_%28unit%29
     * 1 kn     = 1 nmi (sea/nautical miles;exakt) = 1,852.3 km/h (exakt)
     *                                                            = 0,514444 m/s
     * 1 m/s = 3,6 km/h (exakt) = 1,944 kn = 2,237 mph
     * 1 km/h = 0,540 kn = 0,278 m/s = 0,621 mph
     * 1 mph = 1,609344 km/h (exakt) = 0,8690 kn = 0,447 m/s
     * 1 mile = 1609,344 meter
     * 1.852 meter = 1 Sea/nautical miles, Nautic mile - international
     *
     *
     * Converts, re-calculates speed units. Possible units to convert vice
     * versa are:
     * m/s (meter per second), mps (miles per second), mph (miles per hour),
     * km/h (kilometer per hour), kn (knots), nmiph (nautical miles per hour),
     * bf (Beaufort (Bf))
     *
     * @param integer $value Current value of speed
     * @param string $codeTo Target code to convert the value. Possible: 'm/s',
     * 'mps', 'mph', 'km/h', 'kn', 'nmiph', 'bf'
     *
     * @return float Returns the calculated value for the target code
     * @throws Mumsys_Weather_Item_Unit_Exception If implementation is missing
     */
    public function convert( $value, string $codeTo = 'm/s' )
    {
        $codeFrom = $this->getCode();
        if ( $codeFrom === $codeTo ) {
            return $value;
        }

        $codesConv = array(
            'm/s' => array(
                'mps' => self::MS_TO_MPS,
                'mph' => self::MS_TO_MPH,
                'km/h' => self::MS_TO_KMPH,
                'kn' => self::MS_TO_KNOTS,
                'nmiph' => self::MS_TO_NMIPH,
                'bf' => self::MS_TO_BF,
            ),
            'km/h' => array(
                'm/s' => self::KMPH_TO_MS,
                'mph' => self::KMPH_TO_MPH,
            ),
            'mph' => array(
                'm/s' => self::MPH_TO_MS,
                'mps' => self::MPH_TO_MPS,
                'km/h' => self::MPH_TO_KMPH,
                'kn' => self::MPH_TO_KNOTS,
                'nmiph' => self::MPH_TO_NMIPH,
                'bf' => self::MPH_TO_BF
            )
        );

        if ( isset( $codesConv[$codeFrom][$codeTo] ) ) {
            if ( $codeTo == 'bf' ) {
                $newValue = round( $value * $codesConv[$codeFrom][$codeTo], 0 );
            } else {
                $newValue = $value * $codesConv[$codeFrom][$codeTo];
            }
        } else {
            $msg = sprintf(
                'Speed conversion not implemented yet for "%1$s" to "%2$s"',
                $codeFrom, $codeTo
            );
            throw new Mumsys_Weather_Item_Unit_Exception( $msg );
        }

        return $newValue;
    }


    /**
     * Sets incoming defaults.
     *
     * @param array $params Properties of code, key, label, sign to be set
     *
     * @return array Item properties as mix of defaults or custom values if
     * given.
     * @throws Mumsys_Weather_Item_Unit_Exception
     */
    private function _initInputDefaults( array $params ): array
    {
        $result = array();
        switch ( $params['code'] )
        {
            case 'm/s':
                $result = array(
                    'key' => 'meter per second',
                    'label' => 'Meter per second',
                );
                break;

            case 'mps':
                $result = array(
                    'key' => 'miles per second',
                    'label' => 'Miles per second',
                );
                break;

            case 'mph':
                $result = array(
                    'key' => 'miles per hour',
                    'label' => 'Miles per hour',
                );
                break;

            case 'km/h':
                $result = array(
                    'key' => 'kilometer per hour',
                    'label' => 'Kilometer per hour',
                );
                break;

            case 'kn':
                $result = array(
                    'key' => 'knots',
                    'label' => 'Knots',
                );
                break;

            case 'nmiph':
                $result = array(
                    'key' => 'nautical miles per hour',
                    'label' => 'Nautical miles per hour',
                );
                break;

            case 'bf':
                $result = array(
                    'key' => 'nautical miles per hour',
                    'label' => 'Nautical miles per hour',
                );
                break;

            default:
                $mesg = sprintf(
                    'Invalid "code" to get a speed unit item: "%1$s"',
                    $params['code']
                );
                throw new Mumsys_Weather_Item_Unit_Exception( $mesg );
        }

        $result['code'] = $params['code'];

        if ( isset( $params['key'] ) ) {
            $result['key'] = $params['key'];
        }

        if ( isset( $params['label'] ) ) {
            $result['label'] = $params['label'];
        }

        if ( isset( $params['sign'] ) ) {
            $result['sign'] = $params['sign'];
        }

        return $result;
    }

}
