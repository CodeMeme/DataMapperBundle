<?php

namespace CodeMeme\DataMapperBundle\Mapper;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

use CodeMeme\DataMapperBundle\Mapper\Adapter\AdapterInterface;
use CodeMeme\DataMapperBundle\Normalizer\NormalizerInterface;

class Mapper extends ContainerAware implements AdapterInterface, NormalizerInterface
{

    CONST NORMALIZE = 1;

    CONST DENORMALIZE = -1;

    CONST NONE = 0;

    protected $adapters;

    protected $normalizers;

    public function __construct(ContainerInterface $container = null, $adapters = array(), $normalizers = array())
    {
        $this->adapters     = new ArrayCollection;
        $this->normalizers  = new ArrayCollection;
        
        $this->setContainer($container);
        $this->addAdapters($adapters);
        $this->addNormalizers($normalizers);
    }

    public function convert($from, $to = null, $processing = self::NONE)
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
        
        switch ($processing) {
            case self::NORMALIZE;
                $this->converted = $this->normalize($this->converted);
                break;
            
            case self::DENORMALIZE;
                $this->converted = $this->denormalize($this->converted);
                break;
            
            default:
                break;
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

    public function normalize(Array $data)
    {
        foreach ($this->getNormalizers() as $normalizer) {
            $data = $normalizer->normalize($data);
        }
        
        return $data;
    }

    public function denormalize(Array $data)
    {
        foreach ($this->getNormalizers() as $normalizer) {
            $data = $normalizer->denormalize($data);
        }
        
        return $data;
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

    public function getNormalizers()
    {
        return $this->normalizers;
    }

    public function addNormalizer($normalizer)
    {
        $this->getNormalizers()->add(
            (is_string($normalizer)) ? new $normalizer : $normalizer
        );
        
        return $this;
    }

    public function addNormalizers($normalizers)
    {
        foreach ($normalizers as $normalizer) {
            $this->addNormalizer($normalizer);
        }
        
        return $this;
    }

    public function setNormalizers($normalizers)
    {
        $this->normalizers = $normalizers;
        
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
