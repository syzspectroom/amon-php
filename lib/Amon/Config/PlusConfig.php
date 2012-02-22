<?php

namespace Amon\Config;
use Amon\Exception;

/**
 * Configuration class for Amon Plus application
 */
class PlusConfig implements ConfigInterface
{

    public function __construct($config = null)
    {
        // TODO: Implement __construct method.
    }

    /**
     * Return url to send logs to
     *
     * @return string The formatted url
     */
    function getLogUrl()
    {
        // TODO: Implement getLogUrl() method.
    }

    /**
     * Return url to send exceptions logs to
     *
     * @return string The formatted url
     */
    function getExceptionUrl()
    {
        // TODO: Implement getExceptionUrl() method.
    }}