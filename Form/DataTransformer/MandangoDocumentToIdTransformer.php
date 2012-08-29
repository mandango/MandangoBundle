<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mandango\MandangoBundle\Form\DataTransformer;

use Mandango\MandangoBundle\Form\ChoiceList\MandangoDocumentChoiceList;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\DataTransformer\TransformationFailedException;

/**
 * MandangoDocumentToIdTransformer.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoDocumentToIdTransformer implements DataTransformerInterface
{
    private $choiceList;

    public function __construct(MandangoDocumentChoiceList $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    public function transform($document)
    {
        if (null === $document) {
            return null;
        }

        return (string) $document->getId();
    }

    public function reverseTransform($key)
    {
        if (null === $key) {
            return null;
        }

        $documents = $this->choiceList->getDocuments();

        return array_key_exists($key, $documents) ? $documents[$key] : null;
    }
}
