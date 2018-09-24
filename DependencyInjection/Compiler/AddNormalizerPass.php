<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AddNormalizerPass.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class AddNormalizerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container)
    {
        $this->addMethodCall($container, 'tsantos_serializer.normalizer');
        $this->addMethodCall($container, 'tsantos_serializer.denormalizer');
    }

    private function addMethodCall(ContainerBuilder $container, string $tag): void
    {
        $definition = $container->getDefinition('tsantos_serializer.normalizer_registry');

        $services = $this->findAndSortTaggedServices($tag, $container);

        foreach ($services as $id) {
            $definition->addMethodCall('add', [new Reference($id)]);
        }
    }
}
