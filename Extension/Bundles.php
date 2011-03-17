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

namespace Mandango\MandangoBundle\Extension;

use Mandango\Mondator\Extension;
use Mandango\Mondator\Definition;
use Mandango\Mondator\Output;

/**
 * Mandango "Bundles" extension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class Bundles extends Extension
{
    /**
     * {@inheritdoc}
     */
    protected function doProcess()
    {
        foreach (array('bundle_name', 'bundle_namespace', 'bundle_dir') as $parameter) {
            if (!isset($this->configClass[$parameter]) || !$this->configClass[$parameter]) {
                return;
            }
        }

        /*
         * Definitions.
         */
        $classes = array(
            'document_bundle'   => '%bundle_namespace%\Document\%class_name%',
            'repository_bundle' => '%bundle_namespace%\Document\%class_name%Repository',
        );
        foreach ($classes as &$class) {
            $class = strtr($class, array(
                '%bundle_namespace%' => $this->configClass['bundle_namespace'],
                '%class_name%'       => substr($this->class, strrpos($this->class, '\\') + 1),
            ));
        }

        // document
        $this->definitions['document']->setParentClass('\\'.$classes['document_bundle']);

        $output = new Output($this->configClass['bundle_dir'].'/Document');
        $this->definitions['document_bundle'] = new Definition($classes['document_bundle'], $output);
        $this->definitions['document_bundle']->setParentClass('\\'.$this->definitions['document_base']->getClass());
        $this->definitions['document_bundle']->setIsAbstract(true);
        $this->definitions['document_bundle']->setDocComment(<<<EOF
/**
 * {$this->class} document bundle.
 */
EOF
        );

        // repository
        $this->definitions['repository']->setParentClass('\\'.$classes['repository_bundle']);

        $output = new Output($this->configClass['bundle_dir'].'/Document');
        $this->definitions['repository_bundle'] = new Definition($classes['repository_bundle'], $output);
        $this->definitions['repository_bundle']->setParentClass('\\'.$this->definitions['repository_base']->getClass());
        $this->definitions['repository_bundle']->setDocComment(<<<EOF
/**
 * {$this->class} document repository bundle
 */
EOF
        );
    }
}
