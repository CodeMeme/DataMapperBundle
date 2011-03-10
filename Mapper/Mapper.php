<?php

namespace CodeMeme\DataMapperBundle\Mapper;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

use CodeMeme\DataMapperBundle\Mapper\Adapter\AdapterInterface;
use CodeMeme\DataMapperBundle\Normalizer\NormalizerInterface;

class Mapper extends ContainerAware implements AdapterInterface, NormalizerInterface
{

    protected $adapters;

    protected $normalizer;

    public function __construct(ContainerInterface $container = null, $adapters = array(), $normalizer = null)
    {
        $this->adapters     = new ArrayCollection;
        
        $this->setContainer($container);
        $this->addAdapters($adapters);
        $this->setNormalizer($normalizer);
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
        
        if ($this->hasNormalizer()) {
            $this->converted = $this->normalize($this->converted);
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
        $this->adapters = $adapters;
        
        return $this;
    }

    public function getNormalizer()
    {
        return $this->normalizer;
    }

    public function setNormalizer($normalizer)
    {
        $this->normalizer = $normalizer;
        
        return $this;
    }

    public function denormalize(Array $data)
    {
        return $this->getNormalizer()->denormalize($data);
    }

    public function hasNormalizer()
    {
        return !!$this->getNormalizer();
    }

    public function normalize(Array $data)
    {
        return $this->getNormalizer()->normalize($data);
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
