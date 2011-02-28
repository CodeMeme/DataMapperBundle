<?php

namespace CodeMeme\DataMapperBundle\Tests\Mapper\Adapter;

use CodeMeme\DataMapperBundle\Mapper\Adapter\ArrayAdapter;
use CodeMeme\DataMapperBundle\Tests\TestCase;

class ArrayAdapterTest extends \PHPUnit_Framework_TestCase
{

    public function testSupportsArray()
    {
        $this->assertTrue($this->getAdapter()->supports(array()));
    }

    public function testDoesNotSupportObject()
    {
        $this->assertFalse($this->getAdapter()->supports(json_decode('{}')));
    }

    public function testArrayConvertsToArray()
    {
        $a = array('foo' => 'bar');
        
        $this->assertEquals($a, $this->getAdapter()->convert($a));
        $this->assertEquals($a, $this->getAdapter()->convert($a, array()));
    }

    public function testConvertMergesArrays()
    {
        $a = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );
        
        $b = array(
            'foo' => 'oof',
            'baz' => 'bing',
        );
        
        $this->assertEquals(
            $this->getAdapter()->convert($a, $b),
            array(
                'foo' => 'bar',
                'bar' => 'baz',
                'baz' => 'bing',
            )
        );
    }

    protected function getAdapter()
    {
        return new ArrayAdapter;
    }

}
