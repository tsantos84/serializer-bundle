<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class Configuration.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        // BC to LTS Symfony projects. To be removed on Symfony v4.4
        if (version_compare(Kernel::VERSION, '3.4.0', '<=')) {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('tsantos_serializer', 'array');
        } else {
            $treeBuilder = new TreeBuilder('tsantos_serializer');
            $rootNode = $treeBuilder->getRootNode();
        }

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('debug')
                    ->defaultValue('%kernel.debug%')
                ->end()
                ->scalarNode('circular_reference_handler')
                    ->defaultValue('tsantos_serializer.default_circular_reference_handler')
                ->end()
                ->scalarNode('format')
                    ->cannotBeEmpty()
                    ->defaultValue('json')
                ->end()
                ->scalarNode('hydrator_namespace')
                    ->cannotBeEmpty()
                    ->defaultValue('App\\Hydrator')
                ->end()
                ->booleanNode('enable_property_grouping')
                    ->defaultFalse()
                ->end()
                ->booleanNode('enable_max_depth_check')
                    ->defaultFalse()
                ->end()
                ->scalarNode('hydrator_path')
                    ->defaultValue('%kernel.cache_dir%/tsantos_serializer/hydrators')
                ->end()
                ->enumNode('generation_strategy')
                    ->values(['never', 'always', 'file_not_exists'])
                    ->defaultValue('file_not_exists')
                ->end()
                ->arrayNode('include_dir')
                    ->beforeNormalization()->ifString()->then(function ($v) { return [$v]; })->end()
                    ->defaultValue([
                        '%kernel.project_dir%/src/{Entity,Document,Model,ValueObject}',
                    ])
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('exclude_dir')
                    ->beforeNormalization()->ifString()->then(function ($v) { return [$v]; })->end()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('mapping')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('cache')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->enumNode('type')
                                    ->values(['file', 'doctrine', 'psr'])
                                    ->defaultValue('psr')
                                ->end()
                                ->scalarNode('id')
                                    ->defaultValue('cache.serializer')
                                ->end()
                                ->scalarNode('prefix')
                                    ->defaultValue('TSantosSerializer')
                                ->end()
                                ->scalarNode('path')
                                    ->defaultValue('%kernel.cache_dir%/tsantos_serializer/metadata')
                                ->end()
                            ->end()
                        ->end()
                        ->booleanNode('auto_configure')
                            ->defaultTrue()
                        ->end()
                        ->arrayNode('paths')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('path')
                                        ->isRequired()
                                        ->validate()
                                            ->ifTrue(function (string $v) { return !is_dir($v); })
                                            ->thenInvalid('The path "%s" does not exit')
                                        ->end()
                                    ->end()
                                    ->scalarNode('namespace')->defaultValue('')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
