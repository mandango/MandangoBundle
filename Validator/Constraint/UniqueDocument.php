<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mandango\MandangoBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * UniqueConstraint.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class UniqueDocument extends Constraint
{
    public $message = 'This value is already used.';
    public $service = 'mandango.validator.unique_document';
    public $fields = array();
    public $caseInsensitive = array();

    /**
     * {@inheritDoc}
     */
    public function getDefaultOption()
    {
        return 'fields';
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions()
    {
        return array('fields');
    }

    /**
     * The validator must be defined as a service with this name.
     *
     * @return string
     */
    public function validatedBy()
    {
        return $this->service;
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}