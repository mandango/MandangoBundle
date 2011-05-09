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

use Mandango\Mondator\Definition\Method;
use Mandango\Mondator\Dumper;
use Mandango\Mondator\Extension;

/**
 * DocumentValidation extension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class DocumentValidation extends Extension
{
    /**
     * {@inheritdoc}
     */
    protected function doClassProcess()
    {
        $validation = array(
            'constraints' => array(),
            'getters'     => array(),
        );

        // constraints
        if (isset($this->configClass['validation'])) {
            $validation['constraints'] = $this->configClass['validation'];
        }

        // getters

        // fields
        foreach ($this->configClass['fields'] as $name => $field) {
            if (empty($field['inherited']) && isset($field['validation']) && $field['validation']) {
                $validation['getters'][$name] = $field['validation'];
            }
        }
        // referencesOne
        foreach ($this->configClass['referencesOne'] as $name => $referenceOne) {
            if (empty($referenceOne['inherited']) && isset($referenceOne['validation']) && $referenceOne['validation']) {
                $validation['getters'][$name] = $referenceOne['validation'];
            }
        }
        // referencesMany
        foreach ($this->configClass['referencesMany'] as $name => $referenceMany) {
            if (empty($referenceMany['inherited']) && isset($referenceMany['validation']) && $referenceMany['validation']) {
                $validation['getters'][$name] = $referenceMany['validation'];
            }
        }
        // embeddedsOne
        foreach ($this->configClass['embeddedsOne'] as $name => $embeddedOne) {
            if (empty($embeddedOne['inherited']) && isset($embeddedOne['validation']) && $embeddedOne['validation']) {
                $validation['getters'][$name] = $embeddedOne['validation'];
            }
        }
        // embeddedsMany
        foreach ($this->configClass['embeddedsMany'] as $name => $embeddedMany) {
            if (empty($embeddedMany['inherited']) && isset($embeddedMany['validation']) && $embeddedMany['validation']) {
                $validation['getters'][$name] = $embeddedMany['validation'];
            }
        }

        $validation = Dumper::exportArray($validation, 12);

        $method = new Method('public', 'loadValidatorMetadata', '\Symfony\Component\Validator\Mapping\ClassMetadata $metadata', <<<EOF
        \$validation = $validation;

        foreach (\Mandango\MandangoBundle\Extension\DocumentValidation::parseNodes(\$validation['constraints']) as \$constraint) {
            \$metadata->addConstraint(\$constraint);
        }

        foreach (\$validation['getters'] as \$getter => \$constraints) {
            foreach (\Mandango\MandangoBundle\Extension\DocumentValidation::parseNodes(\$constraints) as \$constraint) {
                \$metadata->addGetterConstraint(\$getter, \$constraint);
            }
        }

        return true;
EOF
        );
        $method->setStatic(true);
        $method->setDocComment(<<<EOF
    /**
     * Maps the validation.
     *
     * @param \Symfony\Component\Validator\Mapping\ClassMetadata \$metadata The metadata class.
     */
EOF
        );

        $this->definitions['document_base']->addMethod($method);
    }

    /*
     * Code from Symfony\Component\Validator\Mapping\Loader\YamlFileLoader
     */
    static public function parseNodes(array $nodes)
    {
        $values = array();

        foreach ($nodes as $name => $childNodes) {
            if (is_numeric($name) && is_array($childNodes) && count($childNodes) == 1) {
                $options = current($childNodes);

                if (is_array($options)) {
                    $options = static::parseNodes($options);
                }

                $values[] = static::newConstraint(key($childNodes), $options);
            } else {
                if (is_array($childNodes)) {
                    $childNodes = static::parseNodes($childNodes);
                }

                $values[$name] = $childNodes;
            }
        }

        return $values;
    }

    /*
     * Code from Symfony\Component\Validator\Mapping\Loader\FileLoader
     */
    static protected function newConstraint($name, $options)
    {
        if (false !== strpos($name, '\\') && class_exists($name)) {
            $className = (string) $name;
        } else {
            $className = 'Symfony\\Component\\Validator\\Constraints\\'.$name;
        }

        return new $className($options);
    }
}
