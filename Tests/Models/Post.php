<?php

namespace CodeMeme\DataMapperBundle\Tests\Models;

use CodeMeme\DataMapperBundle\Tests\Models\Comment;
use Doctrine\Common\Collections\ArrayCollection;

class Post
{

    public $id;

    protected $name;

    protected $slug;

    protected $category;

    protected $body;

    private $dateModified;

    protected $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

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

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getDateModified()
    {
        return $this->dateModified;
    }

    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function setComments(ArrayCollection $comments)
    {
        $this->comments = $comments;
        
        return $this;
    }

}