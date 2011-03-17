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

namespace Mandango\MandangoBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * MandangoBundle.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoMondatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('mandango.mondator')) {
            return;
        }

        $mondatorDefinition = $container->getDefinition('mandango.mondator');

        // core
        $definition = new Definition('Mandango\Extension\Core');
        $definition->addArgument(array(
            'metadata_class'  => $container->getParameter('mandango.metadata_class'),
            'metadata_output' => $container->getParameter('mandango.metadata_output'),
        ));
        $container->setDefinition('mandango.extension.core', $definition);

        $mondatorDefinition->addMethodCall('addExtension', array(new Reference('mandango.extension.core')));

        // bundles
        $definition = new Definition('Mandango\MandangoBundle\Extension\Bundles');
        $container->setDefinition('mandango.extension.bundles', $definition);

        $mondatorDefinition->addMethodCall('addExtension', array(new Reference('mandango.extension.bundles')));

        // validation
        $definition = new Definition('Mandango\MandangoBundle\Extension\DocumentValidation');
        $container->setDefinition('mandango.extension.document_validation', $definition);

        $mondatorDefinition->addMethodCall('addExtension', array(new Reference('mandango.extension.document_validation')));

        // custom
        foreach ($container->findTaggedServiceIds('mandango.mondator.extension') as $id => $attributes) {
            $definition->addMethodCall('addExtension', array(new Reference($id)));
        }
    }
}
