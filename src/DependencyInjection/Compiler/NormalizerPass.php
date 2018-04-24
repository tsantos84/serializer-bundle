<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class NormalizerPass
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class NormalizerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('tsantos_serializer.normalizer_registry')) {
            return;
        }

        $definition = $container->getDefinition('tsantos_serializer.normalizer_registry');

        foreach ($container->findTaggedServiceIds('tsantos_serializer.normalizer') as $id => $tags) {
            foreach ($tags as $tag) {
                $definition->addMethodCall('add', [new Reference($id)]);
            }
        }

        foreach ($container->findTaggedServiceIds('tsantos_serializer.denormalizer') as $id => $tags) {
            foreach ($tags as $tag) {
                $definition->addMethodCall('add', [new Reference($id)]);
            }
        }
    }
}