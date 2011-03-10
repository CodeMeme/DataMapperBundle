<?php

namespace CodeMeme\DataMapperBundle\Mapper;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

use CodeMeme\DataMapperBundle\Mapper\Adapter\AdapterInterface;

class Mapper extends ContainerAware implements AdapterInterface
{

    protected $adapters;

    protected $normalizer;

    protected $map;

    public function __construct(ContainerInterface $container = null, $adapters = array(), $map = array())
    {
        $this->container    = $container;
        $this->adapters     = new ArrayCollection;
        $this->map          = new ArrayCollection;
        
        $this->setAdapters($adapters);
        $this->setMap($map);
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
        
        $this->converted = $this->normalize($this->converted);
        
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
        $lifo = array_reverse($this->getAdapters()->toArray());
        
        foreach ($lifo as $adapter) {
            if ($adapter->supports($from)) {
                return $adapter->convertFrom($from);
            }
        }
    }

    public function convertTo($to, Array $normalized)
    {
        $lifo = array_reverse($this->getAdapters()->toArray());
        
        foreach ($lifo as $adapter) {
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
        $this->getAdapters()->add(
            (is_string($adapter)) ? new $adapter($this->container) : $adapter
        );
        
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
        $this->getAdapters()->clear();
        
        $this->addAdapters($adapters);
        
        return $this;
    }

    public function getMap()
    {
        return $this->map;
    }

    public function setMap($map)
    {
        $this->getMap()->clear();
        
        foreach ($map as $key => $value) {
            $this->getMap()->set($key, $value);
        }
        
        return $this;
    }

    public function denormalize(Array $data)
    {
        $denormalized = array();
        
        foreach ($data as $key => $value) {
            if ($newKey = $this->getMap()->indexOf($key)) {
                $denormalized[$newKey] = $value;
            } else {
                $denormalized[$key] = $value;
            }
        }
        
        return $denormalized;
    }

    public function hasMap()
    {
        return !$this->getMap()->isEmpty();
    }

    public function normalize(Array $data)
    {
        $normalized = array();
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Ensure we recursively denormalize the value before proceeding
                $value = $this->normalize($value);
            }
            
            if ($this->getMap()->containsKey($key)) {
                $mapped = $this->getMap()->get($key);
                
                if ($mapped instanceof Reference) {
                    $mapped = $this->getContainer()->get($mapped);
                }
                
                if ($mapped instanceof Mapper) {
                    // Key isn't re-assigned, but an object that needs to be mapped on it's own
                    $value = is_array($value)
                           ? $mapped->normalize($value)
                           : null;
                } else {
                    // Re-assign key to normalized key
                    $key = $mapped;
                }
                
            } else if ($this->getMap()->contains($key)) {
                // No need to re-assign key
            }
            
            // Assign the value to the normalized key
            $normalized[$key] = $value;
        }
        
        return $normalized;
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
