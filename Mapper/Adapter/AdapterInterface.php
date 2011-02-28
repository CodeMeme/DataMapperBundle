<?php

namespace CodeMeme\DataMapperBundle\Mapper\Adapter;

interface AdapterInterface
{

    public function convert($from, $to = null);

    public function convertFrom($from);

    public function convertTo($to, Array $values);

    public function supports($object);

}