<?php

/**
 * Mumsys_DateTime_Formatter
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) by Florian Blasel for FloWorks Company, 2021
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  DateTime
 * Created: 2021-12-17
 */


/**
 * DateTime Formatter for locale specific settings. Eg. having local based
 * month or day names. Otherwise you dont need this class.
 *
 * Thiss class requires intl extension
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  DateTime
 */
class Mumsys_DateTime_Formatter
    extends Mumsys_Abstract
    implements Mumsys_DateTime_Interface
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * Locale to be used. E.g: en_US
     * @var string
     */
    private $_locale;

    /**
     * Date format string. E.g: Y-m-d H:i:s
     * @var string
     */
    private $_icupattern;

    /**
     * DateTime object to initially start converting with __toSting magics.
     * @var \DateTime
     */
    private $_datetime;


    /**
     * Initialise the object.
     *
     * @param string $icupattern Output format, e.g: yyyy-MM-dd HH:mm:ss
     * @param string $locale Locale to use, e.g: en_US
     * @param DateTime|null $datetime Optional datetime to run with __toSting method
     */
    public function __construct( $icupattern, $locale = 'en_US',
        \DateTime | null $datetime = null )
    {
        $this->setLocale( $locale );
        $this->setPattern( $icupattern );

        if ( $datetime !== null ) {
            $this->_datetime = $datetime;
        }
    }


    /**
     * Sets the locale.
     *
     * Unverified! Make sure a valid locale will be set.
     *
     * @param string $locale Locale like en_UK
     */
    public function setLocale( $locale ): void
    {
        $this->_locale = $locale;
    }


    /**
     * Sets the pattern for the output format.
     *
     * Note: the input format is fixed by the \DateTime object itselfs. When using
     * formatLocale() this pattern is used but may differ from standard DateTime patterns.
     * see: https://www.php.net/manual/en/class.intldateformatter.php
     * https://unicode-org.github.io/icu/userguide/format_parse/datetime/#date-field-symbol-table
     *
     * @param string $icupattern Output format, e.g: yyyy-MM-dd HH:mm:ss
     */
    public function setPattern( $icupattern ): void
    {
        $this->_icupattern = $icupattern;
    }


    /**
     * Returns the formatted string from given DateTime object based on given locale setting.
     *
     * This method you need if you need day or month names locale specific.
     * See https://unicode-org.github.io/icu/userguide/format_parse/datetime/ for the pattern format!
     *
     * @param DateTime $datetime DateTime object
     *
     * @return string Formatted, locale specific string
     */
    public function formatLocale( \DateTime $datetime ): string
    {
        $formatter = new \IntlDateFormatter(
            $this->_locale, \IntlDateFormatter::FULL, \IntlDateFormatter::FULL
        );
        $formatter->setPattern( $this->_icupattern );

        return $formatter->format( $datetime );
    }


    /**
     * Returns the formatted string from given DateTime object based on given
     * locale setting on construction.
     *
     * @return string Formatted, locale specific string
     * @throws Mumsys_DateTime_Exception If DateTime not exists to convert/ format
     */
    public function __toString(): string
    {
        if ( $this->_datetime === null ) {
            throw new Mumsys_DateTime_Exception( 'DateTime not set' );
        }

        return $this->formatLocale( $this->_datetime );
    }

}
