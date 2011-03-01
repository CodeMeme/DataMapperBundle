<?php

namespace CodeMeme\DataMapperBundle\Mapper\Adapter;

use CodeMeme\DataMapperBundle\Mapper\Adapter\Adapter;
use CodeMeme\DataMapperBundle\Mapper\Exception;

class ClassAdapter extends Adapter
{

    public function convertFrom($class)
    {
        $r = new \ReflectionClass(get_class($class));
        
        $values = array();
        
        foreach ($r->getProperties() as $property) {
            if ($property->isPublic()) {
                $key = $property->name;
                $values[$key] = $class->$key;
            }
        }
        
        foreach ($r->getMethods() as $method) {
            if (substr($method->name, 0, 3) === 'get') {
                $key = lcfirst(substr($method->name, 3));
                
                $setter = array($class, 'set' . ucfirst($key));
                
                if (is_callable($setter)) {
                    $value = call_user_func(array($class, $method->name));
                    
                    $values[$key] = $this->supports($value)
                                  ? $this->convertFrom($value) ?: null
                                  : $value;
                }
            }
        }
        
        return $values;
    }

    public function convertTo($class, Array $values)
    {
        // Merge any existing values with the new ones
        $values = array_merge($this->convertFrom($class), $values);
        
        $r = new \ReflectionClass(get_class($class));
        
        foreach ($values as $key => $value) {
            if (!$this->setViaPublicProperty($class, $key, $value) &&
                !$this->setViaSetterMethod($class, $key, $value)) {
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