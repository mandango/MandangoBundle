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

use Symfony\Component\Form\AbstractExtension;

/**
 * MandangoExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoExtension extends AbstractExtension
{
    protected function loadTypes()
    {
        return array(
            new Type\MandangoDocumentType(),
        );
    }

    protected function loadTypeGuesser()
    {
        return new MandangoTypeGuesser();
    }
}
