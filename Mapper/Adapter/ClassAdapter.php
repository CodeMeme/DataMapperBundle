<?php

namespace CodeMeme\DataMapperBundle\Mapper\Adapter;

use CodeMeme\DataMapperBundle\Mapper\Adapter\Adapter;
use CodeMeme\DataMapperBundle\Mapper\Exception;

class ClassAdapter extends Adapter
{

    public function convertFrom($class)
    {
        $r = new \ReflectionClass(get_class($class));
        
        $converted = array();
        
        foreach ($r->getProperties() as $property) {
            if ($property->isPublic()) {
                $key = $property->name;
                
                $converted[$key] = $class->$key;
            }
        }
        
        foreach ($r->getMethods() as $method) {
            $key = lcfirst(substr($method->name, 3));
            
            if (empty($key)) {
                continue;
            }
            
            $getter = 'get' . ucfirst($key);
            $setter = 'set' . ucfirst($key);
            
            if (($method->name === $getter) && $r->hasMethod($setter)) {
                $value = $method->invoke($class);
                
                $converted[$key] = $this->supports($value)
                                 ? $this->convert($value) ?: null
                                 : $value;
            }
            
        }
        
        return $converted;
    }

    public function convertTo($class, Array $values, $strict = false)
    {
        // Merge any existing values with the new ones
        $values = array_merge($this->convert($class), $values);
        
        $r = new \ReflectionClass(get_class($class));
        
        foreach ($values as $key => $value) {
            if (null === $value) {
                continue;
            }
            
            if (!$this->setViaPublicProperty($class, $key, $value) &&
                !$this->setViaSetterMethod($class, $key, $value) &&
                $strict) {
                throw new Exception(sprintf("Cannot convert %s for %s", $key, get_class($class)));
            }
        }
        
        return $class;
    }

    protected function setViaPublicProperty($class, $key, $value)
    {
        $r = new \ReflectionClass(get_class($class));
        
        if ($r->hasProperty($key)) {
            $property = $r->getProperty($key);
            
            if ($property->isPublic()) {
                $class->$key = $value;
                
                return true;
            }
        }
        
        return false;
    }

    protected function setViaSetterMethod($class, $key, $value)
    {
        $r = new \ReflectionClass(get_class($class));
        
        $setter = 'set' . ucfirst($key);
        
        if ($r->hasMethod($setter)) {
            $method = $r->getMethod($setter);
            
            $parameter = current($method->getParameters());
            
            // If the setter has typehint (and there are values to assign), instantiate it
            if ($value && $typehintClass = $parameter->getClass()) {
                $typehint = new $typehintClass->name;
                $value = $this->convertTo($typehint, $value);
            }
            
            // Call the setter with the new value
            $method->invoke($class, $value);
            
            return true;
        }
        
        return false;
    }

    public function supports($object)
    {
        return (gettype($object) === 'object') && (get_class($object) !== 'stdClass');
    }

}