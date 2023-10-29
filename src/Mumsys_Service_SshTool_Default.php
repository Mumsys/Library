<?php declare(strict_types=1);

/**
 * Mumsys_Service_SshTool_Default
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2018 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 * created: 2018-05-10
 */


/**
 * SSH config generator, pub key registration, key deployment and keys revocation.
 *
 * Uses config files, each per host, to improve rollouts to other machines.
 * Check the tests to get into it or read the demo/test configs for details.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Service
 */
class Mumsys_Service_SshTool_Default
    extends Mumsys_Abstract
{
    /**
     * Version ID information.
     */
    const VERSION = '1.0.0';

    /**
     * Location to the target ssh config file to save the content to. E.g:
     * "/home/user/.ssh/config"
     * @var string
     */
    private $_sshConfFile;

    /**
     * Location to the path where configs exists to create the ssh config
     * @var string
     */
    private $_confsPath;

    /**
     * Mode to set the file permission in octal, eg: 0600
     * @var int
     */
    private $_sshConfFileMode = 0600;

    /**
     * List of config files as host/configs
     * @var array
     */
    private $_configs = array();

    /**
     * Location to the global identity file
     * @var string
     */
    private $_globalIdenittyFile = '';

    /**
     * Location of the $HOME directory.
     * @var string
     */
    private $_home;

    /**
     * Current running user.
     * @var string
     */
    private $_user;

    /**
     * List of commands generated
     * @ v ar array
     */
    //private $_cmdList; disabled 4SCA


    /**
     * Initialize the object
     *
     * @param string $configsPath optional path of configs for this program.
     * Default: ~/.ssh/conffiles
     * @param string $outFile Optional location to the config file to be created.
     * Default $HOME/.ssh/ssh-config-generated.
     *
     * @throws Exception If config path invalid or not running in cli mode
     * @throws Mumsys_Service_Exception
     */
    public function __construct( string $configsPath = null, string $outFile = null )
    {
        $this->_user = Mumsys_Php_Globals::getServerVar( 'USER', 'unknown' );

        $serverHome = Mumsys_Php_Globals::getServerVar( 'HOME', null );
        $this->_home = './';
        if ( isset( $serverHome ) && ( $_home = (string) $serverHome ) ) {
            $this->_home = $this->_checkPath( $_home );
        }

        if ( $outFile ) {
            if ( is_string( $outFile )
                && is_dir( dirname( $outFile ) . DIRECTORY_SEPARATOR )
            ) {
                $genCfgFile = $outFile;
            } else {
                $mesg = sprintf(
                    'Given config file path not found: "%1$s"', $outFile
                );
                throw new Mumsys_Service_Exception( $mesg );
            }
        } else {
            $genCfgFile = $this->_home . '/.ssh/ssh-config-generated';
        }

        $this->setConfigFile( $genCfgFile );
        $this->setConfigsPath( $configsPath );

        $this->init();
    }


    /**
     * Initialize the object to be prepeard to run actions.
     *
     * You may use setConfigFile(), setConfigsPath() at a later time then init()
     * must be called befor running create|register|revoke|deploy action.
     */
    public function init(): void
    {
        $this->_loadConfigs();
    }


    /**
     * Sets the location to the path where config files exists to create the
     * ssh config file.
     *
     * @param string $path Location to conffiles path (default: ~/.ssh/conffiles)
     */
    public function setConfigsPath( string $path = null )
    {
        if ( $path === null || $path === '' ) {
            $path = $this->_home . '/.ssh/conffiles';
        }

        if ( $path[0] === '~' ) {
            $path = str_replace( '~', $this->_home, $path );
        }

        if ( $path && is_dir( $path . DIRECTORY_SEPARATOR ) ) {
            $this->_confsPath = (string) $path;
        } else {
            $mesg = sprintf( 'Configs paths not found "%1$s"', $path );
            throw new Mumsys_Service_Exception( $mesg );
        }

        $this->_globalIdenittyFile = include $this->_confsPath
            . '/../global/identityFile.php';
    }


    /**
     * Sets the location to the ssh config file to write to.
     *
     * @param string $file Location to the ssh config file
     *
     * @throws Mumsys_Service_Exception If path of the file not exists or
     * target is not writeable
     */
    public function setConfigFile( string $file )
    {
        $this->_checkPath( dirname( $file ) );
        $this->_sshConfFile = $file;
    }


    /**
     * Sets permission mode of the ssh config file to write to.
     *
     * @param int $mode Mode to change the permission in octal
     */
    public function setMode( int $mode = 0600 )
    {
        $this->_sshConfFileMode = $mode;
    }


    /**
     * Add an additional host configuration during runtime.
     *
     * Note: Use this methode after init() was called.
     *
     * @param string $hostname Host to add.
     * @param array $hostConfig Configuration of this host.
     *
     * @throws Mumsys_Service_Exception If config already set
     */
    public function addHostConfig( string $hostname, array $hostConfig ): void
    {
        if ( isset( $this->_configs[$hostname] ) ) {
            $mesg = sprintf( 'Host "%1$s" already set', $hostname );
            throw new Mumsys_Service_Exception( $mesg );
        } else {
            $this->_configs[$hostname] = $hostConfig;
        }
    }


    /**
     * Returns the list of initialised configs.
     *
     * Note: init() must be called first.
     *
     * @return array List of host configs
     */
    public function getHostConfigs(): array
    {
        return $this->_configs;
    }


    /**
     * Generates the ssh config file.
     *
     * If you want to revoke and/or register new keys you should do that first
     * and then create the new ssh config. Otherwise you may can not connect to
     * the target anymore because the new config was set but wasnt deployed
     * before and you end up in connection failures.
     *
     * @param boolean $justOutput Flag to just output the results.
     */
    public function create( bool $justOutput = false ): void
    {
        $string = '';
        foreach ( $this->_configs as $cfg ) {
            $identity = $this->_getIdentityLocation( $cfg['config'] );
            if ( $identity ) {
                $cfg['config']['IdentityFile'] = $identity;
            }

            $string .= $this->_configToString( $cfg['config'] );
        }

        if ( $justOutput ) {
            echo '# output for: ' . $this->_sshConfFile . PHP_EOL;
            echo $string . PHP_EOL;
        } else {
            file_put_contents( $this->_sshConfFile, $string );
            chmod( $this->_sshConfFile, $this->_sshConfFileMode );
        }
    }


    /**
     * Deploy/ publish key files based on given config.
     *
     * It outputs scp commands you may run or pipe them to shell for execution.
     */
    public function deploy(): void
    {
        foreach ( $this->_configs as $cfg ) {

            if ( isset( $cfg['deploy'] ) && $cfg['deploy'] ) {

                $locIDFile = $this->_getIdentityLocation( $cfg['config'] );
                $locIDFilePub = $locIDFile . '.pub';
                $pathIDFile = dirname( $locIDFile );

                foreach ( $cfg['deploy'] as $targetHost => $listIdFiles ) {

                    $configList = array();

                    foreach ( $listIdFiles as $idSrc => $idTarget ) {

                        if ( is_numeric( $idSrc ) ) {
                            switch ( $idTarget )
                            {
                                case '*':
                                    $idSrc = $pathIDFile . '/*';
                                    $configList[$idSrc] = $pathIDFile;
                                    break;

                                case 'IdentityFile':
                                    $configList[$locIDFile] = $locIDFile;
                                    $configList[$locIDFilePub] = $locIDFilePub;
                                    break;

                                default:
                                    $configList[$idTarget] = $idTarget;
                            }

                        } else {
                            if ( $idSrc == '*' ) {
                                $idSrc = $pathIDFile . '/*';
                            }

                            $addPub = false;
                            if ( $idSrc == 'IdentityFile' ) {
                                $idSrc = $locIDFile;
                                $addPub = true;
                            }

                            if ( $idTarget == 'IdentityFile' ) {
                                $idTarget = $locIDFile;
                                $addPub = true;
                            }

                            $configList[$idSrc] = $idTarget;

                            if ( $addPub ) {
                                $configList[$idSrc . '.pub'] = $idTarget . '.pub';
                            }
                        }
                    }

                    $targetUser = $this->_getUserForHost( $targetHost );
                    $this->_deployExecute( $configList, $targetUser, $targetHost );
                }
            }
        }
    }


    /**
     * Register/ authorise public keys at the target hosts.
     */
    public function register(): void
    {
        foreach ( $this->_configs as $host => $cfg ) {

            if ( isset( $cfg['register'] ) && $cfg['register'] ) {

                $locIDFile = $this->_getIdentityLocation( $cfg['config'] );
                $locIDFilePub = $locIDFile . '.pub';

                foreach ( $cfg['register'] as $targetHost => $listPubFiles ) {

                    if ( $listPubFiles === '*' ) {
                        $this->_registerAllConfigs();
                        continue;
                    }

//                    if ( !isset( $this->_configs[$targetHost] ) ) {
//                        echo "# skip host '$targetHost'. Config for this host not available." . PHP_EOL;
//                        continue;
//                    }

                    $configList = array();
                    foreach ( $listPubFiles as $idSrc => $idTarget ) {

                        if ( !is_numeric( $idSrc ) ) {
                            $mesg = sprintf(
                                'Invalid "%1$s" configuration found in host file '
                                . '"%2$s" for target "%3$s"',
                                'register',
                                $host,
                                $targetHost
                            );
                            throw new Mumsys_Service_Exception( $mesg );

                        } else {
                            switch ( $idTarget )
                            {
                                case '*':
                                case 'IdentityFile':
                                    $configList[$locIDFilePub] = $locIDFilePub;
                                    break;

                                default:
                                    $configList[$idTarget] = $idTarget;
                            }
                        }
                    }

                    $targetUser = $this->_getUserForHost( $targetHost );
                    $this->_registerExecute( $configList, $targetUser, $targetHost );
                }
            }
        }
    }


    /**
     * Revoke a list of public/private keys at the target server.
     *
     * It should also removes the pub keys from the known hosts (incomplete).
     */
    public function revoke()
    {
        foreach ( $this->_configs as $targetHost => $cfg ) {

            if ( isset( $cfg['revoke'] ) && $cfg['revoke'] ) {

                foreach ( $cfg['revoke'] as $file ) {

                    $fileList = array();

                    switch ( $file ) {
                        case 'IdentityFile':
                            $location = $this->_getIdentityLocation( $cfg['config'] );
                            $fileList[0] = $location;
                            $fileList[1] = $location . '.pub';
                            break;
                        default:
                            $fileList[] = $file;
                    }

                    $targetUser = $this->_getUserForHost( $targetHost );
                    $this->_revokeExecute( $fileList, $targetUser, $targetHost );
                }
            }
        }
    }


    //
    // --- protected or private methodes ---------------------------------------
    //


    /**
     * Generates shell commands to deploy configured keys.
     *
     * @param array $configList List of src/target to deploy
     * @param string $user Username to connect to target
     * @param string $targetHost Host to connect to
     */
    private function _deployExecute( array $configList, string $user,
        string $targetHost ): void
    {
        foreach ( $configList as $src => $target ) {
            echo "scp $src $user@$targetHost:$target" . PHP_EOL
//            . PHP_EOL
            ;
        }
    }


    /**
     * Register/ authorise all public key which are configured in host files.
     */
    private function _registerAllConfigs(): void
    {
        $pubList = $this->_getAllPublicKeysByHosts();
        foreach ( $pubList as $targetHost => $pubKey ) {
            $targetUser = $this->_getUserForHost( $targetHost );
            $this->_registerExecute( array($pubKey), $targetUser, $targetHost );
        }
    }


    /**
     * Register/ authorise public keys to allow access.
     *
     * Note: Use register() before you revoke keys!
     *
     * @param array $pubList List of public keys to register/ authorise at the
     * target host
     * @param string $user Username to connect to the target host
     * @param string $targetHost Host to connect to the target host
     */
    private function _registerExecute( array $pubList, $user, $targetHost ): void
    {
        foreach ( $pubList as $location ) {
            if ( substr( $location, -4 ) == '.pub' ) {
                // local  : awk '!seen[$0]++' auth_file > auth_file
                // via ssh: awk '\!seen[\$0]++' auth_file | cat > auth_file

                $authFile = '~/.ssh/authorized_keys';

                $cmdLocal = array(
                    "cat $location",
                    // format the entry
                    "awk '{print \"#\\n# \"$3\"\\n\"$0}'"
                );

                $cmdRemote = array(
                    // push to auth file
                    'cat >> ' . $authFile,
                    // scan, remove dups, re-set file
                    'awk \'\!seen[\\$0]++\' ' . $authFile . ' | cat > ' . $authFile
                );

                // single calls
                echo implode( ' | ', $cmdLocal ) . " | ssh $user@$targetHost \"" . $cmdRemote[0] . '"' . PHP_EOL;
                echo "ssh $user@$targetHost \"" . $cmdRemote[1] . '"' . PHP_EOL;
                echo PHP_EOL;

                // does not work. who can help?
                //echo implode( ' | ', $cmdLocal ) . " | ssh $user@$targetHost \""
                //    . implode( ' && ', $cmdRemote ) . '"' . PHP_EOL;
            }
        }
    }


    /**
     * Removes target key and trys to remove pub key from authorised keys and
     * trys to remove from known hosts.
     *
     * //sed -i.bak '/REGEX_MATCHING_KEY/d' ~/.ssh/authorized_keys
     * // sed -i "s#`cat ~/.ssh/my/id_rsa_fb_2018.pub`##" ~/.ssh/authorized_keys
     * // or: ssh u@h "sed -i 's#`cat ~/.ssh/my/id_rsa_fb_2018.pub`##' ~/.ssh/authorized_keys"
     *
     * @param array $target
     * @param string $user
     * @param string $targetHost
     */
    private function _revokeExecute( array $target, $user, $targetHost )
    {
        //$authFile = '~/.ssh/authorized_keys';

        foreach ( $target as $location ) {
            $cmdRemote = array();
            if ( substr( $location, -4 ) == '.pub' ) {
                $cmdRemote[] = "sed -i 's#`cat $location`##' ~/.ssh/authorized_keys";
            }
            $cmdRemote[] = 'rm -f ' . $location;

            echo "ssh $user@$targetHost \"" . implode( ' ; ', $cmdRemote ) . '"' . PHP_EOL;
        }

    }


    //
    // --- helper --------------------------------------------------------------
    //

    /**
     * Returns the location of the identity file if it was set.
     *
     * @param array $config A host configuration ( $host['config'] )
     *
     * @return string Location to the private key configured in IdentityFile or
     * global IdentityFile location
     */
    public function _getIdentityLocation( array $config ): string
    {
        $idFile = '';
        if ( isset( $config['IdentityFile'] ) ) {
            if ( $config['IdentityFile'] === true ) {
                $idFile = $this->_globalIdenittyFile;
            } else {
                $idFile = $config['IdentityFile'];
            }
        }

        return $idFile;
    }


    /**
     * Returns the username for the target host.
     *
     * @param string $targetHost Hostname of the taget host
     *
     * @return string
     */
    private function _getUserForHost( string $targetHost ): string
    {
        if ( isset( $this->_configs[$targetHost]['config']['User'] ) ) {
            $targetUser = $this->_configs[$targetHost]['config']['User'];
        } else {
            $targetUser = $this->_user;
        }

        return $targetUser;
    }


    /**
     * Returns a list of host/public key from all configs.
     *
     * It will use the suffix '.pub' from the configured IdentityFile.
     * If the files does not exists no error will be thrown
     *
     * @return array List of public keys from all host files
     */
    private function _getAllPublicKeysByHosts(): array
    {
        $pubList = array();
        foreach ( $this->_configs as $host => $cfg ) {
            $key = $this->_getIdentityLocation( $cfg['config'] );
            if ( !$key ) {
                continue;
            }

            $pubList[$host] = $key . '.pub';
        }

        return $pubList;
    }


    /**
     * Returns the host config content as string.
     *
     * @param array $config List of key/value pairs representing a line of a ssh
     * config or a comment line
     *
     * @return string Config string to be added to the target
     */
    private function _configToString( array $config ): string
    {
        $string = '';

        foreach ( $config as $key => $value ) {
            if ( is_numeric( $key ) ) {
                $string .= $value . "\n";
            } else {
                $string .= $key . ' ' . $value . "\n";
            }
        }

        return $string . "\n";
    }


    /**
     * Check the path for the ssh config file if it exists and will be
     * writeable.
     *
     * @param string $path The path to be checked
     *
     * @return string path Existing location/path
     * @throws Mumsys_Service_Exception If path not exists or not writeable
     */
    private function _checkPath( string $path ): string
    {
        $targetPath = filter_var( $path, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

        if ( !is_dir( $targetPath . DIRECTORY_SEPARATOR ) ) {
            $message = sprintf( 'Path does not exists "%1$s"', $targetPath );
            throw new Mumsys_Service_Exception( $message );
        }

        if ( !is_writable( $targetPath . DIRECTORY_SEPARATOR ) ) {
            $message = sprintf( 'Path not writable "%1$s"', $targetPath );
            throw new Mumsys_Service_Exception( $message );
        }

        return $targetPath;
    }


    /**
     * Load the config host files.
     *
     * @return void If configs where already loaded
     * @throws Mumsys_Service_Exception On error loading a config file
     */
    private function _loadConfigs()
    {
        $list = scandir( $this->_confsPath . DIRECTORY_SEPARATOR );
        natcasesort( $list );
        foreach ( $list as $file ) {
            if ( $file[0] === '.' ) {
                continue;
            }

            $location = $this->_confsPath . '/' . $file;
            $config = $this->_loadConfigFile( $location );
            $this->_configs[substr( $file, 0, -4 )] = $config;
        }
    }


    /**
     * Load a host configuration file.
     *
     * @param string $location Location to the host config file
     *
     * @return array Configuration options of a host.php file
     * @throws Mumsys_Service_Exception
     */
    private function _loadConfigFile( string $location ): array
    {
        if ( !is_readable( $location ) ) {
            $mesg = sprintf(
                'Config file not found or wrong permission "%1$s"', $location
            );
            throw new Mumsys_Service_Exception( $mesg );
        }

        if ( ( $config = include $location ) === false ) {
            $mesg = sprintf(
                'Config file could not be loaded "%1$s"', $location
            );
            throw new Mumsys_Service_Exception( $mesg );
        }

        return $config;
    }

}
