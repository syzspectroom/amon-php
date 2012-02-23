<?php
namespace Amon;

class AmonExceptionLogger implements  Interfaces\LoggerInterface
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

}
