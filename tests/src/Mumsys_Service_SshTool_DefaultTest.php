<?php

/**
 * Mumsys_Service_SshTool_DefaultTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2018 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Abstract
 * created: 2018-02-10
 */


/**
 * Mumsys_Service_SshTool_Default Test
 * Generated on 2018-02-10 at 19:07:54.
 */
class Mumsys_Service_SshTool_DefaultTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Service_SshTool_Default
     */
    protected $_object;

    private $_version;
    private $_versions;
    private $_sshFile;
    private $_pathConfigs;
    private $_testConfigFile;
    private $_pathEmptyDir;
    private $_dynTestFile;

    /**
     * Current running user
     * @var string
     */
    private $_user;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_user = MumsysTestHelper::getTestUser();
        $this->_version = '1.0.0';
        $this->_versions = array(
            'Mumsys_Service_SshTool_Default' => $this->_version,
            'Mumsys_Abstract' => Mumsys_Abstract::VERSION,
        );

        $basePathService = MumsysTestHelper::getTestsBaseDir()
            . '/testfiles/Domain/Service/Ssh';
        $this->_sshFile = $basePathService . '/ssh-config-generated';
        $this->_pathConfigs = $basePathService . '/Config/conffiles';
        $this->_testConfigFile = $this->_pathConfigs . '/localhost.php';
        $this->_pathEmptyDir = $basePathService . '/Config/empty';
        $this->_dynTestFile = '';

        $this->_object = new Mumsys_Service_SshTool_Default(
            $this->_pathConfigs, $this->_sshFile
        );

//        $testkeys = $basePath . '/testfiles/Service/Ssh/sshkeys';
//        $configAdds = array(
//            'localhostA' => array(
//                'config' => array(
//                    'Host' => 'localhostA',
//                    'HostName 127.0.0.1',
//                ),
//                'deploy' => array(
//                    $testkeys .'/sshkeyfileA',
//                    $testkeys .'/sshkeyfileA.pub',
//                ),
//            ),
//        );
//
//        foreach($configAdds as $host => $config) {
//            $this->_object->addHostConfig( $host, $config );
//        }
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        if ( file_exists( $this->_sshFile ) ) {
            unlink( $this->_sshFile );
        }

        if ( $this->_dynTestFile && file_exists( $this->_dynTestFile ) ) {
            unlink( $this->_dynTestFile );
        }

        unset( $this->_object );
    }


    public function testVersions()
    {
        $this->assertingEquals(
            $this->_version,
            Mumsys_Service_SshTool_Default::VERSION
        );

        $this->checkVersionList(
            $this->_object->getVersions(), $this->_versions
        );
    }


    /**
     * @covers Mumsys_Service_SshTool_Default::__construct
     * @covers Mumsys_Service_SshTool_Default::_checkPath
     */
    public function test_construct()
    {

        $objectA = new Mumsys_Service_SshTool_Default(
            $this->_pathConfigs, $this->_sshFile
        );
        $objectB = new Mumsys_Service_SshTool_Default( $this->_pathConfigs );

        $this->assertingInstanceOf( 'Mumsys_Service_SshTool_Default', $objectA );
        $this->assertingInstanceOf( 'Mumsys_Service_SshTool_Default', $objectB );
        $this->assertingInstanceOf( 'Mumsys_Abstract', $objectA );

        $this->expectingException( 'Mumsys_Service_Exception' );
        $this->expectingExceptionMessageRegex( '/(Given config file path not found)/i' );
        new Mumsys_Service_SshTool_Default(
            $this->_pathConfigs, $this->_sshFile . '/not/exists'
        );
    }


    /**
     * @covers Mumsys_Service_SshTool_Default::init
     * @covers Mumsys_Service_SshTool_Default::_loadConfigs
     * @covers Mumsys_Service_SshTool_Default::_loadConfigFile
     */
    public function testInit()
    {
        $actualA = $this->_object->getHostConfigs();
        $this->_object->init();
        $actualB = $this->_object->getHostConfigs();
        $this->_object->init(); //4CC?

        $this->assertingSame( $actualB, $actualA );
    }


    /**
     * @covers Mumsys_Service_SshTool_Default::init
     * @covers Mumsys_Service_SshTool_Default::_loadConfigs
     * @covers Mumsys_Service_SshTool_Default::_loadConfigFile
     */
    public function testInitException1()
    {
        $this->expectingException( 'Mumsys_Service_Exception' );
        $this->expectingExceptionMessageRegex( '/(Config file not found)/' );

        $this->_dynTestFile = $this->_pathEmptyDir . '/test.php';
        touch( $this->_dynTestFile );
        chmod( $this->_dynTestFile, 0222 );
        $this->_object->setConfigsPath( $this->_pathEmptyDir );
        $this->_object->init();
    }


    /**
     * @covers Mumsys_Service_SshTool_Default::setConfigsPath
     */
    public function testSetConfigsPath()
    {
        $this->_object->setConfigsPath( $this->_pathConfigs );
        // in create() we will test deeply
        $this->assertingTrue( file_exists( $this->_testConfigFile ) );

        // test exception
        $this->expectingException( 'Mumsys_Service_Exception' );
        $this->expectingExceptionMessageRegex( '/(Configs paths not found)/' );
        $this->_object->setConfigsPath( '~/.ssh/conffiles' );
    }


    /**
     * Exception and 4CC
     * @covers Mumsys_Service_SshTool_Default::setConfigsPath
     */
    public function testSetConfigsPathException()
    {
        $this->expectingException( 'Mumsys_Service_Exception' );
        $this->expectingExceptionMessageRegex( '/(Configs paths not found)/' );
        $this->_object->setConfigsPath();
    }


    /**
     * @covers Mumsys_Service_SshTool_Default::setConfigFile
     * @covers Mumsys_Service_SshTool_Default::_checkPath
     */
    public function testSetConfigFile()
    {
        $this->_object->setConfigFile( $this->_sshFile );

        $this->_object->create();
        // in create() we will test deeply
        $this->assertingTrue( file_exists( $this->_sshFile ) );

        // test exception 1
        $this->expectingException( 'Mumsys_Service_Exception' );
        $this->expectingExceptionMessageRegex( '/(Path does not exists)/' );
        $this->_object->setConfigFile( $this->_sshFile . '/not/exists' );
        $this->_object->create();
    }


    /**
     * @covers Mumsys_Service_SshTool_Default::setConfigFile
     * @covers Mumsys_Service_SshTool_Default::_checkPath
     */
    public function testSetFileCheckPathException()
    {
        $this->expectingException( 'Mumsys_Service_Exception' );
        $this->expectingExceptionMessageRegex( '/(Path not writable)/' );
        $this->_object->setConfigFile( '/root/testsshfile' );
        $this->_object->create();
    }


    /**
     * @covers Mumsys_Service_SshTool_Default::setMode
     */
    public function testSetMode()
    {
        $this->_object->setMode( 0666 );
        $this->_object->create();
        // in create() we will test deeply
        $this->assertingTrue( file_exists( $this->_sshFile ) );

        $expected = 100666;
        $actual = decoct( fileperms( $this->_sshFile ) );
        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_Service_SshTool_Default::addHostConfig
     * @covers Mumsys_Service_SshTool_Default::getHostConfigs
     */
    public function testAddHostConfig()
    {
        $config = array(
            'config' => array(
                '# localhost2 for tests',
                'Host' => 'localhost2',
                '# HostName localhost2',
            )
        );

        $this->_object->addHostConfig( 'localhost2', $config );
        $actual = $this->_object->getHostConfigs();
        $this->assertingTrue( array_key_exists( 'localhost2', $actual ) );

        $this->expectingException( 'Mumsys_Service_Exception' );
        $this->expectingExceptionMessage( 'Host "localhost" already set' );
        $this->_object->addHostConfig( 'localhost', $config );
    }


    /**
     * Test to create a ssh config file.
     *
     * @covers Mumsys_Service_SshTool_Default::create
     * @covers Mumsys_Service_SshTool_Default::_configToString
     * @covers Mumsys_Service_SshTool_Default::_getIdentityLocation
     */
    public function testCreateAction()
    {
        $this->_object->init();
        $this->_object->create();

        $this->assertingTrue( file_exists( $this->_sshFile ) );

        $expectedA = '# localhost' . "\n"
            . 'Host localhost' . "\n"
            . 'HostName localhost' . "\n"
            . 'Port 22' . "\n"
            . 'IdentityFile ~/.ssh/id_rsa' . "\n"
            . 'PreferredAuthentications publickey' . "\n"
            . 'Protocol 2' . "\n"
            . "\n"
            . "# otherhost\n"
            . "Host otherhost\n"
            . "# HostName otherhost.com\n"
            . "Port 22\n"
            . "User otheruser\n"
            . "IdentityFile ./path/to/my/global/id/file\n"
            . "PreferredAuthentications publickey\n"
            . "Protocol 2\n"
            . "\n"
            . "# secondhost\n"
            . "Host secondhost\n"
            . "Port 22\n"
            . "PreferredAuthentications publickey\n"
            . "Protocol 2\n"
            . "\n"
        ;
        $actualA = file_get_contents( $this->_sshFile );

        ob_start();
        $this->_object->create( true );
        $actualB = ob_get_clean();
        $expectedB = '# output for: ' . $this->_sshFile . PHP_EOL
            . $expectedA . PHP_EOL
        ;

        $this->assertingEquals( $expectedA, $actualA );
        $this->assertingEquals( $expectedB, $actualB );
    }


    /**
     * Test to create a ssh config file.
     *
     * @covers Mumsys_Service_SshTool_Default::deploy
     * @covers Mumsys_Service_SshTool_Default::_deployExecute
     * @covers Mumsys_Service_SshTool_Default::_getUserForHost
     *
     * @runInSeparateProcess
     */
    public function testDeployAction()
    {
        $this->_object->init();

        ob_start();
        $this->_object->deploy();
        $actualA = ob_get_clean();
        $expectedA = ''
            . "scp ~/.ssh/id_rsa otheruser@otherhost:~/.ssh/id_rsa\n"
            . "scp ~/.ssh/id_rsa.pub otheruser@otherhost:~/.ssh/id_rsa.pub\n"
            . "scp /simple/test/copy/this/file otheruser@otherhost:/simple/test/copy/this/file\n"
            . "scp ~/.ssh/* otheruser@otherhost:~/.ssh\n"
            . "scp /this/keyfile otheruser@otherhost:~/.ssh/id_rsa\n"
            . "scp /this/keyfile.pub otheruser@otherhost:~/.ssh/id_rsa.pub\n"
            . "scp ~/.ssh/id_rsa " . $this->_user . "@secondhost:/goes/here\n"
            . "scp ~/.ssh/id_rsa.pub " . $this->_user . "@secondhost:/goes/here.pub\n"
            . "scp /this/id_rsa " . $this->_user . "@secondhost:/goes/there/id_key\n"
            . "scp /this/id_rsa.pub " . $this->_user . "@secondhost:/goes/there/id_key.pub\n"
            . "scp ~/.ssh/* " . $this->_user . "@secondhost:~/.ssh/keys/from/localhost\n"
        ;

        $this->assertingEquals( $expectedA, $actualA );
    }


    /**
     * Test to register public key at target hosts.
     *
     * @covers Mumsys_Service_SshTool_Default::register
     * @covers Mumsys_Service_SshTool_Default::_registerAllConfigs
     * @covers Mumsys_Service_SshTool_Default::_registerExecute
     * @covers Mumsys_Service_SshTool_Default::_getAllPublicKeysByHosts
     * @covers Mumsys_Service_SshTool_Default::_getUserForHost
     * @covers Mumsys_Service_SshTool_Default::addHostConfig
     *
     * @runInSeparateProcess
     */
    public function testRegisterAction()
    {
        $this->_object->init();

        ob_start();
        $this->_object->register();
        $actualA = ob_get_clean();
        $expectedA = ''
            . 'cat ~/.ssh/id_rsa.pub | awk \'{print "#\n# "$3"\n"$0}\' | '
            . 'ssh ' . $this->_user . '@localhost "cat >> ~/.ssh/authorized_keys"' . PHP_EOL
            . 'ssh ' . $this->_user . '@localhost "awk \'\!seen[\$0]++\' ~/.ssh/authorized_keys | '
            . 'cat > ~/.ssh/authorized_keys"' . PHP_EOL
            . PHP_EOL

            . 'cat ./path/to/my/global/id/file.pub | awk \'{print "#\n# "$3"\n"$0}\' | '
            . 'ssh otheruser@otherhost "cat >> ~/.ssh/authorized_keys"' . PHP_EOL
            . 'ssh otheruser@otherhost "awk \'\!seen[\$0]++\' ~/.ssh/authorized_keys | '
            . 'cat > ~/.ssh/authorized_keys"' . PHP_EOL
            . PHP_EOL

            . 'cat ~/.ssh/id_rsa.pub | awk \'{print "#\n# "$3"\n"$0}\' | '
            . 'ssh otheruser@otherhost "cat >> ~/.ssh/authorized_keys"' . PHP_EOL
            . 'ssh otheruser@otherhost "awk \'\!seen[\$0]++\' ~/.ssh/authorized_keys | '
            . 'cat > ~/.ssh/authorized_keys"' . PHP_EOL
            . PHP_EOL

            . 'cat ~/.ssh/my/some_other.pub | awk \'{print "#\n# "$3"\n"$0}\' | '
            . 'ssh otheruser@otherhost "cat >> ~/.ssh/authorized_keys"' . PHP_EOL
            . 'ssh otheruser@otherhost "awk \'\!seen[\$0]++\' ~/.ssh/authorized_keys | '
            . 'cat > ~/.ssh/authorized_keys"' . PHP_EOL
            . PHP_EOL
        ;

        $this->assertingEquals( $expectedA, $actualA );

        $this->expectingException( 'Mumsys_Service_Exception' );
        $mesg = 'Invalid "register" configuration found in host file '
            . '"localhost2" for target "host"';
        $this->expectingExceptionMessage( $mesg );

        $hostConfig = array(
            'config' => array(
                '# localhost2 for tests',
            ),
            'register' => array(
                'host' => array(
                    'key' => 'value'
                ),
            ),
        );
        $this->_object->addHostConfig( 'localhost2', $hostConfig );
        try {
            ob_start();
            $this->_object->register();
            $actualB = ob_get_clean();
        } catch ( Exception $e ) {
            $actualB = ob_get_clean();
            throw $e;
        }
        echo $actualB; // if on failure
    }


    /**
     * Test to register public key at target hosts.
     *
     * @covers Mumsys_Service_SshTool_Default::revoke
     * @covers Mumsys_Service_SshTool_Default::_revokeExecute
     * @covers Mumsys_Service_SshTool_Default::_getIdentityLocation
     *
     * @runInSeparateProcess
     */
    public function testRevokeAction()
    {
        $this->_object->init();
        ob_start();
        $this->_object->revoke();
        $actual = ob_get_clean();
        $expected = 'ssh ' . $this->_user . '@localhost "rm -f ~/.ssh/id_rsa"' . PHP_EOL

            . 'ssh ' . $this->_user . '@localhost "sed -i \'s#`cat ~/.ssh/id_rsa.pub`##\' '
            . '~/.ssh/authorized_keys ; rm -f ~/.ssh/id_rsa.pub"' . PHP_EOL

            . 'ssh ' . $this->_user . '@localhost "rm -f ~/.ssh/other_key_to_remove"' . PHP_EOL

            . 'ssh ' . $this->_user . '@localhost "sed -i \'s#`cat ~/.ssh/other_key_to_remove.pub`##\' '
            . '~/.ssh/authorized_keys ; rm -f ~/.ssh/other_key_to_remove.pub"' . PHP_EOL

            . 'ssh ' . $this->_user . '@localhost "sed -i \'s#`cat ~/.ssh/my/id_rsa.pub`##\' '
            . '~/.ssh/authorized_keys ; rm -f ~/.ssh/my/id_rsa.pub"' . PHP_EOL

            . 'ssh ' . $this->_user . '@secondhost "sed -i \'s#`cat ~/.ssh/my/id_rsa.pub`##\' '
            . '~/.ssh/authorized_keys ; rm -f ~/.ssh/my/id_rsa.pub"' . PHP_EOL
        ;

        $this->assertingEquals( $expected, $actual );
    }

}
