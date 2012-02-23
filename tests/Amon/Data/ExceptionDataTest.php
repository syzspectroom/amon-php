<?php
namespace Amon\Data;

/**
 * Test class for ExceptionData.
 * Generated by PHPUnit on 2012-02-24 at 00:32:26.
 */
class ExceptionDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExceptionData
     */
    protected $_object;
    private $_message;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {

        $this->_message= 'test';
        $this->_object = new ExceptionData(new \Exception($this->_message));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    /**
     * @covers Amon\Data\ExceptionData::__construct
     * @covers Amon\Data\ExceptionData::getData
     */
    public function testResultIsArray()
    {
        $this->isTrue(is_array($this->_object->getData()));
    }

    /**
     * @covers Amon\Data\ExceptionData::_fillData
     * @covers Amon\Data\ExceptionData::getData
     */
    public function testResultHasMessage()
    {
        $this->assertArrayHasKey('message', $this->_object->getData());
    }

    /**
     * @covers Amon\Data\ExceptionData::_fillData
     */
    public function testResultMessageEquals()
    {
        $result = $this->_object->getData();
        $this->assertEquals($this->_message, $result['message'] );
    }

}
