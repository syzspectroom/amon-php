<?php
namespace Amon\Request;

use Amon\Exception;

/**
 *
 */
class MockRequest implements RequestInterface
{

    private $_requiredLogFields = array('message', 'tags');
    private $_requiredExceptionFields = array('message', 'tags');

    /**
     * Send mock request
     *
     * @param array  $data
     * @param string $type
     *
     * @return bool
     */
    function doRequest(array $data, $type = 'log')
    {
        return $this->_checkLogData($data, $type);
    }

    /**
     * Check input parameters and return true if they are good
     * and false if they fails
     *
     * @param array  $data Data
     * @param string $type Log type
     *
     * @return bool
     */
    private function _checkLogData(array $data, $type)
    {
        $variable = '_required' . ucfirst($type) . 'Fields';
        $requiredFields = $this->$variable;
        //if input data fields count not 2 return false
        if (count(array_keys($data)) !== count($requiredFields)) {
            return false;
        }
        //next check if all methods exist in data
        foreach ($requiredFields as $requiredField) {
            if (!array_key_exists($requiredField, $data)) {
                return false;
            }
        }

        return true;
    }
}
