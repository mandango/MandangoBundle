<?php

/*
 * Copyright 2010 Pablo Díez <pablodip@gmail.com>
 *
 * This file is part of Mandango.
 *
 * Mandango is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Mandango is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Mandango. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Mandango\MandangoBundle\Form\DataTransformer;

use Mandango\MandangoBundle\Form\ChoiceList\MandangoDocumentChoiceList;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\DataTransformer\DataTransformerInterface;
use Symfony\Component\Form\DataTransformer\TransformationFailedException;

/**
 * MandangoDocumentToIdTransformer.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoDocumentToIdTransformer implements DataTransformerInterface
{
    protected $choiceList;

    public function __construct(MandangoDocumentChoiceList $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    public function transform($document)
    {
        if (null === $document) {
            return null;
        }

        return $document->getId()->__toString();
    }

    public function reverseTransform($key)
    {
        if (null === $key) {
            return null;
        }

        $documents = $this->choiceList->getDocuments();

        return $documents[$key];
    }
}
