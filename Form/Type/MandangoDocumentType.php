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

namespace Mandango\MandangoBundle\Form\Type;

use Mandango\MandangoBundle\Form\ChoiceList\MandangoDocumentChoiceList;
use Mandango\MandangoBundle\Form\DataTransformer\MandangoDocumentToIdTransformer;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Type\AbstractType;

/**
 * MandangoDocumentType.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoDocumentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['multiple']) {
            throw new \RuntimeException('Not implemented yet.');
        } else {
            $builder->prependClientTransformer(new MandangoDocumentToIdTransformer($options['choice_list']));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        $defaultOptions = array(
            'template' => 'choice',
            'multiple' => false,
            'expanded' => false,
            'class'    => null,
            'field'    => null,
            'query'    => null,
            'choices'           => array(),
            'preferred_choices' => array(),
        );

        $options = array_replace($defaultOptions, $options);

        if (!isset($options['choice_list'])) {
            $defaultOptions['choice_list'] = new MandangoDocumentChoiceList(
                $options['class'],
                $options['field'],
                $options['query'],
                $options['choices']
            );
        }

        return $defaultOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mandango_document';
    }
}
