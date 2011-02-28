<?php

namespace CodeMeme\DataMapperBundle\Tests\DependencyInjection;

use CodeMeme\DataMapperBundle\Tests\TestCase;

class DataMapperExtensionTest extends TestCase
{

    public function testInstantiation()
    {
        $this->assertEquals('CodeMeme\\DataMapperBundle\\Mapper\\Mapper', $this->container->getParameter('datamapper.mapper.class'));
    }

    public function testDefaultMapperExists()
    {
        $this->assertTrue($this->container->has('datamapper.default_mapper'));
    }

    public function testDefaultMapperAlias()
    {
        $this->assertTrue($this->container->has('datamapper'));
        
        $this->assertEquals(
            $this->container->get('datamapper'),
            $this->container->get('datamapper.default_mapper')
        );
    }

    public function testAdaptersLoaded()
    {
        $this->assertEquals(3, $this->container->get('datamapper')->getAdapters()->count());
    }

}