<?php

namespace CodeMeme\DataMapperBundle\Mapper\Adapter;

use CodeMeme\DataMapperBundle\Mapper\Adapter\Adapter;

class ArrayAdapter extends Adapter
{

    public function convertFrom($from)
    {
        return (Array) $from;
    }

    public function convertTo($to, Array $values)
    {
        return array_merge($to, $values);
    }

    public function supports($object)
    {
        return is_array($object);
    }

}