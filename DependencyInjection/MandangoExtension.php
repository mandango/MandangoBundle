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

        // default_connection
        if (isset($config['default_connection'])) {
            $container->getDefinition('mandango')->addMethodCall('setDefaultConnectionName', array($config['default_connection']));
        }

        // logging
        if (isset($config['logging']) && $config['logging']) {
            $container->getDefinition('mandango')->addArgument(array(new Reference('mandango.logger'), 'logQuery'));
        }
    }
}
