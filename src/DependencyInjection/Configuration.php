<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use TSantos\Serializer\SerializerClassLoader;

/**
 * Class Configuration
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
            ->scalarNode('format')
                ->cannotBeEmpty()
                ->defaultValue('json')
            ->end()
            ->scalarNode('class_path')
                ->defaultValue('%kernel.cache_dir%/tsantos_serializer/classes')
            ->end()
            ->enumNode('generate_strategy')
                ->values(['never', 'always', 'file_not_exists'])
                ->defaultValue('file_not_exists')
            ->end()
            ->arrayNode('mapping')
                ->isRequired()
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
                    ->arrayNode('paths')
                        ->isRequired()
                        ->requiresAtLeastOneElement()
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
