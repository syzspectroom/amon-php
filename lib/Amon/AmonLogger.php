<?php
namespace Amon;

/**
 * Logging class
 */
class AmonLogger implements Interfaces\LoggerInterface
{
    /**
     * @var Request
     */
    private $_request;

    /**
     * @param Request\RequestInterface $request
     */
    public function __construct(Request\RequestInterface $request)
    {
        $this->_request = $request;
    }


    /**
     * Prepare log message for sending and send to Amon server using Request class
     *
     * @param string       $message Log message
     * @param string|array $tags    Log tags
     *
     * @return void
     */
    public function log($message, $tags = null)
    {
       return $this->_request->doRequest($this->_createMessage($message, $tags));
    }

    /**
     * Format message for sending
     *
     * @param string       $message Log message
     * @param string|array $tags    Log tags
     *
     * @return array formatted message
     */
    private function _createMessage($message, $tags)
    {
        return $data = array(
            'message' => $message,
            'tags'    => $tags
        );
    }
}
