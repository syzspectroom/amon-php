<?php
namespace Amon\Request;

use Amon\Exception;

/**
 * Send request to Amon server
 */
class Request implements RequestInterface
{

    /**
     * @param \Amon\Config\ConfigInterface $config
     */
    public function __construct(\Amon\Config\ConfigInterface $config)
    {
        $this->_config = $config;
    }

    /**
     * Make request to server  and write log entry
     *
     * @param array  $data Data to send
     * @param string $type Log record type
     *
     * @return array Structured response array
     * @throws \Amon\Exception\NetworkErrorException
     * @throws \Amon\Exception\UnknownLogTypeException
     */
    public function doRequest(array $data, $type = 'log')
    {

        $method = 'get' . ucfirst($type) . 'Url';
        if (!method_exists($this->_config, $method)) {
            throw new Exception\UnknownLogTypeException(sprintf("Unknown log type: %s", $type));
        }
        $url = $this->_config->$method();
        $params = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
                'timeout' => 5,
            )
        );
        $context = stream_context_create($params);

        $fp = @fopen($url, 'rb', false, $context);

        $response = @stream_get_contents($fp);

        if (!$fp || $response === false) {
            throw new Exception\NetworkErrorException(sprintf("Problem sending POST to %s", $url));
        }

        return $this->_splitHeader($response);
    }

    /**
     * Split the result header from the content
     * and returns structured response array
     *
     * @param string $response
     *
     * @return array structured response array
     */
    private function _splitHeader($response)
    {
        $result = explode("\r\n\r\n", $response, 2);
        $header = isset($result[0]) ? $result[0] : '';
        $content = isset($result[1]) ? $result[1] : '';

        // return as structured array:
        return array(
            'status'  => 'ok',
            'header'  => $header,
            'content' => $content
        );
    }
}