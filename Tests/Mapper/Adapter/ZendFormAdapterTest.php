<?php

namespace CodeMeme\DataMapperBundle\Tests\Mapper\Adapter;

use CodeMeme\DataMapperBundle\Mapper\Mapper;
use CodeMeme\DataMapperBundle\Mapper\Adapter\ClassAdapter;
use CodeMeme\DataMapperBundle\Mapper\Adapter\ZendFormAdapter;
use CodeMeme\DataMapperBundle\Tests\Models\User;
use CodeMeme\DataMapperBundle\Tests\Models\Address;
use Symfony\Component\Form\Form as SymfonyForm;
use Zend\Form\Form;
use Zend\Form\SubForm;
use Zend\Form\Element\Text;
use Zend\View\PhpRenderer as View;

class ZendFormAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider formProvider
     */
    public function testSupportsForm($form)
    {
        $this->assertTrue($this->getAdapter()->supports($form));
    }

    public function testDoesNotSupportArray()
    {
        $this->assertFalse($this->getAdapter()->supports(array()));
    }

    public function testDoesNotSupportObject()
    {
        $this->assertFalse($this->getAdapter()->supports((Object) array()));
    }

    public function testDoesNotSupportSymfony()
    {
        $this->assertFalse($this->getAdapter()->supports(new SymfonyForm));
    }

    /**
     * @dataProvider formProvider
     */
    public function testFormToArray($form)
    {
        $values = array(
            'name'      =>  'New Name',
            'email'     =>  'New E-mail',
            'address'   =>  array(
                'street'    =>  'New Street',
                'city'      =>  'New City',
                'state'     =>  'New State',
                'zip'       =>  'New Zip',
            ),
        );
        
        $form->populate($values);
        
        $converted = $this->getAdapter()->convertFrom($form);
        
        $this->assertEquals($converted, $form->getValues());
        $this->assertEquals($converted, $values);
    }

    /**
     * @dataProvider formProvider
     */
    public function testArrayToForm($form)
    {
        $values = array(
            'name'      =>  'New Name',
            'email'     =>  'New E-mail',
            'address'   =>  array(
                'street'    =>  'New Street',
                'city'      =>  'New City',
                'state'     =>  'New State',
                'zip'       =>  'New Zip',
            ),
        );
        
        $converted = $this->getAdapter()->convertTo($form, $values);
        
        $this->assertEquals($converted->getValues(), $values);
    }

    /**
     * @dataProvider formProvider
     */
    public function testClassToForm($form, $user)
    {
        $converted = $this->getMapper()->convert($user, $form);
        
        $values = array(
            'name'      =>  'Name',
            'email'     =>  'E-mail',
            'address'   =>  array(
                'street'    =>  'Street',
                'city'      =>  'City',
                'state'     =>  'State',
                'zip'       =>  'Zip',
            ),
        );
        
        $this->assertEquals($converted->getValues(), $values);
    }

    protected function getAdapter()
    {
        return new ZendFormAdapter;
    }

    protected function getMapper()
    {
        return new Mapper(null, array(
            new ClassAdapter,
            new ZendFormAdapter,
        ));
    }

    public function formProvider()
    {
        $form = new Form;
        $form->setView(new View);
        
        $form->addElements(array(
            new Text('name'),
            new Text('email'),
        ));
        
        $address = new SubForm;
        
        $address->addElements(array(
            new Text('street'),
            new Text('city'),
            new Text('state'),
            new Text('zip'),
        ));
        
        $form->addSubForm($address, 'address');
        
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
            array($form, $user),
        );
    }

}
