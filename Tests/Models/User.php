<?php

namespace CodeMeme\DataMapperBundle\Tests\Models;

use CodeMeme\DataMapperBundle\Tests\Models\Address;

class User
{
    public $name;
    public $email;
    public $address;

    public function __construct()
    {
        $this->address = new Address;
    }

}