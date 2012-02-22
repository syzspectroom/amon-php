<?php

//create autoloader
function __autoload($class)
{
    $parts = str_replace('\\', '/', $class);
    $fileName = dirname(__FILE__) . "/../lib/" . $parts . '.php';
    echo $fileName . "\n";
    require $fileName;
}

use Amon\AmonLogger as Amon;
use Amon\Config\BaseConfig;
use Amon\Request\Request;

$request = new Request(new BaseConfig());
$amon = new Amon($request);
$amon->log('log message');
// Tagged logging
$amon->log("test", array('debug', 'benchmark'));

?>
