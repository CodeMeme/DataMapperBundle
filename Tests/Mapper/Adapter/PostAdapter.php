<?php

namespace CodeMeme\DataMapperBundle\Tests\Mapper\Adapter;

use CodeMeme\DataMapperBundle\Mapper\Adapter\Adapter;
use CodeMeme\DataMapperBundle\Mapper\Adapter\ClassAdapter;
use CodeMeme\DataMapperBundle\Tests\Models\Post;
use CodeMeme\DataMapperBundle\Tests\Models\Comment;

use Doctrine\Common\Collections\ArrayCollection;

class PostAdapter extends Adapter
{

    public function convertFrom($class)
    {
        $adapter = new ClassAdapter;
        
        return $adapter->convert($class);
    }

    public function convertTo($class, Array $values, $strict = false)
    {
        $adapter = new ClassAdapter;
        
        $converted = $adapter->convertTo($class, $values);
        
        $comments = new ArrayCollection;
        
        foreach ($converted->getComments() as $key => $comment) {
            $comments->set($key, $adapter->convertTo(new Comment, $comment));
        }
        
        $converted->setComments($comments);
        
        return $converted;
    }

    public function supports($object)
    {
        return $object instanceof Post;
    }

}