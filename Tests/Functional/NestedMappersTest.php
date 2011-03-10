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

    public function testDenormalizeNestedArray()
    {
        $mapper = new Mapper(
            $this->container,
            array('CodeMeme\\DataMapperBundle\\Mapper\\Adapter\\ArrayAdapter'),
            $this->container->getParameter('datamapper.post_map')
        );
        
        $converted = $mapper->convert($this->getDenormalizedPost());
        
        $this->assertEquals($converted, $this->getPostArray());
    }

    public function testDenormalizedNestedToPost()
    {
        $mapper = new Mapper(
            $this->container,
            array(
                'CodeMeme\\DataMapperBundle\\Mapper\\Adapter\\ArrayAdapter',
                'CodeMeme\\DataMapperBundle\\Tests\\Mapper\\Adapter\\PostAdapter',
            ),
            $this->container->getParameter('datamapper.post_map')
        );
        
        $converted = $mapper->convert($this->getDenormalizedPost(), new Post);
        
        $this->assertEquals($converted, $this->getPostClass());
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

    protected function getDenormalizedPost()
    {
        return array(
            'post_id'       => 1,
            'post_title'    => 'My Post',
            'post_slug'     => 'my-post',
            'post_content'  => 'My Body',
            'last_updated'  => null,
            'post_category' => array(
                'name'  => 'Category',
            ),
            'comments' => array(
                0   => array(
                    'comment_id'    => 1,
                    'author'        => 'Name 1',
                    'e_mail'        => 'Email@1',
                    'text'          => 'Comment 1',
                ),
                1   => array(
                    'comment_id'    => 2,
                    'author'        => 'Name 2',
                    'e_mail'        => 'Email@2',
                    'text'          => 'Comment 2',
                ),
            ),
        );
    }

}