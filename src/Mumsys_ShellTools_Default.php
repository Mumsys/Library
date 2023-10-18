<?php declare(strict_types=1);

/**
 * Mumsys_ShellTools_Default
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2023 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  ShellTools
 * Created: 2023-07-27
 */


/**
 * Default shell tools adapter handling implementation to manage mixed adapters
 * in an application.
 */
class Mumsys_ShellTools_Default
    extends Mumsys_Abstract
//    extends class Mumsys_ShellTools_Abstract
//    implements Mumsys_ShellTools_Interface
{
    /**
     * Version ID information.
     */
    public const VERSION = '1.0.0';

    /**
     * Logger
     * @var Mumsys_Logger_Interface
     */
    private Mumsys_Logger_Interface $_logger;

    /**
     * Config
     * @var Mumsys_Config_Interface
     */
    private Mumsys_Config_Interface $_config;

    /**
     * List of Adapter implementing Mumsys_ShellTools_Adapter_Interface
     * @var array<string, Mumsys_ShellTools_Adapter_Interface>
     */
    private array $_adapterListAvailable = array();

    /**
     * List of Adapter implementing Mumsys_ShellTools_Adapter_Interface of current cmd line.
     * @var array<string, Mumsys_ShellTools_Adapter_Interface>
     */
    private array $_adapterListToUse = array();


    /**
     * Init the default manager hanling adapters.
     *
     * @param array<Mumsys_ShellTools_Adapter_Interface> $adapterList List of adapters to be handled.
     *
     * @param Mumsys_Logger_Interface $oLogger Logger interface
     * @param Mumsys_Config_Interface $oConfig Config interface
     */
    public function __construct( array $adapterList,
        Mumsys_Logger_Interface $oLogger, Mumsys_Config_Interface $oConfig )
    {
        $this->_logger = $oLogger;
        $this->_config = $oConfig;

        $getOptsPrefixKey = 'getopts';
        $getOptsDefault = array(
            '--help|-h' => 'Print this help information',
            '--helplong' => 'Print this help and additional/ developer informations',
            '--test|-t' => 'Flag: Test before execute.'
        );
        $this->_config->register( $getOptsPrefixKey . '/_default_', $getOptsDefault );

        /** @var Mumsys_ShellTools_Adapter_Interface $oAdapter 4SCA */
        foreach ( $adapterList as $oAdapter ) {
            $this->addAdapter( $oAdapter );

            foreach ( $oAdapter->getCliOptions() as $optKey => $optValues ) {
                $this->_config->register(
                    $getOptsPrefixKey . '/' . $optKey, $optValues
                );
            }
        }
    }


    /**
     * Checks the list of adapters to be valid by given input.
     *
     * @param array<string, scalar|array<string, scalar>> $cliResults Results of cli input
     *
     * @throws Mumsys_ShellTools_Adapter_Exception If errors found
     */
    public function validate( array $cliResults ): void
    {
        foreach ( $this->_adapterListAvailable as $className => $oAdapter ) {
            $status = $oAdapter->validate( $cliResults );
            if ( $status === true ) {
                $message = sprintf(
                    'Add "%1$s" to list of relevant adapters (current cmd line)',
                    $className
                );
                $this->_logger->log( $message, 7 );
                $this->_adapterListToUse[$className] = $oAdapter;
            }
        }
    }


    /**
     * Executes relevant adapters.
     *
     * Relevant adapters are detected in validate().
     *
     * @param bool $realExecution Flag to disable real execution (false) true by default.
     */
    public function execute( bool $realExecution = true ): void
    {
        /** @var Mumsys_ShellTools_Adapter_Interface $oAdapter 4SCA */
        foreach ( $this->_adapterListToUse as $oAdapter ) {
            $this->_logger->log( '-----------------------------', 7 );
            $oAdapter->execute( $realExecution );
        }
    }


    /**
     * Adds an adapter implementing Mumsys_ShellTools_Adapter_Interface.
     *
     * @param Mumsys_ShellTools_Adapter_Interface $adapter Adapter to add
     *
     * @throws Mumsys_ShellTools_Exception If adapter already set
     */
    public function addAdapter( Mumsys_ShellTools_Adapter_Interface $adapter ): void
    {
        $className = get_class( $adapter );
        if ( isset( $this->_adapterListAvailable[$className] ) ) {
            $mesg = 'Adapter already set: "' . $className . '"';
            throw new Mumsys_ShellTools_Exception( $mesg );
        }

        $this->_adapterListAvailable[$className] = $adapter;
    }

}
