<?php
/**
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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
        $definition = $container->getDefinition('tsantos_serializer.normalizer_registry');
        $this->addMethodCall($definition, array_merge($container->findTaggedServiceIds('tsantos_serializer.normalizer'), $container->findTaggedServiceIds('tsantos_serializer.denormalizer')));
    }

    private function addMethodCall(Definition $definition, array $services): void
    {
        foreach ($services as $id => $tags) {
            array_map(function () use ($id, $definition) {
                $definition->addMethodCall('add', [new Reference($id)]);
            }, $tags);
        }
    }
}
