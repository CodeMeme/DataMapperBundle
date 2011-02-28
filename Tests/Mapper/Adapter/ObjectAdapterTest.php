<?php

namespace CodeMeme\DataMapperBundle\Tests\Mapper\Adapter;

use CodeMeme\DataMapperBundle\Mapper\Adapter\ObjectAdapter;
use CodeMeme\DataMapperBundle\Tests\TestCase;

class ObjectAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider testProvider
     */
    public function testObjectToArrayWithObject($array, $object)
    {
        $this->assertEquals($array, ObjectAdapter::objectToArray($object));
    }

    /**
     * @dataProvider testProvider
     */
    public function testObjectToArrayWithArray($array, $object)
    {
        $this->assertEquals($array, ObjectAdapter::objectToArray($array));
    }

    /**
     * @dataProvider testProvider
     */
    public function testObjectToArrayDeep($array, $object)
    {
        $converted = ObjectAdapter::objectToArray($object);
        $this->assertType(\PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $converted['baz']);
    }

    /**
     * @dataProvider testProvider
     */
    public function testArrayToObject($array, $object)
    {
        $this->assertEquals($object, ObjectAdapter::arrayToObject($array));
    }

    /**
     * @dataProvider testProvider
     */
    public function testSupportsStdClass($array, $object)
    {
        $this->assertTrue($this->getAdapter()->supports($object));
    }

    public function testDoesNotSupportArray()
    {
        $this->assertFalse($this->getAdapter()->supports(array()));
    }

    public function testDoesNotSupportClass()
    {
        $c = new \SimpleXMLElement('<xml />');
        
        $this->assertFalse($this->getAdapter()->supports($c));
    }

    /**
     * @dataProvider testProvider
     */
    public function testConvertFrom($array, $object)
    {
        $this->assertEquals($array, $this->getAdapter()->convertFrom($object));
    }

    /**
     * @dataProvider testProvider
     */
    public function testConvertTo($array, $object)
    {
        $o = (Object) array();
        
        $this->assertEquals($object, $this->getAdapter()->convertTo($o, $array));
    }

    /**
     * @dataProvider testProvider
     */
    public function testConvertReturnsAnArray($array, $object)
    {
        $this->assertEquals($array, $this->getAdapter()->convert($object));
    }

    /**
     * @dataProvider testProvider
     */
    public function testConvertMergesObjects($array, $object)
    {
        $o = (Object) array(
            'foo' => 'oof',
            'bar' => 'rab',
        );
        
        $converted = $this->getAdapter()->convert($object, $o);
        $this->assertEquals('bar', $converted->foo);
        $this->assertEquals('rab', $converted->bar);
    }

    protected function getAdapter()
    {
        return new ObjectAdapter;
    }

    protected function getArray()
    {
        return array(
            'foo' => 'bar',
            'baz' => array(
                'bing',
                'bang',
                'boom' => array(5, 4, 3, 2),
                '1' => array(
                    '2',
                    3,
                    4,
                    'five',
                ),
            ),
        );
    }

    protected function getObject()
    {
        return (Object) array(
            'foo' => 'bar',
            'baz' => (Object) array(
                'bing',
                'bang',
                'boom' => (Object) array(5, 4, 3, 2),
                '1' => (Object) array(
                    '2',
                    3,
                    4,
                    'five',
                ),
            ),
        );
    }

    public function testProvider()
    {
        return array(
            array($this->getArray(), $this->getObject()),
        );
    }

}
