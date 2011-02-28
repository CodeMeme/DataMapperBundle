<?php

namespace CodeMeme\DataMapperBundle\Tests\Mapper\Adapter;

use CodeMeme\DataMapperBundle\Mapper\Adapter\ClassAdapter;
use CodeMeme\DataMapperBundle\Tests\TestCase;

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
        
        $this->assertType('CodeMeme\DataMapperBundle\Tests\Mapper\Adapter\Post', $converted);
        $this->assertType('CodeMeme\DataMapperBundle\Tests\Mapper\Adapter\Category', $converted->getCategory());
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

}

/**
 * @class Post
 */
class Post
{

    public $id;

    protected $name;

    protected $slug;

    protected $category;

    private $dateModified;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(Category $category = null)
    {
        $this->category = $category;
    }

    public function getDateModified()
    {
        return $this->dateModified;
    }

    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;
    }

}

/**
 * @class Category
 */
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