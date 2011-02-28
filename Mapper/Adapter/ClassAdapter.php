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
            // Set public properties first
            if (($property = $r->getProperty($key)) && $property->isPublic()) {
                $class->$key = $value;
            } else if ($method = $r->getMethod($setter = 'set' . ucfirst($key))) {
                // Setters should expect a single parameter
                $parameter = current($method->getParameters());
                
                // If the setter has typehint (and there are values to assign), instantiate it
                if ($value && $typehintClass = $parameter->getClass()) {
                    $typehint = new $typehintClass->name;
                    $value = $this->convertTo($typehint, $value);
                }
                
                // Call the setter with the new value
                $method->invoke($class, $value);
            } else {
                throw new Exception(sprintf("Cannot convert %s for %s", $key, get_class($class)));
            }
        }
        
        return $class;
    }

    public function supports($object)
    {
        return (gettype($object) === 'object') && (get_class($object) !== 'stdClass');
    }

}