<?php
namespace Amon\Request;

/**
 *
 */
interface RequestInterface
{
    /**
     * Send request
     *
     * @abstract
     *
     * @param array  $data
     * @param string $type
     */
    function doRequest(array $data, $type = null);
}
