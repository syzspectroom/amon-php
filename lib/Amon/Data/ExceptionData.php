<?php

class ExceptionData
{
    private $_exception;
    private $_data;

    function __construct(\Exception $exception)
    {
        $this->_exception = $exception;
    }

    private function _fillData()
    {
        // spoof 404 error
        $error_class = get_class($this->exception);
        if ($error_class == "Http404Error") {
            $error_class = "ActionController::UnknownAction";
        }

        $this->_data = array(
            'exception_class' => $error_class,
            'message'         => $this->_exception->getMessage(),
            'backtrace'       => $this->_createTrace()
        );

    }

    private function _createTrace()
    {
        $createdTrace = array();

        $trace = $this->_exception->getTrace();
        foreach ($trace as $t) {
            if (!isset($t["file"])) {
                continue;
            }
            $createdTrace[] = sprintf("%s:%d:in '%s'", $t['file'], $t['line'], $t['function']);
        }

        return $createdTrace;
    }

    private function _createData()
    {
        if (isset($_SERVER["HTTP_HOST"])) {

            // request data
            $session = isset($_SESSION) ? $_SESSION : array();

            // sanitize headers
            if (!function_exists("getallheaders")) {
                $headers = $this->_getallheaders();
            }
            else {
                $headers = getallheaders();
            }

            if (isset($headers["Cookie"])) {
                $sessionKey = preg_quote(ini_get("session.name"), "/");
                $headers["Cookie"] = preg_replace("/$sessionKey=\S+/", "$sessionKey=[FILTERED]", $headers["Cookie"]);
            }

            $server = $_SERVER;
            $keys = array("HTTPS", "HTTP_HOST", "REQUEST_URI", "REQUEST_METHOD", "REMOTE_ADDR");
            $this->fill_keys($server, $keys);


            $protocol = $server["HTTPS"] && $server["HTTPS"] != "off" ? "https://" : "http://";
            $url = $server["HTTP_HOST"] ? "$protocol$server[HTTP_HOST]$server[REQUEST_URI]" : "";

            $data['data']["request"] = array(
                "url"            => $url,
                "request_method" => strtolower($server["REQUEST_METHOD"]),
                "session"        => $session
            );

            if (!empty(Amon::$controller) && !empty(Amon::$action)) {
                $data["request"]["controller"] = Amon::$controller;
                $data["request"]["action"] = Amon::$action;
            }

            $params = array_merge($_GET, $_POST);

            if (!empty($params)) {
                $data["request"]["parameters"] = $params;
            }
        }

        $this->data = (array)$data;
    }

    private function _fill_keys(&$arr, $keys)
    {
        foreach ($keys as $key)
        {
            if (!isset($arr[$key])) {
                $arr[$key] = false;
            }
        }
    }

    private function _getallheaders()
    {
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == "HTTP_") {
                $headers[str_replace(" ", "-", ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
