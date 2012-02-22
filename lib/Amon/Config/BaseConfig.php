<?php
namespace Amon\Config;
use Amon\Exception;

/**
 * Configuration class for basic Amon application
 */
class BaseConfig implements ConfigInterface
{
    /**
     * Default amon config file
     *
     * @var string
     */
    private $_configFile = '/etc/amon.conf';

    private $_defaultConfig = array(
        'host' => 'http://127.0.0.1',
        'port' => 2464,
    );
    private $_logUrl = '/api/log';
    private $_exceptionUrl = '/api/exception';
    private $_config;

    /**
     * Amon server url generated from config
     *
     * @var string
     */
    private $_url;

    /**
     * If $config parameter is not set, config will be read from amon config file
     *
     * @param array $config Optional server parameters
     */
    public function __construct($config = null)
    {
        if (!is_null($config)) {
            //first check user config format
            $this->_checkConfig($config);

            $this->_config = $this->_createConfig($config);
        }
        else {
            $this->_config = $this->_readConfigFile();
        }
    }

    /**
     * Return url to send logs to
     *
     * @return string Log url
     */
    public function getLogUrl()
    {
        $logUrl = $this->_url . $this->_logUrl;

        return $logUrl;
    }

    /**
     * Return url to send excetions to
     *
     * @return string Exception url
     */
    public function getExceptionUrl()
    {
        $exceptionUrl = $this->_url . $this->_exceptionUrl;

        return $exceptionUrl;
    }

    /**
     * Read configuration from amon config file
     *
     * @return array Configuration array
     * @throws \Amon\Exception\ConfigFileNotFoundException Throws exception if config file not found
     */
    private function _readConfigFile()
    {
        //suppress warning
        $amonConf = @file_get_contents($this->_configFile);

        //throw error if file not found
        if (false === $amonConf) {
            throw new Exception\ConfigFileNotFoundException(sprintf('configuration file not found in %s',
                                                                    $this->_configFile));
        }
        $toJson = json_decode($amonConf);
        return $this->_createConfig(array(
                                        'host' => $toJson->web_app->host,
                                        'port' => $toJson->web_app->port,
                                    ));
    }

    /**
     * Check input config array and raplace missed config params with default
     *
     * @param array $config Config array
     *
     * @return array Checked config array
     */
    private function _createConfig(array $config)
    {
        $result['host'] = (isset($config['host'])) ? $this->_checkHost($config['host']) : $this->_defaultConfig['host'];
        $result['port'] = (isset($config['port'])) ? $config['port'] : $this->_defaultConfig['port'];
        $this->_url = sprintf("%s:%d", $result['host'], $result['port']);

        return $result;
    }

    /**
     * Check user config and throw exception if user gives wrong parameter
     *
     * @param array $config
     *
     * @throws \Amon\Exception\UnknownParameterException
     */
    private function _checkConfig(array $config)
    {
        //get all config parameters from user
        $UserKeys = array_keys($config);
        foreach ($UserKeys as $key) {
            if (!in_array($key, array_keys($this->_defaultConfig))) {
                throw new Exception\UnknownParameterException(sprintf('Wrong parameter %s', $key));
            }
        }
    }

    /**
     * Add http if necessary
     *
     * @param string $host Host
     *
     * @return string
     */
    private function _checkHost($host)
    {
        if (substr($host, 0, 7) != 'http://') {
            $host = sprintf("http://%s", $host);
        }

        return $host;
    }
}