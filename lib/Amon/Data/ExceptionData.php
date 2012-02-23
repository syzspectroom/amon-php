<?php
namespace Amon\Data;
/**
 * Exception parse class
 */
class ExceptionData
{
    /**
     * @var \Exception
     */
    private $_exception;

    /**
     * @var array
     */
    private $_data;

    /**
     * @param \Exception $exception
     *
     * @return array
     */
    function __construct(\Exception $exception)
    {
        $this->_exception = $exception;

        $this->_data = $this->_fillData();

        return $this->getdata();
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * return parsed Exception data as array
     *
     * @return array Exception data
     */
    private function _fillData()
    {
        $error_class = get_class($this->_exception);

        return array(
            'exception_class' => $error_class,
            'message'         => $this->_exception->getMessage(),
            'backtrace'       => $this->_createTrace(),
            'data'            => $this->_createRequestData()
        );
    }

    /**
     * Convert exception trace to array
     *
     * @return array Trace
     */
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

    /**
     * Create array from request params
     *
     * @return array Request data
     */
    private function _createRequestData()
    {
        $data = array();

        if (isset($_SERVER["HTTP_HOST"])) {

            // request data
            $session = isset($_SESSION) ? $_SESSION : array();

            // sanitize headers
            $headers = $this->_getallheaders();

            if (isset($headers["Cookie"])) {
                $sessionKey = preg_quote(ini_get("session.name"), "/");
                $headers["Cookie"] = preg_replace("/$sessionKey=\S+/", "$sessionKey=[FILTERED]", $headers["Cookie"]);
            }

            $server = $_SERVER;
            $this->_fillLKeys($server);

            $protocol = (false !== $server["HTTPS"] && $server["HTTPS"] != "off") ? "https://" : "http://";
            $url = (false !== $server["HTTP_HOST"]) ? sprintf("%s%s%s", $protocol, $server['HTTP_HOST'], $server['REQUEST_URI']) : "";

            $data["request"] = array(
                "url"            => $url,
                "request_method" => strtolower($server["REQUEST_METHOD"]),
                "session"        => $session,
                "cookie"         => $headers['Cokie']
            );

            $params = array_merge($_GET, $_POST);

            if (!empty($params)) {
                $data["request"]["parameters"] = $params;
            }
        }

        return $data;
    }

    /**
     * Fill missed $_SERVER array keys with FALSE
     *
     * @param $arr $_SERVER array
     */
    private function _fillKeys(&$arr)
    {
        $keys = array("HTTPS", "HTTP_HOST", "REQUEST_URI", "REQUEST_METHOD", "REMOTE_ADDR");

        foreach ($keys as $key)
        {
            if (!isset($arr[$key])) {
                $arr[$key] = false;
            }
        }
    }

    /**
     * Create getallheaders function if it's not exist
     *
     * @return array all the HTTP headers in the current request
     */
    private function _getallheaders()
    {
        // sanitize headers
        if (function_exists("getallheaders")) {
            return getallheaders();
        }
        else {
            $headers = array();
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == "HTTP_") {
                    $headers[str_replace(" ", "-", ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        }
    }
}
