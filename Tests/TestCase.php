<?php

namespace CodeMeme\DataMapperBundle\Tests;

use CodeMeme\DataMapperBundle\DependencyInjection\DataMapperExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->container = $this->getContainer();
        $this->extension = new DataMapperExtension;
        
        $loader = new XmlFileLoader($this->container, __DIR__.'/Fixtures');
        $loader->load('mappers.xml');
        
        $this->extension->configLoad(array(), $this->container);
    }

    public function tearDown()
    {
        unset($this->container, $this->extension);
    }

    protected function getContainer()
    {
        return new ContainerBuilder(new ParameterBag(array(
            'kernel.bundles'          => array('DataMapperBundle' => 'CodeMeme\\DataMapperBundle\\DataMapperBundle'),
            'kernel.root_dir'         => __DIR__,
            'kernel.debug'            => false,
            'kernel.compiled_classes' => array(),
        )));
    }

}