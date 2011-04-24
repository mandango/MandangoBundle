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

use Symfony\Component\Form\Type\Guesser\ValidatorTypeGuesser;
use Symfony\Component\Validator\Mapping\ClassMetadataFactoryInterface;
use Mandango\Inflector;
use Mandango\Metadata;

/**
 * MandangoValidatorTypeGuesser.
 *
 * It just fix the form and schema underscore names with the validator
 * getters camelized names.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoValidatorTypeGuesser extends ValidatorTypeGuesser
{
    private $metadata;

    /**
     * Constructor.
     *
     * @param Mandango\Metadata $metadata The Mandango's metadata.
     */
    public function __construct(Metadata $metadata, ClassMetadataFactoryInterface $metadataFactory)
    {
        $this->metadata = $metadata;

        parent::__construct($metadataFactory);
    }

    /**
     * @inheritDoc
     */
    public function guessType($class, $property)
    {
        $property = $this->renameProperty($class, $property);

        return parent::guessType($class, $property);
    }

    /**
     * @inheritDoc
     */
    public function guessRequired($class, $property)
    {
        $property = $this->renameProperty($class, $property);

        return parent::guessRequired($class, $property);
    }

    /**
     * @inheritDoc
     */
    public function guessMaxLength($class, $property)
    {
        $property = $this->renameProperty($class, $property);

        return parent::guessMaxLength($class, $property);
    }

    protected function renameProperty($class, $property)
    {
        if (!$this->metadata->hasClass($class)) {
            return $property;
        }

        $metadata = call_user_func(array($class, 'metadata'));

        if (
            isset($metadata['fields'][$property])
            ||
            isset($metadata['references_one'][$property])
            ||
            isset($metadata['references_many'][$property])
        ) {
            return Inflector::camelize($property);
        }

        return $property;
    }
}
