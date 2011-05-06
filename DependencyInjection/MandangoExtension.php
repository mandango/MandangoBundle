<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mandango\MandangoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * MandangoBundle.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoExtension extends Extension
{
    /**
     * Responds to the "mandango" configuration parameter.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('mandango.xml');

        $processor = new Processor();
        $configuration = new Configuration($container->getParameter('kernel.debug'));
        $config = $processor->process($configuration->getConfigTree(), $configs);

        // model_dir
        if (isset($config['model_dir'])) {
            $container->setParameter('mandango.model_dir', $config['model_dir']);
        }

        // logging
        if (isset($config['logging']) && $config['logging']) {
            $container->getDefinition('mandango')->addArgument(array(new Reference('mandango.logger'), 'logQuery'));
        }

        // default_connection
        if (isset($config['default_connection'])) {
            $container->getDefinition('mandango')->addMethodCall('setDefaultConnectionName', array($config['default_connection']));
        }

        // extra config classes dirs
        $container->setParameter('mandango.extra_config_classes_dirs', $config['extra_config_classes_dirs']);

        // connections
        foreach ($config['connections'] as $name => $connection) {
            $definition = new Definition($connection['class'], array(
                $connection['server'],
                $connection['database'],
                $connection['options'],
            ));

            $connectionDefinitionName = sprintf('mandango.%s_connection', $name);
            $container->setDefinition($connectionDefinitionName, $definition);

            // ->setConnection
            $container->getDefinition('mandango')->addMethodCall('setConnection', array(
                $name,
                new Reference($connectionDefinitionName),
            ));
        }
    }
}
