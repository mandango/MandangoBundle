<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
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
