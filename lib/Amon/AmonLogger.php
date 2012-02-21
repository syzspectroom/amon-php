<?php
//require_once dirname(__FILE__)."/lib/config.php";
//require_once dirname(__FILE__)."/lib/data.php";
//require_once dirname(__FILE__)."/lib/errors.php";
//require_once dirname(__FILE__)."/lib/remote.php";

namespace Amon;
use Amon\AmonLibs\Config as AmonConfig;
use Amon\AmonLibs\Data as AmonData;
use Amon\AmonLibs\Remote as AmonRemote;
use Amon\AmonLibs\Errors;

class AmonLogger
{

    static $exceptions;

    static $previous_exception_handler;
    static $previous_error_handler;

    static $controller;
    static $action;
    static $config_array;

    /**
     *  Overwrite the default configuration
     *  AmonLogger::config(array('port', 'host', 'application_key'))
     *
     * @static
     *
     * @param array $array config
     */
    public static function config($array)
    {
        self::$config_array = (object)$array;
        // Construct the url
        self::$config_array->url = sprintf("%s:%d",
                                           self::$config_array->host,
                                           self::$config_array->port);
    }

    /**
     * Check for the config array or default to /etc/lib.conf
     *
     * @return \Amon\AmonLibs\Config
     */
    private function _get_config_object()
    {
        if (empty(self::$config_array)) {
            $config = new AmonConfig();
        }
        else
        {
            $config = self::$config_array;
        }

        return $config;
    }

    /**
     * Write log message
     *
     * @static
     *
     * @param string       $message Log message
     * @param string|array $tags    Additional tag
     */
    public static function log($message, $tags = '')
    {
        $data = array(
            'message' => $message,
            'tags'    => $tags
        );
        $config = self::_get_config_object();
        $log_url = sprintf("%s/api/log", $config->url);
        if ($config->application_key) {
            $log_url = sprintf("%s/%s", $log_url, $config->application_key);
        }

        AmonRemote::request($log_url, $data);
    }

    static function setup_exception_handler()
    {

        self::$exceptions = array();
        self::$action = "";
        self::$controller = "";

        // set exception handler & keep old exception handler around
        self::$previous_exception_handler = set_exception_handler(
            array("Amon\\AmonLogger", "handle_exception")
        );

        self::$previous_error_handler = set_error_handler(
            array("Amon\\AmonLogger", "handle_error")
        );

        register_shutdown_function(
            array("Amon\\AmonLogger", "shutdown")
        );
    }


    static function shutdown()
    {
        if ($e = error_get_last()) {
            self::handle_error($e["type"], $e["message"], $e["file"], $e["line"]);
        }
    }

    static function handle_error($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return;
        }

        switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
                $ex = new Errors\PhpNotice($errstr, $errno, $errfile, $errline);
                break;

            case E_WARNING:
            case E_USER_WARNING:
                $ex = new Errors\PhpWarning($errstr, $errno, $errfile, $errline);
                break;

            case E_STRICT:
                $ex = new Errors\PhpStrict($errstr, $errno, $errfile, $errline);
                break;

            case E_PARSE:
                $ex = new Errors\PhpParse($errstr, $errno, $errfile, $errline);
                break;

            default:
                $ex = new Errors\PhpError($errstr, $errno, $errfile, $errline);
        }

        self::handle_exception($ex, false);

        if (self::$previous_error_handler) {
            call_user_func(self::$previous_error_handler, $errno, $errstr, $errfile, $errline);
        }
    }

    /**
     * Exception handle class. Pushes the current exception onto the exception
     * stack and calls the previous handler, if it exists. Ensures seamless
     * integration.
     *
     * @static
     *
     * @param \Exception $exception
     * @param bool       $call_previous
     */
    static function handle_exception($exception, $call_previous = true)
    {
        $config = self::_get_config_object();
        $exception_url = sprintf("%s/api/exception", $config->url);
        if ($config->application_key) {
            $exception_url = sprintf("%s/%s", $exception_url, $config->application_key);
        }

        self::$exceptions[] = $exception;

        $data = new AmonData($exception);
        AmonRemote::request($exception_url, $data->data);

        // if there's a previous exception handler, we call that as well
        if ($call_previous && self::$previous_exception_handler) {
            call_user_func(self::$previous_exception_handler, $exception);
        }
    }

}


