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

use Mandango\Inflector;
use Symfony\Component\Form\Type\Guesser\ValidatorTypeGuesser;

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

        var_dump($class, $property);

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
