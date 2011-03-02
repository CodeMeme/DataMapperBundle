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

    public function normalize(Array $data, $strict = false)
    {
        $normalized = array();
        
        foreach ($data as $key => $value) {
            if ($this->getMap()->containsKey($key)) {
                // Re-assign key to normalized key
                $key = $this->getMap()->get($key);
            } else if ($this->getMap()->contains($key)) {
                // No need to re-assign key
            } else if ($strict) {
                // Key not found in normalization map, so skip
                continue;
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