<?php

namespace CodeMeme\DataMapperBundle\Mapper\Adapter;

class ObjectAdapter extends Adapter
{

    public static function objectToArray($object)
    {
        if (is_object($object)) {
            $object = (Array) $object;
        }
        
        if (is_array($object)) {
            return array_map(array('self', 'objectToArray'), $object);
        } else {
            return $object;
        }
    }

    public static function arrayToObject($array)
    {
        if (is_array($array)) {
            return (Object) array_map(array('self', 'arrayToObject'), $array);
        } else {
            return $array;
        }
    }

    public function convertFrom($from)
    {
        return self::objectToArray($from);
    }

    public function convertTo($object, Array $values)
    {
        $existing = self::objectToArray($object);
        
        $values = array_merge(
            self::objectToArray($object),
            $values
        );
        
        return self::arrayToObject($values);
    }

    public function supports($object)
    {
        return is_object($object) && get_class($object) === 'stdClass';
    }

}