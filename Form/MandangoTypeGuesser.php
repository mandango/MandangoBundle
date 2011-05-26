<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mandango\MandangoBundle\Form;

use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use Mandango\MetadataFactory;

/**
 * MandangoTypeGuesser
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoTypeGuesser implements FormTypeGuesserInterface
{
    private $metadataFactory;

    /**
     * Constructor.
     *
     * @param Mandango\MetadataFactory $metadata The Mandango's metadata.
     */
    public function __construct(MetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @inheritDoc
     */
    public function guessType($class, $property)
    {
        if (!$this->metadataFactory->hasClass($class)) {
            return;
        }

        $metadata = $this->metadataFactory->getClass($class);

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
                    return new TypeGuess('number', array(), Guess::MEDIUM_CONFIDENCE);
                case 'integer':
                    return new TypeGuess('integer', array(), Guess::MEDIUM_CONFIDENCE);
                case 'raw':
                    return new TypeGuess('text', array(), Guess::MEDIUM_CONFIDENCE);
                case 'serialized':
                    return new TypeGuess('text', array(), Guess::MEDIUM_CONFIDENCE);
                case 'string':
                    return new TypeGuess('text', array(), Guess::MEDIUM_CONFIDENCE);
            }
        }

        // referencesOne
        if (isset($metadata['referencesOne'][$property])) {
            return new TypeGuess('mandango_document', array(
                'class' => $metadata['referencesOne'][$property]['class'],
            ), Guess::HIGH_CONFIDENCE);
        }

        // referencesMany
        if (isset($metadata['referencesMany'][$property])) {
            return new TypeGuess('mandango_document', array(
                'class' => $metadata['referencesMany'][$property]['class'],
                'multiple' => true,
            ), Guess::HIGH_CONFIDENCE);
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

    /**
     * @inheritDoc
     */
    public function guessMinLength($class, $property)
    {
    }
}
