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

/**
 * Class Configuration.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $tb = new TreeBuilder();

        $root = $tb
            ->root('tsantos_serializer', 'array')
            ->addDefaultsIfNotSet()
            ->children()
        ;

        $root
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
            ->booleanNode('enable_property_grouping')
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
                                ->defaultValue('file')
                            ->end()
                            ->scalarNode('id')->end()
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
            ->end();

        return $tb;
    }
}
