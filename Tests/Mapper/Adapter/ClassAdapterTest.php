<?php

namespace CodeMeme\DataMapperBundle\Tests\Mapper\Adapter;

use CodeMeme\DataMapperBundle\Mapper\Adapter\ClassAdapter;
use CodeMeme\DataMapperBundle\Tests\Models\Post;
use CodeMeme\DataMapperBundle\Tests\Models\Category;
use CodeMeme\DataMapperBundle\Tests\Models\User;
use CodeMeme\DataMapperBundle\Tests\Models\Address;

class ClassAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider postProvider
     */
    public function testSupportsClass($post)
    {
        $this->assertTrue($this->getAdapter()->supports($post));
    }

    public function testDoesNotSupportArray()
    {
        $this->assertFalse($this->getAdapter()->supports(array()));
    }

    public function testDoesNotSupportObject()
    {
        $this->assertFalse($this->getAdapter()->supports((Object) array()));
    }

    /**
     * @dataProvider postProvider
     */
    public function testConvertFromReturnsArray($post)
    {
        $this->assertType(
            \PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY,
            $this->getAdapter()->convertFrom($post)
        );
    }

    /**
     * @dataProvider postProvider
     */
    public function testConvertFromUsesPublicProperties($post)
    {
        $converted = $this->getAdapter()->convert($post);
        
        $this->assertArrayHasKey('id', $converted);
        $this->assertEquals(1, $converted['id']);
    }

    /**
     * @dataProvider postProvider
     */
    public function testConvertFromUsesGetters($post)
    {
        $converted = $this->getAdapter()->convert($post);
        
        $this->assertArrayHasKey('name', $converted);
        $this->assertArrayHasKey('slug', $converted);
    }

    /**
     * @dataProvider postProvider
     */
    public function testConvertFromDeep($post)
    {
        $converted = $this->getAdapter()->convert($post);
        
        $this->assertArrayHasKey('dateModified', $converted);
        
        $this->assertType(
            \PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY,
            $converted['dateModified']
        );
    }

    public function testConvertToUsesPublicProperties()
    {
        $post = $this->getAdapter()->convertTo(new Post, array('id' => 1));
        
        $this->assertEquals(1, $post->id);
    }

    public function testConvertToUsesSetters()
    {
        $post = $this->getAdapter()->convertTo(new Post, array(
            'id'    =>  1,
            'name'  =>  'My Post',
            'slug'  =>  'My Slug',
        ));
        
        $this->assertEquals('My Post', $post->getName());
        $this->assertEquals('My Slug', $post->getSlug());
    }

    public function testConvertToMergesClasses()
    {
        $oldPost = new Post;
        $oldPost->id = 1;
        $oldPost->setName('Old Post');
        $oldPost->setSlug('Old Slug');
        
        $newPost = $this->getAdapter()->convertTo($oldPost, array(
            'id'    =>  2,
            'name'  =>  'New Post',
        ));
        
        $this->assertEquals(2, $newPost->id);
        $this->assertEquals('New Post', $newPost->getName());
        $this->assertEquals('Old Slug', $newPost->getSlug());
    }

    public function testConvertToSupportsNestedClasses()
    {
        $category = new Category;
        $category->setName('Code');
        
        $oldPost = new Post;
        $oldPost->setName('My Post');
        $oldPost->setSlug('my-post');
        $oldPost->setCategory($category);
        
        $values = $this->getAdapter()->convertFrom($oldPost);
        
        $newPost = new Post;
        $newPost->id = 1;
        
        $converted = $this->getAdapter()->convertTo($newPost, $values);
        
        $this->assertType('CodeMeme\DataMapperBundle\Tests\Models\Post', $converted);
        $this->assertType('CodeMeme\DataMapperBundle\Tests\Models\Category', $converted->getCategory());
    }

    /**
     * @dataProvider userPRovider
     */
    public function testConvertUserWithAddress($user)
    {
        $values = $this->getAdapter()->convertFrom($user);
        
        $this->assertType(
            \PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY,
            $values
        );
        
        $this->assertType(
            \PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY,
            $values['address']
        );
    }

    protected function getAdapter()
    {
        return new ClassAdapter;
    }

    public function postProvider()
    {
        $post = new Post;
        
        $post->id = 1;
        $post->setName('My Post');
        $post->setSlug('my-post');
        $post->setDateModified(new \DateTime);
        
        $category = new Category;
        $category->setName('Code');
        
        $post->setCategory($category);
        
        return array(
            array($post),
        );
    }

    public function userProvider()
    {
        $user           = new User;
        $user->name     = 'Name';
        $user->email    = 'E-mail';
        
        $address            = new Address;
        $address->street    =   'Street';
        $address->city      =   'City';
        $address->state     =   'State';
        $address->zip       =   'Zip';
        
        $user->address  = $address;
        
        return array(
            array($user),
        );
    }

}
