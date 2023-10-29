<?php

/**
 * Mumsys_Weather_Item_Unit_Default
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
 * Default unit item for weather unit properties.
 *
 * Weather informations have a huge list of possible values. This class handles
 * only the unit values e.g. for temperature, pressure, wind speed, wind
 * direction, or other universal units like lengths, distances(miles, km),
 * speed (miles/h, km/h) where distances and speed are slitly different or
 * geographical directions...
 * @link https://en.wikipedia.org/wiki/Conversion_of_units Units in general
 *
 * This class contains the minimum of methodes which are used through the
 * abstract class.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 */
class Mumsys_Weather_Item_Unit_Default
    extends Mumsys_Weather_Item_Unit_Abstract
    implements Mumsys_Weather_Item_Unit_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * Application domain prefix
     * @var string
     */
    protected $_domainPrefix = 'weather.item.unit.default.';

}
