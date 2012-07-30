<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
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
            'metadata_factory_class'  => $container->getParameter('mandango.metadata_factory.class'),
            'metadata_factory_output' => $container->getParameter('mandango.metadata_factory.output'),
            'default_behaviors'       => $container->hasParameter('mandango.default_behaviors')
                                       ? $container->getParameter('mandango.default_behaviors')
                                       : array(),
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
            $mondatorDefinition->addMethodCall('addExtension', array(new Reference($id)));
        }
    }
}
