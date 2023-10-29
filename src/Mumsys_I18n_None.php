<?php

/**
 * Mumsys_I18n_None
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2013 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  I18n
 * Created: 2013-12-17
 */


/**
 * Dummy implementation for the internationalization / translation Interface (I18n).
 *
 * There is no real translation! This is just the wrapper class as default for
 * further implementations e.g. using "gettext" or other drivers.
 * You can implement this interface and switch later to your driver. This dummy
 * helps to have/ prepare multi-lang support without having an existing
 * translation. Implementing it will help to check performance issues which
 * will come. This class is the minimum layer.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  I18n
 */
class Mumsys_I18n_None
    extends Mumsys_I18n_Abstract
    implements Mumsys_I18n_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '3.2.1';


    /**
     * Simple translation.
     *
     * @param string $string String to translate
     *
     * @return string The translated string (or the source string if no translation was found)
     */
    public function _t( $string )
    {
        return $string;
    }


    /**
     * Domain translation.
     *
     * @param string $domain Translation domain
     * @param string $string String to translate
     *
     * @return string The translated string (or the source string if no translation was found)
     */
    public function _dt( $domain, $string )
    {
        return $string;
    }


    /**
     * Domain translation plural version.
     * Returns the translated string by the given plural and quantity.
     *
     * @param string $domain Translation domain
     * @param string $singular Singular string
     * @param string $plural Plural string
     * @param integer $number Quantity for languages with more than one plural form
     *
     * @return string Returns the translated string as singular or plural form based on given number.
     * @throws Mumsys_I18n_Exception Throws exception on errors
     */
    public function _dtn( $domain, $singular, $plural, $number )
    {
        $index = $this->getPluralIndex( $number );

        if ( $index >= 1 ) {
            return $plural;
        }

        return $singular;
    }

}
