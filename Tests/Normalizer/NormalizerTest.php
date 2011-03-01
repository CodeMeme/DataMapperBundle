<?php

namespace CodeMeme\DataMapperBundle\Tests\Normalizer;

use CodeMeme\DataMapperBundle\Normalizer\Normalizer;

class NormalizerTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructWithoutMap()
    {
        $n = new Normalizer;
        
        $this->assertTrue($n->getMap()->isEmpty());
    }

    public function testConstructWithMap()
    {
        $n = new Normalizer(array(
            'first_name' => 'firstName',
        ));
        
        $this->assertEquals(1, $n->getMap()->count());
        $this->assertEquals('firstName', $n->getMap()->get('first_name'));
    }

    /**
     * @dataProvider normalizerProvider
     */
    public function testNormalize($n)
    {
        $normalized = $n->normalize(array(
            'first_name' => 'John',
            'last_name'  => 'Doe',
        ));
        
        $this->assertEquals(array(
            'firstName' =>  'John',
            'lastName'  =>  'Doe',
        ), $normalized);
    }

    /**
     * @dataProvider normalizerProvider
     */
    public function testNormalizeTwice($n)
    {
        $normalized = $n->normalize(array(
            'first_name' => 'John',
            'last_name'  => 'Doe',
        ));
        
        $this->assertEquals(array(
            'firstName' =>  'John',
            'lastName'  =>  'Doe',
        ), $n->normalize( $normalized ));
    }

    /**
     * @dataProvider normalizerProvider
     */
    public function testNormalizeExtraKeys($n)
    {
        $normalized = $n->normalize(array(
            'id'            =>  1,
            'first_name'    =>  'John',
            'last_name'     =>  'Doe',
            2               =>  'two',
        ));
        
        $this->assertEquals(array(
            'id'        =>  1,
            'firstName' =>  'John',
            'lastName'  =>  'Doe',
            2           =>  'two',
        ), $normalized);
    }

    /**
     * @dataProvider normalizerProvider
     */
    public function testNormalizeOverwritesKeys($n)
    {
        $normalized = $n->normalize(array(
            'firstName'     =>  'foo',
            'first_name'    =>  'bar',
            'last_name'     =>  'bar',
            'lastName'      =>  'baz',
        ));
        
        $this->assertEquals(array(
            'firstName' =>  'bar',
            'lastName'  =>  'baz',
        ), $normalized);
    }

    /**
     * @dataProvider normalizerProvider
     */
    public function testDenormalize($n)
    {
        $d = $n->denormalize(array(
            'firstName' =>  'John',
            'lastName'  =>  'Doe',
        ));
        
        $this->assertEquals(array(
            'first_name' => 'John',
            'last_name'  => 'Doe',
        ), $d);
    }

    /**
     * @dataProvider normalizerProvider
     */
    public function testDenormalizeTwice($n)
    {
        $d = $n->denormalize(array(
            'firstName' =>  'John',
            'lastName'  =>  'Doe',
        ));
        
        $this->assertEquals(array(
            'first_name' => 'John',
            'last_name'  => 'Doe',
        ), $n->denormalize( $d ));
    }

    /**
     * @dataProvider normalizerProvider
     */
    public function testDeormalizeExtraKeys($n)
    {
        $d = $n->denormalize(array(
            'id'        =>  1,
            'firstName' =>  'John',
            'lastName'  =>  'Doe',
            2           =>  'two',
        ));
        
        $this->assertEquals(array(
            'id'            =>  1,
            'first_name'    =>  'John',
            'last_name'     =>  'Doe',
            2               =>  'two',
        ), $d);
    }

    /**
     * @dataProvider normalizerProvider
     */
    public function testDenormalizeOverwritesKeys($n)
    {
        $d = $n->denormalize(array(
            'first_name'    =>  'bar',
            'firstName'     =>  'foo',
            'lastName'      =>  'baz',
            'last_name'     =>  'bar',
        ));
        
        $this->assertEquals(array(
            'first_name' =>  'foo',
            'last_name'  =>  'bar',
        ), $d);
    }

    /**
     * @dataProvider normalizerProvider
     */
    public function normalizerProvider()
    {
        $normalizer = new Normalizer(array(
            'first_name'    => 'firstName',
            'last_name'     => 'lastName',
        ));
        
        return array(
            array($normalizer),
        );
    }

}