<?php

/**
 * Mumsys_I18n_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2013 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  I18n
 * @version     1.0.0
 * Created: 2013-12-17
 */


/**
 * Internationalization / translation interface (I18n)
 *
 * @see http://de.wikipedia.org/wiki/I18n
 * @see http://www.w3.org/International/questions/qa-i18n.de.php
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  I18n
 */
interface Mumsys_I18n_Interface
{
    /**
     * Returns the plural index number to be used for the plural translation.
     *
     * @param  int|float $number Custom number to find the plural form
     *
     * @return int Number of the plural index
     */
    public function getPluralIndex( $number );


    /**
     * Replaces/ sets the current locale.
     *
     * @param string $locale ISO-3166 locale string.
     */
    public function setlocale( $locale = '' );


    /**
     * Returns the current locale.
     *
     * @return string Current locale in ISO-3166 format
     */
    public function getLocale();


    /**
     * Returns the translated string.
     *
     * @param string $string String to translate
     *
     * @return string The translated string (or the source string if no translation was found)
     *
     * @throws Mumsys_I18n_Exception Throws exception on errors
     */
    public function _t( $string );


    /**
     * Returns the translated string by given domain.
     *
     * @param string $domain Translation domain
     * @param string $string String to translate
     *
     * @return string The translated string (or the source string if no
     * translation was found)
     * @throws Mumsys_I18n_Exception Throws exception on errors
     */
    public function _dt( $domain, $string );


    /**
     * Returns the translated string by the given domain, plural and quantity.
     *
     * Example: You want to translate a string which has a number in it and
     * based on the quantiy the string changes:
     * singular.: "I want to buy %d ball"
     * plural   : "I want to buy %d balls"
     *
     * @param string $domain Translation domain
     * @param string $singular Singular string
     * @param string $plural Plural string
     * @param integer $number Quantity for languages with more than one plural form
     *
     * @return string Returns the translated string as singular or plural form
     * based on given number.
     * @throws Mumsys_I18n_Exception Throws exception on errors
     */
    public function _dtn( $domain, $singular, $plural, $number );
}
