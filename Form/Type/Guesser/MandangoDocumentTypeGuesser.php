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

namespace Mandango\MandangoBundle\Form\Type\Guesser;

use Symfony\Component\Form\Type\Guesser\Guess;
use Symfony\Component\Form\Type\Guesser\TypeGuess;
use Symfony\Component\Form\Type\Guesser\TypeGuesserInterface;
use Mandango\Metadata;

/**
 * MandangoDocumentTypeGuesser
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoDocumentTypeGuesser implements TypeGuesserInterface
{
    private $metadata;

    /**
     * Constructor.
     *
     * @param Mandango\Metadata $metadata The Mandango's metadata.
     */
    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @inheritDoc
     */
    public function guessType($class, $property)
    {
        $metadata = $this->getClassMetadata($class);

        // field
        if (isset($metadata['fields'][$property])) {
            switch ($metadata['fields'][$property]['type']) {
                case 'bin_data':
                    return new TypeGuess('file', array(), Guess::MEDIUM_CONFIDENCE);
                case 'boolean':
                    return new TypeGuess('checkbox', array(), Guess::HIGH_CONFIDENCE);
                case 'date':
                    return new TypeGuess('date', array(), Guess::MEDIUM_CONFIDENCE);
                case 'float':
                case 'integer':
                    return new TypeGuess('number', array(), Guess::MEDIUM_CONFIDENCE);
                case 'raw':
                    return new TypeGuess('text', array(), Guess::MEDIUM_CONFIDENCE);
                case 'serialized':
                    return new TypeGuess('text', array(), Guess::MEDIUM_CONFIDENCE);
                case 'string':
                    return new TypeGuess('text', array(), Guess::MEDIUM_CONFIDENCE);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function guessRequired($class, $property)
    {
    }

    /**
     * @inheritDoc
     */
    public function guessMaxLength($class, $property)
    {
    }

    protected function getClassMetadata($class)
    {
        if (!$this->metadata->hasClass($class)) {
            return array();
        }

        return call_user_func(array($class, 'metadata'));
    }
}
