<?php

namespace CodeMeme\DataMapperBundle\Normalizer;

interface NormalizerInterface
{

    public function normalize(Array $data);
    public function denormalize(Array $data);

}