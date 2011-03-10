<?php

namespace CodeMeme\DataMapperBundle\Normalizer;

use CodeMeme\DataMapperBundle\Mapper\Mapper;
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
            if (is_array($value)) {
                // Ensure we recursively denormalize the value before proceeding
                $value = $this->normalize($value);
            }
            
            if ($this->getMap()->containsKey($key)) {
                $mapped = $this->getMap()->get($key);
                
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
            } else if ($strict) {
                // Key not found in normalization map, so skip
                continue;
            }
            
            // Assign the value to the normalized key
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