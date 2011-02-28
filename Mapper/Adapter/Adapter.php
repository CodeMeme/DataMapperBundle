<?php

namespace CodeMeme\DataMapperBundle\Mapper\Adapter;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class Adapter extends ContainerAware implements AdapterInterface
{

    public function __construct(ContainerInterface $container = null)
    {
        $this->setContainer($container);
    }

    public function convert($from, $to = null)
    {
        if ($this->supports($from)) {
            $this->converted = $this->convertFrom($from);
        } else {
            throw new Exception(sprintf('%s does not support converting from %s', get_class($this), is_object($from) ? get_class($from) : gettype($from)));
        }
        
        if (null === $to) {
            return $this->converted;
        }
        
        if ($this->supports($to)) {
            return $this->convertTo($to, $this->converted);
        } else {
            throw new Exception(sprintf('%s does not support converting to %s', get_class($this), is_object($to) ? get_class($to) : gettype($to)));
        }
    }

    public function getContainer()
    {
        if (null === $this->container) {
            throw new Exception('Container is missing');
        }
        
        return $this->container;
    }

}
