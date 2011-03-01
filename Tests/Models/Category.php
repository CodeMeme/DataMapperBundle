<?php

namespace CodeMeme\DataMapperBundle\Tests\Models;

class Category
{

    protected $name;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

}