<?php

/*
 * This file is part of the jolicode/elastically library.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JoliCode\Elastically\Bridge\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('elastically');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('prefix')
                    ->info('A prefix for all elasticsearch indices')
                    ->defaultNull()
                ->end()
                ->arrayNode('index_class_mapping')
                    ->info('a mapping between an index name and a FQCN')
                    ->example(['my-index' => 'My\Dto'])
                    ->normalizeKeys(false)
                    ->prototype('scalar')
                        ->info('A FQCN')
                        ->example('My\Dto')
                    ->end()
                ->end()
                ->arrayNode('serializer')
                    ->children()
                        ->arrayNode('context_mapping')
                            ->normalizeKeys(false)
                            ->useAttributeAsKey('context_mapping')
                            ->prototype('variable')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('client')
                    ->prototype('variable')->end()
                ->end()
                ->scalarNode('mapping_directory')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
