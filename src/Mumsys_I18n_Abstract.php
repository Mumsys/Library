<?php

/**
 * Mumsys_I18n_Abstract
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
 * Abstract class for the internationalization Interface (I18n)
 *
 * @todo Improve/ update tests
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  I18n
 */
abstract class Mumsys_I18n_Abstract
    extends Mumsys_Abstract
    implements Mumsys_I18n_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '3.2.1';

    /**
     * The locale the translation is for/belongs to.
     * @var string
     */
    private $_locale;


    /**
     * Initialize the I18n interface
     *
     * @param string $locale The locale string the translation belongs to e.g.
     * de or de_DE,
     * @param array $options Optional options to be set to setup your individual
     * driver.
     */
    public function __construct( $locale = null, array $options = array() )
    {
        unset( $options ); // currently unused here

        if ( !isset( $locale ) ) {
            throw new Mumsys_I18n_Exception( 'Locale not set' );
        }

        $this->setlocale( $locale );
    }


    /**
     * Replaces/ sets the current locale.
     *
     * @param string $locale ISO-3166 locale string.
     *
     * @throws Mumsys_I18n_Exception if locale does not fit the ISO-3166 format
     */
    public function setlocale( $locale = '' )
    {
        if ( strlen( $locale ) > 5 ) {
            $mesg = sprintf( 'Invalid locale "%1$s"', $locale );
            throw new Mumsys_I18n_Exception( $mesg );
        }

        $this->_locale = (string) $locale;
    }


    /**
     * Returns the current locale.
     *
     * @return string Current locale in ISO-3166 format
     */
    public function getLocale()
    {
        return $this->_locale;
    }


    /**
     * Returns the plural index number to be used for the plural translation.
     * 0 = Singular, 1 = Plural version.
     *
     * Taken from Zend Framework 1.5
     * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
     * @license http://framework.zend.com/license/new-bsd New BSD License
     *
     * @param int|float $number Number to find the plural form
     *
     * @return int|float Number of the plural index
     */
    public function getPluralIndex( $number )
    {
        $number = abs( $number );

        if ( $this->_locale == 'pt_BR' ) {
            $this->_locale = 'xbr'; // temporary set a locale for brasilian
        }

        if ( strlen( $this->_locale ) > 3 ) {
            $this->_locale = substr( $this->_locale, 0, -strlen( strrchr( $this->_locale, '_' ) ) );
        }

        switch ( $this->_locale )
        {
            case 'af':
            case 'az':
            case 'bn':
            case 'bg':
            case 'ca':
            case 'da':
            case 'de':
            case 'el':
            case 'en':
            case 'eo':
            case 'es':
            case 'et':
            case 'eu':
            case 'fa':
            case 'fi':
            case 'fo':
            case 'fur':
            case 'fy':
            case 'gl':
            case 'gu':
            case 'ha':
            case 'he':
            case 'hu':
            case 'is':
            case 'it':
            case 'ku':
            case 'lb':
            case 'ml':
            case 'mn':
            case 'mr':
            case 'nah':
            case 'nb':
            case 'ne':
            case 'nl':
            case 'nn':
            case 'no':
            case 'om':
            case 'or':
            case 'pa':
            case 'pap':
            case 'ps':
            case 'pt':
            case 'so':
            case 'sq':
            case 'sv':
            case 'sw':
            case 'ta':
            case 'te':
            case 'tk':
            case 'ur':
            case 'zu':
                return ( $number == 1 ) ? 0 : 1;
            case 'am':
            case 'bh':
            case 'fil':
            case 'fr':
            case 'gun':
            case 'hi':
            case 'ln':
            case 'mg':
            case 'nso':
            case 'xbr':
            case 'ti':
            case 'wa':
                return ( ( $number == 0 ) || ( $number == 1 ) ) ? 0 : 1;
            case 'be':
            case 'bs':
            case 'hr':
            case 'ru':
            case 'sr':
            case 'uk':
                // return (($number % 10 == 1) && ($number % 100 != 11)) ? 0
                // : ((($number % 10 >= 2) && ($number % 10 <= 4) &&
                //   (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2);
                if ( ( $number % 10 == 1 ) && ( $number % 100 != 11 ) ) {
                    return 0;
                } else if ( ( $number % 10 >= 2 ) && ( $number % 10 <= 4 )
                    && ( ( $number % 100 < 10 ) || ( $number % 100 >= 20 ) )
                ) {
                    return 1;
                } else {
                    return 2;
                }

            case 'cs':
            case 'sk':
                //return ($number == 1) ? 0 : ((($number >= 2) && ($number <= 4)) ? 1 : 2);
                if ( $number == 1 ) {
                    return 0;
                } else if ( ( $number >= 2 ) && ( $number <= 4 ) ) {
                    return 1;
                } else {
                    return 2;
                }

            case 'ar':
                // return ($number == 0) ? 0 : (($number == 1) ? 1 :
                // (($number == 2) ? 2 : ((($number >= 3) && ($number <= 10)) ?
                // 3 : ((($number >= 11) && ($number <= 99)) ? 4 : 5))));
                if ( $number == 0 ) {
                    return 0;
                } else if ( $number == 1 ) {
                    return 1;
                } else if ( $number == 2 ) {
                    return 2;
                } else if ( ( $number >= 3 ) && ( $number <= 10 ) ) {
                    return 3;
                } else if ( ( $number >= 11 ) && ( $number <= 99 ) ) {
                    return 4;
                } else {
                    return 5;
                }

            case 'cy':
                // return ($number == 1) ? 0 : (($number == 2) ? 1 :
                // ((($number == 8) || ($number == 11)) ? 2 : 3));
                if ( $number == 1 ) {
                    return 0;
                } else if ( $number == 2 ) {
                    return 1;
                } else if ( ( $number == 8 ) || ( $number == 11 ) ) {
                    return 2;
                } else {
                    return 3;
                }

            case 'ga':
                return ( $number == 1 ) ? 0 : ( ( $number == 2 ) ? 1 : 2 );

            case 'lt':
                // return (($number % 10 == 1) && ($number % 100 != 11)) ? 0 :
                // ((($number % 10 >= 2) && (($number % 100 < 10) ||
                // ($number % 100 >= 20))) ? 1 : 2);
                if ( ( $number % 10 == 1 ) && ( $number % 100 != 11 ) ) {
                    return 0;
                } else if ( ( $number % 10 >= 2 ) &&
                    ( ( $number % 100 < 10 ) || ( $number % 100 >= 20 ) )
                ) {
                    return 1;
                } else {
                    return 2;
                }

            case 'lv':
                // return ($number == 0) ? 0 : ((($number % 10 == 1)
                // && ($number % 100 != 11)) ? 1 : 2);
                if ( $number == 0 ) {
                    return 0;
                } else if ( ( ( $number % 10 == 1 ) && ( $number % 100 != 11 ) ) ) {
                    return 1;
                } else {
                    return 2;
                }

            case 'mk':
                return ( $number % 10 == 1 ) ? 0 : 1;

            case 'mt':
                // return ($number == 1) ? 0 : ((($number == 0) ||
                // (($number % 100 > 1) && ($number % 100 < 11))) ? 1 :
                // ((($number % 100 > 10) && ($number % 100 < 20)) ? 2 : 3));
                if ( $number == 1 ) {
                    return 0;
                } else if ( $number == 0 || ( ( $number % 100 > 1 ) && ( $number % 100 < 11 ) ) ) {
                    return 1;
                } else if ( ( $number % 100 > 10 ) && ( $number % 100 < 20 ) ) {
                    return 2;
                } else {
                    return 3;
                }

            case 'pl':
                // return ($number == 1) ? 0 : ((($number % 10 >= 2) &&
                // ($number % 10 <= 4) && (($number % 100 < 12) ||
                // ($number % 100 > 14))) ? 1 : 2);
                if ( $number == 1 ) {
                    return 0;
                } else if ( ( $number % 10 >= 2 ) && ( $number % 10 <= 4 ) &&
                    ( ( $number % 100 < 12 ) || ( $number % 100 > 14 ) )
                ) {
                    return 1;
                } else {
                    return 2;
                }

            case 'ro':
                // return ($number == 1) ? 0 : ((($number == 0) ||
                // (($number % 100 > 0) && ($number % 100 < 20))) ? 1 : 2);
                if ( $number == 1 ) {
                    return 0;
                } else if ( ( $number == 0 ) ||
                    ( ( $number % 100 > 0 ) && ( $number % 100 < 20 ) )
                ) {
                    return 1;
                } else {
                    return 2;
                }

            case 'sl':
                // return ($number % 100 == 1) ? 0 : (($number % 100 == 2) ?
                // 1 : ((($number % 100 == 3) || ($number % 100 == 4)) ? 2 : 3));
                if ( $number % 100 == 1 ) {
                    return 0;
                } else if ( ( $number % 100 == 2 ) ) {
                    return 1;
                } else if ( ( $number % 100 == 3 ) || ( $number % 100 == 4 ) ) {
                    return 2;
                } else {
                    return 3;
                }

            default:
                return 0;
        }
    }

}
