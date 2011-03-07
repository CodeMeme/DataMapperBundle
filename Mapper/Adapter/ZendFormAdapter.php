<?php

namespace CodeMeme\DataMapperBundle\Mapper\Adapter;

use Zend\Form\Form;

class ZendFormAdapter extends Adapter
{

    public function convertFrom($form)
    {
        return $form->getValues();
    }

    public function convertTo($form, Array $values)
    {
        return $form->populate($values);
    }

    public function supports($object)
    {
        return $object instanceof Form;
    }

}