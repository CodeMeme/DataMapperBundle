<?php

namespace CodeMeme\DataMapperBundle\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;

class Normalizer implements NormalizerInterface
{

    protected $map;

    public function __construct($map = array())
    {
        $this->map = new ArrayCollection;
        
        $this->setMap($map);
    }

    public function normalize(Array $data)
    {
        $normalized = array();
        
        foreach ($data as $key => $value) {
            if ($this->getMap()->containsKey($key)) {
                $key = $this->getMap()->get($key);
            }
            
            $normalized[$key] = $value;
        }
        
        return $normalized;
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

    public function getMap()
    {
        return $this->map;
    }

    public function setMap($map)
    {
        foreach ($map as $key => $value) {
            $this->getMap()->set($key, $value);
        }
        
        return $this;
    }

}