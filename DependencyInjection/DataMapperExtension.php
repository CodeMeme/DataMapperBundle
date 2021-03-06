<?php

namespace CodeMeme\DataMapperBundle\DependencyInjection;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\FileLocator;

class DataMapperExtension extends Extension
{

    public function load(Array $configs, ContainerBuilder $container)
    {
        if (! $container->has('datamapper')) {
            $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('datamapper.xml');
            
            $container->setAlias('datamapper', 'datamapper.default_mapper');
        }
        
        foreach ($configs as $config) {
            $this->doConfigLoad($config, $container);
        }
    }

    public function doConfigLoad($config, ContainerBuilder $container)
    {
        if (isset($config['mappers'])) {
            $this->loadMappers($config['mappers'], $container);
        }
    }

    protected function loadMappers($mappers, ContainerBuilder $container)
    {
        foreach ($mappers as $name => $mapper) {
            $mapperClass = isset($mapper['class'])
                         ? $mapper['class']
                         : $container->getParameter('datamapper.mapper.class');
            
            $mapperDef = new Definition('CodeMeme\DataMapperBundle\Mapper\Mapper');
            
            if (isset($mapper['fields'])) {
                $fields = $this->loadFields($mapper['fields']);
                
                $mapperDef->addMethodCall('setFields', array($fields));
            }
            
            $container->setDefinition(
                sprintf('datamapper.%s_mapper', $name),
                $mapperDef
            );
        }
    }

    protected function loadFields($fields)
    {
        foreach ($fields as $field => $reference) {
            if (is_string($reference) && 0 === strpos($reference, '@')) {
                $fields[$field] = $this->resolveReference($reference);
            } else if (is_array($reference)) {
                $fields[$field] = $this->loadFields($reference);
            }
        }
        
        return $fields;
    }

    protected function resolveReference($reference)
    {
        return new Reference(substr($reference, 1));
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://www.codememe.org/schema/dic/datamapper';
    }

}