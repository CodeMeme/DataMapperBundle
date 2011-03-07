<?php

namespace CodeMeme\DataMapperBundle\Mapper\Adapter;

use Symfony\Component\Form\Form;

class SymfonyFormAdapter extends Adapter
{

    public function convertFrom($form)
    {
        return $form->getDisplayedData();
    }

    public function convertTo($form, Array $values)
    {
        $form->submit($values);
        
        return $form;
    }

    public function supports($object)
    {
        return $object instanceof Form;
    }

}