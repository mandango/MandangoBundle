<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
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
    protected function doClassProcess()
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
            'document_bundle'   => '%bundle_namespace%\Model\%class_name%',
            'repository_bundle' => '%bundle_namespace%\Model\%class_name%Repository',
            'query_bundle'      => '%bundle_namespace%\Model\%class_name%Query',
        );
        foreach ($classes as &$class) {
            $class = strtr($class, array(
                '%bundle_namespace%' => $this->configClass['bundle_namespace'],
                '%class_name%'       => substr($this->class, strrpos($this->class, '\\') + 1),
            ));
        }

        // document
        $this->definitions['document']->setParentClass('\\'.$classes['document_bundle']);

        $output = new Output($this->configClass['bundle_dir'].'/Model');
        $this->definitions['document_bundle'] = new Definition($classes['document_bundle'], $output);
        $this->definitions['document_bundle']->setParentClass('\\'.$this->definitions['document_base']->getClass());
        $this->definitions['document_bundle']->setAbstract(true);
        $this->definitions['document_bundle']->setDocComment(<<<EOF
/**
 * {$this->class} bundle document.
 */
EOF
        );

        if (!$this->configClass['isEmbedded']) {
            // repository
            $this->definitions['repository']->setParentClass('\\'.$classes['repository_bundle']);

            $output = new Output($this->configClass['bundle_dir'].'/Model');
            $this->definitions['repository_bundle'] = new Definition($classes['repository_bundle'], $output);
            $this->definitions['repository_bundle']->setParentClass('\\'.$this->definitions['repository_base']->getClass());
            $this->definitions['repository_bundle']->setDocComment(<<<EOF
/**
 * {$this->class} bundle document repository.
 */
EOF
            );

            // query
            $this->definitions['query']->setParentClass('\\'.$classes['query_bundle']);

            $output = new Output($this->configClass['bundle_dir'].'/Model');
            $this->definitions['query_bundle'] = new Definition($classes['query_bundle'], $output);
            $this->definitions['query_bundle']->setParentClass('\\'.$this->definitions['query_base']->getClass());
            $this->definitions['query_bundle']->setDocComment(<<<EOF
/**
 * {$this->class} bundle document query.
 */
EOF
            );
        }
    }
}
