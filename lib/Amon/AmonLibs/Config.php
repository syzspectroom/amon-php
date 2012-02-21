<?php

namespace Amon\AmonLibs;

class Config
{
    private $configFile = '/etc/amon.conf';

    function __construct()
    {
        $amon_conf = file_get_contents($this->configFile);
        $to_json = json_decode($amon_conf);
        $this->host = ($to_json->web_app->host == NULL) ? 'http://127.0.0.1' : $to_json->web_app->host;

        // Check if the host is IP address and add http if necessary
        if (substr($this->host, 0, 7) != 'http://') {
            $this->host = sprintf("http://%s", $this->host);
        }

        $this->port = ($to_json->web_app->port == NULL) ? 2464 : $to_json->web_app->port;

        $this->url = sprintf("%s:%d", $this->host, $this->port);
    }

}
