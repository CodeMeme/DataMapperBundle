<?php

namespace CodeMeme\DataMapperBundle\Mapper;

use CollegeDegrees\EdudirectBundle\Mapper\Adapter\AdapterInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Mapper extends ContainerAware implements AdapterInterface
{

    protected $adapters;

    public function __construct(ContainerInterface $container = null, $adapters = array())
    {
        $this->adapters = new ArrayCollection;
        
        $this->setContainer($container);
        $this->setAdapters($adapters);
    }

    public function convert($from, $to = null)
    {
        if ($this->supports($from)) {
            $this->converted = $this->convertFrom($from);
        } else {
            throw new Exception(sprintf(
                'No adapter supports converting from %s to %s',
                is_object($from) ? get_class($from) : gettype($from),
                print_r($from, true)
            ));
        }
        
        if (null === $to) {
            return $this->converted;
        }
        
        if ($this->supports($to)) {
            return $this->convertTo($to, $this->converted);
        } else {
            throw new Exception(sprintf(
                'No adapter supports converting %s to %s',
                is_object($from) ? get_class($from) : gettype($from),
                is_object($to) ? get_class($to) : gettype($to)
            ));
        }
    }

    public function convertFrom($from)
    {
        foreach ($this->getAdapters() as $adapter) {
            if ($adapter->supports($from)) {
                return $adapter->convertFrom($from);
            }
        }
    }

    public function convertTo($to, Array $normalized)
    {
        foreach ($this->getAdapters() as $adapter) {
            if ($adapter->supports($to)) {
                return $adapter->convertTo($to, $normalized);
            }
        }
    }

    public function getContainer()
    {
        if (null === $this->container) {
            throw new Exception('Container is missing');
        }
        
        return $this->container;
    }

    public function getAdapters()
    {
        return $this->adapters;
    }

    public function addAdapter($adapter)
    {
        $this->getAdapters()->add($adapter);
        
        return $this;
    }

    public function addAdapters($adapters)
    {
        foreach ($adapters as $adapter) {
            $this->addAdapter($adapter);
        }
        
        return $this;
    }

    public function setAdapters($adapters)
    {
        $container = $this->container;
        
        $adapters = array_map(function($adapter) use ($container) {
            return (is_string($adapter)) ? new $adapter(($container)) : $adapter;
        }, $adapters);
        
        $this->addAdapters($adapters);
        
        return $this;
    }

    public function supports($object)
    {
        foreach ($this->getAdapters() as $adapter) {
            if ($adapter->supports($object)) {
                return true;
            }
        }
        
        return false;
    }

}
