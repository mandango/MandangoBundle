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

use Mandango\Inflector;
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
        foreach ($this->configClass['fields'] as $name => $field) {
            // base
            $baseFieldValidation = array();

            // custom
            $customFieldValidation = array();
            // notnull
            if (isset($field['notnull']) && $field['notnull']) {
                $customFieldValidation[] = array('NotNull' => null);
            }
            // notblank
            if (isset($field['notblank']) && $field['notblank']) {
                $customFieldValidation[] = array('NotBlank' => null);
            }
            // explicit
            if (isset($field['validation']) && $field['validation']) {
                $customFieldValidation = array_merge($customFieldValidation, $field['validation']);
            }

            // merge
            $validation['getters'][Inflector::camelize($name)] = array_merge_recursive($customFieldValidation, $baseFieldValidation);
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
        $method->setIsStatic(true);
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
