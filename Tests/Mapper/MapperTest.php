<?php

namespace CodeMeme\DataMapperBundle\Tests\Mapper;

use CodeMeme\DataMapperBundle\Mapper\Mapper;
use CodeMeme\DataMapperBundle\Mapper\Adapter\ArrayAdapter;
use CodeMeme\DataMapperBundle\Mapper\Adapter\ObjectAdapter;
use CodeMeme\DataMapperBundle\Tests\TestCase;

class MapperTest extends TestCase
{

    public function testInstantiation()
    {
        $this->assertEquals(0, $this->getMapper()->getAdapters()->count());
    }

    public function testNoAdaptersThrowsException()
    {
        $this->setExpectedException('CodeMeme\DataMapperBundle\Mapper\Exception');
        
        $this->getMapper()->convert(array('foo' => 'bar'));
    }

    public function testMatchingAdapterDoesNotThrowsException()
    {
        $mapper = $this->getMapper();
        $mapper->getAdapters()->add(new ArrayAdapter);
        
        $mapper->convert(array('foo' => 'bar'));
    }

    public function testSupportsReturnsFalseWithNoMatchingAdapters()
    {
        $this->assertFalse($this->getMapper()->supports('Some unsupported string'));
    }

    public function testSupportsReturnsTrueWithMatchingAdapter()
    {
        $mapper = $this->getMapper();
        $mapper->getAdapters()->add(new ArrayAdapter);
        
        $this->assertTrue($mapper->supports(array()));
    }

    public function testAddAdapters()
    {
        $mapper = $this->getMapper();
        $mapper->addAdapters(array(
            new ArrayAdapter,
            new ObjectAdapter,
        ));
        
        $this->assertEquals(2, $mapper->getAdapters()->count());
    }

    /**
     * @dataProvider testProvider
     */
    public function testConvertArrayToObject($array, $object)
    {
        $mapper = $this->getMapper();
        $mapper->addAdapters(array(
            new ArrayAdapter,
            new ObjectAdapter,
        ));
        
        $this->assertEquals($object, $mapper->convert($array, (Object) array()));
    }

    protected function getMapper()
    {
        return new Mapper;
    }

    public function testProvider()
    {
        $array = array(
            'foo' => 'bar',
            'baz' => array(
                'bing',
                'bang',
                'boom' => true,
            ),
        );
        
        $object = (Object) array(
            'foo' => 'bar',
            'baz' => (Object) array(
                'bing',
                'bang',
                'boom' => true,
            ),
        );
        
        return array(
            array($array, $object)
        );
    }

}