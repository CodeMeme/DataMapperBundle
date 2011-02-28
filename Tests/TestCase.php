<?php

namespace CodeMeme\DataMapperBundle\Tests;

use CodeMeme\DataMapperBundle\DependencyInjection\DataMapperExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Yaml\Yaml;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->container = $this->getContainer();
        $this->loader = new DataMapperExtension;
        
        $config = Yaml::load(__DIR__.'/Fixtures/mappers.yml');
        $this->loader->configLoad($config, $this->container);
    }

    public function tearDown()
    {
        unset($this->container, $this->loader);
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