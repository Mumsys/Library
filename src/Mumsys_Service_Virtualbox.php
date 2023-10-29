<?php

/**
 * Mumsys_Service_Virtualbox
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2020 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 * @version     1.0.0
 * Created: 2020-01-05
 */


/**
 * Virtualbox service helper class to start, list, stop a VM.
 * Requires 'Virtualbox Extension Pack'
 */
class Mumsys_Service_Virtualbox
    extends Mumsys_Service_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * VM name to work with
     * @var string
     */
    private $_vmName = 'your-vm-name';

    /**
     * VM start command
     * @var string
     */
    private $_vmStart = 'VBoxManage startvm \'%1$s\' --type headless';

    /**
     * VM stop command
     * @var string
     */
    private $_vmStop = 'VBoxManage controlvm \'%1$s\' poweroff --type headless';

    /**
     * VM list activ vm's command
     * @var string
     */
    private $_vmListActiv = 'VBoxManage list runningvms';

    /**
     * VM list all vm's command
     * @var string
     */
    private $_vmListAll = 'VBoxManage list vms';

    /**
     * VM status befor start running this parts to end/ leave the vm in previous state.
     * E.g. if it was UP leave it without shutdown, otherwise shutdown
     * @var boolean
     */
    private $_statusBefore = false;

    /**
     * @var Mumsys_Logger_Interface
     */
    private $_logger;


    /**
     * Initialize the object.
     *
     * @param array $options List of key/value pairs to set private properties.
     *  'name' => '_vmName',
     *  'start' => '_vmStart',
     *  'stop' => '_vmStop',
     *  'listall' => '_vmListAll',
     *  'listactiv' => '_vmListActiv',
     *
     * @param Mumsys_Logger_Interface $logger Logger
     */
    public function __construct( array $options, Mumsys_Logger_Interface $logger )
    {
        // external to internal properties
        $map = array(
            'name' => '_vmName',
            'start' => '_vmStart',
            'stop' => '_vmStop',
            'listall' => '_vmListAll',
            'listactiv' => '_vmListActiv',
        );

        foreach ( $options as $key => $value ) {
            if ( isset( $map[$key] ) ) {
                $this->{$map[$key]} = trim( (string) $value );
            }
        }

        $this->_logger = $logger;

        // pre check 'VBoxManage' exists
        $result = $this->execute( 'which VBoxManage' );
        if ( $result['code'] !== 0 ) {
            throw new Mumsys_Service_Exception( '"VBoxManage" not available. Exit' );
        }
    }


    /**
     * Start the vm.
     *
     * @return boolean True on success, false on error
     */
    public function startVM()
    {
        $result = $this->execute( sprintf( $this->_vmStart, $this->_vmName ) );

        if ( $result['code'] === 0 ) {
            return true;
        }

        return false;
    }


    /**
     * Stop/ shutdown the vm.
     *
     * @return boolean True on success, false on error
     */
    public function stopVM()
    {
        $result = $this->execute( sprintf( $this->_vmStop, $this->_vmName ) );

        if ( $result['code'] === 0 ) {
            return true;
        }

        return false;
    }


    /**
     * Returns the list of VM's available
     *
     * @return array List of VM's available (['"vmName" { hash }'],...)
     */
    public function listAll(): array
    {
        $result = $this->execute( $this->_vmListAll );

        return $result['content'];
    }


    /**
     * Returns the list of activ/currently running VM's
     *
     * @return array List of activ VM's (['"vmName" { hash }'],...)
     */
    public function listActiv(): array
    {
        $result = $this->execute( $this->_vmListActiv );

        return $result['content'];
    }


    /**
     * Checks if the vm is already running.
     *
     * @return boolean True if already active otherwise false
     */
    public function checkStatus()
    {
        $result = $this->execute( $this->_vmListActiv );

        foreach ( $result['content'] as $vmLine ) {
            if ( preg_match( '/(' . $this->_vmName . ')/', $vmLine ) === 1 ) {
                $this->_statusBefore = true;
                break;
            }
        }

        $status = ( $this->_statusBefore === true ) ? 'Already Up' : 'Down';
        $this->_logger->log( 'VM status: "' . $status . '"', 7 );

        return $this->_statusBefore;
    }


    /**
     * Execute and return the result of an shell command.
     *
     * @param string $cmd Command to be executed.
     *
     * @return array Returns array containing key/value pairs of the exec result:
     *  'message' => last line of the cammand output
     *  'code' => The returned exit code
     *  'content' => array containing all of the output from stdout of the cmd
     */
    public function execute( string $cmd ): array
    {
        $data = $code = null;
        $_cmd = escapeshellcmd( $cmd );

        $this->_logger->log( 'Run cmd: "' . $_cmd . '"', 7 );
        $lastLine = exec( $_cmd, $data, $code );

        if ( $code > 0 ) {
            $this->_logger->log( 'Warning! Error from shell execution detected, Set a high log level', 0 );
        }

        $this->_logger->log( 'cmd was : "' . $_cmd . '"', 7 );
        $this->_logger->log( 'cmd code: ' . $code, 7 );
        foreach ( $data as $k => &$value ) {
            $this->_logger->log( 'cmd data[' . $k . ']: ' . $value, 7 );
        }

        $result = array(
            'message' => $lastLine,
            'code' => $code,
            'content' => $data
        );

        return $result;
    }

}
