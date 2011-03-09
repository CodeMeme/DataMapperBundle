<?php

namespace CodeMeme\DataMapperBundle\Tests\Functional;

use CodeMeme\DataMapperBundle\Mapper\Mapper;
use CodeMeme\DataMapperBundle\Tests\TestCase;
use CodeMeme\DataMapperBundle\Tests\Models\Post;
use CodeMeme\DataMapperBundle\Tests\Models\Category;
use CodeMeme\DataMapperBundle\Tests\Models\Comment;

class NestedMappersTest extends TestCase
{

    /**
     * @dataProvider postProvider
     */
    public function testConvertFromSupportsCollections($class, $array)
    {
        $converted = $this->container->get('datamapper.post_mapper')->convert($class);
        
        $this->assertEquals($converted, $array);
    }

    /**
     * @dataProvider postProvider
     */
    public function testConvertToSupportsCollections($class, $array)
    {
        $converted = $this->container->get('datamapper.post_mapper')->convert($array, new Post);
        
        $this->assertEquals($converted, $class);
    }

    public function postProvider()
    {
        return array(
            array($this->getPostClass(), $this->getPostArray()),
        );
    }

    protected function getPostClass()
    {
        $post = new Post;
        
        $post->setId(1);
        $post->setName('My Post');
        $post->setSlug('my-post');
        $post->setBody('My Body');
        
        $category = new Category;
        $category->setName('Category');
        
        $post->setCategory($category);
        
        $comment1 = new Comment;
        $comment1->setId(1);
        $comment1->setName('Name 1');
        $comment1->setEmail('Email@1');
        $comment1->setBody('Comment 1');
        
        $comment2 = new Comment;
        $comment2->setId(2);
        $comment2->setName('Name 2');
        $comment2->setEmail('Email@2');
        $comment2->setBody('Comment 2');
        
        $post->getComments()->add($comment1);
        $post->getComments()->add($comment2);
        
        return $post;
    }

    protected function getPostArray()
    {
        return array(
            'id'            => 1,
            'name'          => 'My Post',
            'slug'          => 'my-post',
            'body'          => 'My Body',
            'dateModified'  => null,
            'category'      => array(
                'name'  => 'Category',
            ),
            'comments'  => array(
                0   => array(
                    'id'    => 1,
                    'name'  => 'Name 1',
                    'email' => 'Email@1',
                    'body'  => 'Comment 1',
                ),
                1   => array(
                    'id'    => 2,
                    'name'  => 'Name 2',
                    'email' => 'Email@2',
                    'body'  => 'Comment 2',
                ),
            ),
        );
    }

}