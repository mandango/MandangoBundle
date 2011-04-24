<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
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
