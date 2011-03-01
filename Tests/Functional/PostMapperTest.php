<?php

namespace CodeMeme\DataMapperBundle\Tests\Functional;

use CodeMeme\DataMapperBundle\Mapper\Mapper;
use CodeMeme\DataMapperBundle\Tests\TestCase;
use CodeMeme\DataMapperBundle\Tests\Models\Post;

class PostMapperTest extends TestCase
{

    public function testPostToStdClassToArrayAndBack()
    {
        $post = new Post;
        
        $post->setId(1);
        $post->setName('My Post');
        $post->setSlug('my-post');
        $post->setBody('My Body');
        
        $mapper = $this->container->get('datamapper.post_mapper');
        
        $converted = $mapper->convert( $mapper->convert($post, (Object) array()) );
        
        $this->assertEquals(array(
            'id'            =>  1,
            'name'          =>  'My Post',
            'slug'          =>  'my-post',
            'body'          =>  'My Body',
            'category'      =>  null,
            'dateModified'  =>  null,
        ), $converted);
        
        $post = $mapper->convert($converted, $post);
        
        $this->assertEquals(1, $post->getId());
        $this->assertEquals('My Post', $post->getName());
        $this->assertEquals('my-post', $post->getSlug());
    }

    public function testDenormalizedArrayToPostAndBack()
    {
        $d = array(
            'post_id'       =>  1,
            'post_title'    =>  'My Post',
            'post_slug'     =>  'my-post',
            'post_content'  =>  'My Body',
            'post_category' =>  null,
            'last_updated'  =>  null,
        );
        
        $mapper = $this->container->get('datamapper.post_mapper');
        
        $post = $mapper->convert($d, new Post, Mapper::NORMALIZE);
        
        $this->assertEquals(1, $post->getId());
        $this->assertEquals('My Post', $post->getName());
        $this->assertEquals('my-post', $post->getSlug());
        $this->assertEquals('My Body', $post->getBody());
        
        $denormalized = $mapper->convert($post, array(), Mapper::DENORMALIZE);
        
        $this->assertEquals($denormalized, $d);
    }

}