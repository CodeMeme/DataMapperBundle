<?php

namespace CodeMeme\DataMapperBundle\Tests\Mapper\Adapter;

use CodeMeme\DataMapperBundle\Mapper\Adapter\SymfonyFormAdapter;
use CodeMeme\DataMapperBundle\Tests\Models\User;
use CodeMeme\DataMapperBundle\Tests\Models\Address;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\TextareaField;
use Symfony\Component\Form\CheckboxField;
use Symfony\Component\Form\CollectionField;
use Symfony\Component\HttpFoundation\Request;
use Zend\Form\Form as ZendForm;

class SymfonyFormTest extends \PHPUnit_Framework_TestCase
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

    public function testDoesNotSupportZendForm()
    {
        $this->assertFalse($this->getAdapter()->supports(new ZendForm));
    }

    /**
     * @dataProvider formProvider
     */
    public function testFormToArray($form)
    {
        $form->submit(array(
            'name'      =>  'New Name',
            'email'     =>  'New E-mail',
            'address'   =>  array(
                'street'    =>  'New Street',
                'city'      =>  'New City',
                'state'     =>  'New State',
                'zip'       =>  'New Zip',
            ),
        ));
        
        $this->assertEquals($this->getAdapter()->convertFrom($form), $form->getDisplayedData());
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
        
        $this->assertEquals($converted->getDisplayedData(), $values);
        $this->assertTrue($converted->getData() instanceof User);
        $this->assertTrue($converted->getData()->address instanceof Address);
    }

    protected function getAdapter()
    {
        return new SymfonyFormAdapter;
    }

    public function formProvider()
    {
        $form = new Form('user');
        
        $form->add(new TextField('name'));
        $form->add(new TextField('email'));
        
        $address = new Form('address');
        $address->add(new TextField('street'));
        $address->add(new TextField('city'));
        $address->add(new TextField('state'));
        $address->add(new TextField('zip'));
        
        $form->add($address);
        
        $form->setData(new User);
        
        return array(
            array($form),
        );
    }

}
