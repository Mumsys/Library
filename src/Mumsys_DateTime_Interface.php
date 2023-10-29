<?php

/**
 * Mumsys_DateTime_Exception
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) by Florian Blasel, 2021
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  DateTime
 * @version 1.0.0
 * Created: 2021-12-17
 */


/**
 * Interface for DateTime classes.
 */
interface Mumsys_DateTime_Interface
{
    /**
     * Sets the locale.
     *
     * Unverified! Make sure a valid locale will be set.
     *
     * @param string $locale Locale like en_UK
     */
    public function setLocale( $locale ): void;

    /**
     * Sets the pattern for the output format.
     *
     * Note: the input format is fixed by the \DateTime object itselfs. When using
     * formatLocale() this pattern is used but may differ from standard DateTime patterns.
     *
     * @param string $pattern Output format, e.g: Y-m-d H:i:s
     */
    public function setPattern( $pattern ): void;

    /**
     * Returns the formatted string from given DateTime object based on given locale setting.
     *
     * This method you need if you need day or month names locale specific.
     *
     * @param DateTime $datetime DateTime object
     *
     * @return string Formatted, locale specific string
     */
    public function formatLocale( \DateTime $datetime ): string;
}
