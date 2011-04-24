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

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * MandangoExtension configuration structure.
 *
 * Based on the DoctrineMongoDBBundle's configuration.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return Symfony\Component\DependencyInjection\Configuration\NodeInterface
     */
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mandango', 'array');

        $this->addConnectionsSection($rootNode);

        $rootNode
            ->children()
                ->booleanNode('logging')->end()
                ->scalarNode('default_connection')->end()
            ->end()
        ;

        return $treeBuilder->buildTree();
    }

    /**
     * Adds the configuration for the "connections" key
     */
    protected function addConnectionsSection($rootNode)
    {
        $rootNode
            ->fixXmlConfig('connection')
            ->children()
                ->arrayNode('connections')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        //->performNoDeepMerging()
                        ->children()
                            ->scalarNode('class')->defaultValue('Mandango\Connection')->end()
                            ->scalarNode('server')->end()
                            ->scalarNode('database')->end()
                        ->end()
                        ->append($this->addConnectionOptionsNode())
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Adds the NodeBuilder for the "options" key of a connection.
     */
    protected function addConnectionOptionsNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('options');

        $node
            ->performNoDeepMerging()
            ->addDefaultsIfNotSet() // adds an empty array of omitted
            // options go into the Mongo constructor
            // http://www.php.net/manual/en/mongo.construct.php
            ->children()
                ->booleanNode('connect')->end()
                ->scalarNode('persist')->end()
                ->scalarNode('timeout')->end()
                ->booleanNode('replicaSet')->end()
                ->scalarNode('username')->end()
                ->scalarNode('password')->end()
            ->end()
        ->end();

        return $node;
    }
}
