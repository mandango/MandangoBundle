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

use Mandango\Group\ReferenceGroup;
use Mandango\MandangoBundle\Form\ChoiceList\MandangoDocumentChoiceList;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\DataTransformer\TransformationFailedException;

/**
 * MandangoDocumentToArrayTransformer.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoDocumentsToArrayTransformer implements DataTransformerInterface
{
    private $choiceList;

    public function __construct(MandangoDocumentChoiceList $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    public function transform($group)
    {
        if (null === $group) {
            return array();
        }

        if (!$group instanceof ReferenceGroup) {
            throw new UnexpectedTypeException($group, 'Mandango\Group\ReferenceGroup');
        }

        $array = array();
        foreach ($group as $document) {
            $array[] = (string) $document->getId();
        }

        return $array;
    }

    public function reverseTransform($keys)
    {
        $documents = $this->choiceList->getDocuments();

        $array = array();
        foreach ($keys as $key) {
            if (!isset($documents[(string) $key])) {
                throw new TransformationFailedException('Some Mandango document does not exist.');
            }
            $array[] = $documents[(string) $key];
        }

        return $array;
    }
}
