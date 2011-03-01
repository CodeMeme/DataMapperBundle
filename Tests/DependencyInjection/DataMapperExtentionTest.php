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

    public function testNormalizersLoaded()
    {
        $loader = new XmlFileLoader($this->container, __DIR__.'/../Fixtures');
        $loader->load('mappers.xml');
        
        $this->assertTrue($this->container->has('datamapper.post_normalizer'));
        
        $class = $this->container->getParameter('datamapper.normalizer.class');
        
        $this->assertEquals($class, get_class($this->container->get('datamapper.post_normalizer')));
        $this->assertEquals($class, get_class($this->container->get('datamapper.author_normalizer')));
        $this->assertEquals($class, get_class($this->container->get('datamapper.comment_normalizer')));
        
        $class = $this->container->getParameter('datamapper.mapper.class');
        
        $this->assertEquals($class, get_class($this->container->get('datamapper.post_mapper')));
        $this->assertEquals($class, get_class($this->container->get('datamapper.author_mapper')));
        $this->assertEquals($class, get_class($this->container->get('datamapper.comment_mapper')));
    }

}