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

use JoliCode\Elastically\IndexNameMapper;
use JoliCode\Elastically\Serializer\StaticContextBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ElasticallyExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__) . '/Resources/config'));

        $loader->load('services.php');

        $container
            ->getDefinition(IndexNameMapper::class)
            ->replaceArgument('$prefix', $config['prefix'])
            ->replaceArgument('$indexClassMapping', $config['index_class_mapping'])
        ;
        $container
            ->getDefinition(StaticContextBuilder::class)
            ->replaceArgument('$mapping', $config['serializer']['context_mapping'])
        ;
    }
}
