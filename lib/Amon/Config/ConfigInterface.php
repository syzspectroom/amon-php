<?php
namespace Amon\Config;

/**
 * Interface for configuration
 */
interface ConfigInterface
{
    /**
     * Return url to send logs to
     *
     * @return string The formatted url
     */
    function getLogUrl();

    /**
     * Return url to send exceptions logs to
     *
     * @return string The formatted url
     */
    function getExceptionUrl();
}