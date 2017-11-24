<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Bundle\DependencyInjection;

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
            ->scalarNode('class_path')
                ->defaultValue('%kernel.cache_dir%/tsantos_serializer/classes')
            ->end()
            ->scalarNode('generate_strategy')
                ->defaultValue(SerializerClassLoader::AUTOGENERATE_ALWAYS)
            ->end()
            ->arrayNode('mapping')
                ->children()
                    ->arrayNode('cache')
                        ->children()
                            ->enumNode('type')
                                ->values(['file', 'service'])
                                ->defaultValue('file')
                            ->end()
                            ->scalarNode('id')->end()
                            ->scalarNode('path')
                                ->defaultValue('%kernel.cache_dir%/tsantos_serializer/metadata')
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('paths')
                        ->prototype('array')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('path')->isRequired()->end()
                                ->scalarNode('namespace')->defaultValue('')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $tb;
    }
}
