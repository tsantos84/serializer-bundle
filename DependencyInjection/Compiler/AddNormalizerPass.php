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
        $services = \array_merge(
            $this->findAndSortTaggedServices('tsantos_serializer.normalizer', $container),
            $this->findAndSortTaggedServices('tsantos_serializer.denormalizer', $container)
        );

        $definition = $container->getDefinition('tsantos_serializer.normalizer_registry');

        $calls = [];

        foreach ($services as $service) {
            if (isset($calls[(string) $service])) {
                continue;
            }
            $definition->addMethodCall('add', [$service]);
            $calls[(string) $service] = true;
        }
    }
}
