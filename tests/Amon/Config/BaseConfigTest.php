<?php
namespace Amon\Config;

/**
 * Test class for BaseConfig.
 * Generated by PHPUnit on 2012-02-22 at 16:11:01.
 */
class BaseConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BaseConfig
     */
    protected $configClass;
    protected $customConfigClass;
    protected $inputConfig;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->configClass = new BaseConfig();
        $this->inputConfig = array('host'=> '255.255.255.1',
                                   'port'=> 12345);
        $this->customConfigClass = new BaseConfig($this->inputConfig);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Amon\Config\BaseConfig::__construct
     */
    public function testClassConstruct()
    {
        $this->assertInstanceOf('Amon\\Config\\ConfigInterface',$this->configClass);
    }

    /**
     * passing wrong parameter to config class
     * exception must be thrown
     *
     * @covers Amon\Config\BaseConfig::__construct
     * @covers Amon\Config\BaseConfig::_checkConfig
     */
    public function testWrongConfigConstruct()
    {
        $wrongConfig = array('host' => '255.255.255.1',
                             'port' => 12345,
                             'wrong'=> 'wrong');

        $this->setExpectedException('Amon\\Exception\\UnknownParameterException');
        new BaseConfig($wrongConfig);
    }

    /**
     * Pass host parameter without http prefix
     * http prefix must be added
     *
     * @covers Amon\Config\BaseConfig::_checkHost
     * @covers Amon\Config\BaseConfig::_createConfig
     */
    public function testAddHttpToHost()
    {
        $this->assertContains('http://', $this->customConfigClass->getLogUrl());
    }

    /**
     * @covers Amon\Config\BaseConfig::getLogUrl
     * @covers Amon\Config\BaseConfig::_createConfig
     */
    public function testCustomConfigGetLogUrl()
    {
        $url = sprintf("http://%s:%d/api/log", $this->inputConfig['host'], $this->inputConfig['port']);
        $this->assertEquals($url, $this->customConfigClass->getLogUrl());
    }

    /**
     * @covers Amon\Config\BaseConfig::getExceptionUrl
     * @covers Amon\Config\BaseConfig::_readConfigFile
     */
    public function testCustomConfigGetExceptionUrl()
    {
        $url = sprintf("http://%s:%d/api/exception", $this->inputConfig['host'], $this->inputConfig['port']);
        $this->assertEquals($url, $this->customConfigClass->getExceptionUrl());
    }

    /**
     * @covers Amon\Config\BaseConfig::getLogUrl
     * @covers Amon\Config\BaseConfig::_readConfigFile
     * @covers Amon\Config\BaseConfig::_createConfig
     */
    public function testGetLogUrl()
    {
        $this->assertContains('/api/log', $this->configClass->getLogUrl());
    }

    /**
     * @covers Amon\Config\BaseConfig::getExceptionUrl
     * @covers Amon\Config\BaseConfig::_readConfigFile
     * @covers Amon\Config\BaseConfig::_createConfig
     */
    public function testGetExceptionUrl()
    {
        $this->assertContains('/api/exception', $this->configClass->getExceptionUrl());
    }
}