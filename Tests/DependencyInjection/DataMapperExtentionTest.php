<?php

namespace CodeMeme\DataMapperBundle\Tests\DependencyInjection;

use CodeMeme\DataMapperBundle\Tests\TestCase;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

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

    public function testMapsLoaded()
    {
        $loader = new XmlFileLoader($this->container, __DIR__.'/../Fixtures');
        $loader->load('mappers.xml');
        
        $this->assertTrue($this->container->hasParameter('datamapper.post_map'), "Could not find datamapper.post_map");
        $this->assertTrue($this->container->hasParameter('datamapper.author_map'), "Could not find datamapper.author_map");
        $this->assertTrue($this->container->hasParameter('datamapper.comment_map'), "Could not find datamapper.comment_map");
    }

}