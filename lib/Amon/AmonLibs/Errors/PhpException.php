<?
namespace Amon\AmonLibs\Errors;

class PhpException extends \ErrorException
{
    function __construct($errstr, $errno, $errfile, $errline)
    {
        parent::__construct($errstr, 0, $errno, $errfile, $errline);
    }
}