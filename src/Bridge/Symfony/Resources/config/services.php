<?php

/*
 * This file is part of the jolicode/elastically library.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use JoliCode\Elastically\Client;
use JoliCode\Elastically\IndexBuilder;
use JoliCode\Elastically\Indexer;
use JoliCode\Elastically\IndexNameMapper;
use JoliCode\Elastically\Mapping\YamlProvider;
use JoliCode\Elastically\ResultSetBuilder;
use JoliCode\Elastically\Serializer\StaticContextBuilder;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(IndexNameMapper::class)
            ->args([
                '$prefix' => abstract_arg('prefix'),
                '$indexClassMapping' => abstract_arg('index class mapping'),
            ])

        ->set(StaticContextBuilder::class)
            ->args([
                '$mapping' => abstract_arg('mapping'),
            ])

        ->set(ResultSetBuilder::class)
            ->args([
                '$indexNameMapper' => service(IndexNameMapper::class),
                '$contextBuilder' => service(StaticContextBuilder::class),
                '$denormalizer' => service('serializer'),
            ])

        ->set(Client::class)
            ->args([
                '$config' => abstract_arg('config'),
                '$logger' => service('logger')->nullOnInvalid(),
                '$resultSetBuilder' => service(ResultSetBuilder::class),
                '$indexNameMapper' => service(IndexNameMapper::class),
            ])

        ->set(Indexer::class)
            ->args([
                '$client' => service(Client::class),
                '$serializer' => service('serializer'),
                '$bulkMaxSize' => 100,
                '$bulkRequestParams' => [],
                '$contextBuilder' => service(StaticContextBuilder::class),
            ])

        ->set('elastically.mapping.provider', YamlProvider::class)
            ->args([
                '$configurationDirectory' => abstract_arg('configurationDirectory'),
            ])

        ->set(IndexBuilder::class)
            ->args([
                '$mappingProvider' => service('elastically.mapping.provider'),
                '$client' => service(Client::class),
                '$indexNameMapper' => service(IndexNameMapper::class),
            ])

    ;
};
