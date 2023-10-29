<?php

/**
 * Mumsys_Service_Spss_Reader
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2015 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 * @version     1.0.0
 * Created: 2017-11-30
 */


/**
 * Reader manager for the SPSS parser.
 *
 * @see https://github.com/flobee/spss
 *
 * Usage:
 * <pre>
 * $parser = \SPSS\Sav\Reader::fromString( file_get_contents( $spssFile );
 * $manager = new Mumsys_Service_Spss_Reader( $parser );
 * $varItems = $manager->getVariableItems( array('label1','label2','label3'...);
 * </pre>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 */
class Mumsys_Service_Spss_Reader
    extends Mumsys_Service_Spss_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '2.2.0';

    /**
     * Variable mapping (internal -> public name)
     * @var array
     */
    private $_varMapping;

    /**
     * Character encoding.
     * @var string
     */
    private $_encoding;

    /**
     * [Major, Minor, Revision] information
     * @var array
     */
    private $_version;

    /**
     * Floating point informations.
     * @var array
     */
    private $_floatInfo;


    /**
     * Returns a list of \Variable items by given list of public variable names.
     *
     * @param array $labels list of public keys to return the respresented item
     *
     * @return array List of key|ID/Variable item pairs
     */
    public function getVariableItems( array $labels = array() ): array
    {
        $list = array();
        $tmp = array();
        $map = $this->getVariableMapByLabels( $labels );

        foreach ( $this->_spss->variables as $dataID => $varObj ) {
            if ( $varObj instanceof SPSS\Sav\Record\Variable ) {
                if ( $this->_checkVariableNameExists( $map, $varObj ) ) {
                    $tmp[$dataID] = $varObj;
                }
            }
        }

        // re-map to initial order
        foreach ( $map as $internal => & $public ) {
            foreach ( $tmp as $id => & $obj ) {
                if ( $obj->name === $internal ) {
                    $list[$id] = $obj;
                }
            }
        }

        return $list;
    }


    /**
     * Returns a list of variable mapping for required variable names.
     *
     * @param array $labels List of labels to search for
     *
     * @return array List of public name/internal key name variables
     */
    public function getVariableMapByLabels( array $labels = array() ): array
    {
        $map = $this->getVariableMap();
        $result = array();

        foreach ( $labels as $key ) {
            foreach ( $map as $internal => & $public ) {
                if ( isset( $result[$internal] ) ) {
                    continue;
                }

                if ( $key === $public ) {
                    $result[$internal] = $public;
                }
            }
        }

        return $result;
    }


    /**
     * Returns a list of variable mapping (internal/public pairs) for required
     * variable internal keys.
     *
     * @param array $keys List of internal keys to search for
     *
     * @return array List of internal/public name pairs
     */
    public function getVariableMapByKeys( array $keys = array() ): array
    {
        $map = $this->getVariableMap();
        $result = array();

        foreach ( $map as $internal => & $public ) {
            foreach ( $keys as $key ) {
                if ( isset( $map[$key] ) && !isset( $result[$internal] ) ) {
                    $result[$internal] = $public;
                }
            }
        }

        return $result;
    }


    /**
     * Returns a list of variable mappings.
     *
     * @param array $regexs List of regular expressions to lock for several
     * matches, empty array returns all variable mappings.
     *
     * @return array List of internal key /public name variables
     */
    public function getVariableMapByRegex( array $regexs = array() ): array
    {
        $result = array();

        $map = $this->getVariableMap();

        if ( $regexs ) {
            foreach ( $map as $internal => $public ) {
                foreach ( $regexs as $req ) {
                    if ( ( $match = preg_match( $req, $public ) ) === false ) {
                        throw new Mumsys_Service_Spss_Exception( 'Regex error' );
                    }

                    if ( $match ) {
                        $result[$internal] = $public;
                    }
                }
            }
        } else {
            $result = $map;
        }

        return $result;
    }


    /**
     * Return the variable mapping list of key(internal)/value(external) pairs.
     *
     * Internal is used for the data, external used in SPSS tables to show the
     * variable names including camel case.
     *
     * @return array List of key(internal)/value(external) pairs or empty array
     * for not found/exists.
     */
    public function getVariableMap(): array
    {
        if ( $this->_varMapping !== null ) {
            return $this->_varMapping;
        }

        $this->_varMapping = array();

        foreach ( $this->_spss->info as & $obj ) {
            if ( $obj instanceof \SPSS\Sav\Record\Info\LongVariableNames ) {
                $this->_varMapping = $obj->data;
                break;
            }
        }

        return $this->_varMapping;
    }


    /**
     * Returns the list of values/ results.
     *
     * @return array List of key/records pairs
     */
    public function getData(): array
    {
        return $this->_spss->data;
    }


    /**
     * Returns the custom document info.
     *
     * @return array List of messages
     */
    public function getDocumentInfo(): array
    {
        return $this->_spss->documents;
    }


    /**
     * Returns the character encoding.
     *
     * @return string String of the character encoding, e.g. UTF-8
     */
    public function getEncoding()
    {
        if ( $this->_encoding !== null ) {
            return $this->_encoding;
        }

        foreach ( $this->_spss->info as $obj ) {
            if ( $obj instanceof \SPSS\Sav\Record\Info\CharacterEncoding ) {
                $this->_encoding = $obj->value;
                break;
            }
        }

        return $this->_encoding;
    }


    /**
     * Returns the SPSS or PSPP version of the .sav file.
     *
     * @return array List of [Major, Minor, Revision] information
     */
    public function getVersionOfSource()
    {
        if ( $this->_version !== null ) {
            return $this->_version;
        }

        foreach ( $this->_spss->info as $obj ) {
            if ( $obj instanceof \SPSS\Sav\Record\Info\MachineInteger ) {
                $this->_version = $obj->version;
                break;
            }
        }

        return $this->_version;
    }


    /**
     * Returns the machine floating point info.
     *
     * @return array Information of: sysmis, highest, lowest.
     */
    public function getFloatingPointInfo()
    {
        if ( $this->_floatInfo !== null ) {
            return $this->_floatInfo;
        }

        foreach ( $this->_spss->info as $obj ) {
            if ( $obj instanceof \SPSS\Sav\Record\Info\MachineFloatingPoint ) {
                $this->_floatInfo = array(
                    'sysmis' => $obj->sysmis,
                    'highest' => $obj->highest,
                    'lowest' => $obj->lowest
                );
                break;
            }
        }

        return $this->_floatInfo;
    }


    /**
     * Check if a Variable item is in the list of mapping we want.
     *
     * @param array $labelMap List of internal/public map
     * @param SPSS\Sav\Record\Variable $oVar Variable item
     *
     * @return boolean true for found, otherwise false
     */
    private function _checkVariableNameExists( $labelMap, $oVar ): bool
    {
        $result = false;

        foreach ( $labelMap as $internal => & $public ) {
            if ( $oVar->name === $internal ) {
                $result = true;
                break;
            }
        }

        return $result;
    }

}
